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
          </h5>
          <div class="dt-responsive table-responsive">
            <table id="simpletable" class="table table-striped table-bordered nowrap">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Supplier Code</th>
                  <th scope="col">Company</th>
                  <th scope="col">Status</th>
                  <th scope="col">Address</th>
                  <th scope="col">Phone</th>
                  <th scope="col">Email</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                  <tr>
                    <td scope="row"><?=$sl++?></td>
                    <td><a href="<?=url('admin/' . $controllerRoute . '/edit/'.Helper::encoded($row->id))?>" class="text-primary" title="Edit <?=$module['title']?>"><?=$row->supplier_code?></a></td>
                    <td><?=$row->name?></td>
                    <td>
                      <?php if($row->status){?>
                        <span class="badge bg-success"><i class="fa fa-check"></i> ACTIVE</span>
                      <?php } else {?>
                        <span class="badge bg-danger"><i class="fa fa-block"></i> DEACTIVE</span>
                      <?php }?>
                    </td>
                    <td><?=wordwrap(($row->b_street_address1 . '' . $row->b_street_address2 . '' . $row->b_city . '' . $row->b_state . '' . $row->b_postcode . '' . $row->b_country),30,"<br>\n")?></td>
                    <td><?=$row->phone?></td>
                    <td><?=$row->email?></td>
                    <td>
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