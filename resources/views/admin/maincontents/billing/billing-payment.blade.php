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
                    <!-- <p class="order-header">ORDER #: <?=(($getOrder)?$getOrder->order_no:'')?></p> -->
                    <!-- <a href="<?=url('admin/billing/billing-ongoing')?>" class="my-btn btn-orange">Ongoing Orders</i></a> -->
                    <a href="<?=url('admin/billing/past-orders')?>" class="my-btn btn-orange">Past Orders</i></a>
                </div>
                <div class="my-5">
                    <?php if($getOrder){ if($getOrder->delivery_mode != ''){?>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <ul class="payment-methord">
                                    <li>
                                        <a href="javascript:void(0);" class="my-btn outline-sky text-sky mb-4" type="button" data-bs-toggle="modal" data-bs-target="#cashModal">
                                            <!-- <input type="radio" class="radioOption" name="payment_mode" id="payment_mode1" value="CASH" <?=(($getOrder)?(($getOrder->payment_mode == 'CASH')?'checked':''):'')?> style="display: none;"> -->
                                            <label for="payment_mode1"><?=(($getOrder)?(($getOrder->payment_mode == 'CASH')?'<i class="fa fa-check"></i>':''):'')?>&nbsp;CASH</label>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="my-btn outline-sky text-sky mb-4" type="button" data-bs-toggle="modal" data-bs-target="#cardModal">
                                            <!-- <input type="radio" class="radioOption" name="payment_mode" id="payment_mode2" value="CARD" <?=(($getOrder)?(($getOrder->payment_mode == 'CARD')?'checked':''):'')?> style="display: none;"> -->
                                            <label for="payment_mode2"><?=(($getOrder)?(($getOrder->payment_mode == 'CARD')?'<i class="fa fa-check"></i>':''):'')?>&nbsp;CARD</label>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="my-btn outline-sky text-sky mb-4" type="button" data-bs-toggle="modal" data-bs-target="#voucherModal">
                                            <!-- <input type="radio" class="radioOption" name="payment_mode" id="payment_mode3" value="VOUCHER" <?=(($getOrder)?(($getOrder->payment_mode == 'VOUCHER')?'checked':''):'')?> style="display: none;"> -->
                                            <label for="payment_mode3"><?=(($getOrder)?(($getOrder->payment_mode == 'VOUCHER')?'<i class="fa fa-check"></i>':''):'')?>&nbsp;VOUCHER</label>
                                        </a>
                                    </li>
                                    <?php if($getOrder->payment_mode != ''){?>
                                        <?php if($getOrder->payment_mode == 'CASH'){?>
                                            <li>
                                                <p>
                                                    <small>Cash tendered : $<?=number_format($getOrder->cash_tendered, 2)?></small><br>
                                                    <small>Cash to be returned : $<?=number_format($getOrder->cash_return, 2)?></small>
                                                </p>
                                            </li>
                                        <?php }?>
                                        <?php if($getOrder->payment_mode != 'VOUCHER'){?>
                                            <li>
                                                <a href="javascript:void(0);" class="my-btn btn-green mb-4" type="button" data-bs-toggle="modal" data-bs-target="#placeOrderModal">FINALISE</a>
                                            </li>
                                        <?php } else {?>
                                            <h6 class="text-success">You have to pay $<?=number_format($getOrder->net_amount, 2)?> by using CASH or CARD to finalize this order</h6>
                                        <?php }?>
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
                            <th width="40%">ORDER #: <?=(($getOrder)?$getOrder->order_no:'')?></th>
                            <th class="text-center">Qty</th>
                            <th>Price</th>
                            <!-- <th class="text-end">Subtotal</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totItemQty = 0; if($getOrderItems){ foreach($getOrderItems as $getOrderItem){?>
                            <tr>
                                <td>
                                    <span><?=$getOrderItem->product_name?></span>
                                </td>
                                <td class="text-center">
                                    <button class="btn-plus-minus" onclick="itemQtyDecrease(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);">-</button>
                                    <input type="hidden" id="qty-val-<?=$getOrderItem->item_id?>" value="<?=$getOrderItem->qty?>">
                                    <span id="qty-text-<?=$getOrderItem->item_id?>"><?=$getOrderItem->qty?></span>
                                    <button class="btn-plus-minus" onclick="itemQtyIncrease(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);">+</button>
                                    <?php $totItemQty += $getOrderItem->qty; ?>
                                </td>
                                <td>$<?=number_format($getOrderItem->price,2)?></td>
                                <!-- <td class="text-end">
                                    $<?=number_format($getOrderItem->subtotal,2)?>
                                    <a href="javascript:void(0);" onclick="itemDelete(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);"><i class='bx bxs-trash'></i></a>
                                </td> -->
                            </tr>
                        <?php } }?>
                    </tbody>
                </table>
            </div>
            <div class="table-footer-wrapper">
                <div class="footer-notes p-4 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="me-2">Notes </p>
                        <input type="text" class="form-control" id="note" value="<?=$getOrder->note?>" placeholder="Notes">
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
                    <h1 class="modal-title fs-4 text-center w-100 text-black mb-2" id="adminpinmodalLabel">Are you sure that you want to finalize this order ?</h1>
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
<div class="modal fade" id="cardModal" tabindex="-1" aria-labelledby="adminpinmodalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h3>Card Details</h3>
                <form action="" id="card-form">
                    @csrf
                    <input type="hidden" name="order_id" id="order_id" value="<?=(($getOrder)?$getOrder->id:0)?>">
                    <input type="hidden" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222">
                    <input type="hidden" name="payment_mode" id="payment_mode1" value="CARD">
                    <input type="hidden" name="note" id="note" value="<?=$getOrder->note?>">
                    <div class="row mt-3 mb-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="card_holder_name" id="card_holder_name" placeholder="Card Holder Name">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="card_number" id="card_number" maxlength="16" minlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '');" placeholder="Card Number">
                        </div>
                    </div>
                    <div class="row mt-3 mb-3">
                        <div class="col-md-3">
                            <select class="form-control" name="card_expiry_month" id="card_expiry_month">
                                <option value="" selected>Select</option>
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" name="card_expiry_year" id="card_expiry_year">
                                <option value="" selected>Select</option>
                                <?php for($y=date('Y');$y<=2050;$y++){?>
                                <option value="<?=$y?>"><?=$y?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="password" class="form-control" name="card_cvv" id="card_cvv" maxlength="3" minlength="3" placeholder="Card CVV">
                        </div>
                    </div>
                    <div class="row mt-3 mb-3">
                        <div class="col-md-12">
                            <button type="submit" class="my-btn btn-sky">SUBMIT</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="adminpinmodalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="voucher-form">
                    @csrf
                    <input type="hidden" name="order_id" id="order_id" value="<?=(($getOrder)?$getOrder->id:0)?>">
                    <input type="hidden" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222">
                    <input type="hidden" name="payment_mode" id="payment_mode1" value="VOUCHER">
                    <input type="hidden" name="note" id="note" value="<?=$getOrder->note?>">
                    <div class="row">
                        <div class="col-md-12 mt-3 mb-3">
                            <input type="text" class="form-control" name="card_holder_name" id="card_holder_name" placeholder="Voucher Number">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="my-btn btn-sky">APPLY</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="cashModal" tabindex="-1" aria-labelledby="adminpinmodalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="cash-form">
                    @csrf
                    <input type="hidden" name="order_id" id="order_id" value="<?=(($getOrder)?$getOrder->id:0)?>">
                    <input type="hidden" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222">
                    <input type="hidden" name="payment_mode" id="payment_mode1" value="CASH">
                    <input type="hidden" name="note" id="note" value="<?=$getOrder->note?>">
                    <div class="row">
                        <div class="col-md-12 mt-3 mb-3">
                            <label for="cash_tendered">Cash Tendered</label>
                            <input type="text" class="form-control" name="cash_tendered" id="cash_tendered" placeholder="Cash Tendered">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="my-btn btn-sky">APPLY</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var base_url = '<?=url('/')?>';
    function itemDelete(itemId, orderId){
        // Show confirmation box
        if (confirm("Are you sure you want to delete this product from cart ?")) {
            // User clicked "Yes", proceed with AJAX
            $.ajax({
                url: base_url + "/admin/billing/item-delete",
                type: "POST",
                data: {"_token": "{{ csrf_token() }}", key : "db9effa4e748d16e72654ef8bac2a947be6e2222", item_id : itemId, order_id : orderId},
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function(res) {
                    $("#loader").hide();
                    if(res.status){
                        toastAlert("success", res.message);
                        $('#order-item').empty();
                        $('#order-item').html(res.data.item_table_html);
                        // setTimeout(function() {
                        //     location.reload();
                        // }, 3000);
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
        } else {
            toastAlert("error", "Delete action cancelled");
        }
    }
    function itemQtyIncrease(itemId, orderId){
        var qtyVal = parseInt($('#qty-val-' + itemId).val()) + 1;
        $('#qty-val-' + itemId).val(qtyVal);
        $('#qty-text-' + itemId).text(qtyVal);
        $.ajax({
            url: base_url + "/admin/billing/billing-update-qty",
            type: "POST",
            data: {"_token": "{{ csrf_token() }}", key : "db9effa4e748d16e72654ef8bac2a947be6e2222", item_id : itemId, order_id : orderId, qtyVal : qtyVal},
            beforeSend: function () {
                $("#loader").show();
            },
            success: function(res) {
                $("#loader").hide();
                if(res.status){
                    toastAlert("success", res.message);
                    $('#order-item').empty();
                    $('#order-item').html(res.data.item_table_html);
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
    function itemQtyDecrease(itemId, orderId){
        var qtyVal = parseInt($('#qty-val-' + itemId).val()) - 1;
        if(qtyVal >= 1){
            $('#qty-val-' + itemId).val(qtyVal);
            $('#qty-text-' + itemId).text(qtyVal);
            $.ajax({
                url: base_url + "/admin/billing/billing-update-qty",
                type: "POST",
                data: {"_token": "{{ csrf_token() }}", key : "db9effa4e748d16e72654ef8bac2a947be6e2222", item_id : itemId, order_id : orderId, qtyVal : qtyVal},
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function(res) {
                    $("#loader").hide();
                    if(res.status){
                        toastAlert("success", res.message);
                        $('#order-item').empty();
                        $('#order-item').html(res.data.item_table_html);
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
        } else {
            toastAlert("error", 'Product quantity can\'t be less than 1');
        }
    }
    $(document).ready(function() {
        $(".radioOption").change(function() {
            var selectedValue   = $("input[name='payment_mode']:checked").val(); // Get checked value
            var order_id        = '<?=(($getOrder)?$getOrder->id:0)?>';
            var note            = $('#note').val();
            $.ajax({
                url: base_url + "/admin/billing/billing-select-payment-mode",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    key : "db9effa4e748d16e72654ef8bac2a947be6e2222",
                    order_id: order_id,
                    payment_mode: selectedValue,
                    note: note,
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
                        }, 1000);
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
        $("#card-form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: base_url + "/admin/billing/billing-select-payment-mode",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "JSON",
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function (res) {
                    $("#loader").hide();
                    if(res.status){
                        toastAlert("success", res.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
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
        $("#voucher-form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: base_url + "/admin/billing/billing-select-payment-mode",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "JSON",
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function (res) {
                    $("#loader").hide();
                    if(res.status){
                        toastAlert("success", res.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
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
        $("#cash-form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: base_url + "/admin/billing/billing-select-payment-mode",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "JSON",
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function (res) {
                    $("#loader").hide();
                    if(res.status){
                        $('#cashModal').modal('hide');
                        toastAlert("success", res.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
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
                    var redirect_url = base_url + '/admin/billing/list';
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