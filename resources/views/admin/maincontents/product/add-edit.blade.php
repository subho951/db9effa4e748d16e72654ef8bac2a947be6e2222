<?php
use App\Models\ProductDiscountVoucher;
use App\Models\ProductMultipleBuy;
use App\Helpers\Helper;
$controllerRoute                = $module['controller_route'];
$current_url                    = url()->current();
?>
<!-- Styling (Optional) -->
<style>
    .product-form-page {
        color: #1f2937;
    }
    .product-form-hero {
        background: linear-gradient(135deg, #111827 0%, #24415c 56%, #0f766e 100%);
        border-radius: 8px;
        padding: 22px 24px;
        color: #fff;
        box-shadow: 0 16px 40px rgba(15, 23, 42, .16);
        margin-bottom: 22px;
    }
    .product-form-hero h4 {
        color: #fff;
        margin: 0;
        font-weight: 800;
    }
    .product-form-hero .breadcrumb-text,
    .product-form-hero .breadcrumb-text a {
        color: rgba(226, 232, 240, .74);
        font-size: 13px;
    }
    .product-form-hero .btn {
        border-radius: 8px;
        font-weight: 700;
    }
    .product-form-card {
        border: 0;
        border-radius: 8px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        background: #fff;
        padding: 22px;
    }
    .product-form-page .form-container > .row {
        row-gap: 18px;
    }
    .product-form-page .form-label {
        color: #374151;
        font-weight: 800;
        margin-bottom: 8px;
    }
    .product-form-page .form-control,
    .product-form-page .form-select {
        width: 100% !important;
        min-height: 42px;
        border-radius: 8px;
        border-color: #d9e2ec;
        color: #111827;
        box-shadow: none;
    }
    .product-form-page .form-control:focus,
    .product-form-page .form-select:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
    }
    .product-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #111827;
        font-weight: 800;
        margin: 8px 0 0;
        padding-top: 18px;
        border-top: 1px solid #eef2f7;
    }
    .product-section-title::before {
        content: "";
        width: 9px;
        height: 28px;
        border-radius: 999px;
        background: linear-gradient(180deg, #2563eb, #0f766e);
    }
    .product-helper-strip {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px 14px;
        color: #64748b;
        font-size: 13px;
        margin-bottom: 18px;
    }
    .product-action-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px;
        margin-bottom: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
    }
    .product-action-bar .btn,
    .product-bottom-action .btn,
    .btn-sky {
        border-radius: 8px !important;
        font-weight: 800;
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }
    .product-image-panel {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 8px;
        padding: 16px;
    }
    .product-image-panel img {
        border-radius: 8px !important;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        background: #fff;
    }
    .discounts-section,
    .discount-offers-section {
        margin-top: 8px;
        border: 1px solid #e2e8f0;
        background: #fff;
        border-radius: 8px;
        padding: 18px;
    }
    .discount-vouchers-section,
    .discount-offer-section.field_wrapper2,
    .field_wrapper.discount-vouchers-section {
        border: 1px solid #e2e8f0 !important;
        background: #f8fafc;
        border-radius: 8px !important;
        padding: 14px !important;
    }
    .discount-offer-row {
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 14px;
        padding-bottom: 14px;
    }
    .discount-offer-row:last-child {
        border-bottom: 0;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .discount-offer-days {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 14px;
        min-height: 42px;
        align-items: center;
    }
    .discount-offer-days .form-check-label {
        font-size: 13px;
        color: #475569;
    }
    .product-form-page .form-check-input {
        cursor: pointer;
    }
    .product-form-page .form-switch .form-check-input {
        width: 2.75em;
        height: 1.45em;
    }
    .product-bottom-action {
        position: sticky;
        bottom: 0;
        z-index: 4;
        background: rgba(255, 255, 255, .92);
        backdrop-filter: blur(8px);
        border-top: 1px solid #eef2f7;
        padding: 14px 0 0;
        margin-top: 18px !important;
    }
    .dropdown {
        position: absolute;
        border: 1px solid #ccc;
        max-height: 150px;
        overflow-y: auto;
        background: #fff;
        display: none;
        width: 200px;
        z-index: 1000;
    }
    .dropdown div {
        padding: 8px;
        cursor: pointer;
    }
    .dropdown div:hover {
        background: #f0f0f0;
    }
    .btn-sky{
      background: #696cff;
      color: #fff;
      border-radius: 5px;
      outline: none;
      border: 1px solid transparent;
      padding: 5px 10px;
      transition: all .3s ease-in-out;
      display: flex;
      justify-content: center;
      align-items: center
    }
    .btn-sky i{
      margin-left: 5px;
    }
    .btn-sky:hover{
      background: transparent;
      color: #000;
      border-radius: 5px;
      outline: none;
      border: 1px solid #696cff;
    }
</style>
<div class="container-xxl flex-grow-1 container-p-y product-form-page">
<div class="product-form-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
   <div>
      <div class="breadcrumb-text mb-2">
         <a href="<?=url('admin/dashboard')?>">Dashboard</a> /
         <a href="<?=url('admin/' . $controllerRoute . '/list/')?>"><?=$module['title']?> List</a> /
         <?=$page_header?>
      </div>
      <h4><?=$page_header?></h4>
   </div>
   <a href="<?=url('admin/' . $controllerRoute . '/list/')?>" class="btn btn-outline-light"><i class="fa fa-arrow-left"></i> Back to Products</a>
</div>
<div class="row">
   <?php
      if($row){
         $sku                              = $row->sku;
         $name                             = $row->name;
         $receipt_short_name               = $row->receipt_short_name;
         $shelf_tag_short_name             = $row->shelf_tag_short_name;
         $barcode                          = $row->barcode;
         $brand_id                         = $row->brand_id;
         $category_id                      = $row->category_id;
         $supplier_sku                     = $row->supplier_sku;
         $supplier_product_name            = $row->supplier_product_name;
         $supplier_id                      = $row->supplier_id;
         $size_id                          = $row->size_id;
         $style                            = $row->style;
         $cost_price_ex_tax                = $row->cost_price_ex_tax;
         $cost_price_tax                   = $row->cost_price_tax;
         $retail_price_inc_tax             = $row->retail_price_inc_tax;
         $cost_price_inc_tax               = round($cost_price_ex_tax + (($cost_price_ex_tax * $generalSetting->tax_percent) / 100), 2);
         $added_amount                     = round($retail_price_inc_tax - $cost_price_inc_tax, 2);
         $markup_amount                    = ($retail_price_inc_tax > 0) ? round(($added_amount / $retail_price_inc_tax) * 100, 2) : 0.00;
         $markup_type                      = 'PERCENTAGE';
         $cover_image                      = $row->cover_image;
         $shop_stock                       = $row->shop_stock;
         $warehouse_stock                  = $row->warehouse_stock;
         $status                           = $row->status;
         $uId                              = $row->id;
      } else {
         $sku                              = '';
         $name                             = '';
         $receipt_short_name               = '';
         $shelf_tag_short_name             = '';
         $barcode                          = '';
         $brand_id                         = '';
         $category_id                      = '';
         $supplier_sku                     = '';
         $supplier_product_name            = '';
         $supplier_id                      = '';
         $size_id                          = '';
         $style                            = '';
         $cost_price_ex_tax                = '';
         $cost_price_tax                   = '';
         $cost_price_inc_tax               = '';
         $markup_amount                    = 0.00;
         $markup_type                      = 'PERCENTAGE';
         $added_amount                     = '';
         $retail_price_inc_tax             = '';
         $cover_image                      = '';
         $shop_stock                       = '';
         $warehouse_stock                  = '';
         $status                           = '';
         $uId                              = '';
      }
      ?>
   <section class="info-body">
      <div class="containers-fluid">
         <div class="row">
            <div class="col-12">
               <form method="POST" action="" enctype="multipart/form-data">
                  @csrf
                  <div class="containers">
                     <div class="form-container product-form-card">
                        <div class="row">
                           <div class="col-12">
                              <div class="product-action-bar">
                                 <div>
                                    <h5 class="mb-1 fw-bold">Product Information</h5>
                                    <small class="text-muted">Maintain catalogue identity, stock, supplier and pricing details.</small>
                                 </div>
                                 <div class="d-flex flex-wrap gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                                    <a href="<?=url('admin/' . $controllerRoute . '/list/')?>" class="btn btn-outline-secondary"><i class="fa fa-times"></i> Cancel</a>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <label class="form-label" for="sku">SKU <small class="text-danger">*</small></label>
                              <input type="text" class="form-control" placeholder="Enter SKU" id="sku" name="sku" value="<?=$sku?>" minlength="4" maxlength="10" pattern="[A-Za-z0-9]+" title="SKU must be at least 4 letters or numbers" required style="width: 40%;">
                           </div>
                           <div class="col-md-3">
                              <label class="form-label" for="barcode">Barcode <small class="text-danger">*</small></label>
                              <input type="text" class="form-control no-space" placeholder="Enter Barcode" id="barcode" name="barcode" value="<?=$barcode?>" minlength="8" maxlength="25" pattern="[A-Za-z0-9]+" title="Barcode must be unique and contain only letters or numbers" required style="width: 82%;">
                           </div>
                           <div class="col-md-3">
                              <label class="form-label" for="shop_stock">Shop Stock <small class="text-danger">*</small></label>
                              <input type="text" class="form-control" id="shop_stock" name="shop_stock" value="<?=$shop_stock?>" minlength="1" maxlength="1" onkeypress="return isNumber(event)" <?=((empty($row))?'':'readonly')?> style="width: 25%;">
                           </div>
                           <div class="col-md-3">
                              <label class="form-label" for="warehouse_stock">Warehouse Stock</label>
                              <input type="text" class="form-control" id="warehouse_stock" name="warehouse_stock" min="1" value="<?=$warehouse_stock?>" minlength="1" maxlength="1" onkeypress="return isNumber(event)" <?=((empty($row))?'':'readonly')?> style="width: 25%;">
                           </div>
                           <!-- <div class="col-md-2 align-items-center">
                              <label class="form-label" for="warehouse_stock">Status</label>
                              <div class="form-check form-switch mt-0">
                                 <input class="form-check-input" type="checkbox" name="status" role="switch" id="status" <?=(($status == 1)?'checked':'')?>>
                                 <label class="form-check-label" for="status">Active</label>
                              </div>
                           </div> -->

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

                           <div class="col-md-4">
                              <label class="form-label" for="supplier_id">Supplier Name <small class="text-danger">*</small></label>
                              <select name="supplier_id" class="form-select" id="supplier_id" required>
                                 <option value="" selected>Select Supplier</option>
                                 <?php if($suppliers){ foreach($suppliers as $supplier){?>
                                 <option value="<?=$supplier->id?>" <?=(($supplier->id == $supplier_id)?'selected':'')?>><?=$supplier->name?></option>
                                 <?php } }?>
                               </select>
                           </div>
                           <div class="col-md-4">
                              <label class="form-label" for="brand_id">Brand <small class="text-danger">*</small></label>
                              <select name="brand_id" class="form-select" id="brand_id" required>
                                 <option value="" selected>Select Brand</option>
                                 <option value="New">New</option>
                                 <?php if($brands){ foreach($brands as $brand){?>
                                 <option value="<?=$brand->id?>" <?=(($brand->id == $brand_id)?'selected':'')?>><?=$brand->name?></option>
                                 <?php } }?>
                               </select>
                               <input type="text" class="form-control" placeholder="Enter Brand Name" id="brand_name" name="brand_name" style="display:none;">
                           </div>
                           <div class="col-md-4">
                              <label class="form-label" for="category_id">Category <small class="text-danger">*</small></label>
                              <select name="category_id" class="form-select" id="category_id" required>
                                 <option value="" selected>Select Category</option>
                                 <?php if($categories){ foreach($categories as $category){?>
                                 <option value="<?=$category->id?>" <?=(($category->id == $category_id)?'selected':'')?>><?=$category->name?></option>
                                 <?php } }?>
                               </select>
                           </div>
                           <div class="col-md-4">
                              <label class="form-label" for="supplier_sku">Supplier SKU</label>
                              <input type="text" class="form-control" placeholder="Enter Supplier SKU" id="supplier_sku" name="supplier_sku" value="<?=$supplier_sku?>">
                           </div>
                           <div class="col-md-4">
                              <label class="form-label" for="supplier_product_name">Supplier Product Name</label>
                              <input type="text" class="form-control" placeholder="Enter Supplier Product Name" id="supplier_product_name" name="supplier_product_name" value="<?=$supplier_product_name?>">
                           </div>
                           <div class="col-md-4">
                              <label class="form-label" for="size_id">Vol_id <small class="text-danger">*</small></label>
                              <select name="size_id" class="form-select" id="size_id" required>
                                 <option value="" selected>Select Vol_id</option>
                                 <?php if($sizes){ foreach($sizes as $size){?>
                                 <option value="<?=$size->id?>" <?=(($size->id == $size_id)?'selected':'')?>><?=$size->id?> - <?=$size->name?> <?=$size->unit_name?></option>
                                 <?php } }?>
                               </select>
                           </div>
                           <div class="col-md-4">
                              <label class="form-label" for="style">Style</label>
                              <input type="text" class="form-control" placeholder="Enter Style" name="style" id="style" value="<?=$style?>">
                           </div>

                           <div class="col-md-4 align-items-center">
                              <label class="form-label" for="warehouse_stock">Status</label>
                              <div class="form-check form-switch mt-0">
                                 <input class="form-check-input" type="checkbox" name="status" role="switch" id="status" <?=(($status == 1)?'checked':'')?>>
                                 <label class="form-check-label" for="status">Active</label>
                              </div>
                           </div>

                           <div class="col-12"><h5 class="product-section-title">Pricing</h5></div>
                           <div class="col-md-3">
                              <label class="form-label" for="cost_price_ex_tax">Cost Price (Excl. GST) ($)</label>
                              <input type="number" class="form-control" placeholder="Enter Cost Price" name="cost_price_ex_tax" id="cost_price_ex_tax" value="<?=$cost_price_ex_tax?>" min="0" step="0.01" required>
                              <input type="hidden" name="cost_price_tax" id="cost_price_tax" value="<?=$cost_price_tax?>">
                              <input type="hidden" name="cost_price_inc_tax" id="cost_price_inc_tax" value="<?=$cost_price_inc_tax?>">
                              <input type="hidden" name="markup_type" value="PERCENTAGE">
                           </div>
                           <div class="col-md-3">
                              <label class="form-label" for="retail_price_inc_tax">Retail Price (Incl. GST) ($)</label>
                              <input type="number" class="form-control" placeholder="Enter Retail Price Incl. GST" name="retail_price_inc_tax" id="retail_price_inc_tax" value="<?=$retail_price_inc_tax?>" min="0" step="0.01" required>
                           </div>
                           <div class="col-md-3">
                              <label class="form-label" for="added_amount">Margin ($)</label>
                              <input type="text" class="form-control" name="added_amount" id="added_amount" value="<?=$added_amount?>" readonly>
                           </div>
                           <div class="col-md-3">
                              <label class="form-label" for="markup_amount">Margin (%)</label>
                              <input type="text" class="form-control" name="markup_amount" id="markup_amount" value="<?=$markup_amount?>" readonly>
                           </div>

                           <div class="discounts-section">
                              <h5 class="product-section-title mb-3">Discount Vouchers</h5>
                              <?php
                              $discountVouchers = ProductDiscountVoucher::where('status', '=', 1)->where('product_id', '=', $uId)->get();
                              ?>
                              <div class="col-auto">
                                 <div class="form-check form-switch mt-0 pt-0 pb-0">
                                    <input class="form-check-input mt-0" type="checkbox" role="switch" id="discount_voucher" <?=((count($discountVouchers) > 0)?'checked':'')?>>
                                 </div>
                              </div>
                              <div class="field_wrapper discount-vouchers-section" style="border: 1px solid #04163d52; padding: 10px; border-radius: 10px;<?=((count($discountVouchers) > 0)?'':'display: none;')?>">
                                 <?php
                                 if($discountVouchers){ $sl= 101; foreach($discountVouchers as $discountVoucher){
                                 ?>
                                    <div class="row align-items-center gap-2 gap-lg-0 mb-2">
                                       <div class="col-lg-2">
                                          <input type="text" class="form-control" name="voucher_code[]" id="voucher_code<?=$sl?>" placeholder="Voucher Code" oninput="getSuggestions(this.value, <?=$sl?>);" value="<?=$discountVoucher->voucher_code?>">
                                          <input type="hidden" name="coupon_id[]" id="coupon_id<?=$sl?>" value="<?=$discountVoucher->coupon_id?>">
                                          <div id="suggestions<?=$sl?>" class="dropdown"></div>
                                       </div>
                                       <div class="col-auto">
                                          <span>then</span>
                                       </div>
                                       <div class="col-lg-2">
                                          <input type="text" class="form-control" placeholder="Discount Value" name="discount_value[]" id="discount_value<?=$sl?>" value="<?=$discountVoucher->discount_value?>" readonly>
                                       </div>
                                       <div class="col-lg-1">
                                          <input type="text" class="form-control" placeholder="Type" name="discount_type[]" id="discount_type<?=$sl?>" value="<?=$discountVoucher->discount_type?>" readonly style="font-size: 9px;">
                                       </div>
                                       <div class="col-lg-2">
                                          <span>= retail less discount</span>
                                       </div>
                                       <div class="col-lg-2">
                                          <input type="text" class="form-control" placeholder="retail less discount" name="retail_discount[]" id="retail_discount<?=$sl?>" value="<?=$discountVoucher->retail_discount?>" readonly>
                                       </div>
                                       <div class="col-lg-2 d-flex align-items-center gap-2">
                                          <input type="text" class="form-control" placeholder="retail discounted price" name="retail_discounted_price[]" id="retail_discounted_price<?=$sl?>" value="<?=$discountVoucher->retail_discounted_price?>" readonly>
                                          <a href="javascript:void(0);" class="remove_button"><i class="fa fa-minus-circle text-danger"></i></a>
                                       </div>
                                    </div>
                                 <?php $sl++; } }?>
                                 <div class="row align-items-center gap-2 gap-lg-0">
                                    <div class="col-lg-2">
                                       <input type="text" class="form-control" name="voucher_code[]" id="voucher_code1" placeholder="Voucher Code" oninput="getSuggestions(this.value, 1);">
                                       <input type="hidden" name="coupon_id[]" id="coupon_id1">
                                       <div id="suggestions1" class="dropdown"></div>
                                    </div>
                                    <div class="col-auto">
                                       <span>then</span>
                                    </div>
                                    <div class="col-lg-2">
                                       <input type="text" class="form-control" placeholder="Discount Value" name="discount_value[]" id="discount_value1" readonly>
                                    </div>
                                    <div class="col-lg-1">
                                       <input type="text" class="form-control" placeholder="Type" name="discount_type[]" id="discount_type1" readonly style="font-size: 9px;">
                                    </div>
                                    <div class="col-lg-2">
                                       <span>= retail less discount</span>
                                    </div>
                                    <div class="col-lg-2">
                                       <input type="text" class="form-control" placeholder="retail less discount" name="retail_discount[]" id="retail_discount1" readonly>
                                    </div>
                                    <div class="col-lg-2 d-flex align-items-center gap-2">
                                       <input type="text" class="form-control" placeholder="retail discounted price" name="retail_discounted_price[]" id="retail_discounted_price1" readonly>
                                       <a style="opacity: 0;" class="d-none d-lg-block"><i class="fa fa-minus-circle text-danger"></i></a>
                                    </div>
                                 </div>
                              </div>
                              <div class="row align-items-center gap-2 gap-lg-0 discount-vouchers-section" style="<?=((count($discountVouchers) > 0)?'':'display: none;')?>">
                                 <div class="col-12 mt-3">
                                    <button class="my-btn btn-sky add_button" type="button">Add<i class='bx bx-plus'></i></button>
                                 </div>
                              </div>
                           </div>






                           <div class="discount-offers-section">
                              <h5 class="product-section-title mb-3">Discount Offers</h5>
                              <?php
                              $discountOffers = ProductMultipleBuy::where('status', '!=', 3)->where('product_id', '=', $uId)->get();
                              $dayLabels = [
                                 'ALL' => 'All',
                                 'MON' => 'Mon',
                                 'TUE' => 'Tue',
                                 'WED' => 'Wed',
                                 'THU' => 'Thu',
                                 'FRI' => 'Fri',
                                 'SAT' => 'Sat',
                                 'SUN' => 'Sun',
                              ];
                              ?>
                              <div class="col-auto">
                                 <div class="form-check form-switch mt-0 pt-0 pb-0">
                                    <input class="form-check-input mt-0" type="checkbox" role="switch" id="discount_offers" name="discount_offers" <?=((count($discountOffers) > 0)?'checked':'')?>>
                                    <label class="form-check-label" for="discount_offers">Enable discount offers</label>
                                 </div>
                              </div>

                              <div class="field_wrapper2 discount-offer-section" style="<?=((count($discountOffers) > 0)?'':'display: none;')?>">
                                 <?php if(count($discountOffers) > 0){ $sl= 1001; foreach($discountOffers as $discountOffer){
                                    $offerDays = json_decode($discountOffer->offer_available_days ?? '["ALL"]', true);
                                    if(!is_array($offerDays) || empty($offerDays)){ $offerDays = ['ALL']; }
                                    $offerName = (($discountOffer->offer_name != '')?$discountOffer->offer_name:'Discount Offer');
                                    $offerDisplayName = (($discountOffer->offer_display_name != '')?$discountOffer->offer_display_name:$offerName);
                                    $offerScope = (($discountOffer->discount_scope == 'BRAND')?'BRAND':'PRODUCT');
                                    $offerType = (($discountOffer->barcode_discount_type == 'PERCENTAGE')?'PERCENTAGE':'FLAT');
                                 ?>
                                    <div class="discount-offer-row" data-offer-row="<?=$sl?>">
                                       <div class="row g-3">
                                          <div class="col-md-4">
                                             <label class="form-label">Offer name (internal) <small class="text-danger">*</small></label>
                                             <input type="text" class="form-control" name="offer_name[<?=$sl?>]" value="<?=htmlspecialchars((string)$offerName, ENT_QUOTES, 'UTF-8')?>" placeholder="Offer name">
                                          </div>
                                          <div class="col-md-4">
                                             <label class="form-label">Offer display name (in POS)</label>
                                             <input type="text" class="form-control" name="offer_display_name[<?=$sl?>]" value="<?=htmlspecialchars((string)$offerDisplayName, ENT_QUOTES, 'UTF-8')?>" placeholder="POS display name">
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Discount type</label>
                                             <select class="form-select" name="discount_scope[<?=$sl?>]">
                                                <option value="PRODUCT" <?=(($offerScope == 'PRODUCT')?'selected':'')?>>Product</option>
                                                <option value="BRAND" <?=(($offerScope == 'BRAND')?'selected':'')?>>Brand</option>
                                             </select>
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Status</label>
                                             <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="offer_status[<?=$sl?>]" value="1" <?=(($discountOffer->status == 1)?'checked':'')?>>
                                                <label class="form-check-label">Active</label>
                                             </div>
                                          </div>
                                          <div class="col-md-3">
                                             <label class="form-label">Offer start date</label>
                                             <input type="date" class="form-control" name="offer_start_date[<?=$sl?>]" value="<?=($discountOffer->offer_start_date ?: date('Y-m-d'))?>">
                                          </div>
                                          <div class="col-md-3">
                                             <label class="form-label">Offer end date</label>
                                             <input type="date" class="form-control" name="offer_end_date[<?=$sl?>]" value="<?=$discountOffer->offer_end_date?>">
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label d-block">Expiry</label>
                                             <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="offer_no_expiry[<?=$sl?>]" value="1" <?=(($discountOffer->offer_no_expiry || $discountOffer->offer_end_date == '')?'checked':'')?>>
                                                <label class="form-check-label">No expiry</label>
                                             </div>
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Start time</label>
                                             <input type="time" class="form-control" name="offer_start_time[<?=$sl?>]" value="<?=($discountOffer->offer_start_time ? date('H:i', strtotime($discountOffer->offer_start_time)) : '')?>">
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">End time</label>
                                             <input type="time" class="form-control" name="offer_end_time[<?=$sl?>]" value="<?=($discountOffer->offer_end_time ? date('H:i', strtotime($discountOffer->offer_end_time)) : '')?>">
                                          </div>
                                          <div class="col-md-4">
                                             <label class="form-label">Applicable days</label>
                                             <div class="discount-offer-days">
                                                <?php foreach($dayLabels as $dayValue => $dayLabel){?>
                                                   <div class="form-check">
                                                      <input class="form-check-input" type="checkbox" name="offer_available_days[<?=$sl?>][]" value="<?=$dayValue?>" <?=(in_array($dayValue, $offerDays)?'checked':'')?>>
                                                      <label class="form-check-label"><?=$dayLabel?></label>
                                                   </div>
                                                <?php }?>
                                             </div>
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Min qty</label>
                                             <input type="number" min="1" class="form-control" name="offer_min_qty[<?=$sl?>]" value="<?=max(1, (int)$discountOffer->product1_min_qty)?>">
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Value type</label>
                                             <select class="form-select" name="offer_discount_type[<?=$sl?>]">
                                                <option value="FLAT" <?=(($offerType == 'FLAT')?'selected':'')?>>Flat $</option>
                                                <option value="PERCENTAGE" <?=(($offerType == 'PERCENTAGE')?'selected':'')?>>Percentage</option>
                                             </select>
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Discount</label>
                                             <input type="number" min="0" step="0.01" class="form-control" name="offer_discount_amount[<?=$sl?>]" value="<?=$discountOffer->discount_amount?>">
                                          </div>
                                          <div class="col-md-2 d-flex align-items-end">
                                             <a href="javascript:void(0);" class="btn btn-outline-danger remove_button2"><i class="fa fa-minus-circle"></i>&nbsp;Remove</a>
                                          </div>
                                       </div>
                                    </div>
                                 <?php $sl++; } } else { $sl = 301; ?>
                                    <div class="discount-offer-row" data-offer-row="<?=$sl?>">
                                       <div class="row g-3">
                                          <div class="col-md-4">
                                             <label class="form-label">Offer name (internal) <small class="text-danger">*</small></label>
                                             <input type="text" class="form-control" name="offer_name[<?=$sl?>]" placeholder="Offer name">
                                          </div>
                                          <div class="col-md-4">
                                             <label class="form-label">Offer display name (in POS)</label>
                                             <input type="text" class="form-control" name="offer_display_name[<?=$sl?>]" placeholder="POS display name">
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Discount type</label>
                                             <select class="form-select" name="discount_scope[<?=$sl?>]">
                                                <option value="PRODUCT">Product</option>
                                                <option value="BRAND">Brand</option>
                                             </select>
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Status</label>
                                             <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="offer_status[<?=$sl?>]" value="1" checked>
                                                <label class="form-check-label">Active</label>
                                             </div>
                                          </div>
                                          <div class="col-md-3">
                                             <label class="form-label">Offer start date</label>
                                             <input type="date" class="form-control" name="offer_start_date[<?=$sl?>]" value="<?=date('Y-m-d')?>">
                                          </div>
                                          <div class="col-md-3">
                                             <label class="form-label">Offer end date</label>
                                             <input type="date" class="form-control" name="offer_end_date[<?=$sl?>]">
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label d-block">Expiry</label>
                                             <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="offer_no_expiry[<?=$sl?>]" value="1" checked>
                                                <label class="form-check-label">No expiry</label>
                                             </div>
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Start time</label>
                                             <input type="time" class="form-control" name="offer_start_time[<?=$sl?>]">
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">End time</label>
                                             <input type="time" class="form-control" name="offer_end_time[<?=$sl?>]">
                                          </div>
                                          <div class="col-md-4">
                                             <label class="form-label">Applicable days</label>
                                             <div class="discount-offer-days">
                                                <?php foreach($dayLabels as $dayValue => $dayLabel){?>
                                                   <div class="form-check">
                                                      <input class="form-check-input" type="checkbox" name="offer_available_days[<?=$sl?>][]" value="<?=$dayValue?>" <?=(($dayValue == 'ALL')?'checked':'')?>>
                                                      <label class="form-check-label"><?=$dayLabel?></label>
                                                   </div>
                                                <?php }?>
                                             </div>
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Min qty</label>
                                             <input type="number" min="1" class="form-control" name="offer_min_qty[<?=$sl?>]" value="1">
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Value type</label>
                                             <select class="form-select" name="offer_discount_type[<?=$sl?>]">
                                                <option value="FLAT">Flat $</option>
                                                <option value="PERCENTAGE">Percentage</option>
                                             </select>
                                          </div>
                                          <div class="col-md-2">
                                             <label class="form-label">Discount</label>
                                             <input type="number" min="0" step="0.01" class="form-control" name="offer_discount_amount[<?=$sl?>]">
                                          </div>
                                          <div class="col-md-2 d-flex align-items-end">
                                             <a href="javascript:void(0);" class="btn btn-outline-danger remove_button2"><i class="fa fa-minus-circle"></i>&nbsp;Remove</a>
                                          </div>
                                       </div>
                                    </div>
                                 <?php }?>
                              </div>

                              <div class="row align-items-center gap-2 gap-lg-0 discount-offer-section" style="<?=((count($discountOffers) > 0)?'':'display: none;')?>">
                                 <div class="col-12 mt-3">
                                    <button class="my-btn btn-sky add_button2" type="button">Add Discount Offer<i class='bx bx-plus'></i></button>
                                 </div>
                              </div>
                           </div>
                           <div class="mb-3 col-md-12">
                              <div class="product-image-panel d-flex align-items-start align-items-sm-center gap-4">
                                 <?php if($cover_image != ''){?>
                                   <img src="<?=env('UPLOADS_URL').'/product/'.$cover_image?>" alt="<?=$name?>" class="d-block rounded mt-3 mb-3" height="100" width="100" style="border-radius: 50%;" id="uploadedAvatar" />
                                 <?php } else {?>
                                   <img src="<?=env('NO_IMAGE')?>" alt="<?=$name?>" class="d-block rounded mt-3 mb-3" height="100" width="100" style="border-radius: 50%;" id="uploadedAvatar" />
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
                                    <p class="text-muted mb-1">Recommended size: 800 x 800 px (1:1 square ratio).</p>
                                    <p class="text-muted mb-0">Allowed formats: JPG, JPEG or PNG.</p>
                                 </div>
                              </div>
                           </div>
                           <!-- <div class="col-12 mt-3">
                              <button class="my-btn btn-green w-100" style="max-width: 100%;">Save</button>
                           </div> -->
                        </div>
                     </div>
                     <div class="product-bottom-action">
                        <button type="submit" class="btn btn-primary me-2"><i class="fa fa-save"></i> Save Product</button>
                        <a href="<?=url('admin/' . $controllerRoute . '/list/')?>" class="btn btn-outline-secondary"><i class="fa fa-times"></i> Cancel</a>
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
      function calculateMargins() {
         var taxPercent = parseFloat('<?=$generalSetting->tax_percent?>') || 0;
         var costPrice = parseFloat($('#cost_price_ex_tax').val()) || 0;
         var retailPrice = parseFloat($('#retail_price_inc_tax').val()) || 0;
         var costTax = (costPrice * taxPercent) / 100;
         var costPriceIncTax = costPrice + costTax;
         var marginAmount = retailPrice - costPriceIncTax;
         var marginPercent = retailPrice > 0 ? (marginAmount / retailPrice) * 100 : 0;

         $('#cost_price_tax').val(taxPercent.toFixed(2));
         $('#cost_price_inc_tax').val(costPriceIncTax.toFixed(2));
         $('#added_amount').val(marginAmount.toFixed(2));
         $('#markup_amount').val(marginPercent.toFixed(2));
      }

      $('#cost_price_ex_tax, #retail_price_inc_tax').on('input', calculateMargins);
      calculateMargins();
   });
