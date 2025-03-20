<?php
use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
?>
<div class="row">
    <div class="col-md-12">
        <div class="table-container table-responsive">
            <h3><?=$page_header?></h3>
            <table class="table text-center">
                <thead>
                    <tr>
                        <th width="8%">#</th>
                        <th width="8%">ORDER ID</th>
                        <th>ITEMS</th>
                        <th>AMOUNT</th>
                        <th>OPERATOR</th>
                        <th>DATE</th>
                        <th>TIME</th>
                        <th>STATUS</th>
                        <th width="8%">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totItemQty = 0;
                    if(count($rows) > 0){ $k=1; foreach($rows as $row){
                        $orderItemCount     = OrderDetail::where('order_id', '=', $row->id)->count();
                        $getOperator        = Admin::select('name')->where('id', '=', $row->operator_id)->first();
                        $getOrderItemTotal  = OrderDetail::where('order_id', '=', $row->id)->sum('qty');
                        $totItemQty         = $getOrderItemTotal;
                    ?>
                        <tr>
                            <td><?=$k++?></td>
                            <td><?=$row->order_no?></td>
                            <td><?=$totItemQty?></td>
                            <td>$<?=number_format($row->net_amount,2)?></td>
                            <td><?=(($getOperator)?$getOperator->name:'')?></td>
                            <td><?=date_format(date_create($row->order_date), "M d, Y")?></td>
                            <td><?=date_format(date_create($row->order_time), "h:i A")?></td>
                            <td>
                                <?php if($row->status == 0){?>
                                    <span class="badge bg-secondary">Item Added</span>
                                <?php }?>
                                <?php if($row->status == 1){?>
                                    <span class="badge bg-info">Delivery Mode Added</span>
                                <?php }?>
                                <?php if($row->status == 2){?>
                                    <span class="badge bg-primary">Payment Mode Added</span>
                                <?php }?>
                                <?php if($row->status == 3){?>
                                    <span class="badge bg-warning">Hold</span>
                                <?php }?>
                                <?php if($row->status == 4){?>
                                    <span class="badge bg-danger">Cancel Order</span>
                                <?php }?>
                            </td>
                            <td><a href="<?=url('admin/billing/billing-item/' . Helper::encoded($row->id))?>"><button class="btn btn-custom">RECALL</button></a></td>
                        </tr>
                    <?php } } else {?>
                        <tr>
                            <td colspan="9" style="color: red !important; text-align: center;">No hold orders found</td>
                        </tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
        <div class="order-summery-left-bottom">
            <div class="row my-4">
                <div class="col-md-12 d-flex justify-content-between">
                    <a href="<?=url('admin/billing/list')?>" class="btn btn-custom">BACK</a>
                </div>
            </div>
        </div>
    </div>
</div>