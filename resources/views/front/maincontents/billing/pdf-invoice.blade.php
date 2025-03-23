<?php
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Helpers\Helper;
$generalSetting = GeneralSetting::find(1);
?>
<title><?=$generalSetting->site_name?>-Invoice-<?=$getOrderDetail->order_no?></title>
<style>
   *{
      font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
      
   }
   .border {
      border: 1px solid rgb(108,117,125);
   }
   .border-secondary {
      --bs-border-opacity: 1;
      border-color: rgba(var(--bs-secondary-rgb), var(--bs-border-opacity)) !important;
   }
   .invoice_heading{
      text-align: center;
      font-size: 1.75rem;
      line-height: 1;
   }
    
   td{
      vertical-align: top;
      padding: 10px;
      border-bottom: 1px solid #000;
      font-size: 15px;
   }
</style>
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="<?=env('UPLOADS_URL')?><?=$generalSetting->site_favicon?>" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<div style="border: 1px solid rgb(108,117,125);">
   <table class="table" style="width: 100% !important; border-collapse: collapse;">
      <tbody>
         <tr>
            <td colspan="2" class="bg-light text-center" style="background-color: #f8f9fa; padding: 0; border-bottom: 1px solid rgb(108,117,125);">
               <h3 class="invoice_heading"><strong><?=$generalSetting->site_name?><br/> Invoice</strong></h3>
               <!-- <h3 class="mb-0"><strong>Invoice</strong></h3> -->
            </td>
         </tr>
         <tr>
            <td style="width:60%; border-bottom: 1px solid rgb(108,117,125);">
               <div class="row gx-2 gy-2">
                  <div><strong>Delivery Mode : </strong><?=$getOrderDetail->delivery_mode?></div>
                  <?php if($getOrderDetail->delivery_mode != 'Take'){?>
                     <div class="col">
                        <?php if($getOrderDetail->delivery_mode == 'Pickup'){?>
                           <strong><?=$getOrderDetail->delivery_mode?> Details</strong>
                           <address>
                              <strong><?=$getOrderDetail->pickup_name?></strong><br>
                              <?=$getOrderDetail->pickup_phone?><br>
                              <?=$getOrderDetail->pickup_email?>
                           </address>
                        <?php }?>
                        <?php if($getOrderDetail->delivery_mode == 'Deliver'){?>
                           <strong><?=$getOrderDetail->delivery_mode?> Details</strong>
                           <address>
                              <strong><?=$getOrderDetail->delivery_name?></strong><br>
                              <?=$getOrderDetail->delivery_address?>, <?=$getOrderDetail->delivery_suburb?><br>
                              <?=$getOrderDetail->delivery_state?> <?=$getOrderDetail->delivery_postcode?><br>
                              <?=$getOrderDetail->delivery_phone?><br>
                              <?=$getOrderDetail->delivery_email?>
                           </address>
                        <?php }?>
                     </div>
                  <?php }?>
               </div>
            </td>
            <td style="width:40%; border-bottom: 1px solid rgb(108,117,125); background-color: #f8f9fa; border-left: 1px solid rgb(108,117,125);">
               <div class="row gx-2 gy-1">
                  <div style="margin: 7px 0;"><span style="width: 110px; display: inline-block;">Invoice No</span>: #<?=$getOrderDetail->order_no?></div>
                  <div style="margin: 7px 0;"><span style="width: 110px; display: inline-block;">Date</span>: <code style="font-size: 13px; "><?=date_format(date_create($getOrderDetail->order_date), "M d, Y")?> <?=date_format(date_create($getOrderDetail->order_time), "h:i A")?></code></div>
                  <div style="margin: 7px 0;"><span style="width: 110px; display: inline-block;">Payment Status</span> <code style="font-size: 12px; margin-top: -5px; display: inline-block;">: <?=(($getOrderDetail->payment_status)?'SUCCESS':'FAILED')?></code></div>
                  <div style="margin: 7px 0;"><span style="width: 110px; display: inline-block;">Payment Mode</span> <code style="font-size: 12px; margin-top: -5px; display: inline-block;">: <?=$getOrderDetail->payment_mode?></code></div>
                  <div style="margin: 7px 0;"><span style="width: 110px; display: inline-block;">Date/Time</span> <code style="font-size: 12px; margin-top: -5px; display: inline-block;">: <?=date_format(date_create($getOrderDetail->payment_date_time), "M d, Y h:i A")?></code></div>
               </div>
            </td>
         </tr>
         <tr>
            <td colspan="2" style="padding: 0;">
               <table class="table" style="width: 100% !important; border-collapse: collapse;">
                  <thead>
                     <tr class="bg-light">
                        <td style="width: 10%;"><strong>#</strong></td>
                        <td style="width: 55%;"><strong>Item Name</strong></td>
                        <td style="width: 15%;"><strong>Qty</strong></td>
                        <td style="width: 15%; text-align: right;"><strong>Price</strong></td>
                        <td style="width: 15%; text-align: right;"><strong>Amount</strong></td>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                     $orderDetails = OrderDetail::where('order_id', '=', $getOrderDetail->id)->get();
                     $sl=1;
                     $subtotal=0;
                     if($orderDetails){ foreach($orderDetails as $orderDetail){
                        $getProduct    = Product::where('id', '=', $orderDetail->item_id)->first();
                        $subtotal      += $orderDetail->subtotal;
                     ?>
                        <tr>
                           <td class="col-1 text-center"><?=$sl++?></td>
                           <td class="col-6"><?=$getProduct->name?></td>
                           <td class="col-1 text-center"><?=$orderDetail->qty?></td>
                           <td style="text-align: right;">$<?=number_format($orderDetail->price,2)?></td>
                           <td style="text-align: right;">$<?=number_format($orderDetail->subtotal,2)?></td>
                        </tr>
                     <?php } }?>
                  </tbody>
               </table>
            </td>
         </tr>
         <tr>
            <td style="width: 60%"></td>
            <td style="width: 40%; border-left: 1px solid #000;">Sub Total: <span style="float: right">$<?=number_format($subtotal,2)?></span></td>
         </tr>
         <tr>
            <td style="width: 60%"></td>
            <td style="width: 40%; border-left: 1px solid #000;">Discount: <span style="float: right">$<?=number_format($getOrderDetail->discount_amount,2)?></span></td>
         </tr>
         <tr>
            <td style="width: 60%"></td>
            <td style="width: 40%; border-left: 1px solid #000;">Shipping: <span style="float: right">$<?=number_format($getOrderDetail->delivery_amount,2)?></span></td>
         </tr>
         <tr>
            <td style="width: 60%"></td>
            <td style="width: 40%; border-left: 1px solid #000;">Net Total: <span style="float: right">$<?=number_format($getOrderDetail->net_amount,2)?></span></td>
         </tr>
         <tr>
            <td style="width: 60%"></td>
            <td style="width: 40%; border-left: 1px solid #000;">Amount in Words: <span class="float-start"><br><i><?=Helper::getIndianCurrency($getOrderDetail->net_amount)?></i></span></td>
         </tr>
         <tr>
            <td rowspan="2" colspan="1" style="border: 1px solid #000; border-left: 0;">
               <?=$getOrderDetail->note?>
            </td>
            <td colspan="1" style="height: 30px;"></td>
         </tr>
         <tr>
            <td style="text-align: center;">Authorized Signature <br>For <?=$generalSetting->site_name?></td>
         </tr>
         <tr>
            <td colspan="2">
               This is computer generated invoice does not require signature
            </td>
         </tr>
      </tbody>
   </table>
</div>
   <!-- <footer class="text-center mt-4">
      <div class="btn-group btn-group-sm d-print-none"> <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none"><i class="fa fa-print"></i> Print & Download</a> </div>
   </footer> -->
