<?php
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Helpers\Helper;
$generalSetting = GeneralSetting::find(1);
?>
<title><?=$generalSetting->site_name?>-Delivery-Order-<?=session('name')?></title>
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="<?=env('UPLOADS_URL')?><?=$generalSetting->site_favicon?>" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<div class="container-fluid invoice-container" style="margin: 15px auto;padding: 40px;max-width: 1250px;background-color: #fff;border: 1px solid #ccc;-moz-border-radius: 6px;-webkit-border-radius: 6px;-o-border-radius: 6px;border-radius: 6px;">
    <header class="text-center">
        <div class="btn-group btn-group-sm d-print-none"> <a href="javascript:window.print();" class="btn btn-light border text-black-50 shadow-none"><i class="fa fa-print"></i> PRINT</a> </div>
    </header>
    <table class="table" style="width: 100% !important; border-collapse: collapse;">
        <tbody>
            <tr>
                <td colspan="2" class="bg-light text-center" style="background-color: #f8f9fa; padding: 0; border-bottom: 1px solid rgb(108,117,125);">
                    <h3 class="invoice_heading"><strong><?=$generalSetting->site_name?><br/> Delivery Order : <?=session('name')?></strong></h3>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered border border-secondary mb-0" style="width: 100% !important; border-collapse: collapse;">
        <thead>
            <tr>
                <th>#</th>
                <th>Order No.</th>
                <th>Order Date/Time</th>
                <th>Delivery Mode</th>
                <th>Customer Info</th>
                <th>Delivery Info</th>
                <th>Net Amount</th>
                <th>Payment Status</th>
                <th>Payment Date/Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if($delivery_data){ $sl=1; foreach($delivery_data as $delivery_data_row){?>
                <tr>
                    <td><?=$sl++?></td>
                    <td><?=$delivery_data_row['order_no']?></td>
                    <td><?=$delivery_data_row['order_date']?><br><?=$delivery_data_row['order_time']?></td>
                    <td><?=$delivery_data_row['delivery_mode']?></td>
                    <td>
                        <?=$delivery_data_row['delivery_name']?><br>
                        <?=$delivery_data_row['delivery_phone']?><br>
                        <?=$delivery_data_row['delivery_email']?>
                    </td>
                    <td>
                        <?=$delivery_data_row['delivery_address']?><br>
                        <?=$delivery_data_row['delivery_suburb']?><br>
                        <?=$delivery_data_row['delivery_state']?><br>
                        <?=$delivery_data_row['delivery_postcode']?>
                    </td>
                    <td>$<?=number_format($delivery_data_row['net_amount'],2)?></td>
                    <td><?=(($delivery_data_row['payment_status'])?'PAID':'UNPAID')?></td>
                    <td><?=$delivery_data_row['payment_date_time']?></td>
                </tr>
            <?php } } else {?>
                <tr>
                    <td colspan="9" style="text-align: center;">No delivery orders found</td>
                </tr>
            <?php }?>
        </tbody>
    </table>
</div>
