<?php

use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Admin;
use App\Helpers\Helper;

$generalSetting = GeneralSetting::find(1);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Receipt-<?= $getOrder->order_no ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?=env('UPLOADS_URL')?><?=$generalSetting->site_favicon?>" />
    <style>
        @page {
            size: 80mm auto;
            margin: 3mm;
        }

        body {
            width: 80mm;
            margin: 0;
            padding: 0;
            font-family: Arial, monospace;
            font-size: 11px;
            line-height: 1.3;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .big {
            font-size: 22px;
            letter-spacing: 1px;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .item {
            margin-bottom: 5px;
        }

        .item-name {
            font-weight: bold;
        }

        .discount {
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        .footer {
            font-size: 10px;
            text-align: center;
            margin-top: 5px;
        }

        .barcode {
            text-align: center;
            margin: 10px 0;
        }

        .barcode img {
            max-width: 100%;
            height: 40px;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <!-- LOGO / BRAND -->
    <div class="center">
        <div class="big bold"><?=$generalSetting->site_name?></div>
        <div class="bold">AUSTRALIA</div>
    </div>

    <div class="line"></div>

    <!-- INVOICE HEADER -->
    <div class="row">
        <div class="bold">TAX INVOICE</div>
        <div><?= $getOrder->order_no ?></div>
    </div>

    <div class="line"></div>

    <!-- COLUMN HEADERS -->
    <div class="row bold">
        <div>Qty</div>
        <div>Ea</div>
        <div>Price</div>
    </div>

    <div class="line"></div>
    <?php
    $orderDetails = OrderDetail::where('order_id', '=', $getOrder->id)->get();
    $sl=1;
    // $net_amount=0;
    if($orderDetails){ foreach($orderDetails as $orderDetail){
    $getProduct    = Product::where('id', '=', $orderDetail->item_id)->first();
    // $net_amount      += $orderDetail->net_amount;
    ?>
        <!-- ITEMS -->
        <div class="item">
            <div class="row">
                <div><?=$orderDetail->qty?> x</div>
                <div>$<?=number_format($orderDetail->price,2)?></div>
                <div class="right">$<?=number_format(($orderDetail->price * $orderDetail->qty),2)?></div>
            </div>
            <div class="item-name"><?=$getProduct->receipt_short_name?></div>
            <!-- <div>paris and swan deal</div> -->
        </div>
    <?php } }?>

    <!-- DISCOUNT -->
    <!-- <div class="item discount">
        <div class="row">
            <div>1 x</div>
            <div>-$10.00</div>
            <div class="right">-$10.00</div>
        </div>
        <div>Discount / paris and swan deal</div>
    </div> -->

    <div class="line"></div>

    <!-- TOTALS -->
    <table>
        <tr>
            <td>Total</td>
            <td class="right bold">$<?=number_format($getOrder->net_amount,2)?></td>
        </tr>
        <!-- <tr>
            <td>GST</td>
            <td class="right">$<?=number_format($getOrder->net_amount,2)?></td>
        </tr> -->
        <tr>
            <td>Paid by <?=$getOrder->payment_mode?></td>
            <td class="right bold">$<?=number_format($getOrder->payment_amount,2)?></td>
        </tr>
        <tr>
            <td>Owing</td>
            <td class="right">$0.00</td>
        </tr>
    </table>

    <div class="line"></div>

    <!-- BARCODE -->
    <div class="barcode">
        <!-- <img src="https://barcode.tec-it.com/barcode.ashx?data=IA2707&code=Code128&translate-esc=on" alt="barcode"> -->
        <img 
        src="https://barcode.tec-it.com/barcode.ashx?data=<?= $getOrder->order_no ?>&code=Code128&translate-esc=on"
        alt="barcode"
        style="max-width:100%; height:40px;">
        <!-- <div><?= $getOrder->order_no ?></div> -->
    </div>

    <?php
    $getOperator = Admin::select('name')->where('id', '=', $getOrder->operator_id)->first();
    ?>

    <!-- FOOTER -->
    <div class="footer">
        Served by <b><?= (($getOperator)?$getOperator->name:'') ?></b><br>
        <?=date_format(date_create($getOrder->order_time), "h:i a")?>, <?=date_format(date_create($getOrder->order_date), "d M Y")?><br><br>
        <?=$generalSetting->site_phone?> &nbsp; <?=$generalSetting->site_mail?><br>
        BKD Pty Limited trading as <?=$generalSetting->site_name?><br>
        ABN 39056682510<br><br>
        For online orders go to<br>
        www.diamondliquor.com.au
    </div>

</body>

</html>