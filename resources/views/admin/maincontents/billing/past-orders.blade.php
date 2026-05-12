<?php
use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
?>
<style>
    .past-orders-table {
        table-layout: fixed;
        width: 100%;
        font-size: 12px;
    }
    .past-orders-table th,
    .past-orders-table td {
        padding: 3px 4px !important;
        line-height: 1.12;
        vertical-align: middle;
    }
    .past-orders-table th {
        white-space: nowrap;
        font-size: 11px;
    }
    .past-orders-table .nowrap-cell {
        white-space: nowrap;
    }
    .past-orders-table .clip-cell {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .past-orders-table .payment-details {
        display: block;
        margin-top: 1px;
        white-space: nowrap;
        font-size: 10px;
    }
    .past-orders-table .action-buttons {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
        white-space: nowrap;
    }
    .past-orders-table .compact-action-btn {
        width: 28px;
        height: 24px;
        padding: 0 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 !important;
        border-radius: 4px;
    }
    .past-orders-table .receipt-btn {
        min-width: 54px;
        height: 24px;
        padding: 0 6px !important;
        margin: 0 !important;
        border-radius: 4px;
        font-size: 11px;
        line-height: 1;
    }
</style>
<div class="row">
    <!-- <div class="col-md-12 mb-3">
        <div class="order-summery-left-bottom">
            <div class="row my-4">
                <div class="col-md-12 d-flex justify-content-between">
                    <a href="<?=url('admin/billing/list')?>" class="btn btn-custom">Create New Order</a>
                </div>
            </div>
        </div>
    </div> -->
    <div class="order-summery-left-bottom">
        <div class="row my-4">
            <div class="col-md-12 d-flex justify-content-between">
                <a href="<?=url('admin/billing/list')?>" class="btn btn-custom">BACK</a>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="table-container table-responsive">
            <h3><?=$page_header?></h3>
            <table class="table text-center past-orders-table">
                <colgroup>
                    <col style="width: 4%;">
                    <col style="width: 9%;">
                    <col style="width: 5%;">
                    <col style="width: 8%;">
                    <col style="width: 13%;">
                    <col style="width: 10%;">
                    <col style="width: 15%;">
                    <col style="width: 12%;">
                    <col style="width: 14%;">
                    <col style="width: 10%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ORDER ID</th>
                        <th>ITEMS</th>
                        <th>AMOUNT</th>
                        <th>OPERATOR</th>
                        <th>DELIVERY MODE</th>
                        <th>PAYMENT MODE</th>
                        <th>DATE/TIME</th>
                        <!-- <th>NOTE</th> -->
                        <th>ACTION</th>
                        <th>RECEIPT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(count($rows) > 0){ $k=1; foreach($rows as $row){
                        $orderItemCount = OrderDetail::where('order_id', '=', $row->id)->count();
                        $getOperator = Admin::select('name')->where('id', '=', $row->operator_id)->first();
                        $to_email = '';
                    ?>
                        <tr>
                            <td class="nowrap-cell"><?=$k++?></td>
                            <td class="nowrap-cell"><?=$row->order_no?></td>
                            <td class="nowrap-cell"><?=$orderItemCount?></td>
                            <td class="nowrap-cell">$<?=number_format($row->net_amount,2)?></td>
                            <td class="clip-cell" title="<?=(($getOperator)?$getOperator->name:'')?>"><?=(($getOperator)?$getOperator->name:'')?></td>
                            <td class="nowrap-cell"><?=$row->delivery_mode?></td>
                            <td class="nowrap-cell">
                                <?=$row->payment_mode?>
                                <?php if($row->payment_mode == 'CASH'){?>
                                    <span class="payment-details">T: $<?=number_format($row->cash_tendered, 2)?> R: $<?=number_format($row->cash_return, 2)?></span>
                                <?php }?>
                            </td>
                            <td class="nowrap-cell"><?=date_format(date_create($row->order_date), "M d, Y")?><br><?=date_format(date_create($row->order_time), "h:i A")?></td>
                            <!-- <td><?=$row->note?></td> -->
                            <td class="nowrap-cell">
                                <div class="action-buttons">
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
                                        <a href="<?=url('admin/billing/billing-invoice-email/' . Helper::encoded($row->id))?>">
                                            <button class="btn btn-custom btn-sm compact-action-btn" style="background: #0a9b74;border: 1px solid #0a9b74;" title="Email Invoice"><i class="fa fa-envelope"></i></button>
                                        </a>
                                    <?php }?>
                                <?php }?>
                                <?php if($row->pdf_invoice != ''){?>
                                    <a href="<?=env('UPLOADS_URL') . '/invoice/' . $row->pdf_invoice?>" download target="_blank" title="View Invoice">
                                        <button class="btn btn-custom btn-sm compact-action-btn" style="background: #00bcd4;border: 1px solid #00bcd4;"><i class="fa-solid fa-file-pdf"></i></button>
                                    </a>
                                    <!-- <a href="<?=url('admin/billing/billing-pdf-invoice/' . Helper::encoded($row->id))?>" target="_blank">
                                        <button class="btn btn-custom btn-sm" style="padding: 10px 10px;background: #00bcd4;border: 1px solid #00bcd4;"><i class="fa fa-download"></i></button>
                                    </a> -->
                                <?php }?>
                                <a href="<?=url('admin/billing/billing-invoice/' . Helper::encoded($row->id))?>" target="_blank" title="Print Invoice">
                                    <button class="btn btn-custom btn-sm compact-action-btn" style="background: #3f51b5;border: 1px solid #3f51b5;"><i class="fa fa-print"></i></button>
                                </a>
                                </div>
                            </td>
                            <td class="nowrap-cell">
                                <a href="<?=url('admin/billing/billing-print-receipt/' . Helper::encoded($row->id))?>" target="_blank" title="Print Receipt">
                                    <button class="btn btn-custom btn-sm receipt-btn" style="background: #FFEB3B;border: 1px solid #FFEB3B;color: #000;">Receipt</button>
                                </a>
                            </td>
                        </tr>
                    <?php } } else {?>
                        <tr>
                            <td colspan="10" style="color: red !important; text-align: center;">No past orders found</td>
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
