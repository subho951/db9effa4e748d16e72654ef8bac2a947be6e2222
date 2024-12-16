<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
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
            <a href="<?=url('admin/' . $controllerRoute . '/upload-product/')?>" class="btn btn-outline-success btn-sm float-right">Upload <?=$module['title']?></a>
          </h5>
          <div class="dt-responsive table-responsive">
            <table id="simpletable" class="table table-striped table-bordered nowrap">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Name</th>
                  <th scope="col">SKU ID</th>
                  <!-- <th scope="col">Receipt Short Name</th>
                  <th scope="col">Shelf Tag Short Name</th> -->
                  <th scope="col">Barcode</th>
                  <th scope="col">Brand</th>
                  <th scope="col">Supplier</th>
                  <th scope="col">Cost Price Ex. Tax</th>
                  <th scope="col">Cost Price Inc. Tax</th>
                  <th scope="col">Retail Price Inc. Tax</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                  <tr>
                    <td scope="row"><?=$sl++?></td>
                    <td><?=$row->name?></td>
                    <td><?=$row->sku?></td>
                    <!-- <td><?=$row->receipt_short_name?></td>
                    <td><?=$row->shelf_tag_short_name?></td> -->
                    <td><?=$row->barcode?></td>
                    <td><?=$row->brand_name?></td>
                    <td><?=$row->supplier_name?></td>
                    <td>$<?=number_format($row->cost_price_ex_tax,2)?></td>
                    <td>$<?=number_format($row->cost_price_inc_tax,2)?></td>
                    <td>$<?=number_format($row->retail_price_inc_tax,2)?></td>
                    <td>
                      <a href="<?=url('admin/' . $controllerRoute . '/edit/'.Helper::encoded($row->id))?>" class="btn btn-outline-primary btn-sm" title="Edit <?=$module['title']?>"><i class="fa fa-edit"></i></a>
                      <a href="<?=url('admin/' . $controllerRoute . '/delete/'.Helper::encoded($row->id))?>" class="btn btn-outline-danger btn-sm" title="Delete <?=$module['title']?>" onclick="return confirm('Do You Want To Delete This <?=$module['title']?>');"><i class="fa fa-trash"></i></a>
                      <?php if($row->status){?>
                        <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-success btn-sm" title="Activate <?=$module['title']?>"><i class="fa fa-check"></i></a>
                      <?php } else {?>
                        <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-warning btn-sm" title="Deactivate <?=$module['title']?>"><i class="fa fa-times"></i></a>
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