<?php
use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
?>
<form action="<?=url('user/billing/print-delivery-order/')?>" method="POST" target="_blank">
    @csrf
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="order-summery-left-bottom">
                <div class="row my-4">
                    <div class="col-md-8 d-flex justify-content-between">
                        <!-- <a href="<?=url('user/billing/list')?>" class="btn btn-custom">Create New Order</a> -->
                    </div>
                    <div class="col-md-3 d-flex justify-content-between">
                        <button type="submit" class="btn btn-custom" id="actionButton" style="display: none;">Generate Delivery Order</button>
                    </div>
                    <div class="col-md-1 d-flex justify-content-between">
                        <select class="form-control" id="delivery_mode">
                            <option value="all" selected>All</option>
                            <!-- <option value="Take">Take</option> -->
                            <option value="Deliver">Deliver</option>
                            <!-- <option value="Pickup">Pickup</option> -->
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-container table-responsive">
                <h3><?=$page_header?></h3>
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th width="8%"><input type="checkbox" id="checkAll"> #</th>
                            <th width="8%">ORDER ID</th>
                            <th>ITEMS</th>
                            <th>AMOUNT</th>
                            <th>DELIVERY MODE</th>
                            <th>DATE/TIME</th>
                            <th width="12%">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($rows) > 0){ $k=1; foreach($rows as $row){
                            $orderItemCount = OrderDetail::where('order_id', '=', $row->id)->count();
                            // $getOperator = Admin::select('name')->where('id', '=', $row->operator_id)->first();
                        ?>
                            <tr class="all <?=$row->delivery_mode?>">
                                <td>
                                    <input type="checkbox" name="order_id[]" value="<?=$row->id?>" class="rowCheckbox">
                                    <?=$k++?>
                                </td>
                                <td><?=$row->order_no?></td>
                                <td><?=$orderItemCount?></td>
                                <td>$<?=number_format($row->net_amount,2)?></td>
                                <td><?=$row->delivery_mode?></td>
                                <td><?=date_format(date_create($row->order_date), "M d, Y")?><br><?=date_format(date_create($row->order_time), "h:i A")?></td>
                                <td>
                                    <?php if($row->delivery_mode != 'Take'){?>
                                        <?php
                                        if($row->delivery_mode == 'Pickup'){
                                            $to_email                       = $row->pickup_email;
                                        }
                                        if($row->delivery_mode == 'Deliver'){
                                            $to_email                       = $row->delivery_email;
                                        }
                                        if($to_email != ''){
                                        ?>
                                            <a href="<?=url('user/billing/billing-invoice-email/' . Helper::encoded($row->id))?>">
                                                <button class="btn btn-custom btn-sm" style="padding: 10px 10px;background: #0a9b74;border: 1px solid #0a9b74;"><i class="fa fa-envelope"></i></button>
                                            </a>
                                        <?php }?>
                                    <?php }?>
                                    <?php if($row->pdf_invoice != ''){?>
                                        <a href="<?=env('UPLOADS_URL') . '/invoice/' . $row->pdf_invoice?>" target="_blank">
                                            <button class="btn btn-custom btn-sm" style="padding: 10px 10px;background: #00bcd4;border: 1px solid #00bcd4;"><i class="fa fa-download"></i></button>
                                        </a>
                                        <!-- <a href="<?=url('user/billing/billing-pdf-invoice/' . Helper::encoded($row->id))?>" target="_blank">
                                            <button class="btn btn-custom btn-sm" style="padding: 10px 10px;background: #00bcd4;border: 1px solid #00bcd4;"><i class="fa fa-download"></i></button>
                                        </a> -->
                                    <?php }?>
                                    <a href="<?=url('user/billing/billing-invoice/' . Helper::encoded($row->id))?>" target="_blank">
                                        <button class="btn btn-custom btn-sm" style="padding: 10px 10px;background: #3f51b5;border: 1px solid #3f51b5;"><i class="fa fa-print"></i></button>
                                    </a>
                                </td>
                            </tr>
                        <?php } } else {?>
                            <tr>
                                <td colspan="7" style="color: red !important; text-align: center;">No past orders found</td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <div class="order-summery-left-bottom">
                <div class="row my-4">
                    <div class="col-md-12 d-flex justify-content-between">
                        <a href="<?=url('user/billing/list')?>" class="btn btn-custom">BACK</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function(){
        $('#delivery_mode').on('change', function(){
            var delivery_mode = $('#delivery_mode').val();
            if(delivery_mode == 'all'){
                $('.all').show();
            } else {
                $('.all').hide();
                $('.' + delivery_mode).show();
            }
        });
        // Function to check if any checkbox is selected
        function toggleButton() {
            if ($(".rowCheckbox:checked").length > 0) {
                $("#actionButton").show();
            } else {
                $("#actionButton").hide();
            }
        }

        // When header checkbox is clicked
        $("#checkAll").on("click", function () {
            $(".rowCheckbox:visible").prop("checked", $(this).prop("checked"));
            toggleButton(); // Update button visibility
        });

        // When any row checkbox is clicked
        $(".rowCheckbox").on("click", function () {
            if ($(".rowCheckbox:checked:visible").length === $(".rowCheckbox:visible").length) {
                $("#checkAll").prop("checked", true);
            } else {
                $("#checkAll").prop("checked", false);
            }
            toggleButton(); // Update button visibility
        });
    })
</script>