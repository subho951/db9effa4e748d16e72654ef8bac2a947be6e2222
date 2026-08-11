<?php
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
$escape               = function($value){
  return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
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
            <a href="<?=url('admin/' . $controllerRoute . '/add/')?>" class="btn btn-outline-success btn-sm float-right">Add <?=$module['title']?></a>
          </h5>
          <div class="dt-responsive table-responsive">
            <table id="simpletable" class="table table-striped table-bordered nowrap">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">PO no.</th>
                  <th scope="col">Supplier</th>
                  <th scope="col">Delivery location</th>
                  <th scope="col">Order date</th>
                  <th scope="col">Total lines</th>
                  <th scope="col">Total quantity</th>
                  <th scope="col">Subtotal</th>
                  <th scope="col">Tax total</th>
                  <th scope="col">Total (inc. tax)</th>
                  <th scope="col">File</th>
                  <th scope="col">Receive</th>
                </tr>
              </thead>
              <tbody>
                <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                  <?php
                    $isClosed = ((int)$row->status === 2);
                    $currencySymbol = (($row->currency_symbol ?? '') != ''?$row->currency_symbol:'$');
                  ?>
                  <tr>
                    <th scope="row"><?=$sl++?></th>
                    <td>
                      <?php if($isClosed){?>
                        <?=$row->po_no?> <span class="badge bg-label-secondary">Closed</span>
                      <?php } else {?>
                        <a href="<?= url('admin/purchase-orders/edit/' . Helper::encoded($row->id)) ?>"><?=$row->po_no?></a>
                      <?php }?>
                    </td>
                    <td>
                      <?=$row->supplier_name?><br>
                      <?=wordwrap($row->supplier_address,20,"<br>\n")?><br>
                      <?=$row->supplier_phone?>
                    </td>
                    <td>
                      <?=$row->delivery_name?><br>
                      <?=wordwrap($row->delivery_address,20,"<br>\n")?><br>
                      <?=$row->delivery_phone?>
                    </td>
                    <td><?=$row->order_date?></td>
                    <td><?=$row->total_lines?></td>
                    <td><?=$row->total_quantity?></td>
                    <td><?=$escape($currencySymbol)?><?=number_format((float)$row->subtotal, 2)?></td>
                    <td><?=$escape($currencySymbol)?><?=number_format((float)$row->tax_total, 2)?></td>
                    <td><?=$escape($currencySymbol)?><?=number_format((float)$row->total_inc_tax, 2)?></td>
                    <td>
                      <?php if($row->invoice_file){?>
                        <a target="_blank" href="<?=env('UPLOADS_URL').'/purchase-order/'.$row->invoice_file?>" class="btn btn-outline-primary btn-sm" title="<?=$row->po_no?>">PO File</a>
                      <?php }?>
                    </td>
                    <td>
                      <?php if($isClosed){?>
                        <span class="badge bg-success">Received</span>
                      <?php } elseif((int)$row->total_lines > 0){?>
                        <a href="<?=url('admin/purchase-orders/receive/' . Helper::encoded($row->id))?>" class="btn btn-outline-success btn-sm" title="Receive <?=$row->po_no?>"><i class="fa fa-truck-loading"></i>&nbsp;Receive</a>
                      <?php }?>
                    </td>
                  </tr>
                <?php } }?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
