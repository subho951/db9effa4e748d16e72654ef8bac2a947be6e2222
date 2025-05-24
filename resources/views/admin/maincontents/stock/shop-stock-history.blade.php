<?php
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
?>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light"><a href="<?=url('admin/dashboard')?>">Dashboard</a> /</span> <?=$page_header?>
  </h4>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">
            Current Stock : <span><?=(($product)?$product->warehouse_stock:'')?></span>
          </h5>
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <div class="dt-responsive table-responsive">
                <table class="table table-bordered nowrap">
                  <thead>
                    <tr>
                      <th scope="col">Type</th>
                      <th scope="col">Opening</th>
                      <th scope="col">Txn</th>
                      <th scope="col">Closing</th>
                      <th scope="col">Note</th>
                      <th scope="col">Stock Date</th>
                      <th scope="col">Timestamp</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if(count($stocks)>0){ $sl=1; foreach($stocks as $stock){?>
                      <tr style="background-color: <?=(($stock->txn_type == 'IN')?'#9ce53717':'#ff000014')?>;">
                        <td><?=$stock->txn_type?></td>
                        <td><?=$stock->opening_qty?></td>
                        <td><?=$stock->txn_qty?></td>
                        <td><?=$stock->closing_qty?></td>
                        <td><span style="font-size: 10px;"><?=$stock->note?></span></td>
                        <td><span style="font-size: 10px;"><?=date_format(date_create($stock->stock_date), "M d, Y")?></span></td>
                        <td><span style="font-size: 10px;"><?=date_format(date_create($stock->created_at), "M d, Y h:i A")?></span></td>
                      </tr>
                    <?php } } else {?>
                      <tr>
                        <td colspan="6" style="text-align: center; color: red; font-size: 12px;">No transactions available</td>
                      </tr>
                    <?php }?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>