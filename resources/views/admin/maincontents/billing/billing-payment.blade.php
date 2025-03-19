<?php
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
?>
<div class="row">
    <!-- Sidebar Section -->
    <div class="col-md-5">
        <div class="order-summary">
            <div class="order-summary-left">
                <div class="mb-3 d-flex justify-content-between">
                    <p class="order-header">ORDER #: <?=(($getOrder)?$getOrder->order_no:'')?></p>
                    <a href="<?=url('admin/billing/past-orders')?>" class="my-btn btn-orange">Past Orders</i></a>
                </div>
                <div class="my-5">
                    <?php if($getOrder){ if($getOrder->delivery_mode != ''){?>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <ul class="payment-methord">
                                    <li>
                                        <a href="javascript:void(0);" class="my-btn outline-sky text-sky mb-4">
                                            <input type="radio" class="radioOption" name="payment_mode" id="payment_mode1" value="CASH" <?=(($getOrder)?(($getOrder->payment_mode == 'CASH')?'checked':''):'')?> style="display: none;">
                                            <label for="payment_mode1"><?=(($getOrder)?(($getOrder->payment_mode == 'CASH')?'<i class="fa fa-check"></i>':''):'')?>&nbsp;CASH</label>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="my-btn outline-sky text-sky mb-4">
                                            <input type="radio" class="radioOption" name="payment_mode" id="payment_mode2" value="CARD" <?=(($getOrder)?(($getOrder->payment_mode == 'CARD')?'checked':''):'')?> style="display: none;">
                                            <label for="payment_mode2"><?=(($getOrder)?(($getOrder->payment_mode == 'CARD')?'<i class="fa fa-check"></i>':''):'')?>&nbsp;CARD</label>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="my-btn outline-sky text-sky mb-4">
                                            <input type="radio" class="radioOption" name="payment_mode" id="payment_mode3" value="VOUCHER" <?=(($getOrder)?(($getOrder->payment_mode == 'VOUCHER')?'checked':''):'')?> style="display: none;">
                                            <label for="payment_mode3"><?=(($getOrder)?(($getOrder->payment_mode == 'VOUCHER')?'<i class="fa fa-check"></i>':''):'')?>&nbsp;VOUCHER</label>
                                        </a>
                                    </li>
                                    <?php if($getOrder->payment_mode != ''){?>
                                    <li>
                                        <a href="javascript:void(0);" class="my-btn btn-green mb-4" type="button" data-bs-toggle="modal" data-bs-target="#placeOrderModal">FINALISE</a>
                                    </li>
                                    <?php }?>
                                </ul>
                            </div>
                        </div>
                    <?php } }?>
                </div>
            </div>
            <div class="order-summery-left-bottom">
                <div class="row my-4">
                    <div class="col-md-12 d-flex justify-content-start">
                        <a href="<?=url('admin/billing/list')?>" class="my-btn btn-sky">BACK</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Order Items Section -->
    <div class="col-md-7" id="order-item">
        <?php if(count($getOrderItems) > 0){ ?>
            <div class="order-summary-right table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="40%">Item</th>
                            <th>Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totItemQty = 0; if($getOrderItems){ foreach($getOrderItems as $getOrderItem){?>
                            <tr>
                                <td>
                                    <span><?=$getOrderItem->product_name?></span><br>
                                    <small style="font-size: 10px;color: #0096eb;">SKU : <?=$getOrderItem->product_sku?></small>
                                </td>
                                <td>$<?=number_format($getOrderItem->price,2)?></td>
                                <td class="text-center">
                                    <button class="btn-plus-minus" onclick="itemQtyDecrease(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);">-</button>
                                    <input type="hidden" id="qty-val-<?=$getOrderItem->item_id?>" value="<?=$getOrderItem->qty?>">
                                    <span id="qty-text-<?=$getOrderItem->item_id?>"><?=$getOrderItem->qty?></span>
                                    <button class="btn-plus-minus" onclick="itemQtyIncrease(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);">+</button>
                                    <?php $totItemQty += $getOrderItem->qty; ?>
                                </td>
                                <td class="text-end">
                                    $<?=number_format($getOrderItem->subtotal,2)?>
                                    <a href="javascript:void(0);" onclick="itemDelete(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);"><i class='bx bxs-trash'></i></a>
                                </td>
                            </tr>
                        <?php } }?>
                    </tbody>
                </table>
            </div>
            <div class="table-footer-wrapper">
                <div class="footer-notes p-4 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="me-2">Notes </p>
                        <input type="text" class="form-control" id="note" placeholder="Notes">
                    </div>
                </div>
                <div class="order-footer p-4">
                    <div class="d-flex justify-content-between">
                        <p>Delivery</p>
                        <p>$<?=number_format($getOrder->delivery_amount,2)?></p>
                    </div>
                    <div class="d-flex justify-content-between py-3">
                        <p>Total Discounts</p>
                        <p>$<?=number_format($getOrder->discount_amount,2)?></p>
                    </div>
                </div>
                <div class="d-flex justify-content-between totals p-4">
                    <p>ITEMS <?=count($getOrderItems)?></p>
                    <p>TOTAL $<?=number_format($getOrder->net_amount,2)?></p>
                </div>
            </div>
        <?php }?>
    </div>
