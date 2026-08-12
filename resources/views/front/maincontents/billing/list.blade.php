<?php
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
?>
<style type="text/css">
    .hidden-important {
        display: none !important;
    }
    .disabled-link {
        pointer-events: none; /* Prevent clicking */
        opacity: 0.6;         /* Optional: make it look disabled */
        cursor: not-allowed;
    }
    .admin-pin-input {
        max-width: 220px;
        margin: 0 auto;
        text-align: center;
        letter-spacing: 0.4em;
    }
</style>
<div class="row">
    <!-- Sidebar Section -->
    <div class="col-md-5">
        <div class="order-summary">
            <div class="order-summary-left">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <!-- <p class="order-header">ORDER #: <?=(($getOrder)?$getOrder->order_no:'')?></p> -->
                    <!-- <a href="<?=url('user/billing/billing-ongoing')?>" class="my-btn btn-orange">Ongoing Orders</i></a> -->
                    <a href="<?=url('user/billing/past-orders')?>" class="my-btn btn-orange">Past Orders</i></a>
                </div>
                <div class="my-1 my-md-4">
                    <div class="row">
                        <div class="col-6 d-flex">
                            <input type="text" class="form-control outline-red scan-input" id="barcode" placeholder="Scan barcode" aria-label="Barcode" minlength="1" maxlength="100" autocomplete="off">
                        </div>
                        <div class="col-6 d-flex justify-content-end"><a href="javascript:void(0);" class="my-btn btn-sky enter-btn" id="addToCartBtn">Enter</a></div>
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
                            <a href="javascript: void(0);" class="my-btn btn-yellow me-3 disabled-link" type="button" data-bs-toggle="modal" data-bs-target="#cancelSaleModal" id="cancel-item-btn">Cancel Item</a>
                            <a href="<?=url('user/billing/billing-search/' . Helper::encoded((($getOrder)?$getOrder->id:'')))?>" class="my-btn btn-sky enter-btn" id="admin-search-btn" data-admin-action="search">Search</a>
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
                            <?php if(count($getOrderItems) > 0){ ?>
                                <a href="<?=url('user/billing/billing-recall')?>" class="my-btn btn-yellow me-3 disabled-link" onclick="return false;">Recall</a>
                            <?php } else {?>
                                <a href="<?=url('user/billing/billing-recall')?>" class="my-btn btn-yellow me-3">Recall</a>
                            <?php }?>
                            
                            <?php if(count($getOrderItems) > 0){ ?>
                                <a href="javascript: void(0);" class="my-btn btn-yellow" type="button" data-bs-toggle="modal" data-bs-target="#holdSaleModal">Hold</a>
                            <?php } else {?>
                                <a href="javascript: void(0);" class="my-btn btn-yellow disabled-link" type="button" onclick="return false;">Hold</a>
                            <?php }?>
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
                        <a href="javascript:void(0);" type="button" class="my-btn btn-yellow d-block Modify-btn" id="price-modify-btn" data-admin-action="modify">Modify</a>
                        <a href="javascript:vold(0)" type="button" class="my-btn btn-sky d-block Modify-btn" id="price-update-btn" style="display: none !important;">Update</a>
                        <a href="javascript:void(0);" type="button" class="my-btn btn-sky d-block Modify-btn" id="price-return-btn" data-admin-action="return">Return</a>
                    </div>
                </div>
            </div>
            <div class="order-summery-left-bottom">
                <div class="row mt-0 mt-sm-2 mt-md-4">
                    <div class="col-md-12 d-flex justify-content-between">
                        <!-- <a class="my-btn btn-orange w-auto" href="javascript: vold(0)" type="button" data-bs-toggle="modal" data-bs-target="#adminpinmodal">Admin</a> -->
                        <?php if($getOrder){ if($getOrder->delivery_mode != ''){?>
                            <a href="<?=url('user/billing/billing-payment/' . Helper::encoded((($getOrder)?$getOrder->id:0)))?>" class="my-btn btn-green btn-lg">PAYMENT</a>
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
                            <tr class="order-row" data-item-id="<?=$getOrderItem->id?>">
                                <td>
                                    <?php if($getOrderItem->subtotal >= 0){?>
                                        <input type="radio" name="order_details_id" id="order_details_id<?=$getOrderItem->id?>" value="<?=$getOrderItem->id?>" style="display:none;">
                                    <?php }?>
                                    <span style="<?= (($getOrderItem->status == 4)?'color: red;':'')?>"><?=$getOrderItem->product_name?></span>
                                    <!-- <small style="font-size: 10px;color: #0096eb;">SKU : <?=$getOrderItem->product_sku?></small> -->
                                    <?php if($getOrderItem->subtotal < 0){?>
                                        <small style="font-size: 9px; color:#000;" class="badge bg-warning">RETURN</small>
                                    <?php }?>
                                </td>
                                <td class="text-center">
                                    <?php if($getOrderItem->status != 4){?>
                                        <button class="btn-plus-minus" onclick="itemQtyDecrease(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);">-</button>
                                    <?php }?>
                                    <input type="hidden" id="qty-val-<?=$getOrderItem->item_id?>" value="<?=$getOrderItem->qty?>">
                                    <span id="qty-text-<?=$getOrderItem->item_id?>" style="<?= (($getOrderItem->status == 4)?'color: red;':'')?>"><?=$getOrderItem->qty?></span>
                                    <?php if($getOrderItem->status != 4){?>
                                        <button class="btn-plus-minus" onclick="itemQtyIncrease(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);">+</button>
                                        <?php $totItemQty += $getOrderItem->qty; ?>
                                    <?php }?>
                                </td>
                                <!-- <td class="text-end">
                                $<?=number_format($getOrderItem->subtotal,2)?>
                                    <a href="javascript:void(0);" onclick="itemDelete(<?=$getOrderItem->item_id?>,<?=(($getOrder)?$getOrder->id:0)?>);"><i class='bx bxs-trash'></i></a>
                                </td> -->
                                <td class="text-end">
                                    <span class="price-text" style="<?= (($getOrderItem->status == 4)?'color: red;':'')?>">$<?=number_format($getOrderItem->price,2)?></span>
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
                <div class="footer-notes px-4 py-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="me-2">Notes </p>
                        <input type="text" class="form-control" id="note" value="<?=$getOrder->note?>" placeholder="Notes">
                        <!-- <div class="d-flex justify-content-between totals p-4"><p>ITEMS : ?=$totItemQty?></p></div> -->
                        <div class="items-count">
                            <p>ITEMS : <?=$totItemQty?></p>
                        </div>
                    </div>
                </div>
                <div class="order-footer px-4">
                    <div class="d-flex justify-content-between py-1">
                        <p>Discounts</p>
                        <p>$<?=number_format($getOrder->discount_amount,2)?></p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p>Delivery</p>
                        <p>$<?=number_format($getOrder->delivery_amount,2)?></p>
                    </div>
                </div>
                <div class="d-flex justify-content-between totals p-1 p-sm-2 p-md-4">
                    <p></p>
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
            <form action="" class="otp-form" name="otp-form" id="otp-form" novalidate>
                @csrf
                <input type="hidden" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222">
                <input type="hidden" name="action" id="admin_action" value="">
                <div class="modal-body">
                    <h1 class="modal-title fs-4 text-center w-100 text-black mb-3" id="adminpinmodalLabel">Admin Login</h1>
                    <div class="mb-3">
                        <label for="admin_pin" class="form-label d-block text-center text-black">PIN</label>
                        <input type="password" class="form-control admin-pin-input" autocomplete="new-password" inputmode="numeric" maxlength="4" name="pin" id="admin_pin">
                    </div>
                    <div class="mt-4 d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary text-white px-4 mx-1" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-green text-black px-4 mx-1 validate">OK</button>
                    </div>
                </div>
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
                    <h1 class="modal-title fs-4 text-center w-100 text-black mb-2" id="adminpinmodalLabel">Are you sure that you want to cancel this item?</h1>
                    <div class="mt-4 d-flex justify-content-center"> 
                        <!-- <button class="btn btn-success text-black px-4 mx-1" onclick="orderChangeStatus(<?=(($getOrder)?$getOrder->id:0)?>, 4, 'cancelSaleModal')">Yes</button> -->
                        <button class="btn btn-success text-black px-4 mx-1" id="confirmCancelBtn">Yes</button>
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

    // ✅ Delegated click handler (works after adding new rows)
    $(document).on('click', '.order-row', function() {
        $(this).find('input[type="radio"]').prop("checked", true);
        
        $('#cancel-item-btn').removeClass('disabled-link');
        $('#price-modify-btn').removeClass('disabled-link');

        $('.order-row').removeClass('selected');
        // $('.price-val').hide();
        // $('.price-text').show();

        $(this).addClass("selected");
        // $(this).find('.price-text').hide();
        // $(this).find('.price-val').show().focus();
    });

    // Get references
    const barcodeInput = document.getElementById('barcode');
    const addToCartBtn = document.getElementById('addToCartBtn');

    function addToCart(){
        var barcode = $.trim($('#barcode').val());
        var order_id = '<?=(($getOrder)?$getOrder->id:0)?>';
        if(barcode.length >= 1 && barcode.length <= 100){
            $.ajax({
                type: "POST",
                url: base_url + "/user/billing/add-to-cart",
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
                        $("#barcode").focus();
                    }else{
                        toastAlert("error", res.message);
                        $('#barcode').val('');
                        $("#barcode").focus();
                    }
                },
                error:function (xhr, ajaxOptions, thrownError){
                    $("#loader").hide();
                    var res = xhr.responseJSON;
                    if(!res.status) {
                        toastAlert("error", res.message);
                        $('#barcode').val('');
                        $("#barcode").focus();
                    }
                }
            });
        } else {
            toastAlert('error', 'Enter a barcode (up to 100 characters)');
            $('#barcode').val('');
            $("#barcode").focus();
        }
    }

    // Bind click event
    addToCartBtn.addEventListener('click', addToCart);

    // Bind Enter key on input
    barcodeInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault(); // Prevent form submission if inside a form
        addToCartBtn.click(); // Simulate button click
      }
    });
    
    function itemDelete(itemId, orderId){
        // Show confirmation box
        if (confirm("Are you sure you want to delete this product from cart ?")) {
            // User clicked "Yes", proceed with AJAX
            $.ajax({
                url: base_url + "/user/billing/item-delete",
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
                        $("#barcode").focus();
                    }else{
                        toastAlert("error", res.message);
                        $("#barcode").focus();
                    }
                },
                error:function (xhr, ajaxOptions, thrownError){
                    $("#loader").hide();
                    var res = xhr.responseJSON;
                    if(!res.status) {
                        toastAlert("error", res.message);
                        $("#barcode").focus();
                    }
                }
            });
        } else {
            toastAlert("error", "Delete action cancelled");
            $("#barcode").focus();
        }
    }
    function orderChangeStatus(orderId, status, modalId, itemId = null){
        console.log("Order ID:", orderId);
        console.log("Item ID:", itemId);
        console.log("Status:", status);

        $.ajax({
            url: base_url + "/user/billing/billing-change-status",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                key : "db9effa4e748d16e72654ef8bac2a947be6e2222",
                order_id: orderId,
                item_id: itemId,
                status: status
            },
            beforeSend: function () {
                $("#loader").show();
            },
            success: function(res) {
                $("#loader").hide();
                if(res.status){
                    $('#' + modalId).modal('hide');
                    var redirectUrl = base_url + '/admin/billing/list';
                    toastAlert("success", res.message);
                    setTimeout(function() {
                        window.location.href = redirectUrl;
                    }, 1000);
                }else{
                    toastAlert("error", res.message);
                    $("#barcode").focus();
                }
            },
            error:function (xhr, ajaxOptions, thrownError){
                $("#loader").hide();
                var res = xhr.responseJSON;
                if(!res.status) {
                    toastAlert("error", res.message);
                    $("#barcode").focus();
                }
            }
        });
    }
    function itemQtyIncrease(itemId, orderId){
        var qtyVal = parseInt($('#qty-val-' + itemId).val()) + 1;
        $('#qty-val-' + itemId).val(qtyVal);
        $('#qty-text-' + itemId).text(qtyVal);
        $.ajax({
            url: base_url + "/user/billing/billing-update-qty",
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
                    $("#barcode").focus();
                }else{
                    toastAlert("error", res.message);
                    $("#barcode").focus();
                }
            },
            error:function (xhr, ajaxOptions, thrownError){
                $("#loader").hide();
                var res = xhr.responseJSON;
                if(!res.status) {
                    toastAlert("error", res.message);
                    $("#barcode").focus();
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
                url: base_url + "/user/billing/billing-update-qty",
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
                        $("#barcode").focus();
                    }else{
                        toastAlert("error", res.message);
                        $("#barcode").focus();
                    }
                },
                error:function (xhr, ajaxOptions, thrownError){
                    $("#loader").hide();
                    var res = xhr.responseJSON;
                    if(!res.status) {
                        toastAlert("error", res.message);
                        $("#barcode").focus();
                    }
                }
            });
        } else {
            toastAlert("error", 'Product quantity can\'t be less than 1');
        }
    }
    $(document).ready(function() {
        $("#barcode").focus();
        $(".deliveryOption").on("click", function(e) {
            e.preventDefault();
            var radioId         = $(this).data("radio");
            var selectedOption  = $("#" + radioId);
            selectedOption.prop("checked", true);
            $(".deliveryOption").removeClass("take-btn").addClass("outline-red");
            $(this).removeClass("outline-red").addClass("take-btn");
            var selectedValue   = selectedOption.val(); // Get clicked value
            var order_id        = '<?=(($getOrder)?$getOrder->id:0)?>';
            var note            = $('#note').val();
            $.ajax({
                url: base_url + "/user/billing/billing-select-delivery-address",
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
        $(document).on('click', '[data-admin-action]', function (e) {
            e.preventDefault();
            $('#otp-form').trigger('reset');
            $('#admin_action').val($(this).attr('data-admin-action'));
            $('#adminpinmodal').modal('show');
        });

        $("#otp-form").submit(function (e) {
            e.preventDefault();
            var requestedAction = $('#admin_action').val();
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: base_url + "/user/billing/validate-admin-pin",
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
                        $('#otp-form').trigger("reset");
                        $('#adminpinmodal').modal('hide');

                        if(requestedAction === 'search'){
                            window.location.href = $('#admin-search-btn').attr('href');
                        }else if(requestedAction === 'return'){
                            itemReturn(<?=(($getOrder)?$getOrder->id:0)?>);
                        }else if(requestedAction === 'modify'){
                            toastAlert("success", res.message);
                            $('.price-text').hide();
                            $('.price-val').show();
                            $("#price-modify-btn").addClass("hidden-important");
                            $('#price-update-btn').show();
                        }
                    }else{
                        toastAlert("error", "Incorrect");
                        $('#otp-form').trigger("reset");
                        $('#admin_action').val(requestedAction);
                        $('#admin_pin').trigger('focus');
                    }
                },
                error:function (xhr, ajaxOptions, thrownError){
                    $("#loader").hide();
                    var res = xhr.responseJSON || {};
                    toastAlert("error", res.message || "Incorrect");
                    $('#otp-form').trigger("reset");
                    $('#admin_action').val(requestedAction);
                    $('#admin_pin').trigger('focus');
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
                url: base_url + "/user/billing/billing-price-update",
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
    function itemReturn(orderId){
        let order_details_id = $('input[name="order_details_id"]:checked').val();
        if(order_details_id != undefined){
            $.ajax({
                url: base_url + "/user/billing/billing-item-return",
                type: "POST",
                data: {"_token": "{{ csrf_token() }}", key : "db9effa4e748d16e72654ef8bac2a947be6e2222", order_id : orderId, order_details_id : order_details_id},
                beforeSend: function () {
                    $("#loader").show();
                },
                success: function(res) {
                    $("#loader").hide();
                    if(res.status){
                        var redirectUrl = base_url + '/user/billing/list';
                        toastAlert("success", res.message);
                        setTimeout(function() {
                            window.location.href = redirectUrl;
                        }, 1000);
                    }else{
                        toastAlert("error", res.message);
                        $("#barcode").focus();
                    }
                },
                error:function (xhr, ajaxOptions, thrownError){
                    $("#loader").hide();
                    var res = xhr.responseJSON;
                    if(!res.status) {
                        toastAlert("error", res.message);
                        $("#barcode").focus();
                    }
                }
            });
        } else {
            toastAlert("warning", 'Please select return item !!!');
        }
    }
    $(document).on('click', '#cancel-item-btn', function() {
        // Get selected row item ID
        let selectedItemId = $('.order-row.selected').data('item-id');

        // Store in modal button (so you can access later)
        $('#cancelSaleModal').attr('data-item-id', selectedItemId);
    });
    $(document).on('click', '#confirmCancelBtn', function() {
        let modal = $('#cancelSaleModal');
        let itemId = modal.data('item-id'); // retrieve item ID stored earlier
        let orderId = <?= (($getOrder) ? $getOrder->id : 0) ?>; // from PHP
        let status = 4; // cancel status code

        orderChangeStatus(orderId, status, 'cancelSaleModal', itemId);
    });

    $(document).ready(function() {

        $('#adminpinmodal').on('shown.bs.modal', function () {
            setTimeout(function() {
                $('#admin_pin').trigger('focus');
            }, 200);
        });

        $('#adminpinmodal').on('hidden.bs.modal', function () {
            $('#otp-form').trigger('reset');
        });

    });
</script>