</script>
<script type="text/javascript">
   $(document).ready(function(){

      $('#discount_voucher').change(function() {
         if ($(this).is(':checked')) {
            $('.discount-vouchers-section').show();
         } else {
            $('.discount-vouchers-section').hide();
         }
      });

       var maxField = 10; //Input fields increment limitation
       var addButton = $('.add_button'); //Add button selector
       var wrapper = $('.field_wrapper'); //Input field wrapper
       var x = 1; //Initial field counter is 1
       
       // Once add button is clicked
       $(addButton).click(function(){
           //Check maximum number of input fields
           if(x < maxField){ 
               x++; //Increase field counter
               var fieldHTML = '<div class="row align-items-center gap-2 gap-lg-0 mt-2">\
                                    <div class="col-lg-2">\
                                       <input type="text" class="form-control" name="voucher_code[]" id="voucher_code' + x + '" placeholder="Voucher Code" oninput="getSuggestions(this.value, ' + x + ');">\
                                       <input type="hidden" name="coupon_id[]" id="coupon_id' + x + '">\
                                       <div id="suggestions' + x + '" class="dropdown"></div>\
                                    </div>\
                                    <div class="col-auto">\
                                       <span>then</span>\
                                    </div>\
                                    <div class="col-lg-2">\
                                       <input type="text" class="form-control" placeholder="Discount Value" name="discount_value[]" id="discount_value' + x + '" readonly>\
                                    </div>\
                                    <div class="col-lg-1">\
                                       <input type="text" class="form-control" placeholder="Type" name="discount_type[]" id="discount_type' + x + '" readonly style="font-size: 9px;">\
                                    </div>\
                                    <div class="col-lg-2">\
                                       <span>= retail less discount</span>\
                                    </div>\
                                    <div class="col-lg-2">\
                                       <input type="text" class="form-control" placeholder="retail less discount" name="retail_discount[]" id="retail_discount' + x + '" readonly>\
                                    </div>\
                                    <div class="col-lg-2 d-flex align-items-center gap-2">\
                                       <input type="text" class="form-control" placeholder="retail discounted price" name="retail_discounted_price[]" id="retail_discounted_price' + x + '" readonly>\
                                       <a href="javascript:void(0);" class="remove_button"><i class="fa fa-minus-circle text-danger"></i></a>\
                                    </div>\
                              </div>'; //New input field html
               $(wrapper).append(fieldHTML); //Add field html
           }else{
               alert('A maximum of '+maxField+' fields are allowed to be added. ');
           }
       });
       
       // Once remove button is clicked
       $(wrapper).on('click', '.remove_button', function(e){
           e.preventDefault();
           $(this).parent('div').parent('div').remove(); //Remove field html
           x--; //Decrease field counter
       });
   });
