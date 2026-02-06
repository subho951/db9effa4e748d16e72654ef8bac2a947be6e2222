<?php

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
// $poData                 = PurchaseOrderItem::where('id', '=', $id)->first();
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Purchase Order - <?= $poData->po_no ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* PAGE SETUP FOR PDF */
        @page {
            size: A4 portrait;
            margin: 15mm 12mm 15mm 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }

        .company-row {
            display: flex;
            justify-content: space-between;
            align-items: stretch;   /* force same height */
        }

        .company-left,
        .company-right {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;  /* align content to top */
        }

        .company-row h2 {
            margin: 0;
        }

        .company-right {
            text-align: right;
            font-size: 12px;
        }

        /* HEADER */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: top;
        }

        .header-right {
            text-align: right;
        }

        /* INFO SECTION */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-box {
            display: table-cell;
            width: 33.33%;
            vertical-align: top;
            padding-right: 10px;
            box-sizing: border-box;
            word-wrap: break-word;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
            /* repeat header in PDF */
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th {
            border-bottom: 2px solid #000;
            text-align: left;
            padding: 6px 4px;
            font-size: 12px;
        }

        td {
            border-bottom: 1px solid #ccc;
            padding: 6px 4px;
            vertical-align: top;
            font-size: 12px;
            word-break: break-word;
        }

        .text-right {
            text-align: right;
        }

        .totals-wrapper {
            width: 100%;
            margin-top: 15px;
            page-break-before: auto;
        }

        .notes-box,
        .totals {
            float: left;
            box-sizing: border-box;
        }

        .notes-box {
            width: 55%;
            padding-right: 10px;
        }

        .notes-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .notes-box td {
            border: 1px solid #0000005b;
            padding: 8px;
            min-height: 110px;
            /* 5 row height */
        }

        .totals {
            width: 45%;
        }

        .totals table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .clearfix {
            clear: both;
        }

        /* PRINT BUTTON */
        .print-btn {
            margin-bottom: 10px;
            text-align: right;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Print Button -->
        <!-- <div class="print-btn">
            <button onclick="window.print()">Print</button>
        </div> -->

        <!-- Header -->
        <div class="header">
            <div>
                <h2>Purchase Order <strong><?= $poData->po_no ?></strong></h2>
            </div>
            <div class="company">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <!-- Left: Title (+ optional logo) -->
                        <td style="width:50%; vertical-align:top;">
                            <!-- <h2 style="margin:0;">Barmania!</h2> -->

                            <!-- Optional Logo below title -->
                            
                            <img src="data:image/jpeg;base64,<?= base64_encode(file_get_contents(base_path('public/material/backend/assets/img/barmaniaSKU-logo.jpg'))) ?>"
                                alt="Barmania Logo"
                                style="max-width:120px; height:auto; margin-top:6px;">
                           
                        </td>

                        <!-- Right: Details -->
                        <td style="width:50%; text-align:right; vertical-align:top; font-size:12px;">
                            <div><strong>Wholesale Spirits & Liquours</strong></div>
                            <div>ABN: 39056682510</div>
                            <div>LIQW824009885</div>
                            <div>Date ordered: <?= date_format(date_create($poData->order_date), "d/m/Y") ?></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Info -->
        <div class="info-section">
            <div class="info-box">
                <strong>Supplier:</strong><br>
                <?= $poData->supplier_name ?><br>
                <?= $poData->supplier_address ?><br>
                <?= $poData->supplier_phone ?>
            </div>
            <div class="info-box">
                <strong>Bill to:</strong><br>
                Barmania<br>
                5-7 Bermill Street Warehouse 7<br>
                Rockdale 2216<br>
                02 8338 0229
            </div>
            <div class="info-box">
                <strong>Ship to:</strong><br>
                <?= $poData->delivery_name ?><br>
                <?= $poData->delivery_address ?><br>
                <?= $poData->delivery_phone ?>
            </div>
        </div>

        <!-- <div style="text-align:right; font-size:12px;">Prices are tax exclusive</div> -->

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Supplier SKU</th>
                    <th>Merchant SKU</th>
                    <th>Product name</th>
                    <th class="text-right">Order QTY</th>
                    <th class="text-right">Unit price</th>
                    <!-- <th class="text-right">Tax rate</th> -->
                    <th class="text-right">Line total<br>ex GST</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $po_items = PurchaseOrderItem::where('purchase_order_id', '=', $id)->get();
                if ($po_items) {
                    $sl = 101;
                    foreach ($po_items as $po_item) {
                ?>
                        <tr>
                            <td><?= $po_item->supplier_sku ?></td>
                            <td><?= $po_item->merchant_sku ?></td>
                            <td><?= strtoupper($po_item->item_name) ?></td>
                            <td class="text-right"><?= $po_item->qty ?></td>
                            <td class="text-right">$<?= number_format($po_item->cost_price, 2) ?></td>
                            <!-- <td class="text-right"><?= $po_item->tax_percent ?>%</td> -->
                            <td class="text-right">$<?= number_format($po_item->total_inc_tax, 2) ?></td>
                        </tr>
                <?php $sl++;
                    }
                } ?>
            </tbody>
        </table>

        <!-- Totals + Notes -->
        <div class="totals-wrapper">

            <!-- Notes -->
            <div class="notes-box">
                <strong>Notes:</strong>
                <table>
                    <tr>
                        <td>
                            <?= !empty($poData->note) ? nl2br($poData->note) : '' ?>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Totals -->
            <div class="totals">
                <table>
                    <tr>
                        <td>Total Lines</td>
                        <td><?= $poData->total_lines ?></td>
                    </tr>
                    <tr>
                        <td>Total Bottles</td>
                        <td><?= $poData->total_quantity ?></td>
                    </tr>
                    <tr>
                        <td>Ex GST</td>
                        <td>$<?= number_format($poData->subtotal, 2) ?></td>
                    </tr>
                    <tr>
                        <td>GST</td>
                        <td>$<?= number_format($poData->tax_total, 2) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total inc GST</strong></td>
                        <td><strong>$<?= number_format($poData->total_inc_tax, 2) ?></strong></td>
                    </tr>
                </table>
            </div>

            <div class="clearfix"></div>
        </div>

    </div>

</body>

</html>