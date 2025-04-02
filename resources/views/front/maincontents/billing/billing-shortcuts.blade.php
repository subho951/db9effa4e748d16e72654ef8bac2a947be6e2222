<?php
use App\Models\Order;
use App\Models\OrderDetail;
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
?>
<section class="info-body">
    <div class="container-fluid">
        <div class="order-summery-left-bottom">
            <div class="row my-4">
                <div class="col-md-12 d-flex justify-content-between">
                    <a href="<?=url('user/billing/list')?>" class="my-btn btn-sky">BACK</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="custome-dtl">
                    <?php if($fast_buttons){ foreach($fast_buttons as $fast_button){?>
                        <?php
                        $order_id = (($getOrder)?$getOrder->id:0);
                        $checkProductExistCart = OrderDetail::where('order_id', '=', $order_id)->where('item_id', '=', $fast_button->product_id)->count();
                        ?>
                        <div class="custome-info-box <?=(($checkProductExistCart > 0)?'active':'')?>" onclick="searchProductCartAdd(<?=$fast_button->product_id?>, <?=$fast_button->qty?>, <?=$fast_button->id?>);" id="fast-button-<?=$fast_button->id?>">
                            <h4><?=$fast_button->name?></h4>
                            <!-- <p>$<?=$fast_button->price?></p>
                            <p>Qty : <?=$fast_button->qty?></p> -->
                        </div>
                    <?php } }?>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    var base_url = '<?=url('/')?>';
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
                    toastAlert("success", res.message, true, res.data.redirect_url);
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