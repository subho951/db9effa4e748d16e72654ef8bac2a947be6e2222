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
                    <!-- <a href="<?=url('admin/billing/past-orders')?>" class="my-btn btn-orange">Past Orders</i></a> -->
                </div>
                <div class="my-5">
                    <?php if($getOrder){ if($getOrder->delivery_mode == 'Deliver'){?>
                        <div class="row" id="deliver">
                            <div class="col-12">
                                <div class="deliver-form-box" id="delivery-form">
                                    <form action="" id="deliverForm">
                                        @csrf
                                        <input type="hidden" name="order_id" id="order_id" value="<?=(($getOrder)?$getOrder->id:0)?>">
                                        <input type="hidden" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222">
                                        <h4>Deliver</h4>
                                        <div class="row">
                                            <div class="col-12 col-sm-6 col-md-6 mb-3">
                                              <input type="text" class="form-control" id="delivery_phone" name="delivery_phone" placeholder="Mobile No. *" aria-label="Mobile No." value="<?=(($getOrder)?$getOrder->delivery_phone:'')?>">
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6 mb-3">
                                                <input type="text" class="form-control" id="delivery_name" name="delivery_name" placeholder="Name *" aria-label="Name" value="<?=(($getOrder)?$getOrder->delivery_name:'')?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6 col-md-6 mb-3">
                                              <input type="email" class="form-control" id="delivery_email" name="delivery_email" placeholder="Email" aria-label="Email" value="<?=(($getOrder)?$getOrder->delivery_email:'')?>">
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6 mb-3">
                                              <input type="text" class="form-control" placeholder="Tag" id="customer_tag" name="customer_tag" aria-label="Tag" value="<?=(($getOrder)?$getOrder->customer_tag:'')?>" minlength="2" maxlength="2">
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="deliver-form-box">
                                                        <h4>Address</h4>
                                                        <div class="row">
                                                            <div class="col mb-3">
                                                                <div class="pass  position-relative">
                                                                    <input type="text" id="delivery_address" name="delivery_address" class="form-control" placeholder="Street Name *" aria-label="Street Name" value="<?=(($getOrder)?$getOrder->delivery_address:'')?>">
                                                                    <i class="fas fa-location-dot pass-eye loaction-red"></i>
                                                                  </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12 col-sm-4 col-md-4 mb-3">
                                                                <input type="text" class="form-control" id="delivery_suburb" name="delivery_suburb" placeholder="Suburb *" aria-label="Suburb" value="<?=(($getOrder)?$getOrder->delivery_suburb:'')?>">
                                                            </div>
                                                            <div class="col-12 col-sm-4 col-md-4 mb-3">
                                                                <select class="form-select form-select-lg mb-3" id="delivery_state" name="delivery_state" aria-label=".form-select-lg example">
                                                                    <option value="" selected>State *</option>
                                                                    <option value="NSW" <?=(($getOrder)?(($getOrder->delivery_state == 'NSW')?'selected':''):'')?>>NSW</option>
                                                                    <option value="VIC" <?=(($getOrder)?(($getOrder->delivery_state == 'VIC')?'selected':''):'')?>>VIC</option>
                                                                    <option value="SA" <?=(($getOrder)?(($getOrder->delivery_state == 'SA')?'selected':''):'')?>>SA</option>
                                                                    <option value="ACT" <?=(($getOrder)?(($getOrder->delivery_state == 'ACT')?'selected':''):'')?>>ACT</option>
                                                                    <option value="QLD" <?=(($getOrder)?(($getOrder->delivery_state == 'QLD')?'selected':''):'')?>>QLD</option>
                                                                    <option value="WA" <?=(($getOrder)?(($getOrder->delivery_state == 'WA')?'selected':''):'')?>>WA</option>
                                                                    <option value="TAS" <?=(($getOrder)?(($getOrder->delivery_state == 'TAS')?'selected':''):'')?>>TAS</option>
                                                                    <option value="NT" <?=(($getOrder)?(($getOrder->delivery_state == 'NT')?'selected':''):'')?>>NT</option>
                                                                  </select>
                                                            </div>
                                                            <div class="col-12 col-sm-4 col-md-4">
                                                                <input type="text" class="form-control" id="delivery_postcode" name="delivery_postcode" placeholder="PostCode *" aria-label="PostCode" value="<?=(($getOrder)?$getOrder->delivery_postcode:'')?>">
                                                            </div>
                                                        </div>
                                                        <div class="order-summery-left-bottom">
                                                            <div class="row">
                                                                <div class="col-md-12 d-flex justify-content-between">
                                                                    <a href="<?=url('admin/billing/list')?>" class="my-btn btn-orange">CANCEL</a>
                                                                    <button type="submit" class="my-btn btn-green">Save</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } }?>
                    <?php if($getOrder){ if($getOrder->delivery_mode == 'Pickup'){?>
                        <div class="row" id="pickup">
                            <div class="col-12">
                                <form action="" id="pickupForm">
                                    @csrf
                                    <input type="hidden" name="order_id" id="order_id" value="<?=(($getOrder)?$getOrder->id:0)?>">
                                    <input type="hidden" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222">
                                    <div class="deliver-form-box" id="pickup-form">
                                        <h4>Pickup</h4>
                                        <div class="row">
                                            <div class="col-12 col-sm-6 col-md-6 mb-3">
                                              <input type="text" class="form-control" placeholder="Mobile No." id="pickup_phone" name="pickup_phone" aria-label="Mobile No." value="<?=(($getOrder)?$getOrder->pickup_phone:'')?>">
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6 mb-3">
                                              <input type="text" class="form-control" placeholder="Name" id="pickup_name" name="pickup_name" aria-label="Name" value="<?=(($getOrder)?$getOrder->pickup_name:'')?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6 col-md-6 mb-3">
                                              <input type="email" class="form-control" id="pickup_email" name="pickup_email" placeholder="Email (Optional)" aria-label="Email" value="<?=(($getOrder)?$getOrder->pickup_email:'')?>">
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6 mb-3">
                                              <input type="text" class="form-control" placeholder="Tag" id="customer_tag" name="customer_tag" aria-label="Tag" value="<?=(($getOrder)?$getOrder->customer_tag:'')?>" minlength="2" maxlength="2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="order-summery-left-bottom">
                                        <div class="row mt-5">
                                            <div class="col-md-12 d-flex justify-content-between">
                                                <a href="<?=url('admin/billing/list')?>" class="my-btn btn-orange">CANCEL</a>
                                                <button type="submit" class="my-btn btn-green">Complete Order</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } }?>
                </div>
            </div>
            <div class="order-summery-left-bottom">
                <div class="row my-4">
                    <div class="col-md-12 d-flex justify-content-between">
                        <a href="<?=url('admin/billing/billing-item/' . Helper::encoded($getOrder->id))?>" class="my-btn btn-sky">BACK</a>
                        <!-- <a class="my-btn btn-orange w-auto" href="javascript: vold(0)" type="button" data-bs-toggle="modal" data-bs-target="#adminpinmodal">Admin</a> -->
                        <?php if($getOrder){ if($getOrder->delivery_mode != ''){?>
                            <?php if($getOrder->delivery_mode == 'Take'){?>
                                <!-- <a href="<?=url('admin/billing/billing-payment/' . Helper::encoded((($getOrder)?$getOrder->id:0)))?>" class="my-btn btn-green btn-lg">PAYMENT</a> -->
                            <?php }?>
                            <?php if($getOrder->delivery_mode == 'Deliver' && $getOrder->delivery_name != ''){?>
                                <!-- <a href="<?=url('admin/billing/billing-payment/' . Helper::encoded((($getOrder)?$getOrder->id:0)))?>" class="my-btn btn-green btn-lg">PAYMENT</a> -->
                            <?php }?>
                            <?php if($getOrder->delivery_mode == 'Pickup' && $getOrder->pickup_name != ''){?>
                                <!-- <a href="<?=url('admin/billing/billing-payment/' . Helper::encoded((($getOrder)?$getOrder->id:0)))?>" class="my-btn btn-green btn-lg">PAYMENT</a> -->
                            <?php }?>
                        <?php } }?>
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
                        <div class="items-count">
                            <p>ITEMS : <?=$totItemQty?></p>
                        </div>
                    </div>
                </div>
                <div class="order-footer p-4">
                    <div class="d-flex justify-content-between py-1">
                        <p>Discounts</p>
                        <p>$<?=number_format($getOrder->discount_amount,2)?></p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p>Delivery</p>
                        <p>$<?=number_format($getOrder->delivery_amount,2)?></p>
                    </div>
                </div>
                <div class="d-flex justify-content-between totals p-4">
                    <!-- <p>ITEMS <?=count($getOrderItems)?></p> -->
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
<script type="text/javascript">
    var base_url = '<?=url('/')?>';
    function addToCart(){
        var barcode = $('#barcode').val();
        var order_id = '<?=(($getOrder)?$getOrder->id:0)?>';
        if(barcode.length >= 4 && barcode.length <= 25){
            $.ajax({
                type: "POST",
                url: base_url + "/admin/billing/add-to-cart",
                data: {"_token": "{{ csrf_token() }}", key : "db9effa4e748d16e72654ef8bac2a947be6e2222", barcode : barcode, order_id : order_id},
                dataType: "JSON",
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function (res) {
                    $("#loader").hide();
                    if(res.status){
                        toastAlert("success", res.message);
                        $('#order-item').empty();
                        $('#order-item').html(res.data.item_table_html);
                        $('#barcode').val('');
                    }else{
                        toastAlert("error", res.message);
                        $('#barcode').val('');
                    }
                },
                error:function (xhr, ajaxOptions, thrownError){
                    $("#loader").hide();
                    var res = xhr.responseJSON;
                    if(!res.status) {
                        toastAlert("error", res.message);
                        $('#barcode').val('');
                    }
                }
            });
        } else {
            toastAlert('error', 'Barcode or SKU number length must be between 4 and 25 characters. Please enter right barcode or SKU number');
            $('#barcode').val('');
        }
    }
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
                        // }, 2000);
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
    function orderChangeStatus(orderId, orderStatus, modalName){
        $('#' + modalName).modal('hide');
        $.ajax({
            url: base_url + "/admin/billing/billing-change-status",
            type: "POST",
            data: {"_token": "{{ csrf_token() }}", key : "db9effa4e748d16e72654ef8bac2a947be6e2222", order_status : orderStatus, order_id : orderId},
            beforeSend: function () {
                $("#loader").show();
            },
            success: function(res) {
                $("#loader").hide();
                if(res.status){
                    var redirectUrl = base_url + '/admin/billing/list';
                    toastAlert("success", res.message);
                    setTimeout(function() {
                        window.location.href = redirectUrl;
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
    $(document).ready(function() {
        $(".deliveryOption").change(function() {
            var selectedValue   = $("input[name='delivery_mode']:checked").val(); // Get checked value
            var order_id        = '<?=(($getOrder)?$getOrder->id:0)?>';
            var note            = $('#note').val();
            $.ajax({
                url: base_url + "/admin/billing/billing-select-delivery-address",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    key : "db9effa4e748d16e72654ef8bac2a947be6e2222",
                    order_id: order_id,
                    delivery_mode: selectedValue,
                    note: note,
                },
                dataType: "json",
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function(res) {
                    $("#loader").hide();
                    if(res.status){
                        if(res.data && res.data.is_redirect && res.data.redirect_url){
                            window.location.href = res.data.redirect_url;
                            return;
                        } else {
                            toastAlert("success", res.message);
                        }
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
        $("#pickupForm").submit(function (e) {
            e.preventDefault();
            var pickup_phone    = $('#pickup_phone').val();
            var pickup_name     = $('#pickup_name').val();
            var customer_tag    = $('#customer_tag').val();
            if(pickup_phone != ''){
                if(pickup_name != ''){
                    if(customer_tag != ''){
                        var formData = new FormData(this);
                        $.ajax({
                            type: "POST",
                            url: base_url + "/admin/billing/save-delivery-address",
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
                                    if(res.data.is_redirect){
                                        toastAlert("success", res.message, true, res.data.redirect_url);
                                    } else {
                                        toastAlert("success", res.message);
                                        setTimeout(function() {
                                            location.reload();
                                        }, 1000);
                                    }
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
                        toastAlert("error", 'Please enter tag');
                    }
                } else {
                    toastAlert("error", 'Please enter pickup name');
                }
            } else {
                toastAlert("error", 'Please enter pickup mobile no.');
            }
        });
        $("#deliverForm").submit(function (e) {
            e.preventDefault();
            var delivery_name           = $('#delivery_name').val();
            var delivery_phone          = $('#delivery_phone').val();
            var delivery_address        = $('#delivery_address').val();
            var delivery_suburb         = $('#delivery_suburb').val();
            var delivery_state          = $('#delivery_state').val();
            var delivery_postcode       = $('#delivery_postcode').val();
            var delivery_email            = $('#delivery_email').val();
            if(delivery_name != ''){
                if(delivery_phone != ''){
                    if(delivery_address != ''){
                        if(delivery_suburb != ''){
                            if(delivery_state != ''){
                                if(delivery_postcode != ''){
                                    if(delivery_email != ''){
                                        var formData = new FormData(this);
                                        $.ajax({
                                            type: "POST",
                                            url: base_url + "/admin/billing/save-delivery-address",
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
                                                    if(res.data.is_redirect){
                                                        toastAlert("success", res.message, true, res.data.redirect_url);
                                                    } else {
                                                        toastAlert("success", res.message);
                                                        setTimeout(function() {
                                                            location.reload();
                                                        }, 1000);
                                                    }
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
                                        toastAlert("error", 'Please enter delivery email');
                                    }
                                } else {
                                    toastAlert("error", 'Please enter delivery postcode');
                                }
                            } else {
                                toastAlert("error", 'Please enter delivery state');
                            }
                        } else {
                            toastAlert("error", 'Please enter delivery suburb');
                        }
                    } else {
                        toastAlert("error", 'Please enter delivery address');
                    }
                } else {
                    toastAlert("error", 'Please enter delivery mobile no.');
                }
            } else {
                toastAlert("error", 'Please enter delivery name');
            }
        });
    });
</script>
