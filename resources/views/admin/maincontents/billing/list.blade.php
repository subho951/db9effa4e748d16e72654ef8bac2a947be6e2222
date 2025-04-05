<?php
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
?>
<style type="text/css">
    .hidden-important {
        display: none !important;
    }
</style>
<div class="row">
    <!-- Sidebar Section -->
    <div class="col-md-5">
        <div class="order-summary">
            <div class="order-summary-left">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <!-- <p class="order-header">ORDER #: <?=(($getOrder)?$getOrder->order_no:'')?></p> -->
                    <a href="<?=url('admin/billing/billing-ongoing')?>" class="my-btn btn-orange">Ongoing Orders</i></a>
                    <a href="<?=url('admin/billing/past-orders')?>" class="my-btn btn-orange">Past Orders</i></a>
                </div>
                <div class="my-1 my-md-4">
                    <div class="row">
                        <div class="col-6 d-flex"><input type="text" class="form-control outline-red scan-input" id="barcode" placeholder="Scan / Enter Barcode Or SKU" maxlength="13"></div>
                        <div class="col-6 d-flex justify-content-end"><a href="javascript:void(0);" class="my-btn btn-sky enter-btn" onclick="addToCart();">Enter</a></div>
                    </div>
                </div>
                <div class="row mt-0 mt-sm-2 mt-md-4">
                    <div class="col-5 d-flex">
                        <!-- <a href="javascript:void(0);" class="my-btn <?=(($getOrder)?(($getOrder->delivery_mode == 'Take')?'take-btn':'outline-red'):'outline-red')?> text-black">
                            <input type="radio" class="radioOption" name="delivery_mode" id="delivery_mode1" value="Take" <?=(($getOrder)?(($getOrder->delivery_mode == 'Take')?'checked':''):'')?> style="display: none;">
                            <label for="delivery_mode1">&nbsp;Take</label>
                        </a> -->

                        <a href="javascript:void(0);" class="my-btn <?=(($getOrder)?(($getOrder->delivery_mode == 'Take')?'take-btn':'outline-red'):'outline-red')?> text-black deliveryOption" data-radio="delivery_mode1">
                            <input type="radio" class="radioOption" name="delivery_mode" id="delivery_mode1" value="Take" <?=(($getOrder)?(($getOrder->delivery_mode == 'Take')?'checked':''):'')?> style="display: none;">
                            <label for="delivery_mode1">&nbsp;Take</label>
                        </a>
                    </div>
                    <div class="col-7">
                        <div class="d-flex justify-content-end">
                            <a href="javascript: void(0);" class="my-btn btn-yellow me-3" type="button" data-bs-toggle="modal" data-bs-target="#cancelSaleModal">Cancel Sale</a>
                            <a href="<?=url('admin/billing/billing-search/' . Helper::encoded((($getOrder)?$getOrder->id:'')))?>" class="my-btn btn-sky enter-btn">Search</a>
                        </div>
                    </div>
                </div>
                <div class="row mt-0 mt-sm-2 mt-md-4">
                    <div class="col-5 d-flex">
                        <!-- <a href="javascript:void(0);" class="my-btn <?=(($getOrder)?(($getOrder->delivery_mode == 'Deliver')?'take-btn':'outline-red'):'outline-red')?>  text-black">
                            <input type="radio" class="radioOption" name="delivery_mode" id="delivery_mode2" value="Deliver" <?=(($getOrder)?(($getOrder->delivery_mode == 'Deliver')?'checked':''):'')?> style="display: none;">
                            <label for="delivery_mode2">&nbsp;Deliver</label>
                        </a> -->

                        <a href="javascript:void(0);" class="my-btn <?=(($getOrder)?(($getOrder->delivery_mode == 'Deliver')?'take-btn':'outline-red'):'outline-red')?> text-black deliveryOption" data-radio="delivery_mode2">
                            <input type="radio" class="radioOption" name="delivery_mode" id="delivery_mode2" value="Deliver" <?=(($getOrder)?(($getOrder->delivery_mode == 'Deliver')?'checked':''):'')?> style="display: none;">
                            <label for="delivery_mode2">&nbsp;Deliver</label>
                        </a>
                    </div>
                    <div class="col-7">
                        <div class="d-flex justify-content-end">
                            <a href="<?=url('admin/billing/billing-recall')?>" class="my-btn btn-yellow me-3">Recall</a>
                            <!-- <a href="javascript: void(0);" onclick="myFunction()" class="my-btn btn-yellow">Hold</a> -->
                            <a href="javascript: void(0);" class="my-btn btn-yellow" type="button" data-bs-toggle="modal" data-bs-target="#holdSaleModal">Hold</a>
                        </div>
                    </div>
                </div>
                <div class="row mt-0 mt-sm-2 mt-md-4">
                    <div class="col-5 d-flex">
                        <!-- <a href="javascript:void(0);" class="my-btn <?=(($getOrder)?(($getOrder->delivery_mode == 'Pickup')?'take-btn':'outline-red'):'outline-red')?>  text-black">
                            <input type="radio" class="radioOption" name="delivery_mode" id="delivery_mode3" value="Pickup" <?=(($getOrder)?(($getOrder->delivery_mode == 'Pickup')?'checked':''):'')?> style="display: none;">
                            <label for="delivery_mode3">&nbsp;Pickup</label>
                        </a> -->

                        <a href="javascript:void(0);" class="my-btn <?=(($getOrder)?(($getOrder->delivery_mode == 'Pickup')?'take-btn':'outline-red'):'outline-red')?> text-black deliveryOption" data-radio="delivery_mode3">
                            <input type="radio" class="radioOption" name="delivery_mode" id="delivery_mode3" value="Pickup" <?=(($getOrder)?(($getOrder->delivery_mode == 'Pickup')?'checked':''):'')?> style="display: none;">
                            <label for="delivery_mode3">&nbsp;Pickup</label>
                        </a>
                    </div>
                    <div class="col-7 d-flex justify-content-end">
                        <a href="javascript:vold(0)" type="button" data-bs-toggle="modal" data-bs-target="#adminpinmodal" class="my-btn btn-yellow d-block Modify-btn" id="price-modify-btn">Modify</a>
                        <a href="javascript:vold(0)" type="button" class="my-btn btn-sky d-block Modify-btn" id="price-update-btn" style="display: none !important;">Update</a>
                    </div>
                </div>
            </div>
            <div class="order-summery-left-bottom">
                <div class="row mt-0 mt-sm-2 mt-md-4">
                    <div class="col-md-12 d-flex justify-content-between">
                        <!-- <a class="my-btn btn-orange w-auto" href="javascript: vold(0)" type="button" data-bs-toggle="modal" data-bs-target="#adminpinmodal">Admin</a> -->
                        <?php if($getOrder){ if($getOrder->delivery_mode != ''){?>
                            <a href="<?=url('admin/billing/billing-payment/' . Helper::encoded((($getOrder)?$getOrder->id:0)))?>" class="my-btn btn-green btn-lg">PAYMENT</a>
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
                            <th class="text-end">Price</th>
                            <!-- <th class="text-end">Subtotal</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totItemQty = 0; if($getOrderItems){ foreach($getOrderItems as $getOrderItem){?>
                            <tr>
                                <td>
                                    <span><?=$getOrderItem->product_name?></span>
                                    <!-- <small style="font-size: 10px;color: #0096eb;">SKU : <?=$getOrderItem->product_sku?></small> -->
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
                <div class="footer-notes px-4 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="me-2">Notes </p>
                        <input type="text" class="form-control" id="note" value="<?=$getOrder->note?>" placeholder="Notes">
                        <div class="items-count">
                            <p>ITEMS : <?=$totItemQty?></p>
                        </div>
                    </div>
                </div>
                <div class="order-footer px-4 py-2">
                    <div class="d-flex justify-content-between">
                        <p>Delivery</p>
                        <p>$<?=number_format($getOrder->delivery_amount,2)?></p>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <p>Total Discounts</p>
                        <p>$<?=number_format($getOrder->discount_amount,2)?></p>
                    </div>
                </div>
                <div class="d-flex justify-content-between totals p-1 p-sm-2 p-md-4">
                    <!-- <p>ITEMS : ?=$totItemQty?></p> -->
                    <p>TOTAL $<?=number_format($getOrder->net_amount,2)?></p>
                </div>
            </div>
        <?php }?>
    </div>
</div>
<!-- Admin PIN Modal -->
<div class="modal fade" id="adminpinmodal" tabindex="-1" aria-labelledby="adminpinmodalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" class="otp-form" name="otp-form" id="otp-form">
                @csrf
                <input type="hidden" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222">
                <div class="modal-body">
                    <h1 class="modal-title fs-4 text-center w-100 text-black mb-2" id="adminpinmodalLabel">Enter Your Admin Pin</h1>
                    <div class="otp-input-fields">
                        <input type="password" class="otp__digit otp__field__1" autocomplete="off" name="pin1" id="pin1">
                        <input type="password" class="otp__digit otp__field__2" autocomplete="off" name="pin2" id="pin2">
                        <input type="password" class="otp__digit otp__field__3" autocomplete="off" name="pin3" id="pin3">
                        <input type="password" class="otp__digit otp__field__4" autocomplete="off" name="pin4" id="pin4">
                    </div>
                    <div class="mt-4 d-flex justify-content-center"> 
                        <button class="btn btn-green text-black px-4 validate">Submit</button> 
                    </div>
                </div>
                <!-- <div class="result"><p id="_otp" class="_notok"></p></div> -->
            </form>
        </div>
    </div>
</div>
<!-- Cancel Sale Modal -->
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
<!-- Hold Sale Modal -->
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
        if(barcode.length >= 10){
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
            toastAlert('error', 'Barcode or SKU number length will be minimum 10 characters long. Please enter right barcode or SKU number');
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
        $("#barcode").focus();
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
                        if(res.data.is_redirect){
                            toastAlert("success", res.message, true, res.data.redirect_url);
                        } else {
                            toastAlert("success", res.message);
                            setTimeout(function() {
                                window.location.reload();
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
        });
        $("#otp-form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: base_url + "/admin/billing/validate-admin-pin",
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
                        $('#otp-form').trigger("reset");
                        $('#adminpinmodal').modal('hide');
                        $('.price-text').hide();
                        $('.price-val').show();
                        $("#price-modify-btn").addClass("hidden-important");
                        $('#price-update-btn').show();
                    }else{
                        toastAlert("error", res.message);
                        $('#otp-form').trigger("reset");
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
        $("#price-update-btn").click(function() {
            var order_id        = '<?=(($getOrder)?$getOrder->id:0)?>';

            var productPrices   = [];
            // Loop through all elements with class 'product' and get values
            $("input[name='product_price']").each(function() {
                productPrices.push($(this).val());
            });

            var productIds   = [];
            // Loop through all elements with class 'product' and get values
            $("input[name='item_id']").each(function() {
                productIds.push($(this).val());
            });

            // Send data using AJAX
            $.ajax({
                url: base_url + "/admin/billing/billing-price-update",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    key : "db9effa4e748d16e72654ef8bac2a947be6e2222",
                    order_id: order_id,
                    item_price: productPrices,
                    item_id: productIds,
                },
                dataType: "json",
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function(res) {
                    $("#loader").hide();
                    if(res.status){
                        toastAlert("success", res.message);
                        $('#order-item').empty();
                        $('#order-item').html(res.data.item_table_html);
                        $('.price-text').show();
                        $('.price-val').hide();
                        $("#price-modify-btn").removeClass("hidden-important");
                        $('#price-update-btn').addClass("hidden-important");
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
        });
    });
</script>