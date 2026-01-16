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
                </tr>
              </thead>
              <tbody>
                <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                  <tr>
                    <th scope="row"><?=$sl++?></th>
                    <td>
                      <a href="<?= url('admin/purchase-orders/edit/' . Helper::encoded($row->id)) ?>"><?=$row->po_no?></a>
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
                    <td>A$<?=$row->subtotal?></td>
                    <td>A$<?=$row->tax_total?></td>
                    <td>A$<?=$row->total_inc_tax?></td>
                    <td>
                      <a target="_blank" href="<?=env('UPLOADS_URL').'/purchase-order/'.$row->invoice_file?>" class="btn btn-outline-primary btn-sm" title="<?=$row->po_no?>">PO File</a>
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