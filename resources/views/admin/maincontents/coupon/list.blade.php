<?php
use App\Models\Product;
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
          </h5>
          <div class="dt-responsive table-responsive">
            <table id="simpletable" class="table table-striped table-bordered nowrap">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Name</th>
                  <th scope="col">Main Product</th>
                  <th scope="col">Discount Nature</th>
                  <th scope="col">Bundled Products</th>
                  <th scope="col">Discount Type<br>Discount Amount</th>
                  <th scope="col">Voucher Code</th>
                  <th scope="col">From Date<br>To Date</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                  <tr>
                    <td scope="row"><?=$sl++?></td>
                    <td><?=$row->name?></td>
                    <td><?=$row->main_product_name?></td>
                    <td><?=$row->discount_nature?></td>
                    <td>
                      <ul>
                        <?php
                        $bundle_products = json_decode($row->bundle_products);
                        if(count($bundle_products)>0){ for($k=0;$k<count($bundle_products);$k++){
                          $getProduct = Product::select('name', 'sku')->where('id', '=', $bundle_products[$k])->first();
                        ?>
                          <li><?=(($getProduct)?$getProduct->name.' ('.$getProduct->sku.')':'')?></li>
                        <?php } }?>
                      </ul>
                    </td>
                    <td>
                      <?=$row->discount_type?><br>
                      <?=(($row->discount_type == 'Flat')?'$'.number_format($row->discount_amount,2):number_format($row->discount_amount,2).'%')?>
                    </td>
                    <td><?=$row->voucher_code?></td>
                    <td>
                      <?=date_format(date_create($row->from_date), "M d, Y")?><br>
                      <?=date_format(date_create($row->to_date), "M d, Y")?>
                    </td>
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