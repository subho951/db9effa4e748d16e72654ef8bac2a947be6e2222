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
         $size_id                          = $row->size_id;
         $cost_price_ex_tax                = $row->cost_price_ex_tax;
         $cost_price_tax                   = $row->cost_price_tax;
         $cost_price_inc_tax               = $row->cost_price_inc_tax;
         $markup_amount                    = $row->markup_amount;
         $markup_type                      = $row->markup_type;
         $added_amount                     = $row->added_amount;
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
         $size_id                          = '';
         $cost_price_ex_tax                = '';
         $cost_price_tax                   = '';
         $cost_price_inc_tax               = '';
         $markup_amount                    = 0.00;
         $markup_type                      = 'FLAT';
         $added_amount                     = '';
         $retail_price_inc_tax             = '';
         $cover_image                      = '';
         $stock                            = '';
         $status                           = '';
         $uId                              = '';
      }
      ?>
   <section class="info-body">
      <div class="container-fluid">
         <div class="row">
            <div class="col-12">
               <small class="text-danger">Star (*) marked fields are mandatory</small><br>
               <small class="text-dark">* ch=character size/limit of the input field.</small>
               <form method="POST" action="" enctype="multipart/form-data">
                  @csrf
                  <div class="container">
                     <div class="form-container">
                        <!-- <div class="form-header mb-4">
                           <h4>Product Input Fields</h4>
                           <button type="submit" class="my-btn btn-sky">Save</button>
                        </div> -->
                        <div class="row g-3">
                           <h5 class="mb-3">Basic Info</h5>
                           <div class="col-md-3">
                              <label class="form-label" for="sku">SKU&nbsp;(10ch) <small class="text-danger">*</small></label>
                              <input type="text" class="form-control" placeholder="Enter SKU" id="sku" name="sku" value="<?=$sku?>" required>
                           </div>
                           <div class="col-md-3">
                              <label class="form-label" for="barcode">Barcode&nbsp;(25ch) <small class="text-danger">*</small></label>
                              <input type="text" class="form-control" placeholder="Enter Barcode" id="barcode" name="barcode" value="<?=$barcode?>" required>
                           </div>
                           <div class="col-md-2">
                              <label class="form-label" for="shop_stock">Shop Stock&nbsp;(5ch) <small class="text-danger">*</small></label>
                              <input type="number" class="form-control" value="888">
                           </div>
                           <div class="col-md-2">
                              <label class="form-label" for="warehouse_stock">Warehouse Stock&nbsp;(5ch) <small class="text-danger">*</small></label>
                              <input type="number" class="form-control" value="888">
                           </div>
                           <div class="col-md-2 align-items-center">
                              <label class="form-label" for="warehouse_stock">Status</label>
                              <div class="form-check form-switch mt-0">
                                 <input class="form-check-input" type="checkbox" name="status" role="switch" id="status" <?=(($status == 1)?'checked':'')?>>
                                 <label class="form-check-label" for="status">Active</label>
                              </div>
                           </div>

                           <div class="col-md-4">
                              <label class="form-label" for="name">Product Name <small class="text-danger">*</small></label>
                              <input type="text" class="form-control" placeholder="Enter Product Name" id="name" name="name" value="<?=$name?>" required>
                           </div>
                           <div class="col-md-4">
                              <label class="form-label" for="receipt_short_name">Receipt Short Name <small class="text-danger">*</small></label>
                              <input type="text" class="form-control" placeholder="Enter Receipt Short Name" id="receipt_short_name" name="receipt_short_name" value="<?=$receipt_short_name?>" required>
                           </div>
                           <div class="col-md-4">
                              <label class="form-label" for="shelf_tag_short_name">Shelf Tag Short Name <small class="text-danger">*</small></label>
                              <input type="text" class="form-control" placeholder="Enter Shelf Tag Short Name" id="shelf_tag_short_name" name="shelf_tag_short_name" value="<?=$shelf_tag_short_name?>" required>
                           </div>

                           <div class="col-md-3">
                              <label class="form-label" for="supplier_id">Supplier Name <small class="text-danger">*</small></label>
                              <select name="supplier_id" class="form-control" id="supplier_id" required>
                                 <option value="" selected>Select Supplier</option>
                                 <?php if($suppliers){ foreach($suppliers as $supplier){?>
                                 <option value="<?=$supplier->id?>" <?=(($supplier->id == $supplier_id)?'selected':'')?>><?=$supplier->name?></option>
                                 <?php } }?>
                               </select>
                           </div>
                           <div class="col-md-3">
                              <label class="form-label" for="brand_id">Brand <small class="text-danger">*</small></label>
                              <select name="brand_id" class="form-control" id="brand_id" required>
                                 <option value="" selected>Select Brand</option>
                                 <?php if($brands){ foreach($brands as $brand){?>
                                 <option value="<?=$brand->id?>" <?=(($brand->id == $brand_id)?'selected':'')?>><?=$brand->name?></option>
                                 <?php } }?>
                               </select>
                           </div>
                           <div class="col-md-3">
                              <label class="form-label" for="size_id">Size <small class="text-danger">*</small></label>
                              <select name="size_id" class="form-control" id="size_id" required>
                                 <option value="" selected>Select Size</option>
                                 <?php if($sizes){ foreach($sizes as $size){?>
                                 <option value="<?=$size->id?>" <?=(($size->id == $size_id)?'selected':'')?>><?=$size->name?> <?=$size->unit_name?></option>
                                 <?php } }?>
                               </select>
                           </div>
                           <div class="col-md-3">
                              <label class="form-label" for="style">Style <small class="text-danger">*</small></label>
                              <input type="text" class="form-control" placeholder="Enter Style" name="style" id="style" required>
                           </div>

                           <h5 class="mb-3">Pricing</h5>
                           <div class="col-md-2">
                              <label class="form-label" for="cost_price_ex_tax">Cost (Excl. Tax) ($)</label>
                              <input type="text" class="form-control" placeholder="Enter Cost Ex. Tax" name="cost_price_ex_tax" id="cost_price_ex_tax" required>
                           </div>
                           <div class="col-md-2">
                              <label class="form-label" for="cost_price_inc_tax">Cost (Incl. Tax) ($)</label>
                              <input type="hidden" name="cost_price_tax" id="cost_price_tax" value="<?=$cost_price_tax?>">
                              <input type="text" class="form-control" placeholder="Enter Cost Inc. Tax" name="cost_price_inc_tax" id="cost_price_inc_tax" required readonly>
                           </div>
                           <div class="col-md-2">
                              <label class="form-label" for="markup_type">Markup (%)</label>
                              <div class="form-check form-switch mt-0">
                                 <input class="form-check-input" type="checkbox" name="markup_type" role="switch" id="markup_type" <?=(($markup_type == 'PERCENTAGE')?'checked':'')?>>
                                 <label class="form-check-label" for="markup_type" id="markup_type_text">Flat</label>
                              </div>
                           </div>
                           <div class="col-md-2">
                              <label class="form-label" for="markup_amount">Markup ($)</label>
                              <input type="text" class="form-control" placeholder="Enter Markup in $" name="markup_amount" id="markup_amount" value="<?=$markup_amount?>" required>
                           </div>
                           <div class="col-md-2">
                              <label class="form-label" for="added_amount">Added ($)</label>
                              <input type="text" class="form-control" placeholder="Enter Markup in %" name="added_amount" id="added_amount" value="<?=$added_amount?>" required readonly>
                           </div>
                           <div class="col-md-2">
                              <label class="form-label" for="retail_price_inc_tax">Retail Price (Incl. Tax) ($)</label>
                              <input type="text" class="form-control" placeholder="Enter Retail Price" name="retail_price_inc_tax" id="retail_price_inc_tax" value="<?=$retail_price_inc_tax?>" required readonly>
                           </div>

                           <div class="discounts-section">
                              <h5 class="mb-3">Discounts Voucher</h5>
                              <div class="row align-items-center gap-2 gap-lg-0">
                                 <div class="col-auto">
                                    <div class="form-check form-switch mt-0 pt-0 pb-0">
                                       <input class="form-check-input mt-0" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked="">
                                    </div>
                                 </div>
                                 <div class="col-lg-2">
                                    <input type="text" class="form-control" placeholder="Voucher Code">
                                 </div>
                                 <div class="col-auto">
                                    <span>then</span>
                                 </div>
                                 <div class="col-lg-3">
                                    <input type="text" class="form-control" placeholder="Discount Value">
                                 </div>
                                 <div class="col-lg-2">
                                    <span>= retail less discount</span>
                                 </div>
                                 <div class="col-lg-3">
                                    <input type="text" class="form-control" placeholder="retail less discount">
                                 </div>
                              </div>
                              <div class="row align-items-center gap-2 gap-lg-0">
                                 <div class="col-12 mt-3">
                                    <button class="my-btn btn-sky">Add<i class='bx bx-plus'></i></button>
                                 </div>
                              </div>
                           </div>
                           <div class="multiple-buys-section">
                              <h5 class="mb-3">Multiple Buys</h5>
                              <div class="row align-items-center gap-2 gap-lg-0">
                                 <div class="col-auto">
                                    <div class="form-check form-switch mt-0 pt-0 pb-0">
                                       <input class="form-check-input mt-0" type="checkbox" role="switch" id="flexSwitchCheckChecked" >
                                    </div>
                                 </div>
                                 <div class="col-lg-2">
                                    <input type="text" class="form-control" placeholder="Barcode 1 / SKU">
                                 </div>
                                 <div class="col-auto">
                                    <span>and</span>
                                 </div>
                                 <div class="col-lg-3">
                                    <input type="text" class="form-control" placeholder="Barcode 2 / SKU">
                                 </div>
                                 <div class="col-lg-2">
                                    <span>= true, then</span>
                                 </div>
                                 <div class="col-lg-3">
                                    <input type="text" class="form-control" placeholder="Discount $">
                                 </div>
                              </div>
                              <div class="row align-items-center gap-2 gap-lg-0">
                                 <div class="col-12 mt-3">
                                    <button class="my-btn btn-sky">Add<i class='bx bx-plus'></i></button>
                                 </div>
                              </div>
                           </div>
                           <!-- <div class="col-12 mt-3">
                              <button class="my-btn btn-green w-100" style="max-width: 100%;">Save</button>
                           </div> -->
                        </div>
                     </div>
                     <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Save</button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </section>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
   $(document).ready(function() {
      $('#markup_type').change(function() {
         if ($(this).is(':checked')) {
            $('#markup_type_text').text('Percentage');
            var markup_type         = 'PERCENTAGE';
         } else {
            $('#markup_type_text').text('Flat');
            var markup_type         = 'FLAT';
         }
         var cost_price_inc_tax     = parseFloat($('#cost_price_inc_tax').val());
         var markup_amount          = parseFloat($('#markup_amount').val());
         if(markup_type == 'PERCENTAGE'){
            var added_amount = ((cost_price_inc_tax * markup_amount) / 100);
         } else {
            var added_amount = markup_amount;
         }
         var retail_price_inc_tax  = (cost_price_inc_tax + added_amount);
         $('#added_amount').val(added_amount.toFixed(2));
         $('#retail_price_inc_tax').val(retail_price_inc_tax.toFixed(2));
      });
      $('#cost_price_ex_tax').on('input', function(){
         var tax_percent         = '<?=$generalSetting->tax_percent?>';
         var cost_price_ex_tax   = parseFloat($('#cost_price_ex_tax').val());
         var cost_price_tax      = ((cost_price_ex_tax * tax_percent) / 100);
         var cost_price_inc_tax  = (cost_price_ex_tax + cost_price_tax);
         $('#cost_price_tax').val(cost_price_tax.toFixed(2));
         $('#cost_price_inc_tax').val(cost_price_inc_tax.toFixed(2));

         var markup_amount          = parseFloat($('#markup_amount').val());
         if ($('#markup_type').is(':checked')) {
            var markup_type         = 'PERCENTAGE';
         } else {
            var markup_type         = 'FLAT';
         }
         if(markup_type == 'PERCENTAGE'){
            var added_amount = ((cost_price_inc_tax * markup_amount) / 100);
         } else {
            var added_amount = markup_amount;
         }
         var retail_price_inc_tax  = (cost_price_inc_tax + added_amount);
         $('#added_amount').val(added_amount.toFixed(2));
         $('#retail_price_inc_tax').val(retail_price_inc_tax.toFixed(2));
      });
      $('#markup_amount').on('input', function(){
         var cost_price_inc_tax     = parseFloat($('#cost_price_inc_tax').val());
         var markup_amount          = parseFloat($('#markup_amount').val());
         if ($('#markup_type').is(':checked')) {
            var markup_type         = 'PERCENTAGE';
         } else {
            var markup_type         = 'FLAT';
         }
         if(markup_type == 'PERCENTAGE'){
            var added_amount = ((cost_price_inc_tax * markup_amount) / 100);
         } else {
            var added_amount = markup_amount;
         }
         var retail_price_inc_tax  = (cost_price_inc_tax + added_amount);
         $('#added_amount').val(added_amount.toFixed(2));
         $('#retail_price_inc_tax').val(retail_price_inc_tax.toFixed(2));
      });
   });
</script>