</script>
<script type="text/javascript">
   var baseUrl = '<?=url('/')?>'
   function getSuggestions(valam, sl){
      const query = valam;
      var retail_price = parseFloat($('#retail_price_inc_tax').val());
      if (query.length > 1) {
         $.ajax({
            url: baseUrl + "/admin/products/get-suggestions", // Replace with your server endpoint
            method: "GET",
            data: { q: query, retail_price : retail_price },
            success: function (response) {
               // Assuming `response` is an array of suggestions
               response = $.parseJSON(response);
               // console.log(response);
               let suggestionsHTML = "";
               response.forEach((item) => {
                  suggestionsHTML += `<div data-value="${item}" onclick="handleSuggestionClick('${item}',${sl});">${item}</div>`;
               });
               $("#suggestions" + sl).html(suggestionsHTML).show();
            },
            error: function () {
                 //
            },
         });
      } else {
         $("#suggestions" + sl).hide();
      }
   }
   function handleSuggestionClick(valam, sl){
      const value = valam;
      $('#voucher_code' + sl).val(value); // Fill the textbox
      $("#suggestions" + sl).hide();
      var retail_price = parseFloat($('#retail_price_inc_tax').val());
      // Make another AJAX call on selection
      $.ajax({
         url: baseUrl + "/admin/products/select-suggestions", // Replace with your server endpoint
         method: "GET",
         data: { selected: value, retail_price : retail_price },
         success: function (rply) {
            // console.log("Selection processed:", rply);
            rply = $.parseJSON(rply);
            if(rply.status){
               $('#coupon_id' + sl).val(rply.response.coupon_id);
               $('#discount_value' + sl).val(rply.response.discount_value.toFixed(2));
               $('#discount_type' + sl).val(rply.response.discount_type);
               $('#retail_discount' + sl).val(rply.response.retail_discount.toFixed(2));
               $('#retail_discounted_price' + sl).val(rply.response.retail_discounted_price.toFixed(2));
            }
         },
         error: function () {
             console.error("Error processing selection");
         },
      });
   }
