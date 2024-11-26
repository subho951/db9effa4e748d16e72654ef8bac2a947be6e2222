<?php
use App\Helpers\Helper;
$controllerRoute                = $module['controller_route'];
$current_url                    = url()->current();
?>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light"><a href="<?=url('admin/dashboard')?>">Dashboard</a> /</span>
    <span class="text-muted fw-light"><a href="<?=url('admin/' . $controllerRoute . '/list/')?>"><?=$module['title']?> List</a> /</span>
    <?=$page_header?>
  </h4>
  <div class="row">
    <?php
    if($row){
      $sku                              = $row->sku;
      $name                             = $row->name;
      $receipt_short_name               = $row->receipt_short_name;
      $shelf_tag_short_name             = $row->shelf_tag_short_name;
      $barcode                          = $row->barcode;
      $brand_id                         = $row->brand_id;
      $supplier_id                      = $row->supplier_id;
      $cost_price_ex_tax                = $row->cost_price_ex_tax;
      $cost_price_inc_tax               = $row->cost_price_inc_tax;
      $retail_price_inc_tax             = $row->retail_price_inc_tax;
      $cover_image                      = $row->cover_image;
      $stock                            = $row->stock;
      $status                           = $row->status;
      $uId                              = $row->id;
    } else {
      $sku                              = '';
      $name                             = '';
      $receipt_short_name               = '';
      $shelf_tag_short_name             = '';
      $barcode                          = '';
      $brand_id                         = '';
      $supplier_id                      = '';
      $cost_price_ex_tax                = '';
      $cost_price_inc_tax               = '';
      $retail_price_inc_tax             = '';
      $cover_image                      = '';
      $stock                            = '';
      $status                           = '';
      $uId                              = '';
    }
    ?>
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <small class="text-danger">Star (*) marked fields are mandatory</small>
          <form method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="mb-3 col-md-6">
                 <label for="name" class="form-label">Name <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="name" name="name" value="<?=$name?>" required />
              </div>
              <div class="mb-3 col-md-6">
                 <label for="sku" class="form-label">SKU ID <small class="text-danger">*</small></label>
                 <input class="form-control no-space" type="text" id="sku" name="sku" value="<?=$sku?>" required />
              </div>

              <div class="mb-3 col-md-4">
                 <label for="receipt_short_name" class="form-label">Receipt Short Name <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="receipt_short_name" name="receipt_short_name" value="<?=$receipt_short_name?>" required />
              </div>
              <div class="mb-3 col-md-4">
                 <label for="shelf_tag_short_name" class="form-label">Shelf Tag Short Name <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="shelf_tag_short_name" name="shelf_tag_short_name" value="<?=$shelf_tag_short_name?>" required />
              </div>
              <div class="mb-3 col-md-4">
                 <label for="barcode" class="form-label">Barcode <small class="text-danger">*</small></label>
                 <input class="form-control no-space" type="text" id="barcode" name="barcode" value="<?=$barcode?>" required />
              </div>

              <div class="mb-3 col-md-6">
                 <label for="brand_id" class="form-label">Brand <small class="text-danger">*</small></label>
                 <select name="brand_id" class="form-control" id="brand_id" required>
                  <option value="" selected>Select Brand</option>
                  <?php if($brands){ foreach($brands as $brand){?>
                  <option value="<?=$brand->id?>" <?=(($brand->id == $brand_id)?'selected':'')?>><?=$brand->name?></option>
                  <?php } }?>
                </select>
              </div>
              <div class="mb-3 col-md-6">
                 <label for="supplier_id" class="form-label">Supplier <small class="text-danger">*</small></label>
                 <select name="supplier_id" class="form-control" id="supplier_id" required>
                  <option value="" selected>Select Supplier</option>
                  <?php if($suppliers){ foreach($suppliers as $supplier){?>
                  <option value="<?=$supplier->id?>" <?=(($supplier->id == $supplier_id)?'selected':'')?>><?=$supplier->name?></option>
                  <?php } }?>
                </select>
              </div>
              
              <div class="mb-3 col-md-4">
                 <label for="cost_price_ex_tax" class="form-label">Cost Price Ex. Tax <small class="text-danger">*</small></label>
                 <input class="form-control no-space" type="text" id="cost_price_ex_tax" name="cost_price_ex_tax" value="<?=$cost_price_ex_tax?>" required />
              </div>
              <div class="mb-3 col-md-4">
                 <label for="cost_price_inc_tax" class="form-label">Cost Price Inc. Tax <small class="text-danger">*</small></label>
                 <input class="form-control no-space" type="text" id="cost_price_inc_tax" name="cost_price_inc_tax" value="<?=$cost_price_inc_tax?>" required />
              </div>
              <div class="mb-3 col-md-4">
                 <label for="retail_price_inc_tax" class="form-label">Retail Price Inc. Tax <small class="text-danger">*</small></label>
                 <input class="form-control no-space" type="text" id="retail_price_inc_tax" name="retail_price_inc_tax" value="<?=$retail_price_inc_tax?>" required />
              </div>

              <div class="mb-3 col-md-6">
                <label for="username" class="form-label d-block">Stock <small class="text-danger">*</small></label>
                <div class="form-check form-check-inline mt-3">
                  <input name="stock" class="form-check-input" type="radio" value="1" id="stock1" <?=(($stock == 1)?'checked':'')?> required />
                  <label class="form-check-label" for="stock1">
                    Yes
                  </label>
                </div>
                <div class="form-check form-check-inline mt-3">
                  <input name="stock" class="form-check-input" type="radio" value="0" id="stock2" <?=(($stock == 0)?'checked':'')?> required />
                  <label class="form-check-label" for="stock2">
                    No
                  </label>
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="username" class="form-label d-block">Status <small class="text-danger">*</small></label>
                <div class="form-check form-check-inline mt-3">
                  <input name="status" class="form-check-input" type="radio" value="1" id="status1" <?=(($status == 1)?'checked':'')?> required />
                  <label class="form-check-label" for="status1">
                    Active
                  </label>
                </div>
                <div class="form-check form-check-inline mt-3">
                  <input name="status" class="form-check-input" type="radio" value="0" id="status2" <?=(($status == 0)?'checked':'')?> required />
                  <label class="form-check-label" for="status2">
                    Deactive
                  </label>
                </div>
              </div>

              <div class="mb-3 col-md-12">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                  <?php if($cover_image != ''){?>
                    <img src="<?=env('UPLOADS_URL').'/product/'.$cover_image?>" alt="<?=$name?>" class="d-block rounded" height="100" width="100" style="border-radius: 50%;" id="uploadedAvatar" />
                  <?php } else {?>
                    <img src="<?=env('NO_USER_IMAGE')?>" alt="<?=$name?>" class="d-block rounded" height="100" width="100" style="border-radius: 50%;" id="uploadedAvatar" />
                  <?php } ?>
                  <div class="button-wrapper">
                     <label for="cover_image" class="btn btn-primary me-2 mb-4" tabindex="0">
                       <span class="d-none d-sm-block">Upload Cover Image</span>
                       <i class="bx bx-upload d-block d-sm-none"></i>
                       <input type="file" id="cover_image" name="cover_image" class="account-file-input" hidden accept="image/png, image/jpeg" />
                     </label>
                     <?php if($cover_image != ''){?>
                       <a href="<?=url('admin/common-delete-image/'.Helper::encoded($current_url).'/products/cover_image/id/'.$uId)?>" title="Remove image" onclick="return confirm('Do You Want To Delete This Image ?');">
                         <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                           <i class="bx bx-reset d-block d-sm-none"></i>
                           <span class="d-none d-sm-block">Reset</span>
                         </button>
                       </a>
                     <?php } ?>
                     <p class="text-muted mb-0">Allowed JPG, JPEG, ICO, PNG, GIF, SVG, AVIF</p>
                  </div>
                </div>
              </div>
           </div>
           <div class="mt-2">
              <button type="submit" class="btn btn-primary me-2"><?=(($row)?'Save':'Add')?></button>
           </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  // Common function to prevent spaces
  function disallowSpace(event) {
      if (event.key === ' ') {
          event.preventDefault(); // Prevent space key
      }
  }

  function removeSpacesOnInput(event) {
      event.target.value = event.target.value.replace(/\s/g, ''); // Remove spaces
  }

  // Select all inputs with the class 'no-space'
  const textboxes = document.querySelectorAll('.no-space');

  // Attach event listeners to each textbox
  textboxes.forEach((textbox) => {
      textbox.addEventListener('keydown', disallowSpace);
      textbox.addEventListener('input', removeSpacesOnInput);
  });
</script>