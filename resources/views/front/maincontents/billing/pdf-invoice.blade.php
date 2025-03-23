<?php
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Helpers\Helper;
$generalSetting = GeneralSetting::find(1);
?>
<title><?=$generalSetting->site_name?>-Invoice-<?=$getOrderDetail->order_no?></title>
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="<?=env('UPLOADS_URL')?><?=$generalSetting->site_favicon?>" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<div class="container-fluid invoice-container" style="margin: 15px auto;padding: 40px;max-width: 850px;background-color: #fff;border: 1px solid #ccc;-moz-border-radius: 6px;-webkit-border-radius: 6px;-o-border-radius: 6px;border-radius: 6px;">
   <table class="table table-bordered border border-secondary mb-0">
      <tbody>
         <tr>
            <td colspan="2" class="bg-light text-center">
               <h3 class="mb-0"><strong><?=$generalSetting->site_name?></strong></h3>
               <h3 class="mb-0"><strong>Invoice2</strong></h3>
            </td>
         </tr>
         <tr>
            <td class="col-7">
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
            <td class="col-5 bg-light">
               <div class="row gx-2 gy-1 fw-600">
                  <div class="col-5">Invoice No <span class="float-end">:</span></div>
                  <div class="col-7">#<?=$getOrderDetail->order_no?></div>
                  <div class="col-5">Date <span class="float-end">:</span></div>
                  <div class="col-7"><?=date_format(date_create($getOrderDetail->order_date), "M d, Y")?> <?=date_format(date_create($getOrderDetail->order_time), "h:i A")?></div>

                  <div class="col-5">Payment Status <span class="float-end">:</span></div>
                  <div class="col-7" style="font-size: 11px;"><?=(($getOrderDetail->payment_status)?'SUCCESS':'FAILED')?></div>
                  <div class="col-5">Payment Mode <span class="float-end">:</span></div>
                  <div class="col-7" style="font-size: 11px;"><?=$getOrderDetail->payment_mode?></div>
                  <div class="col-5">Date/Time <span class="float-end">:</span></div>
                  <div class="col-7" style="font-size: 11px;"><?=date_format(date_create($getOrderDetail->payment_date_time), "M d, Y h:i A")?></div>
               </div>
            </td>
         </tr>
         <tr>
            <td colspan="2" class="p-0">
               <table class="table table-sm mb-0">
                  <thead>
                     <tr class="bg-light">
                        <td class="col-1 text-center"><strong>#</strong></td>
                        <td class="col-6 "><strong>Item Name</strong></td>
                        <td class="col-1 text-center"><strong>Qty</strong></td>
                        <td class="col-2 text-end"><strong>Price</strong></td>
                        <td class="col-2 text-end"><strong>Amount</strong></td>
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
                           <td class="col-2 text-end">$<?=number_format($orderDetail->price,2)?></td>
                           <td class="col-2 text-end">$<?=number_format($orderDetail->subtotal,2)?></td>
                        </tr>
                     <?php } }?>
                  </tbody>
               </table>
            </td>
         </tr>
         <tr class="bg-light fw-600">
            <td class="col-7 py-1"></td>
            <td class="col-5 py-1 pe-1">Sub Total: <span class="float-end">$<?=number_format($subtotal,2)?></span></td>
         </tr>
         <tr class="bg-light fw-600">
            <td class="col-7 py-1"></td>
            <td class="col-5 py-1 pe-1">Discount: <span class="float-end">$<?=number_format($getOrderDetail->discount_amount,2)?></span></td>
         </tr>
         <tr class="bg-light fw-600">
            <td class="col-7 py-1"></td>
            <td class="col-5 py-1 pe-1">Shipping: <span class="float-end">$<?=number_format($getOrderDetail->delivery_amount,2)?></span></td>
         </tr>
         <tr class="bg-light fw-600">
            <td class="col-7 py-1"></td>
            <td class="col-5 py-1 pe-1">Net Total: <span class="float-end">$<?=number_format($getOrderDetail->net_amount,2)?></span></td>
         </tr>
         <tr class="bg-light fw-600">
            <td class="col-7 py-1"></td>
            <td class="col-5 py-1 pe-1">Amount in Words: <span class="float-start"><i><?=Helper::getIndianCurrency($getOrderDetail->net_amount)?></i></span></td>
         </tr>
         <tr>
            <td class="col-7 text-1">
               <?=$getOrderDetail->note?>
            </td>
            <td class="col-5 pe-1 text-end" style="padding: 0;">
               <div class="text-1 mt-5" style="border-top: 1px solid #6c757d;text-align: center;">Authorized Signature <br>For <?=$generalSetting->site_name?></div>
            </td>
         </tr>
         <tr>
            <td colspan="3" class="p-0">
               <table class="table table-sm mb-0 table-bordered">
                  <tr>
                     <td class="col-12 pe-1">
                        This is computer generated invoice does not require signature
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
   <!-- <footer class="text-center mt-4">
      <div class="btn-group btn-group-sm d-print-none"> <a href="javascript:window.print()" class="btn btn-light border text-black-50 shadow-none"><i class="fa fa-print"></i> Print & Download</a> </div>
   </footer> -->
</div>
