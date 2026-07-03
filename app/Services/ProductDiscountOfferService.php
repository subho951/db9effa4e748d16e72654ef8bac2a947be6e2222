<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductDiscountOfferService
{
    public static function applyToOrder($orderId): void
    {
        $orderItems = DB::table('order_details')
            ->join('products', 'order_details.item_id', '=', 'products.id')
            ->select(
                'order_details.item_id',
                'order_details.qty',
                'order_details.price',
                'products.brand_id'
            )
            ->where('order_details.order_id', '=', $orderId)
            ->where('order_details.status', '=', 1)
            ->get();

        if($orderItems->isEmpty()){
            return;
        }

        $orderMap = $orderItems->mapWithKeys(function($item){
            return [
                $item->item_id => [
                    'qty'      => (int)$item->qty,
                    'price'    => (float)$item->price,
                    'brand_id' => (int)$item->brand_id,
                ],
            ];
        });

        $discounts = [];
        $discountTexts = [];

        self::applyVoucherDiscounts($orderItems, $discounts, $discountTexts);
        self::applyOfferDiscounts($orderItems, $orderMap, $discounts, $discountTexts);

        foreach($orderItems as $item){
            DB::table('order_details')
                ->where('order_id', '=', $orderId)
                ->where('item_id', '=', $item->item_id)
                ->update([
                    'discount_amount' => round($discounts[$item->item_id] ?? 0, 2),
                    'discount_text'   => $discountTexts[$item->item_id] ?? null,
                    'subtotal'        => round(((float)$item->qty * (float)$item->price), 2),
                ]);
        }

        $subtotal = (float) DB::table('order_details')->where('order_id', '=', $orderId)->where('status', '=', 1)->sum('subtotal');
        $discountAmount = (float) DB::table('order_details')->where('order_id', '=', $orderId)->where('status', '=', 1)->sum('discount_amount');
        $discountedAmount = ($subtotal - $discountAmount);
        $deliveryAmount = (float) (DB::table('orders')->where('id', '=', $orderId)->value('delivery_amount') ?? 0);

        DB::table('orders')
            ->where('id', '=', $orderId)
            ->update([
                'subtotal'          => round($subtotal, 2),
                'discount_amount'   => round($discountAmount, 2),
                'discounted_amount' => round($discountedAmount, 2),
                'net_amount'        => round(($discountedAmount + $deliveryAmount), 2),
            ]);
    }

    private static function applyVoucherDiscounts($orderItems, &$discounts, &$discountTexts): void
    {
        $today = Carbon::today()->toDateString();

        foreach($orderItems as $item){
            $voucher = DB::table('product_discount_vouchers as pdv')
                ->join('coupons as c', 'pdv.voucher_code', '=', 'c.voucher_code')
                ->where('pdv.status', '=', 1)
                ->where('c.status', '=', 1)
                ->where('pdv.product_id', '=', $item->item_id)
                ->whereDate('c.from_date', '<=', $today)
                ->whereDate('c.to_date', '>=', $today)
                ->orderBy('pdv.retail_discounted_price', 'asc')
                ->select('pdv.*', 'c.name as coupon_name')
                ->first();

            if(!$voucher){
                continue;
            }

            $discount = min(
                ((float)$item->price * (int)$item->qty),
                ((float)$voucher->retail_discount * (int)$item->qty)
            );
            self::keepBestDiscount($item->item_id, $discount, ($voucher->coupon_name ?: $voucher->voucher_code), $discounts, $discountTexts);
        }
    }

    private static function applyOfferDiscounts($orderItems, $orderMap, &$discounts, &$discountTexts): void
    {
        $rules = DB::table('product_multiple_buys as pmb')
            ->leftJoin('products as owner_products', 'pmb.product_id', '=', 'owner_products.id')
            ->select('pmb.*', 'owner_products.brand_id as owner_brand_id')
            ->where('pmb.status', '=', 1)
            ->get();

        foreach($rules as $rule){
            if(self::isProductDiscountOffer($rule)){
                if(!self::isOfferActiveNow($rule)){
                    continue;
                }
                self::applyProductOwnedOffer($rule, $orderItems, $discounts, $discountTexts);
                continue;
            }

            self::applyLegacyComboRule($rule, $orderMap, $discounts, $discountTexts);
        }
    }

    private static function isProductDiscountOffer($rule): bool
    {
        return trim((string)($rule->offer_name ?? '')) !== '';
    }

    private static function isOfferActiveNow($rule): bool
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        if(!empty($rule->offer_start_date) && $rule->offer_start_date > $today){
            return false;
        }
        if(empty($rule->offer_no_expiry) && !empty($rule->offer_end_date) && $rule->offer_end_date < $today){
            return false;
        }

        $days = json_decode($rule->offer_available_days ?? '["ALL"]', true);
        if(!is_array($days) || empty($days)){
            $days = ['ALL'];
        }
        $currentDay = strtoupper($now->format('D'));
        if(!in_array('ALL', $days, true) && !in_array($currentDay, $days, true)){
            return false;
        }

        $startTime = $rule->offer_start_time ?? null;
        $endTime = $rule->offer_end_time ?? null;
        if($startTime && $endTime){
            $currentTime = $now->format('H:i:s');
            if($startTime <= $endTime && ($currentTime < $startTime || $currentTime > $endTime)){
                return false;
            }
            if($startTime > $endTime && ($currentTime < $startTime && $currentTime > $endTime)){
                return false;
            }
        }

        return true;
    }

    private static function applyProductOwnedOffer($rule, $orderItems, &$discounts, &$discountTexts): void
    {
        $scope = strtoupper((string)($rule->discount_scope ?? 'PRODUCT'));
        $ownerBrandId = (int)($rule->owner_brand_id ?? 0);
        $minQty = max(1, (int)$rule->product1_min_qty);

        foreach($orderItems as $item){
            $matches = ($scope === 'BRAND')
                ? ((int)$item->brand_id === $ownerBrandId && $ownerBrandId > 0)
                : ((int)$item->item_id === (int)$rule->product_id);

            if(!$matches || (int)$item->qty < $minQty){
                continue;
            }

            $lineSubtotal = ((float)$item->price * (int)$item->qty);
            if($rule->barcode_discount_type === 'PERCENTAGE'){
                $discount = (($lineSubtotal * (float)$rule->discount_amount) / 100);
            } else {
                $discount = ((float)$rule->discount_amount * (int)$item->qty);
            }

            $discount = min($lineSubtotal, $discount);
            $text = trim((string)($rule->offer_display_name ?: $rule->offer_name));
            self::keepBestDiscount($item->item_id, $discount, $text, $discounts, $discountTexts);
        }
    }

    private static function applyLegacyComboRule($rule, $orderMap, &$discounts, &$discountTexts): void
    {
        $p1 = (int)$rule->product_id;
        $p2 = (int)$rule->product2_id;

        if(!$orderMap->has($p1)){
            return;
        }

        $qty1 = (int)$orderMap[$p1]['qty'];
        $price1 = (float)$orderMap[$p1]['price'];
        $min1 = max(1, (int)$rule->product1_min_qty);

        $qty2 = (int)($orderMap[$p2]['qty'] ?? 0);
        $price2 = (float)($orderMap[$p2]['price'] ?? 0);
        $min2 = (int)$rule->product2_min_qty;

        if($qty1 >= $min1 && ($p2 == 0 || $min2 == 0)){
            if($rule->barcode_discount_type === 'FLAT'){
                $discount1 = (float)$rule->discount_amount;
                $discountText = 'Flat $'.number_format((float)$rule->discount_amount, 2).' Discount';
            } else {
                $percent = (float)$rule->discount_amount;
                $discount1 = (($price1 * $percent / 100) * $qty1);
                $discountText = $percent.'% Discount';
            }
            self::keepBestDiscount($p1, $discount1, $discountText, $discounts, $discountTexts);
            return;
        }

        if($qty1 < $min1 || $qty2 < $min2){
            return;
        }

        $pairCount = min(floor($qty1 / $min1), floor($qty2 / $min2));
        if($pairCount <= 0){
            return;
        }

        if($rule->barcode_discount_type === 'FLAT'){
            $discount1 = ((float)$rule->discount_amount * $pairCount * $min1);
            $discount2 = ((float)$rule->discount_amount * $pairCount * $min2);
            $discountText = 'Flat $'.number_format((float)$rule->discount_amount, 2).' Combo Discount (x'.$pairCount.')';
        } else {
            $percent = (float)$rule->discount_amount;
            $discount1 = (($price1 * $percent / 100) * $pairCount * $min1);
            $discount2 = (($price2 * $percent / 100) * $pairCount * $min2);
            $discountText = $percent.'% Combo Discount (x'.$pairCount.')';
        }

        self::keepBestDiscount($p1, $discount1, $discountText, $discounts, $discountTexts);
        self::keepBestDiscount($p2, $discount2, $discountText, $discounts, $discountTexts);
    }

    private static function keepBestDiscount($productId, $discount, $text, &$discounts, &$discountTexts): void
    {
        $discount = max(0, round((float)$discount, 2));
        if(!isset($discounts[$productId]) || $discount > $discounts[$productId]){
            $discounts[$productId] = $discount;
            $discountTexts[$productId] = $text;
        }
    }
}