</script>
<script type="text/javascript">

   $(document).ready(function(){
      $('#brand_id').on('change', function(){
         var brand_id = $('#brand_id').val();
         if(brand_id == 'New'){
            $('#brand_name').show();
         } else {
            $('#brand_name').hide();
         }
      });

      $('#discount_offers').change(function() {
         if ($(this).is(':checked')) {
            $('.discount-offer-section').show();
         } else {
            $('.discount-offer-section').hide();
         }
      });

       var maxField = 310; //Input fields increment limitation
       var addButton = $('.add_button2'); //Add button selector
       var wrapper = $('.field_wrapper2'); //Input field wrapper
       var x = 301; //Initial field counter is 1
       
       // Once add button is clicked
       $(addButton).click(function(){
           //Check maximum number of input fields
           if(x < maxField){ 
               x++; //Increase field counter
               var today = new Date().toISOString().slice(0, 10);
               var fieldHTML = '<div class="discount-offer-row" data-offer-row="' + x + '">\
                                  <div class="row g-3">\
                                    <div class="col-md-4">\
                                      <label class="form-label">Offer name (internal) <small class="text-danger">*</small></label>\
                                      <input type="text" class="form-control" name="offer_name[' + x + ']" placeholder="Offer name">\
                                    </div>\
                                    <div class="col-md-4">\
                                      <label class="form-label">Offer display name (in POS)</label>\
                                      <input type="text" class="form-control" name="offer_display_name[' + x + ']" placeholder="POS display name">\
                                    </div>\
                                    <div class="col-md-2">\
                                      <label class="form-label">Discount type</label>\
                                      <select class="form-select" name="discount_scope[' + x + ']">\
                                        <option value="PRODUCT">Product</option>\
                                        <option value="BRAND">Brand</option>\
                                      </select>\
                                    </div>\
                                    <div class="col-md-2">\
                                      <label class="form-label">Status</label>\
                                      <div class="form-check form-switch mt-2">\
                                        <input class="form-check-input" type="checkbox" name="offer_status[' + x + ']" value="1" checked>\
                                        <label class="form-check-label">Active</label>\
                                      </div>\
                                    </div>\
                                    <div class="col-md-3">\
                                      <label class="form-label">Offer start date</label>\
                                      <input type="date" class="form-control" name="offer_start_date[' + x + ']" value="' + today + '">\
                                    </div>\
                                    <div class="col-md-3">\
                                      <label class="form-label">Offer end date</label>\
                                      <input type="date" class="form-control" name="offer_end_date[' + x + ']">\
                                    </div>\
                                    <div class="col-md-2">\
                                      <label class="form-label d-block">Expiry</label>\
                                      <div class="form-check mt-2">\
                                        <input class="form-check-input" type="checkbox" name="offer_no_expiry[' + x + ']" value="1" checked>\
                                        <label class="form-check-label">No expiry</label>\
                                      </div>\
                                    </div>\
                                    <div class="col-md-2">\
                                      <label class="form-label">Start time</label>\
                                      <input type="time" class="form-control" name="offer_start_time[' + x + ']">\
                                    </div>\
                                    <div class="col-md-2">\
                                      <label class="form-label">End time</label>\
                                      <input type="time" class="form-control" name="offer_end_time[' + x + ']">\
                                    </div>\
                                    <div class="col-md-4">\
                                      <label class="form-label">Applicable days</label>\
                                      <div class="discount-offer-days">\
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="offer_available_days[' + x + '][]" value="ALL" checked><label class="form-check-label">All</label></div>\
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="offer_available_days[' + x + '][]" value="MON"><label class="form-check-label">Mon</label></div>\
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="offer_available_days[' + x + '][]" value="TUE"><label class="form-check-label">Tue</label></div>\
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="offer_available_days[' + x + '][]" value="WED"><label class="form-check-label">Wed</label></div>\
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="offer_available_days[' + x + '][]" value="THU"><label class="form-check-label">Thu</label></div>\
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="offer_available_days[' + x + '][]" value="FRI"><label class="form-check-label">Fri</label></div>\
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="offer_available_days[' + x + '][]" value="SAT"><label class="form-check-label">Sat</label></div>\
                                        <div class="form-check"><input class="form-check-input" type="checkbox" name="offer_available_days[' + x + '][]" value="SUN"><label class="form-check-label">Sun</label></div>\
                                      </div>\
                                    </div>\
                                    <div class="col-md-2">\
                                      <label class="form-label">Min qty</label>\
                                      <input type="number" min="1" class="form-control" name="offer_min_qty[' + x + ']" value="1">\
                                    </div>\
                                    <div class="col-md-2">\
                                      <label class="form-label">Value type</label>\
                                      <select class="form-select" name="offer_discount_type[' + x + ']">\
                                        <option value="FLAT">Flat $</option>\
                                        <option value="PERCENTAGE">Percentage</option>\
                                      </select>\
                                    </div>\
                                    <div class="col-md-2">\
                                      <label class="form-label">Discount</label>\
                                      <input type="number" min="0" step="0.01" class="form-control" name="offer_discount_amount[' + x + ']">\
                                    </div>\
                                    <div class="col-md-2 d-flex align-items-end">\
                                      <a href="javascript:void(0);" class="btn btn-outline-danger remove_button2"><i class="fa fa-minus-circle"></i>&nbsp;Remove</a>\
                                    </div>\
                                  </div>\
                                </div>'; //New input field html
               $(wrapper).append(fieldHTML); //Add field html

           }else{
               alert('A maximum of '+maxField+' fields are allowed to be added. ');
           }
       });
       
       // Once remove button is clicked
       $(wrapper).on('click', '.remove_button2', function(e){
           e.preventDefault();
           $(this).closest('.discount-offer-row').remove(); //Remove field html
           x--; //Decrease field counter
       });
   });

   var baseUrl = '<?=url('/')?>'
   function getBarcodeSuggestions(valam, sl){
      const query = valam;
      var barcode = $('.first_barcode').val();
      if (query.length > 1) {
         $.ajax({
            url: baseUrl + "/admin/products/get-barcode-suggestions", // Replace with your server endpoint
            method: "GET",
            data: { q: query, barcode : barcode },
            success: function (response) {
               // Assuming `response` is an array of suggestions
               response = $.parseJSON(response);
               // console.log(response);
               let suggestionsHTML = "";
               response.forEach((item) => {
                  suggestionsHTML += `<div data-value="${item}" onclick="handleBarcodeSuggestionClick('${item}',${sl});">${item}</div>`;
               });
               $("#barcode_suggestions" + sl).html(suggestionsHTML).show();
            },
            error: function () {
                 //
            },
         });
      } else {
         $("#barcode_suggestions" + sl).hide();
      }
   }
   function handleBarcodeSuggestionClick(valam, sl){
      const value = valam;
      $('#second_barcode' + sl).val(value); // Fill the textbox
      $("#barcode_suggestions" + sl).hide();
      var barcode = $('.first_barcode').val();
      // Make another AJAX call on selection
      $.ajax({
         url: baseUrl + "/admin/products/select-barcode-suggestions", // Replace with your server endpoint
         method: "GET",
         data: { selected: value, barcode : barcode },
         success: function (rply) {
            // console.log("Selection processed:", rply);
            rply = $.parseJSON(rply);
            if(rply.status){
               $('#product2_id' + sl).val(rply.response.product_id);
            }
         },
         error: function () {
             console.error("Error processing selection");
         },
      });
   }
   function isNumber(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
         return false;
      }
      return true;
   }
</script>
