<?php
use App\Models\Order;
use App\Models\OrderDetail;
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
?>
<div class="row">
    <!-- Sidebar Section -->
    <div class="col-md-6">
        <div class="order-summary">
            <div class="order-summary-left">
                <div class="mb-3 d-flex justify-content-between">
                    <p class="order-header">ORDER #: <?=(($getOrder)?$getOrder->order_no:'')?></p>
                    <a href="<?=url('user/billing/billing-ongoing')?>" class="my-btn btn-orange">Ongoing Orders</i></a>
                    <a href="<?=url('user/billing/past-orders')?>" class="my-btn btn-orange">Past Orders</i></a>
                </div>
                <div class="my-5">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="search-heading">Search and Shortcuts</h4>
                            <div class="filter_box">
                                <form action="" id="searchForm">
                                    @csrf
                                    <input type="hidden" name="order_id" id="order_id" value="<?=(($getOrder)?$getOrder->id:0)?>">
                                    <input type="hidden" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222">
                                    <div class="input-group">
                                        <input type="text" class="form-control me-4" placeholder="Enter Brand, Name, Barcode and By Supplier" aria-label="Recipient's username with two button addons" id="search_keyword" name="search_keyword">
                                        <button type="submit" class="my-btn btn-sky text-white">Enter</button>
                                    </div>
                                </form>
                                <div class="filter-result" style="height:300px; overflow-y: scroll;">
                                    <ul id="search-products">
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="order-summery-left-bottom">
                        <div class="row my-4">
                            <div class="col-md-12 d-flex justify-content-between">
                                <a href="<?=url('user/billing/list')?>" class="my-btn btn-sky">BACK</a>
                                <?php if(count($fast_buttons) > 3){?>
                                    <a href="<?=url('user/billing/billing-shortcuts/' . Helper::encoded((($getOrder)?$getOrder->id:'')))?>" class="my-btn btn-sky">MORE</a>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Order Items Section -->
    <div class="col-md-6">
        <div class="custome-dtl">
            <?php if($fast_buttons){ foreach($fast_buttons as $fast_button){?>
                <?php
                $order_id = (($getOrder)?$getOrder->id:0);
                $checkProductExistCart = OrderDetail::where('order_id', '=', $order_id)->where('item_id', '=', $fast_button->product_id)->count();
                ?>
                <div class="custome-info-box <?=(($checkProductExistCart > 0)?'active':'')?>" onclick="searchProductCartAdd(<?=$fast_button->product_id?>, <?=$fast_button->qty?>, <?=$fast_button->id?>);" id="fast-button-<?=$fast_button->id?>">
                    <h4><?=$fast_button->name?></h4>
                    <p>$<?=$fast_button->price?></p>
                    <p>Qty : <?=$fast_button->qty?></p>
                </div>
            <?php } }?>
        </div>
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
        $("#searchForm").submit(function (e) {
            e.preventDefault();
            var search_keyword    = $('#search_keyword').val();
            if(search_keyword != ''){
                if(search_keyword.length >= 3){
                    var formData = new FormData(this);
                    $.ajax({
                        type: "POST",
                        url: base_url + "/user/billing/search-result",
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
                                $('#search-products').empty();
                                $('#search-products').html(res.data.item_table_html);
                            }else{
                                toastAlert("error", res.message);
                                $('#search-products').empty();
                                $('#search-products').html(res.data.item_table_html);
                            }
                        },
                        error:function (xhr, ajaxOptions, thrownError){
                            $("#loader").hide();
                            var res = xhr.responseJSON;
                            if(!res.status) {
                                toastAlert("error", res.message);
                                $('#search-products').empty();
                                $('#search-products').html(res.data.item_table_html);
                            }
                        }
                    });
                } else {
                    toastAlert("error", 'Search keyword needs to be minimum three (3) characters');
                }
            } else {
                toastAlert("error", 'Please enter search keyword');
            }
        });
    });
    function searchProductCartAdd(itemId, qty, fastButtonId = ''){
        var order_id = '<?=(($getOrder)?$getOrder->id:0)?>';
        $.ajax({
            type: "POST",
            url: base_url + "/user/billing/search-product-add-to-cart",
            data: {"_token": "{{ csrf_token() }}", key : "db9effa4e748d16e72654ef8bac2a947be6e2222", item_id : itemId, order_id : order_id, qty : qty},
            dataType: "JSON",
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (res) {
                $("#loader").hide();
                if(res.status){
                    toastAlert("success", res.message);
                    $('#fast-button-' + fastButtonId).addClass('active');
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