<div class="order-summary-right table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th width="40%">ORDER #: <?=(($getOrder)?$getOrder->order_no:'')?></th>
                <th class="text-center">Qty</th>
                <th class="text-end">Price</th>
                <!-- <th class="text-end">Subtotal</th> -->
            </tr>
        </thead>
        <tbody>
            <?php $totItemQty = 0; if($getOrderItems){ foreach($getOrderItems as $getOrderItem){?>
                <tr class="order-row">
                    <td>
                        <?php if($getOrderItem->subtotal >= 0){?>
                            <input type="radio" name="order_details_id" id="order_details_id<?=$getOrderItem->id?>" value="<?=$getOrderItem->id?>" style="display:none;">
                        <?php }?>
                        <span><?=$getOrderItem->product_name?></span>
                        <!-- <small style="font-size: 10px;color: #0096eb;">SKU : <?=$getOrderItem->product_sku?></small> -->
                        <?php if($getOrderItem->subtotal < 0){?>
                            <small style="font-size: 9px; color:#000;" class="badge bg-warning">RETURN</small>
                        <?php }?>
                    </td>
                    <td class="text-center">
                        <button class="btn-plus-minus" onclick="itemQtyDecrease(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);">-</button>
                        <input type="hidden" id="qty-val-<?=$getOrderItem->item_id?>" value="<?=$getOrderItem->qty?>">
                        <span id="qty-text-<?=$getOrderItem->item_id?>"><?=$getOrderItem->qty?></span>
                        <button class="btn-plus-minus" onclick="itemQtyIncrease(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);">+</button>
                        <?php $totItemQty += $getOrderItem->qty; ?>
                    </td>
                    <!-- <td class="text-end">
                        $<?=number_format($getOrderItem->subtotal,2)?>
                        <a href="javascript:void(0);" onclick="itemDelete(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);"><i class='bx bxs-trash'></i></a>
                    </td> -->
                    <td class="text-end">
                        <span class="price-text">$<?=number_format($getOrderItem->price,2)?></span>
                        <input type="text" class="form-control price-val" name="product_price" value="<?=$getOrderItem->price?>" style="display: none;">
                        <input type="hidden" name="item_id" value="<?=$getOrderItem->item_id?>" style="display: none;">
                        <a href="javascript:void(0);" onclick="itemDelete(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);"><i class='bx bxs-trash'></i></a>
                    </td>
                </tr>
            <?php } }?>
        </tbody>
    </table>
</div>
<div class="table-footer-wrapper">
    <div class="footer-notes p-2 pb-0">
        <div class="d-flex justify-content-between align-items-center">
            <p class="me-2">Notes </p>
            <input type="text" class="form-control" id="note" value="<?=$getOrder->note?>" placeholder="Notes">
            <div class="items-count">
                <p>ITEMS : <?=$totItemQty?></p>
            </div>
        </div>
    </div>
    <div class="order-footer p-2">
        <div class="d-flex justify-content-between">
            <p>Delivery</p>
            <p>$<?=number_format($getOrder->delivery_amount,2)?></p>
        </div>
        <div class="d-flex justify-content-between py-3">
            <p>Total Discounts</p>
            <p>$<?=number_format($getOrder->discount_amount,2)?></p>
        </div>
    </div>
    <div class="d-flex justify-content-between totals p-1 p-sm-2 p-md-4">
        <!-- <p></p> -->
        <p>TOTAL $<?=number_format($getOrder->net_amount,2)?></p>
    </div>
</div>