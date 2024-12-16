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
            <form method="POST" action="" enctype="multipart/form-data">
              @csrf
              <div class="row">
                <div class="col-md-4">
                  <label for="title">Title</label>
                  <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label for="filename">File</label>
                  <input type="file" name="filename" id="filename" class="form-control" required>
                </div>
                <div class="col-md-2">
                  <button type="submit" class="btn btn-success btn-sm" style="margin-top: 23px;"><i class="fa fa-upload"></i> Upload</button>
                </div>
                <div class="col-md-2">
                  <a href="<?=env('UPLOADS_URL').'products.csv'?>" class="badge bg-primary" style="margin-top: 23px;" download>Sample File</a>
                </div>
              </div>
            </form>
          </h5>
          <div class="dt-responsive table-responsive">
            <table id="simpletable" class="table table-striped table-bordered nowrap">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Title</th>
                  <th scope="col">Filename</th>
                  <th scope="col">Product Count</th>
                  <th scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                  <tr>
                    <td scope="row"><?=$sl++?></td>
                    <td><?=$row->title?></td>
                    <td><a href="<?=env('UPLOADS_URL').'/product/'.$row->filename?>" class="badge bg-primary" download>File</a></td>
                    <td>
                      <?php
                      echo $productCount = Product::where('status', '!=', 3)->where('upload_id', '=', $row->id)->count();
                      ?> products
                    </td>
                    <td>
                      <a href="<?=url('admin/' . $controllerRoute . '/delete-upload-product/'.Helper::encoded($row->id))?>" class="btn btn-outline-danger btn-sm" title="Delete <?=$module['title']?>" onclick="return confirm('Do You Want To Delete This');"><i class="fa fa-trash"></i></a>
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