</div>
<div class="modal fade" id="cancelSaleModal" tabindex="-1" aria-labelledby="adminpinmodalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript: void(0)" class="otp-form" name="otp-form">
                    <h1 class="modal-title fs-4 text-center w-100 text-black mb-2" id="adminpinmodalLabel">Are you sure that you want to cancel the order?</h1>
                    <div class="mt-4 d-flex justify-content-center"> 
                        <button class="btn btn-success text-black px-4 mx-1" onclick="orderChangeStatus(<?=(($getOrder)?$getOrder->id:0)?>, 4, 'cancelSaleModal')">Yes</button> 
                        <button type="button" class="btn btn-danger text-black px-4 mx-1" data-bs-dismiss="modal" aria-label="Close">No</button> 
                    </div>
                </form>
            </div>
            <div class="result"><p id="_otp" class="_notok"></p></div>
        </div>
    </div>
</div>
<div class="modal fade" id="holdSaleModal" tabindex="-1" aria-labelledby="adminpinmodalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript: void(0)" class="otp-form" name="otp-form">
                    <h1 class="modal-title fs-4 text-center w-100 text-black mb-2" id="adminpinmodalLabel">Are you sure that you want to hold the order?</h1>
                    <div class="mt-4 d-flex justify-content-center"> 
                        <button class="btn btn-success text-black px-4 mx-1" onclick="orderChangeStatus(<?=(($getOrder)?$getOrder->id:0)?>, 3, 'holdSaleModal')">Yes</button> 
                        <button type="button" class="btn btn-danger text-black px-4 mx-1" data-bs-dismiss="modal" aria-label="Close">No</button> 
                    </div>
                </form>
            </div>
            <div class="result"><p id="_otp" class="_notok"></p></div>
        </div>
    </div>
</div>
<div class="modal fade" id="placeOrderModal" tabindex="-1" aria-labelledby="adminpinmodalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript: void(0)" class="otp-form" name="otp-form">
                    <h1 class="modal-title fs-4 text-center w-100 text-black mb-2" id="adminpinmodalLabel">Are you sure that you want to place this order?</h1>
                    <div class="mt-4 d-flex justify-content-center"> 
                        <button class="btn btn-success text-black px-4 mx-1" onclick="placeOrder(<?=(($getOrder)?$getOrder->id:0)?>);">Yes</button> 
                        <button type="button" class="btn btn-danger text-black px-4 mx-1" data-bs-dismiss="modal" aria-label="Close">No</button> 
                    </div>
                </form>
            </div>
            <div class="result"><p id="_otp" class="_notok"></p></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var base_url = '<?=url('/')?>';
    $(document).ready(function() {
        $(".radioOption").change(function() {
            var selectedValue   = $("input[name='payment_mode']:checked").val(); // Get checked value
            var order_id        = '<?=(($getOrder)?$getOrder->id:0)?>';
            $.ajax({
                url: base_url + "/admin/billing/billing-select-payment-mode",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    key : "db9effa4e748d16e72654ef8bac2a947be6e2222",
                    order_id: order_id,
                    payment_mode: selectedValue,
                },
                dataType: "json",
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function(res) {
                    $("#loader").hide();
                    if(res.status){
                        toastAlert("success", res.message);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }else{
                        toastAlert("error", res.message);
                    }
                },
                error:function (xhr, ajaxOptions, thrownError){
                    $("#loader").hide();
                    var res = xhr.responseJSON;
                    if(!res.status) {
                        toastAlert("error", res.message);
                    }
                }
            });
        });
    });
    function placeOrder(orderId){
        $('#placeOrderModal').modal('hide');
        var note = $('#note').val();
        $.ajax({
            url: base_url + "/admin/billing/place-order",
            type: "POST",
            data: {"_token": "{{ csrf_token() }}", key : "db9effa4e748d16e72654ef8bac2a947be6e2222", order_id : orderId, note : note},
            beforeSend: function () {
                $("#loader").show();
            },
            success: function(res) {
                $("#loader").hide();
                if(res.status){
                    var redirect_url = base_url + '/admin/billing/past-orders';
                    toastAlert("success", res.message, true, redirect_url);
                }else{
                    toastAlert("error", res.message);
                }
            },
            error:function (xhr, ajaxOptions, thrownError){
                $("#loader").hide();
                var res = xhr.responseJSON;
                if(!res.status) {
                    toastAlert("error", res.message);
                }
            }
        });
    }
</script>