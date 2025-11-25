<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\FastButton;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\WarehouseStock;
use App\Models\ShopStock;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
use Dompdf\Dompdf;
use Dompdf\Options;
class BillingController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Billing',
            'controller'        => 'BillingController',
            'controller_route'  => 'billing',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $user_id = session('user_id');
            /* order no generate */
                $getLastOrder = Order::orderBy('id', 'DESC')->first();
                if($getLastOrder){
                    if($getLastOrder->operator_id == $user_id){
                        if($getLastOrder->status >= 3){
                            $is_new_bill_bo_generate = 1;
                        } else {
                            $is_new_bill_bo_generate = 0;
                        }
                    } else {
                        $getLastOrderOperator = Order::where('operator_id', '=', $user_id)->orderBy('id', 'DESC')->first();
                        if($getLastOrderOperator){
                            if($getLastOrderOperator->status >= 3){
                                $is_new_bill_bo_generate = 1;
                            } else {
                                $is_new_bill_bo_generate = 0;
                            }
                        } else {
                            $is_new_bill_bo_generate = 1;
                        }
                    }
                } else {
                    $is_new_bill_bo_generate = 1;
                }

                if($is_new_bill_bo_generate){
                    if($getLastOrder){
                        $sl_no              = $getLastOrder->sl_no;
                        $next_sl_no         = $sl_no + 1;
                        $next_sl_no_string  = str_pad($next_sl_no, 8, 0, STR_PAD_LEFT);
                        $order_no           = $next_sl_no_string;
                    } else {
                        $next_sl_no         = 1;
                        $next_sl_no_string  = str_pad($next_sl_no, 8, 0, STR_PAD_LEFT);
                        $order_no           = $next_sl_no_string; 
                    }
                    $field = [
                        'sl_no'         => $next_sl_no,
                        'order_no'      => $order_no,
                        'operator_id'   => $user_id,
                        'status'        => 0,
                    ];
                    $order_id = Order::insertGetId($field);
                } else {
                    if($getLastOrder){
                        if($getLastOrder->operator_id == $user_id){
                            $order_id = $getLastOrder->id;
                            $order_no = $getLastOrder->order_no;
                        } else {
                            $getLastOrderOperator = Order::where('operator_id', '=', $user_id)->orderBy('id', 'DESC')->first();
                            if($getLastOrderOperator){
                                $order_id = $getLastOrderOperator->id;
                                $order_no = $getLastOrderOperator->order_no;
                            }
                        }
                    }
                }
            /* order no generate */
            return redirect("user/" . $this->data['controller_route'] . "/billing-item/" . Helper::encoded($order_id));
        }
    /* list */
    /* new or existing order */
        public function billingItem($order_id){
            $order_id                       = Helper::decoded($order_id);
            Order::where('id', '=', $order_id)->update(['status' => 0]);
            $data['getOrder']               = Order::where('id', '=', $order_id)->first();
            $data['getOrderItems']          = DB::table('order_details')
                                                ->join('products', 'order_details.item_id', '=', 'products.id')
                                                ->select('order_details.*', 'products.name as product_name', 'products.sku as product_sku')
                                                // ->where('order_details.status', '=', 1)
                                                ->where('order_details.order_id', '=', $order_id)
                                                ->orderBy('order_details.id', 'ASC')
                                                ->get();
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'billing.list';
            echo $this->user_after_login_billing_layout($title,$page_name,$data);
        }
        public function addToCart(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            if($requestData['key'] == env('PROJECT_KEY')){
                $barcode            = $requestData['barcode'];
                $order_id           = $requestData['order_id'];
                // $getProduct         = Product::where('barcode', '=', $barcode)->first();
                $getProduct         = Product::select(
                                                        'id',
                                                        'name',
                                                        'sku',
                                                        'retail_price_inc_tax',
                                                        'barcode',
                                                    )
                                            ->where(function($query) {
                                                $query->where('status', 1);
                                            })
                                            ->where(function($query) use ($barcode) {
                                                    $query->where('barcode', 'LIKE', '%'.$barcode.'%')
                                                      ->orWhere('sku', 'LIKE', '%'.$barcode.'%');
                                            })
                                            ->first();
                if($getProduct){
                    /* orders details table */
                        $checkAlreadyAdded = OrderDetail::where('order_id', '=', $order_id)->where('item_id', '=', $getProduct->id)->first();
                        if($checkAlreadyAdded){
                            $qty                = $checkAlreadyAdded->qty + 1;
                            /* discount calculation */
                                $discount_amount    = 0;
                                $today = now(); // Get current date and time
                                $minDiscountedPrice = \DB::table('product_discount_vouchers as pdv')
                                                        ->join('coupons as c', 'pdv.voucher_code', '=', 'c.voucher_code') // Join with coupons table
                                                        ->where('pdv.product_id', $getProduct->id) // Filter by product_id
                                                        ->whereDate('c.from_date', '<=', $today) // Coupon must be active
                                                        ->whereDate('c.to_date', '>=', $today) // Coupon must not be expired
                                                        ->orderBy('pdv.retail_discounted_price', 'asc')
                                                        ->select('pdv.*', 'c.from_date', 'c.to_date') // Select needed columns
                                                        ->first();
                                if($minDiscountedPrice){
                                    $per_unit_discount          = (($minDiscountedPrice)?$minDiscountedPrice->retail_discount:0);
                                    $per_unit_discounted_price  = (($minDiscountedPrice)?$minDiscountedPrice->retail_discounted_price:0);
                                    $discount_amount            = ($per_unit_discount * $qty);
                                    $price                      = $getProduct->retail_price_inc_tax;
                                    $subtotal                   = (($price * $qty));
                                } else {
                                    $price                      = $getProduct->retail_price_inc_tax;
                                    $subtotal                   = (($price * $qty));
                                }
                            /* discount calculation */
                            $field1             = [
                                'order_id'              => $order_id,
                                'item_id'               => $getProduct->id,
                                'price'                 => $price,
                                'qty'                   => $qty,
                                'discount_amount'       => $discount_amount,
                                'subtotal'              => $subtotal,
                            ];
                            OrderDetail::where('order_id', '=', $order_id)->where('item_id', '=', $getProduct->id)->update($field1);
                        } else {
                            $qty                = 1;
                            /* discount calculation */
                                $discount_amount    = 0;
                                $today = now(); // Get current date and time
                                $minDiscountedPrice = \DB::table('product_discount_vouchers as pdv')
                                                        ->join('coupons as c', 'pdv.voucher_code', '=', 'c.voucher_code') // Join with coupons table
                                                        ->where('pdv.product_id', $getProduct->id) // Filter by product_id
                                                        ->whereDate('c.from_date', '<=', $today) // Coupon must be active
                                                        ->whereDate('c.to_date', '>=', $today) // Coupon must not be expired
                                                        ->orderBy('pdv.retail_discounted_price', 'asc')
                                                        ->select('pdv.*', 'c.from_date', 'c.to_date') // Select needed columns
                                                        ->first();
                                if($minDiscountedPrice){
                                    $per_unit_discount          = (($minDiscountedPrice)?$minDiscountedPrice->retail_discount:0);
                                    $per_unit_discounted_price  = (($minDiscountedPrice)?$minDiscountedPrice->retail_discounted_price:0);
                                    $discount_amount            = ($per_unit_discount * $qty);
                                    $price                      = $getProduct->retail_price_inc_tax;
                                    // $subtotal                   = (($price * $qty) - $discount_amount);
                                    $subtotal                   = (($price * $qty));
                                    // echo $per_unit_discount . '||' . $per_unit_discounted_price . '||' . $discount_amount . '||' . $price;
                                } else {
                                    $price                      = $getProduct->retail_price_inc_tax;
                                    $subtotal                   = (($price * $qty));
                                }
                            /* discount calculation */
                            $field1             = [
                                'order_id'          => $order_id,
                                'item_id'           => $getProduct->id,
                                'price'             => $price,
                                'qty'               => $qty,
                                'discount_amount'   => $discount_amount,
                                'subtotal'          => $subtotal,
                            ];
                            // Helper::pr($field1);
                            OrderDetail::insert($field1);
                        }
                    /* orders details table */
                    /* product multiple buy logic */
                        $orderId = $order_id;

                        // 1. Fetch order items
                        $orderItems = DB::table('order_details')
                            ->select('item_id', 'qty', 'price')
                            ->where('order_id', $orderId)
                            ->get();

                        // Map product => qty, price
                        $orderMap = $orderItems->mapWithKeys(function ($item) {
                            return [
                                $item->item_id => [
                                    'qty'   => $item->qty,
                                    'price' => $item->price
                                ]
                            ];
                        });

                        // 2. Combo rules
                        $comboRules = DB::table('product_multiple_buys')
                            ->where('status', 1)
                            ->get();

                        $discounts = [];
                        $discountTexts = [];

                        foreach ($comboRules as $rule) {

                            $p1 = $rule->product_id;
                            $p2 = $rule->product2_id;

                            if ($orderMap->has($p1) && $orderMap->has($p2)) {

                                $qty1 = $orderMap[$p1]['qty'];
                                $qty2 = $orderMap[$p2]['qty'];

                                $min1 = $rule->product1_min_qty;
                                $min2 = $rule->product2_min_qty;

                                // ❗ NEW LOGIC: At least one full pair must exist
                                $pairCount = min(
                                    floor($qty1 / $min1),
                                    floor($qty2 / $min2)
                                );

                                // If at least 1 pair exists → apply discount
                                if ($pairCount > 0) {

                                    $price1 = $orderMap[$p1]['price'];
                                    $price2 = $orderMap[$p2]['price'];

                                    $discount1 = 0;
                                    $discount2 = 0;

                                    /* FLAT discount */
                                    if ($rule->barcode_discount_type == "FLAT") {

                                        $pairCount = min(
                                            floor($qty1 / $min1),
                                            floor($qty2 / $min2)
                                        );

                                        $discount1 = $rule->discount_amount * $pairCount * $min1;
                                        $discount2 = $rule->discount_amount * $pairCount * $min2;

                                        $discountText = "Flat Rs {$rule->discount_amount} Combo Discount (x{$pairCount})";
                                    }

                                    /* PERCENTAGE discount */
                                    elseif ($rule->barcode_discount_type == "PERCENTAGE") {

                                        $percent = $rule->discount_amount;

                                        $pairCount = min(
                                            floor($qty1 / $min1),
                                            floor($qty2 / $min2)
                                        );

                                        $discount1 = ($price1 * $percent / 100) * $pairCount * $min1;
                                        $discount2 = ($price2 * $percent / 100) * $pairCount * $min2;

                                        $discountText = "{$percent}% Combo Discount (x{$pairCount})";
                                    }

                                    $discounts[$p1] = $discount1;
                                    $discounts[$p2] = $discount2;

                                    $discountTexts[$p1] = $discountText;
                                    $discountTexts[$p2] = $discountText;
                                }
                            }
                        }

                        /* 4. Update order_details */
                        foreach ($orderItems as $item) {

                            $pid   = $item->item_id;
                            $price = $item->price;
                            $qty   = $item->qty;

                            $discountTotal = $discounts[$pid] ?? 0;

                            DB::table('order_details')
                                ->where('order_id', $orderId)
                                ->where('item_id', $pid)
                                ->update([
                                    'discount_amount' => ($discountTotal),
                                    'discount_text'   => $discountTexts[$pid] ?? null,
                                    'subtotal'        => ($price * $qty)
                                ]);
                        }
                    /* product multiple buy logic */
                    /* orders table */
                        $getTotalAmount     = OrderDetail::where('order_id', '=', $order_id)->sum('subtotal');
                        $getDiscountAmount  = OrderDetail::where('order_id', '=', $order_id)->sum('discount_amount');
                        $subtotal           = $getTotalAmount;
                        $discount_amount    = $getDiscountAmount;
                        $discounted_amount  = ($subtotal - $discount_amount);
                        $delivery_amount    = 0;
                        $net_amount         = ($discounted_amount + $delivery_amount);
                        $field2     = [
                            'subtotal'                  => $subtotal,
                            'discount_amount'           => $discount_amount,
                            'discounted_amount'         => $discounted_amount,
                            'delivery_amount'           => $delivery_amount,
                            'net_amount'                => $net_amount,
                        ];
                        Order::where('id', '=', $order_id)->update($field2);
                    /* orders table */
                    /* item table rearrange on the go */
                        $data['getOrder']               = Order::where('id', '=', $order_id)->first();
                        $data['getOrderItems']          = DB::table('order_details')
                                                            ->join('products', 'order_details.item_id', '=', 'products.id')
                                                            ->select('order_details.*', 'products.name as product_name', 'products.sku as product_sku')
                                                            ->where('order_details.status', '=', 1)
                                                            ->where('order_details.order_id', '=', $order_id)
                                                            ->orderBy('order_details.id', 'ASC')
                                                            ->get();
                        $item_table_html                = view('front.maincontents.billing.ajax-order-item', $data)->render();
                    /* item table rearrange on the go */
                    $apiStatus                          = TRUE;
                    http_response_code(200);
                    $apiResponse                        = [
                        'item_table_html' => $item_table_html,
                    ];
                    $apiMessage                         = 'Product added into cart successfully';
                    $apiExtraField                      = 'response_code';
                    $apiExtraData                       = http_response_code();
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Product not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function itemDelete(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            if($requestData['key'] == env('PROJECT_KEY')){
                $item_id            = $requestData['item_id'];
                $order_id           = $requestData['order_id'];
                $getProduct         = Product::where('id', '=', $item_id)->first();
                if($getProduct){
                    OrderDetail::where('order_id', '=', $order_id)->where('item_id', '=', $item_id)->delete();
                    /* orders table */
                        $getTotalAmount     = OrderDetail::where('order_id', '=', $order_id)->sum('subtotal');
                        $getDiscountAmount  = OrderDetail::where('order_id', '=', $order_id)->sum('discount_amount');
                        $subtotal           = $getTotalAmount;
                        $discount_amount    = $getDiscountAmount;
                        $discounted_amount  = ($subtotal - $discount_amount);
                        $delivery_amount    = 0;
                        $net_amount         = ($discounted_amount + $delivery_amount);
                        $field2     = [
                            'subtotal'                  => $subtotal,
                            'discount_amount'           => $discount_amount,
                            'discounted_amount'         => $discounted_amount,
                            'delivery_amount'           => $delivery_amount,
                            'net_amount'                => $net_amount,
                        ];
                        Order::where('id', '=', $order_id)->update($field2);
                    /* orders table */
                    /* item table rearrange on the go */
                        $data['getOrder']               = Order::where('id', '=', $order_id)->first();
                        $data['getOrderItems']          = DB::table('order_details')
                                                            ->join('products', 'order_details.item_id', '=', 'products.id')
                                                            ->select('order_details.*', 'products.name as product_name', 'products.sku as product_sku')
                                                            ->where('order_details.status', '=', 1)
                                                            ->where('order_details.order_id', '=', $order_id)
                                                            ->orderBy('order_details.id', 'ASC')
                                                            ->get();
                        $item_table_html                = view('front.maincontents.billing.ajax-order-item', $data)->render();
                    /* item table rearrange on the go */
                    $apiStatus                          = TRUE;
                    http_response_code(200);
                    $apiResponse                        = [
                        'item_table_html' => $item_table_html,
                    ];
                    $apiMessage                         = 'Product deleted from cart successfully';
                    $apiExtraField                      = 'response_code';
                    $apiExtraData                       = http_response_code();
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Product not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function billingItemReturn(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            if($requestData['key'] == env('PROJECT_KEY')){
                $order_id           = $requestData['order_id'];
                $order_details_id   = $requestData['order_details_id'];
                $getOrder           = Order::where('id', '=', $order_id)->first();
                if($getOrder){
                    $getOrderItem = OrderDetail::where('order_id', '=', $order_id)->where('id', '=', $order_details_id)->first();
                    $discount_tot = 0;
                    $subtotal_tot = 0;
                    if($getOrderItem){
                        $price          = (0 - $getOrderItem->price);
                        $qty            = $getOrderItem->qty;
                        $id             = $getOrderItem->id;
                        $subtotal       = ($price * $qty);

                        $fields         = [
                            'price'     => $price,
                            'subtotal'  => $subtotal,
                        ];
                        OrderDetail::where('id', '=', $id)->update($fields);
                    }

                    $carts = OrderDetail::where('order_id', '=', $order_id)->get();
                    if($carts){
                        foreach($carts as $cart){
                            $discount_tot   += $cart->discount_amount;
                            $subtotal_tot   += $cart->subtotal;
                        }
                    }

                    $net_amount = ($subtotal_tot - $discount_tot);
                    
                    $fields2         = [
                        'subtotal'              => $subtotal_tot,
                        'discounted_amount'     => $discount_tot,
                        'net_amount'            => $net_amount,
                    ];
                    Order::where('id', '=', $order_id)->update($fields2);

                    $apiMessage                         = 'Items return marked successfully';
                    $apiExtraField                      = 'response_code';
                    $apiExtraData                       = http_response_code();
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Product not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function billingChangeStatus(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            if($requestData['key'] == env('PROJECT_KEY')){
                $order_id           = $requestData['order_id'];
                $item_id            = $requestData['item_id'];
                $status             = $requestData['status'];

                $order_status       = $status;
                if($order_status == 3){
                    $statusName = 'Hold';
                } elseif($order_status == 4){
                    $statusName = 'Cancelled';
                }

                $getOrder           = Order::where('id', '=', $order_id)->first();
                if($getOrder){
                    // Order::where('id', '=', $order_id)->update(['status' => $order_status]);
                    OrderDetail::where('order_id', '=', $order_id)->update(['status' => $order_status]);

                    /* update amounts */
                        $subtotal = 0;
                        $getItems = OrderDetail::where('order_id', '=', $order_id)->get();
                        if($getItems){
                            foreach($getItems as $getItem){
                                $subtotal += $getItem->subtotal;
                            }
                        }

                        $discounted_amount = ($subtotal - $getOrder->discount_amount);
                        $net_amount = ($discounted_amount + $getOrder->delivery_amount);
                        $fields = [
                            'subtotal'              => $subtotal,
                            'discount_amount'       => $getOrder->discount_amount,
                            'discounted_amount'     => $discounted_amount,
                            'delivery_amount'       => $getOrder->delivery_amount,
                            'net_amount'            => $net_amount,
                            'status'                => $order_status,
                        ];
                        Order::where('id', '=', $order_id)->update($fields);
                    /* update amounts */

                    $apiStatus                          = TRUE;
                    http_response_code(200);
                    $apiMessage                         = 'Order item marked as ' . $statusName . ' successfully';
                    $apiExtraField                      = 'response_code';
                    $apiExtraData                       = http_response_code();
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Order not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function billingUpdateQty(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            if($requestData['key'] == env('PROJECT_KEY')){
                $item_id            = $requestData['item_id'];
                $order_id           = $requestData['order_id'];
                $qtyVal             = $requestData['qtyVal'];
                $getOrder           = Order::where('id', '=', $order_id)->first();
                if($getOrder){
                    $getProduct         = Product::where('id', '=', $item_id)->first();
                    if($getProduct){
                        /* orders details table */
                            $checkAlreadyAdded = OrderDetail::where('order_id', '=', $order_id)->where('item_id', '=', $item_id)->first();
                            if($checkAlreadyAdded){
                                $qty                = $qtyVal;
                                /* discount calculation */
                                    $discount_amount    = 0;
                                    $today = now(); // Get current date and time
                                    $minDiscountedPrice = \DB::table('product_discount_vouchers as pdv')
                                                            ->join('coupons as c', 'pdv.voucher_code', '=', 'c.voucher_code') // Join with coupons table
                                                            ->where('pdv.product_id', $getProduct->id) // Filter by product_id
                                                            ->whereDate('c.from_date', '<=', $today) // Coupon must be active
                                                            ->whereDate('c.to_date', '>=', $today) // Coupon must not be expired
                                                            ->orderBy('pdv.retail_discounted_price', 'asc')
                                                            ->select('pdv.*', 'c.from_date', 'c.to_date') // Select needed columns
                                                            ->first();
                                    if($minDiscountedPrice){
                                        $per_unit_discount          = (($minDiscountedPrice)?$minDiscountedPrice->retail_discount:0);
                                        $per_unit_discounted_price  = (($minDiscountedPrice)?$minDiscountedPrice->retail_discounted_price:0);
                                        $discount_amount            = ($per_unit_discount * $qty);
                                        $price                      = $getProduct->retail_price_inc_tax;
                                        // $subtotal                   = (($price * $qty) - $discount_amount);
                                        $subtotal                   = (($price * $qty));
                                        // echo $per_unit_discount . '||' . $per_unit_discounted_price . '||' . $discount_amount . '||' . $price;
                                    } else {
                                        $price                      = $getProduct->retail_price_inc_tax;
                                        $subtotal                   = (($price * $qty));
                                    }
                                /* discount calculation */
                                $field1             = [
                                    'price'                 => $price,
                                    'qty'                   => $qty,
                                    'discount_amount'       => $discount_amount,
                                    'subtotal'              => $subtotal,
                                ];
                                OrderDetail::where('order_id', '=', $order_id)->where('item_id', '=', $getProduct->id)->update($field1);
                            }
                        /* orders details table */
                        /* product multiple buy logic */
                            $orderId = $order_id;

                            // 1. Fetch order items
                            $orderItems = DB::table('order_details')
                                ->select('item_id', 'qty', 'price')
                                ->where('order_id', $orderId)
                                ->get();

                            // Map product => qty, price
                            $orderMap = $orderItems->mapWithKeys(function ($item) {
                                return [
                                    $item->item_id => [
                                        'qty'   => $item->qty,
                                        'price' => $item->price
                                    ]
                                ];
                            });

                            // 2. Combo rules
                            $comboRules = DB::table('product_multiple_buys')
                                ->where('status', 1)
                                ->get();

                            $discounts = [];
                            $discountTexts = [];

                            foreach ($comboRules as $rule) {

                                $p1 = $rule->product_id;
                                $p2 = $rule->product2_id;

                                if ($orderMap->has($p1) && $orderMap->has($p2)) {

                                    $qty1 = $orderMap[$p1]['qty'];
                                    $qty2 = $orderMap[$p2]['qty'];

                                    $min1 = $rule->product1_min_qty;
                                    $min2 = $rule->product2_min_qty;

                                    // ❗ NEW LOGIC: At least one full pair must exist
                                    $pairCount = min(
                                        floor($qty1 / $min1),
                                        floor($qty2 / $min2)
                                    );

                                    // If at least 1 pair exists → apply discount
                                    if ($pairCount > 0) {

                                        $price1 = $orderMap[$p1]['price'];
                                        $price2 = $orderMap[$p2]['price'];

                                        $discount1 = 0;
                                        $discount2 = 0;

                                        /* FLAT discount */
                                        if ($rule->barcode_discount_type == "FLAT") {

                                            $pairCount = min(
                                                floor($qty1 / $min1),
                                                floor($qty2 / $min2)
                                            );

                                            $discount1 = $rule->discount_amount * $pairCount * $min1;
                                            $discount2 = $rule->discount_amount * $pairCount * $min2;

                                            $discountText = "Flat Rs {$rule->discount_amount} Combo Discount (x{$pairCount})";
                                        }

                                        /* PERCENTAGE discount */
                                        elseif ($rule->barcode_discount_type == "PERCENTAGE") {

                                            $percent = $rule->discount_amount;

                                            $pairCount = min(
                                                floor($qty1 / $min1),
                                                floor($qty2 / $min2)
                                            );

                                            $discount1 = ($price1 * $percent / 100) * $pairCount * $min1;
                                            $discount2 = ($price2 * $percent / 100) * $pairCount * $min2;

                                            $discountText = "{$percent}% Combo Discount (x{$pairCount})";
                                        }

                                        $discounts[$p1] = $discount1;
                                        $discounts[$p2] = $discount2;

                                        $discountTexts[$p1] = $discountText;
                                        $discountTexts[$p2] = $discountText;
                                    }
                                }
                            }

                            /* 4. Update order_details */
                            foreach ($orderItems as $item) {

                                $pid   = $item->item_id;
                                $price = $item->price;
                                $qty   = $item->qty;

                                $discountTotal = $discounts[$pid] ?? 0;

                                DB::table('order_details')
                                    ->where('order_id', $orderId)
                                    ->where('item_id', $pid)
                                    ->update([
                                        'discount_amount' => ($discountTotal),
                                        'discount_text'   => $discountTexts[$pid] ?? null,
                                        'subtotal'        => ($price * $qty)
                                    ]);
                            }
                        /* product multiple buy logic */
                        /* orders table */
                            $getTotalAmount     = OrderDetail::where('order_id', '=', $order_id)->sum('subtotal');
                            $getDiscountAmount  = OrderDetail::where('order_id', '=', $order_id)->sum('discount_amount');
                            $subtotal           = $getTotalAmount;
                            $discount_amount    = $getDiscountAmount;
                            $discounted_amount  = ($subtotal - $discount_amount);
                            $delivery_amount    = 0;
                            $net_amount         = ($discounted_amount + $delivery_amount);
                            $field2     = [
                                'subtotal'                  => $subtotal,
                                'discount_amount'           => $discount_amount,
                                'discounted_amount'         => $discounted_amount,
                                'delivery_amount'           => $delivery_amount,
                                'net_amount'                => $net_amount,
                            ];
                            Order::where('id', '=', $order_id)->update($field2);
                        /* orders table */
                        /* item table rearrange on the go */
                            $data['getOrder']               = Order::where('id', '=', $order_id)->first();
                            $data['getOrderItems']          = DB::table('order_details')
                                                                ->join('products', 'order_details.item_id', '=', 'products.id')
                                                                ->select('order_details.*', 'products.name as product_name', 'products.sku as product_sku')
                                                                ->where('order_details.status', '=', 1)
                                                                ->where('order_details.order_id', '=', $order_id)
                                                                ->orderBy('order_details.id', 'ASC')
                                                                ->get();
                            $item_table_html                = view('front.maincontents.billing.ajax-order-item', $data)->render();
                        /* item table rearrange on the go */
                        $apiStatus                          = TRUE;
                        http_response_code(200);
                        $apiResponse                        = [
                            'item_table_html' => $item_table_html,
                        ];
                        $apiMessage                         = 'Order item quantity updated successfully';
                        $apiExtraField                      = 'response_code';
                        $apiExtraData                       = http_response_code();
                    } else {
                        $apiStatus          = FALSE;
                        http_response_code(200);
                        $apiMessage         = 'Product not found';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Order not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function billingSelectDeliveryAddress(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            if($requestData['key'] == env('PROJECT_KEY')){
                $delivery_mode      = $requestData['delivery_mode'];
                $order_id           = $requestData['order_id'];
                $note               = $requestData['note'];
                $getOrder           = Order::where('id', '=', $order_id)->first();
                if($getOrder){
                    Order::where('id', '=', $order_id)->update(['delivery_mode' => $delivery_mode, 'status' => 1, 'note' => $note]);
                    if($delivery_mode == 'Take'){
                        $is_redirect    = 0;
                        $redirect_url   = '';
                    }
                    if($delivery_mode == 'Deliver'){
                        $is_redirect    = 1;
                        $redirect_url   = url('user/billing/billing-delivery-address/' . Helper::encoded($order_id));
                    }
                    if($delivery_mode == 'Pickup'){
                        $is_redirect    = 1;
                        $redirect_url   = url('user/billing/billing-delivery-address/' . Helper::encoded($order_id));
                    }
                    $apiResponse                        = [
                        'is_redirect'   => $is_redirect,
                        'redirect_url'  => $redirect_url,
                    ];
                    $apiStatus                          = TRUE;
                    http_response_code(200);
                    $apiMessage                         = 'Order delivery mode marked as ' . $delivery_mode . ' successfully';
                    $apiExtraField                      = 'response_code';
                    $apiExtraData                       = http_response_code();
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Order not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function billingDeliveryAddress($order_id){
            $order_id                       = Helper::decoded($order_id);
            $data['getOrder']               = Order::where('id', '=', $order_id)->first();
            $data['getOrderItems']          = DB::table('order_details')
                                                ->join('products', 'order_details.item_id', '=', 'products.id')
                                                ->select('order_details.*', 'products.name as product_name', 'products.sku as product_sku')
                                                // ->where('order_details.status', '=', 1)
                                                ->where('order_details.order_id', '=', $order_id)
                                                ->orderBy('order_details.id', 'ASC')
                                                ->get();
            $data['module']                 = $this->data;
            $title                          = 'Delivery Address';
            $page_name                      = 'billing.billing-delivery-address';
            echo $this->user_after_login_billing_layout($title,$page_name,$data);
        }
        public function billingPayment($order_id){
            $order_id                       = Helper::decoded($order_id);
            $data['getOrder']               = Order::where('id', '=', $order_id)->first();
            $data['getOrderItems']          = DB::table('order_details')
                                                ->join('products', 'order_details.item_id', '=', 'products.id')
                                                ->select('order_details.*', 'products.name as product_name', 'products.sku as product_sku')
                                                // ->where('order_details.status', '=', 1)
                                                ->where('order_details.order_id', '=', $order_id)
                                                ->orderBy('order_details.id', 'ASC')
                                                ->get();
            $data['module']                 = $this->data;
            $title                          = 'Payment';
            $page_name                      = 'billing.billing-payment';
            echo $this->user_after_login_billing_layout($title,$page_name,$data);
        }
        public function saveDeliveryAddress(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            if($requestData['key'] == env('PROJECT_KEY')){
                $order_id           = $requestData['order_id'];
                $getOrder           = Order::where('id', '=', $order_id)->first();
                if($getOrder){
                    $delivery_mode  = $getOrder->delivery_mode;
                    if($delivery_mode == 'Take'){
                        $fields = [
                            'customer_tag'          => '',
                            'pickup_name'           => '',
                            'pickup_phone'          => '',
                            'pickup_email'          => '',
                            'delivery_name'         => '',
                            'delivery_phone'        => '',
                            'delivery_email'        => '',
                            'delivery_address'      => '',
                            'delivery_suburb'       => '',
                            'delivery_state'        => '',
                            'delivery_postcode'     => '',
                        ];
                    }
                    if($delivery_mode == 'Deliver'){
                        $fields = [
                            'customer_tag'          => $requestData['customer_tag'],
                            'pickup_name'           => '',
                            'pickup_phone'          => '',
                            'pickup_email'          => '',
                            'delivery_name'         => $requestData['delivery_name'],
                            'delivery_phone'        => $requestData['delivery_phone'],
                            'delivery_email'        => $requestData['delivery_email'],
                            'delivery_address'      => $requestData['delivery_address'],
                            'delivery_suburb'       => $requestData['delivery_suburb'],
                            'delivery_state'        => $requestData['delivery_state'],
                            'delivery_postcode'     => $requestData['delivery_postcode'],
                        ];
                    }
                    if($delivery_mode == 'Pickup'){
                        $fields = [
                            'customer_tag'          => $requestData['customer_tag'],
                            'pickup_name'           => $requestData['pickup_name'],
                            'pickup_phone'          => $requestData['pickup_phone'],
                            'pickup_email'          => $requestData['pickup_email'],
                            'delivery_name'         => '',
                            'delivery_phone'        => '',
                            'delivery_email'        => '',
                            'delivery_address'      => '',
                            'delivery_suburb'       => '',
                            'delivery_state'        => '',
                            'delivery_postcode'     => '',
                        ];
                    }
                    Order::where('id', '=', $order_id)->update($fields);

                    $is_redirect    = 1;
                    $redirect_url   = url('user/billing/billing-payment/' . Helper::encoded($order_id));
                    $apiResponse                        = [
                        'is_redirect'   => $is_redirect,
                        'redirect_url'  => $redirect_url,
                    ];

                    $apiMessage                         = 'Order ' . $delivery_mode . ' address updated successfully';
                    $apiStatus                          = TRUE;
                    http_response_code(200);
                    $apiExtraField                      = 'response_code';
                    $apiExtraData                       = http_response_code();
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Order not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function billingSelectPaymentMode(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            // Helper::pr($requestData);
            if($requestData['key'] == env('PROJECT_KEY')){
                $order_id           = $requestData['order_id'];
                $note               = $requestData['note'];
                $getOrder           = Order::where('id', '=', $order_id)->first();
                $payment_mode       = $requestData['payment_mode'];
                if($payment_mode == 'CASH'){
                    $cash_tendered      = $requestData['cash_tendered'];
                    if($cash_tendered < $getOrder->net_amount){
                        $apiStatus          = FALSE;
                        http_response_code(200);
                        $apiMessage         = 'Cash tendered can\'t be less than order amount';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    } else {
                        if($getOrder){
                            $cash_return = ($cash_tendered - $getOrder->net_amount);
                            Order::where('id', '=', $order_id)->update(['payment_mode' => $payment_mode, 'status' => 2, 'note' => $note, 'cash_tendered' => $cash_tendered, 'cash_return' => $cash_return]);
                            $apiStatus                          = TRUE;
                            http_response_code(200);
                            $apiMessage                         = 'Order payment mode selected as ' . $payment_mode . ' successfully';
                            $apiExtraField                      = 'response_code';
                            $apiExtraData                       = http_response_code();
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'Order not found';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    }
                } elseif($payment_mode == 'CARD'){
                    if($getOrder){
                        Order::where('id', '=', $order_id)->update(['payment_mode' => $payment_mode, 'status' => 2, 'note' => $note]);
                        $apiStatus                          = TRUE;
                        http_response_code(200);
                        $apiMessage                         = 'Order payment mode selected as ' . $payment_mode . ' successfully';
                        $apiExtraField                      = 'response_code';
                        $apiExtraData                       = http_response_code();
                    } else {
                        $apiStatus          = FALSE;
                        http_response_code(200);
                        $apiMessage         = 'Order not found';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                } else {
                    if($getOrder){
                        $card_holder_name = $requestData['card_holder_name'];
                        $getCoupon           = Coupon::where('voucher_code', '=', $card_holder_name)->first();
                        if($getCoupon){
                            $discount_type      = $getCoupon->discount_type;
                            $discount_amount    = $getCoupon->discount_amount;
                            $from_date          = $getCoupon->from_date;
                            $to_date            = $getCoupon->to_date;
                            $subtotal           = $getOrder->subtotal;
                            $delivery_amount    = $getOrder->delivery_amount;

                            $currentDate        = date('Y-m-d');
                            if(($currentDate >= $from_date) && ($currentDate <= $to_date)){
                                if($discount_type == 'Flat'){
                                    $discAmt = $discount_amount;
                                } else {
                                    $discAmt = (($subtotal * $discount_amount) / 100);
                                }
                                $discounted_amount = ($subtotal - $discAmt);
                                $net_amount = ($discounted_amount + $delivery_amount);
                                $fields = [
                                    'discount_amount'       => $discAmt,
                                    'discounted_amount'     => $discounted_amount,
                                    'delivery_amount'       => $delivery_amount,
                                    'net_amount'            => $net_amount,
                                    'payment_mode'          => $payment_mode,
                                    'status'                => 2,
                                    'note'                  => $note
                                ];
                                Order::where('id', '=', $order_id)->update($fields);
                                $apiStatus                          = TRUE;
                                http_response_code(200);
                                $apiMessage                         = 'Coupon code applied successfully';
                                $apiExtraField                      = 'response_code';
                                $apiExtraData                       = http_response_code();
                            } else {
                                $apiStatus          = FALSE;
                                http_response_code(200);
                                $apiMessage         = 'Coupon code expired';
                                $apiExtraField      = 'response_code';
                                $apiExtraData       = http_response_code();
                            }
                        } else {
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'Coupon code not found';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        }
                    } else {
                        $apiStatus          = FALSE;
                        http_response_code(200);
                        $apiMessage         = 'Order not found';
                        $apiExtraField      = 'response_code';
                        $apiExtraData       = http_response_code();
                    }
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function placeOrder(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            if($requestData['key'] == env('PROJECT_KEY')){
                $order_id           = $requestData['order_id'];
                $note               = $requestData['note'];
                $getOrder           = Order::where('id', '=', $order_id)->first();
                if($getOrder){
                    $customer_name  = '';
                    $customer_phone = '';
                    $customer_email = '';
                    if($getOrder){
                        if($getOrder->delivery_mode == 'Deliver'){
                            $customer_name  = $getOrder->delivery_name;
                            $customer_phone = $getOrder->delivery_phone;
                            $customer_email = $getOrder->delivery_email;
                        }
                        if($getOrder->delivery_mode == 'Pickup'){
                            $customer_name  = $getOrder->pickup_name;
                            $customer_phone = $getOrder->pickup_phone;
                            $customer_email = $getOrder->pickup_email;
                        }
                    }
                    $fields = [
                        'customer_name'     => $customer_name,
                        'customer_phone'    => $customer_phone,
                        'customer_email'    => $customer_email,
                        'order_date'        => date('Y-m-d'),
                        'order_time'        => date('H:i:s'),
                        'payment_status'    => 1,
                        'payment_date_time' => date('Y-m-d H:i:s'),
                        'payment_amount'    => $getOrder->net_amount,
                        'note'              => $note,
                        'status'            => 5,
                    ];
                    Order::where('id', '=', $order_id)->update($fields);

                    /* invoice pdf generate */
                        $data['getOrderDetail']         = Order::where('id', '=', $order_id)->first();
                        $order_no                       = (($data['getOrderDetail'])?$data['getOrderDetail']->order_no:'');
                        $generalSetting                 = GeneralSetting::find('1');
                        $subject                        = 'Invoice-' . $order_no;
                        $message                        = view('front.maincontents.billing.pdf-invoice', $data);                        
                        // echo $message;die;
                        $options        = new Options();
                        $options->set('defaultFont', 'Courier');
                        $dompdf         = new Dompdf($options);
                        $html           = $message;
                        $dompdf->loadHtml($html);
                        $dompdf->setPaper('A4', 'portrait');
                        $dompdf->render();
                        $output         = $dompdf->output();
                        // $dompdf->stream("document.pdf", array("Attachment" => true));die;
                        $filename       = $order_no.'.pdf';
                        $pdfFilePath    = 'public/uploads/invoice/' . $filename;
                        file_put_contents($pdfFilePath, $output);
                        Order::where('id', '=', $order_id)->update(['pdf_invoice' => $filename]);
                    /* invoice pdf generate */
                    /* shop stock deduct */
                        $getOrderDetails           = OrderDetail::where('order_id', '=', $order_id)->get();
                        if($getOrderDetails){
                            foreach($getOrderDetails as $getOrderDetail){
                                $order_item_id  = $getOrderDetail->id;
                                $product_id     = $getOrderDetail->item_id;
                                $qty            = $getOrderDetail->qty;
                                $price              = $getOrderDetail->price;

                                if($price > 0){
                                    $getProduct         = Product::where('id', $product_id)->first();
                                    $opening_qty2       = (($getProduct)?$getProduct->shop_stock:0);
                                    $txn_qty2           = $qty;
                                    $closing_qty2       = ($opening_qty2 - $txn_qty2);

                                    $fields12                   = [
                                        'txn_type'              => 'OUT',
                                        'stock_date'            => date('Y-m-d'),
                                        'product_id'            => $product_id,
                                        'opening_qty'           => $opening_qty2,
                                        'txn_qty'               => $txn_qty2,
                                        'closing_qty'           => $closing_qty2,
                                        'note'                  => 'For order #' . $order_no,
                                        'order_id'              => $order_id,
                                        'order_item_id'         => $order_item_id,
                                    ];
                                    ShopStock::insert($fields12);
                                    Product::where('id', $product_id)->update(['shop_stock' => $closing_qty2]);
                                } else {
                                    $getProduct         = Product::where('id', $product_id)->first();
                                    $opening_qty2       = (($getProduct)?$getProduct->shop_stock:0);
                                    $txn_qty2           = $qty;
                                    $closing_qty2       = ($opening_qty2 + $txn_qty2);

                                    $fields12                   = [
                                        'txn_type'              => 'IN',
                                        'stock_date'            => date('Y-m-d'),
                                        'product_id'            => $product_id,
                                        'opening_qty'           => $opening_qty2,
                                        'txn_qty'               => $txn_qty2,
                                        'closing_qty'           => $closing_qty2,
                                        'note'                  => 'For order #' . $order_no,
                                        'order_id'              => $order_id,
                                        'order_item_id'         => $order_item_id,
                                    ];
                                    ShopStock::insert($fields12);
                                    Product::where('id', $product_id)->update(['shop_stock' => $closing_qty2]);
                                }
                            }
                        }
                    /* shop stock deduct */
                    $apiStatus                          = TRUE;
                    http_response_code(200);
                    $apiMessage                         = 'Order placed successfully';
                    $apiExtraField                      = 'response_code';
                    $apiExtraData                       = http_response_code();
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Order not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function billingSearch($order_id){
            $order_id                       = Helper::decoded($order_id);
            $data['getOrder']               = Order::where('id', '=', $order_id)->first();
            $data['getOrderItems']          = DB::table('order_details')
                                                ->join('products', 'order_details.item_id', '=', 'products.id')
                                                ->select('order_details.*', 'products.name as product_name', 'products.sku as product_sku')
                                                ->where('order_details.status', '=', 1)
                                                ->where('order_details.order_id', '=', $order_id)
                                                ->orderBy('order_details.id', 'ASC')
                                                ->get();
            $data['fast_buttons']           = FastButton::select('id', 'product_id', 'name', 'price', 'qty')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Search and Shortcuts';
            $page_name                      = 'billing.billing-search';
            echo $this->user_after_login_billing_layout($title,$page_name,$data);
        }
        public function searchResult(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            if($requestData['key'] == env('PROJECT_KEY')){
                $order_id           = $requestData['order_id'];
                $search_keyword     = $requestData['search_keyword'];
                $getOrder           = Order::where('id', '=', $order_id)->first();
                if($getOrder){
                    $searchProducts   = Product::join('brands', 'products.brand_id', '=', 'brands.id')
                                            ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                            ->select(
                                                        'products.id',
                                                        'products.name',
                                                        'products.sku',
                                                        'products.retail_price_inc_tax',
                                                    )
                                            ->where(function($query) {
                                                $query->where('products.status', 1);
                                            })
                                            ->where(function($query) use ($search_keyword) {
                                                $query->where('products.name', 'LIKE', '%'.$search_keyword.'%')
                                                      ->orWhere('products.barcode', 'LIKE', '%'.$search_keyword.'%')
                                                      ->orWhere('brands.name', 'LIKE', '%'.$search_keyword.'%')
                                                      ->orWhere('suppliers.name', 'LIKE', '%'.$search_keyword.'%');
                                            })
                                            ->orderBy('products.name', 'ASC')
                                            ->get();
                    $products = [];
                    if($searchProducts){
                        foreach($searchProducts as $searchProduct){
                            $products[] = [
                                'id'        => $searchProduct->id,
                                'name'      => $searchProduct->name,
                                'sku'       => $searchProduct->sku,
                                'price'     => $searchProduct->retail_price_inc_tax,
                            ];
                        }
                    }
                    // Helper::pr($products);
                    $data['products']                   = $products;
                    $item_table_html                    = view('front.maincontents.billing.ajax-search-item', $data)->render();
                    $apiResponse                        = [
                                                            'item_table_html' => $item_table_html,
                                                            ];
                    if(!empty($products)){
                        $apiStatus                          = TRUE;
                        http_response_code(200);
                        $apiMessage                         = 'Search result found';
                        $apiExtraField                      = 'response_code';
                        $apiExtraData                       = http_response_code();
                    } else {
                        $apiStatus                          = FALSE;
                        http_response_code(200);
                        $apiMessage                         = 'No products found';
                        $apiExtraField                      = 'response_code';
                        $apiExtraData                       = http_response_code();
                    }
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Order not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function searchProductAddToCart(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            if($requestData['key'] == env('PROJECT_KEY')){
                $item_id            = $requestData['item_id'];
                $order_id           = $requestData['order_id'];
                $post_qty           = $requestData['qty'];
                $getProduct         = Product::where('id', '=', $item_id)->first();
                if($getProduct){
                    /* orders details table */
                        $checkAlreadyAdded = OrderDetail::where('order_id', '=', $order_id)->where('item_id', '=', $getProduct->id)->first();
                        if($checkAlreadyAdded){
                            $qty                = $checkAlreadyAdded->qty + $post_qty;
                            /* discount calculation */
                                $discount_amount    = 0;
                                $today = now(); // Get current date and time
                                $minDiscountedPrice = \DB::table('product_discount_vouchers as pdv')
                                                        ->join('coupons as c', 'pdv.voucher_code', '=', 'c.voucher_code') // Join with coupons table
                                                        ->where('pdv.product_id', $getProduct->id) // Filter by product_id
                                                        ->whereDate('c.from_date', '<=', $today) // Coupon must be active
                                                        ->whereDate('c.to_date', '>=', $today) // Coupon must not be expired
                                                        ->orderBy('pdv.retail_discounted_price', 'asc')
                                                        ->select('pdv.*', 'c.from_date', 'c.to_date') // Select needed columns
                                                        ->first();
                                if($minDiscountedPrice){
                                    $per_unit_discount          = (($minDiscountedPrice)?$minDiscountedPrice->retail_discount:0);
                                    $per_unit_discounted_price  = (($minDiscountedPrice)?$minDiscountedPrice->retail_discounted_price:0);
                                    $discount_amount            = ($per_unit_discount * $qty);
                                    $price                      = $per_unit_discounted_price;
                                    $subtotal                   = (($price * $qty));
                                } else {
                                    $price                      = $getProduct->retail_price_inc_tax;
                                    $subtotal                   = (($price * $qty) - $discount_amount);
                                }
                            /* discount calculation */
                            $field1             = [
                                'order_id'              => $order_id,
                                'item_id'               => $getProduct->id,
                                'price'                 => $price,
                                'qty'                   => $qty,
                                'discount_amount'       => $discount_amount,
                                'subtotal'              => $subtotal,
                            ];
                            OrderDetail::where('order_id', '=', $order_id)->where('item_id', '=', $getProduct->id)->update($field1);
                        } else {
                            $qty                = $post_qty;
                            /* discount calculation */
                                $discount_amount    = 0;
                                $today = now(); // Get current date and time
                                $minDiscountedPrice = \DB::table('product_discount_vouchers as pdv')
                                                        ->join('coupons as c', 'pdv.voucher_code', '=', 'c.voucher_code') // Join with coupons table
                                                        ->where('pdv.product_id', $getProduct->id) // Filter by product_id
                                                        ->whereDate('c.from_date', '<=', $today) // Coupon must be active
                                                        ->whereDate('c.to_date', '>=', $today) // Coupon must not be expired
                                                        ->orderBy('pdv.retail_discounted_price', 'asc')
                                                        ->select('pdv.*', 'c.from_date', 'c.to_date') // Select needed columns
                                                        ->first();
                                if($minDiscountedPrice){
                                    $per_unit_discount          = (($minDiscountedPrice)?$minDiscountedPrice->retail_discount:0);
                                    $per_unit_discounted_price  = (($minDiscountedPrice)?$minDiscountedPrice->retail_discounted_price:0);
                                    $discount_amount            = ($per_unit_discount * $qty);
                                    $price                      = $per_unit_discounted_price;
                                    $subtotal                   = (($price * $qty));
                                } else {
                                    $price                      = $getProduct->retail_price_inc_tax;
                                    $subtotal                   = (($price * $qty) - $discount_amount);
                                }
                            /* discount calculation */
                            $field1             = [
                                'order_id'          => $order_id,
                                'item_id'           => $getProduct->id,
                                'price'             => $price,
                                'qty'               => $qty,
                                'discount_amount'   => $discount_amount,
                                'subtotal'          => $subtotal,
                            ];
                            OrderDetail::insert($field1);
                        }
                    /* orders details table */
                    /* orders table */
                        $getTotalAmount     = OrderDetail::where('order_id', '=', $order_id)->sum('subtotal');
                        $getDiscountAmount  = OrderDetail::where('order_id', '=', $order_id)->sum('discount_amount');
                        $subtotal           = $getTotalAmount;
                        $discount_amount    = $getDiscountAmount;
                        $discounted_amount  = ($subtotal - $discount_amount);
                        $delivery_amount    = 0;
                        $net_amount         = ($discounted_amount + $delivery_amount);
                        $field2     = [
                            'subtotal'                  => $subtotal,
                            'discount_amount'           => $discount_amount,
                            'discounted_amount'         => $discounted_amount,
                            'delivery_amount'           => $delivery_amount,
                            'net_amount'                => $net_amount,
                        ];
                        Order::where('id', '=', $order_id)->update($field2);
                    /* orders table */
                    /* item table rearrange on the go */
                        // $data['getOrder']               = Order::where('id', '=', $order_id)->first();
                        // $data['getOrderItems']          = DB::table('order_details')
                        //                                     ->join('products', 'order_details.item_id', '=', 'products.id')
                        //                                     ->select('order_details.*', 'products.name as product_name', 'products.sku as product_sku')
                        //                                     ->where('order_details.status', '=', 1)
                        //                                     ->where('order_details.order_id', '=', $order_id)
                        //                                     ->orderBy('order_details.id', 'ASC')
                        //                                     ->get();
                        // $item_table_html                = view('front.maincontents.billing.ajax-order-item', $data)->render();
                    /* item table rearrange on the go */
                    $redirect_url   = url('user/billing/billing-item/' . Helper::encoded($order_id));
                    $apiResponse    = [
                                            'redirect_url'  => $redirect_url,
                                        ];
                    $apiStatus                          = TRUE;
                    http_response_code(200);
                    $apiMessage                         = 'Product added into cart successfully';
                    $apiExtraField                      = 'response_code';
                    $apiExtraData                       = http_response_code();
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Product not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function billingShortcuts($order_id){
            $order_id                       = Helper::decoded($order_id);
            $data['getOrder']               = Order::where('id', '=', $order_id)->first();
            $data['getOrderItems']          = DB::table('order_details')
                                                ->join('products', 'order_details.item_id', '=', 'products.id')
                                                ->select('order_details.*', 'products.name as product_name', 'products.sku as product_sku')
                                                ->where('order_details.status', '=', 1)
                                                ->where('order_details.order_id', '=', $order_id)
                                                ->orderBy('order_details.id', 'ASC')
                                                ->get();
            $data['fast_buttons']           = FastButton::select('id', 'product_id', 'name', 'price', 'qty')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Search and Shortcuts';
            $page_name                      = 'billing.billing-shortcuts';
            echo $this->user_after_login_billing_layout($title,$page_name,$data);
        }
        public function validateAdminPin(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            // Helper::pr($requestData);
            if($requestData['key'] == env('PROJECT_KEY')){
                $pin1           = $requestData['pin1'];
                $pin2           = $requestData['pin2'];
                $pin3           = $requestData['pin3'];
                $pin4           = $requestData['pin4'];
                $completePin    = $pin1.$pin2.$pin3.$pin4;
                $getAdmin       = Admin::where('id','=',1)->first();
                if(Hash::check($completePin, $getAdmin->password)){
                    http_response_code(200);
                    $apiStatus          = TRUE;
                    $apiMessage         = 'Admin PIN matched !!!';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                } else {
                    http_response_code(200);
                    $apiStatus          = FALSE;
                    $apiMessage         = 'Admin PIN Doesn\'t match !!!';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function billingPriceUpdate(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            // Helper::pr($requestData);
            if($requestData['key'] == env('PROJECT_KEY')){
                $order_id           = $requestData['order_id'];
                $item_price         = $requestData['item_price'];
                $item_id            = $requestData['item_id'];
                $getOrder           = Order::where('id', '=', $order_id)->first();
                if($getOrder){
                    /* orders details table */
                        if(!empty($item_id)){
                            for($p=0;$p<count($item_id);$p++){
                                $getOrderItem = OrderDetail::where('order_id', '=', $order_id)->where('item_id', '=', $item_id[$p])->first();
                                if($getOrderItem){
                                    $qty = $getOrderItem->qty;
                                    $discount_amount = $getOrderItem->discount_amount;
                                    $subtotal = (($item_price[$p] * $qty) - $discount_amount);
                                    $fields = [
                                        'price'     => $item_price[$p],
                                        'subtotal'  => $subtotal,
                                    ];
                                    OrderDetail::where('order_id', '=', $order_id)->where('item_id', '=', $item_id[$p])->update($fields);
                                }
                            }
                        }
                    /* orders details table */
                    /* orders table */
                        $getTotalAmount     = OrderDetail::where('order_id', '=', $order_id)->sum('subtotal');
                        $getDiscountAmount  = OrderDetail::where('order_id', '=', $order_id)->sum('discount_amount');
                        $subtotal           = $getTotalAmount;
                        $discount_amount    = $getDiscountAmount;
                        $discounted_amount  = ($subtotal - $discount_amount);
                        $delivery_amount    = 0;
                        $net_amount         = ($discounted_amount + $delivery_amount);
                        $field2     = [
                            'subtotal'                  => $subtotal,
                            'discount_amount'           => $discount_amount,
                            'discounted_amount'         => $discounted_amount,
                            'delivery_amount'           => $delivery_amount,
                            'net_amount'                => $net_amount,
                        ];
                        Order::where('id', '=', $order_id)->update($field2);
                    /* orders table */
                    /* item table rearrange on the go */
                        $data['getOrder']               = Order::where('id', '=', $order_id)->first();
                        $data['getOrderItems']          = DB::table('order_details')
                                                            ->join('products', 'order_details.item_id', '=', 'products.id')
                                                            ->select('order_details.*', 'products.name as product_name', 'products.sku as product_sku')
                                                            ->where('order_details.status', '=', 1)
                                                            ->where('order_details.order_id', '=', $order_id)
                                                            ->orderBy('order_details.id', 'ASC')
                                                            ->get();
                        $item_table_html                = view('front.maincontents.billing.ajax-order-item', $data)->render();
                    /* item table rearrange on the go */
                    $apiStatus                          = TRUE;
                    http_response_code(200);
                    $apiResponse                        = [
                        'item_table_html' => $item_table_html,
                    ];
                    $apiMessage                         = 'Product price updated into cart successfully';
                    $apiExtraField                      = 'response_code';
                    $apiExtraData                       = http_response_code();
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Order not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
    /* new or existing order */
    /* past orders */
        public function pastOrders(){
            $data['module']                 = $this->data;
            $title                          = 'Past Orders';
            $page_name                      = 'billing.past-orders';
            $data['rows']                   = Order::select('id', 'order_no', 'order_date', 'order_time', 'net_amount', 'operator_id', 'note', 'delivery_mode', 'pdf_invoice', 'pickup_email', 'delivery_email', 'cash_tendered', 'cash_return', 'payment_mode')->where('status', '=', 5)->where('operator_id', '=', session('user_id'))->orderBy('id', 'DESC')->get();
            echo $this->user_after_login_billing_layout($title,$page_name,$data);
        }
        public function billingInvoice($order_id){
            $order_id                       = Helper::decoded($order_id);
            $data['module']                 = $this->data;
            $data['getOrderDetail']         = Order::where('id', '=', $order_id)->first();
            return view('front.maincontents.billing.print-invoice', $data);
        }
        public function billingInvoiceEmail($order_id){
            $generalSetting                 = GeneralSetting::find(1);
            $order_id                       = Helper::decoded($order_id);
            $data['getOrder']               = Order::where('id', '=', $order_id)->first();
            $to_email                       = '';
            if($data['getOrder']){
                if($data['getOrder']->delivery_mode == 'Pickup'){
                    $to_email                       = $data['getOrder']->pickup_email;
                }
                if($data['getOrder']->delivery_mode == 'Deliver'){
                    $to_email                       = $data['getOrder']->delivery_email;
                }
            }
            $to                             = $to_email;
            if($to != ''){
                $subject                    = $generalSetting->site_name . " Invoice " . (($data['getOrder'])?$data['getOrder']->order_no:'');
                $message                    = $subject;
                $attchment                  = 'public/uploads/invoice/' . $data['getOrder']->pdf_invoice;
                $this->sendMail($to, $subject, $message, $attchment);
                return redirect('/user/billing/past-orders/')->with('success_message', 'Invoice sent successfully');
            } else {
                return redirect('/user/billing/past-orders/')->with('error_message', 'Email address is not available');
            }
        }
        public function billingPDFInvoice($order_id){
            $order_id                       = Helper::decoded($order_id);
            $data['module']                 = $this->data;
            $data['getOrderDetail']         = Order::where('id', '=', $order_id)->first();
            $order_no                       = (($data['getOrderDetail'])?$data['getOrderDetail']->order_no:'');
            $generalSetting                 = GeneralSetting::find('1');
            $subject                        = 'Invoice-' . $order_no;
            $message                        = view('front.maincontents.billing.pdf-invoice', $data);                        
            // echo $message;die;
            $options        = new Options();
            $options->set('defaultFont', 'Courier');
            $dompdf         = new Dompdf($options);
            $html           = $message;
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $output         = $dompdf->output();
            $dompdf->stream("document.pdf", array("Attachment" => false));die;
            $filename       = $order_no.'.pdf';
            $pdfFilePath    = 'public/uploads/invoice/' . $filename;
            file_put_contents($pdfFilePath, $output);
            Order::where('id', '=', $order_id)->update(['pdf_invoice' => $filename]);
            return view('front.maincontents.billing.pdf-invoice', $data);
        }
        public function printDeliveryOrder(Request $request){
            $postData       = $request->all();
            $order_id       = $postData['order_id'];
            $delivery_data  = [];
            if(count($order_id) > 0){
                for($k=0;$k<count($order_id);$k++){
                    $getOrderInfo = Order::where('id', '=', $order_id[$k])->first();
                    if($getOrderInfo){
                        $delivery_data[] = [
                            'order_id'              => $order_id[$k],
                            'order_no'              => $getOrderInfo->order_no,
                            'order_date'            => $getOrderInfo->order_date,
                            'order_time'            => $getOrderInfo->order_time,
                            'delivery_mode'         => $getOrderInfo->delivery_mode,
                            'delivery_name'         => $getOrderInfo->delivery_name,
                            'delivery_phone'        => $getOrderInfo->delivery_phone,
                            'delivery_email'        => $getOrderInfo->delivery_email,
                            'delivery_address'      => $getOrderInfo->delivery_address,
                            'delivery_suburb'       => $getOrderInfo->delivery_suburb,
                            'delivery_state'        => $getOrderInfo->delivery_state,
                            'delivery_postcode'     => $getOrderInfo->delivery_postcode,
                            'net_amount'            => $getOrderInfo->net_amount,
                            'payment_status'        => $getOrderInfo->payment_status,
                            'payment_date_time'     => $getOrderInfo->payment_date_time,
                        ];
                    }
                }
                $data['delivery_data']         = $delivery_data;
            return view('front.maincontents.billing.print-delivery-orders', $data);
            } else {
                return redirect('/user/billing/past-orders/')->with('error_message', 'For generate delivery order please select atleast one order from list');
            }
        }
    /* past orders */
    /* recall orders */
        public function billingRecall(){
            $data['module']                 = $this->data;
            $title                          = 'Recall Orders';
            $page_name                      = 'billing.billing-recall';
            $data['rows']                   = Order::select('id', 'order_no', 'order_date', 'order_time', 'net_amount', 'operator_id', 'status')->where('status', '=', 3)->where('operator_id', '=', session('user_id'))->orderBy('id', 'DESC')->get();
            echo $this->user_after_login_billing_layout($title,$page_name,$data);
        }
    /* recall orders */
    /* ongoing orders */
        public function billingOngoing(){
            $data['module']                 = $this->data;
            $title                          = 'Ongoing Orders';
            $page_name                      = 'billing.billing-ongoing';
            $data['rows']                   = Order::select('id', 'order_no', 'order_date', 'order_time', 'net_amount', 'operator_id', 'status')->where('status', '<', 3)->where('operator_id', '=', session('user_id'))->orderBy('id', 'DESC')->get();
            echo $this->user_after_login_billing_layout($title,$page_name,$data);
        }
    /* ongoing orders */
}
