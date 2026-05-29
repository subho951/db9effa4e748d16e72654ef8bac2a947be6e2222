<?php
use App\Models\ProductDiscountVoucher;
use App\Models\ProductMultipleBuy;
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
<style type="text/css">
  .product-page {
    color: #1f2937;
  }
  .product-hero {
    background: linear-gradient(135deg, #111827 0%, #24415c 56%, #0f766e 100%);
    border-radius: 8px;
    padding: 22px 24px;
    color: #fff;
    box-shadow: 0 16px 40px rgba(15, 23, 42, .16);
    margin-bottom: 22px;
  }
  .product-hero h4 {
    color: #fff;
    margin: 0;
    font-weight: 800;
  }
  .product-hero .breadcrumb-text,
  .product-hero .breadcrumb-text a {
    color: rgba(226, 232, 240, .74);
    font-size: 13px;
  }
  .product-action-btn {
    border-radius: 8px;
    font-weight: 700;
    min-height: 38px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
  }
  .product-filter-card,
  .product-table-card {
    border: 0;
    border-radius: 8px;
    box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
  }
  .product-filter-card .card-body {
    padding: 18px;
  }
  .product-filter-card .form-control {
    border-radius: 8px;
    min-height: 42px;
    border-color: #d9e2ec;
  }
  .product-table-card .card-header {
    background: #fff;
    border-bottom: 1px solid #eef2f7;
    padding: 18px 20px;
  }
  .product-table-card .card-title {
    margin: 0;
    font-weight: 800;
    color: #111827;
  }
  .product-table-wrap {
    position: relative;
  }
  .product-table-wrap .table {
    margin-bottom: 0;
  }
  .product-catalogue-table {
    table-layout: fixed;
    width: 100% !important;
  }
  .product-list-toolbar {
    display: grid !important;
    grid-template-columns: minmax(190px, 1fr) minmax(240px, 380px) minmax(220px, 1fr);
    align-items: center;
  }
  .product-list-search-slot {
    justify-self: center;
    width: 100%;
    max-width: 420px;
  }
  .product-list-search-slot #simpletable_filter {
    float: none !important;
    text-align: center;
    width: 100%;
  }
  .product-list-search-slot .dt-search,
  .product-list-search-slot .dataTables_filter {
    width: 100%;
  }
  .product-table-search {
    position: relative;
    width: 100%;
    margin: 0 !important;
  }
  .product-table-search .product-search-label {
    display: block;
    width: 100%;
    margin: 0;
  }
  .product-table-search .product-table-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    color: #64748b;
    font-size: 14px;
    pointer-events: none;
  }
  .product-list-search-slot label,
  .product-list-search-slot .product-search-label {
    width: 100%;
    margin: 0;
  }
  .product-list-search-slot input,
  .product-table-search input[type="search"],
  .product-table-search input[type="text"] {
    width: 100% !important;
    height: 42px;
    border: 1px solid #d8e1ea !important;
    border-radius: 8px;
    background: #f8fafc;
    color: #111827;
    font-size: 13px;
    font-weight: 600;
    padding: 0 15px 0 40px !important;
    margin-left: 0 !important;
    outline: 0;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .75), 0 8px 18px rgba(15, 23, 42, .05);
    transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
  }
  .product-table-search input::placeholder {
    color: #94a3b8;
    font-weight: 500;
  }
  .product-table-search input:focus {
    background: #fff;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .12), 0 10px 22px rgba(15, 23, 42, .07);
  }
  .product-bulk-actions {
    justify-self: end;
    width: 220px;
  }
  .product-catalogue-table col.col-select,
  .product-catalogue-table col.col-active {
    width: 4ch;
  }
  .product-catalogue-table col.col-sku {
    width: 8ch;
  }
  .product-catalogue-table col.col-product-name {
    width: 35ch;
  }
  .product-catalogue-table col.col-offers {
    width: 30ch;
  }
  .product-catalogue-table col.col-vol {
    width: 8ch;
  }
  .product-catalogue-table col.col-retail {
    width: 12ch;
  }
  .product-catalogue-table col.col-stock {
    width: 5ch;
  }
  .product-catalogue-table col.col-supplier {
    width: 16ch;
  }
  .product-catalogue-table col.col-barcode {
    width: 15ch;
  }
  .product-catalogue-table col.col-delete {
    width: 5ch;
  }
  .product-catalogue-table .select-cell,
  .product-catalogue-table .active-cell,
  .product-catalogue-table .delete-cell {
    text-align: center;
  }
  .product-catalogue-table .char-cell {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .product-row-select,
  #select-all-products {
    width: 16px;
    height: 16px;
  }
  .product-table-wrap thead th {
    background: #f8fafc;
    color: #64748b;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .06em;
    border-bottom: 1px solid #e5edf5;
    white-space: nowrap;
  }
  .product-table-wrap tbody td {
    color: #334155;
    vertical-align: middle;
  }
  .product-catalogue-table th,
  .product-catalogue-table td {
    padding: 4px 6px !important;
  }
  .product-catalogue-table .nowrap-cell {
    white-space: nowrap;
  }
  .product-catalogue-table .ellipsis-cell {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .product-catalogue-table .barcode-cell {
    font-size: 11px;
  }
  .product-catalogue-table .variety-cell {
    overflow: hidden;
  }
  .product-catalogue-table .promo-list li {
    display: flex;
    align-items: center;
    gap: 4px;
    min-width: 0;
  }
  .product-catalogue-table .promo-text {
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .product-catalogue-table .promo-switch {
    flex: 0 0 auto;
  }
  .product-table-wrap tbody tr:hover {
    background: #f8fafc;
  }
  .product-sku-link {
    color: #2563eb;
    font-weight: 800;
  }
  .product-status-btn {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
  }
  .promo-list {
    list-style: none;
    padding-left: 0;
    margin: 0 0 8px;
  }
  .promo-list small {
    display: inline-block;
    color: #475569;
    font-weight: 800;
    margin-bottom: 4px;
  }
  .promo-list li {
    color: #64748b;
    font-size: 12px;
    line-height: 1.55;
    padding: 2px 0;
  }
  /* admin pin modal */
  .otp-input-fields {
      margin: auto;
      background-color: white;
      width: 100%;
      display: flex;
      justify-content: center;
      /* gap: 10px; */
      padding:10px;
    }
    .otp-input-fields input {
      height: 40px;
      width: 40px;
      background-color: transparent;
      border-radius: 4px;
      border: 1px solid #01CA6A;
      text-align: center;
      outline: none;
      font-size: 16px;
      margin: 0 10px;
      /* Firefox */
    }
    .otp-input-fields input::-webkit-outer-spin-button, .otp-input-fields input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    .otp-input-fields input[type=number] {
      -moz-appearance: textfield;
    }
    .otp-input-fields input:focus {
      border-width: 2px;
      border-color: #01CA6A;
      font-size: 20px;
    }
    
    .result {
      max-width: 400px;
      margin: auto;
      margin-bottom: 1.5rem;
      text-align: center;
    }
    .result p {
      font-size: 24px;
      font-family: "Antonio", sans-serif;
      opacity: 1;
      transition: color 0.5s ease;
    }
    .result p._ok {
      color: #01CA6A;
    }
    .result p._notok {
      color: red;
      border-radius: 3px;
    }
    #adminpinmodal.modal .btn-close{
      transform: translate(4px, 6px);
    }
    .product-modal-close {
      width: 34px;
      height: 34px;
      border: 0;
      border-radius: 8px;
      background: #fff;
      color: #111827;
      box-shadow: 0 8px 18px rgba(15, 23, 42, .18);
      font-size: 24px;
      font-weight: 700;
      line-height: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .product-modal-close:hover {
      color: #dc2626;
      background: #f8fafc;
    }
    .validate {
      border-radius: 20px;
      height: 40px;
      background-color: #01CA6A;
      border: 1px solid #01CA6A;
      width: 140px
  }
  @media(max-width: 767px) {
    .product-list-toolbar {
      grid-template-columns: 1fr;
    }
    .product-bulk-actions {
      justify-self: stretch;
      width: 100%;
    }
    div.dt-container div.dt-layout-row {
      display: flex !important;
      width: 100%;
      justify-content: space-between;
      align-items: center;
    }
  }
  @media(max-width: 575px) {
    div.dt-container div.dt-layout-row {
      flex-wrap: wrap;
    }
  }
</style>
<div class="container-xxl flex-grow-1 container-p-y product-page">
  <div class="product-hero d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div>
      <div class="breadcrumb-text mb-2"><a href="<?=url('admin/dashboard')?>">Dashboard</a> / <?=$page_header?></div>
      <h4><?=$page_header?></h4>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="<?=url('admin/' . $controllerRoute . '/add/')?>" class="btn btn-light product-action-btn"><i class="fa fa-plus"></i>Add <?=$module['title']?></a>
      <a href="<?=url('admin/' . $controllerRoute . '/upload-product/')?>" class="btn btn-outline-light product-action-btn"><i class="fa fa-upload"></i>Upload <?=$module['title']?></a>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card product-filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="">
              <input type="hidden" name="mode" value="filter">
              <div class="row align-items-center g-3">
                <div class="col-lg-3 col-md-3">
                  <select class="form-control" name="status">
                    <option value="" <?=(($status == '')?'selected':'')?>>Select Status</option>
                    <option value="1" <?=(($status == '1')?'selected':'')?>>Active</option>
                    <option value="0" <?=(($status == '0')?'selected':'')?>>Inactive</option>
                  </select>
                </div>
                <div class="col-lg-3 col-md-3">
                  <select class="form-control" name="brand_id">
                    <option value="" selected>Select Brand</option>
                    <?php if($brands){ foreach($brands as $brand){?>
                      <option value="<?=$brand->id?>" <?=(($brand_id == $brand->id)?'selected':'')?>><?=$brand->name?></option>
                    <?php } }?>
                  </select>
                </div>
                <div class="col-lg-3 col-md-3">
                  <select class="form-control" name="supplier_id">
                    <option value="" selected>Select Supplier</option>
                    <?php if($suppliers){ foreach($suppliers as $supplier){?>
                      <option value="<?=$supplier->id?>" <?=(($supplier_id == $supplier->id)?'selected':'')?>><?=$supplier->name?></option>
                    <?php } }?>
                  </select>
                </div>
                <div class="col-lg-3 col-md-3 d-flex flex-wrap gap-2">
                  <button type="submit" class="btn btn-primary product-action-btn"><i class="fa fa-filter"></i>Apply</button>
                  <?php if($is_search){?>
                    <a href="<?=url('admin/' . $controllerRoute . '/list/')?>" class="btn btn-outline-secondary product-action-btn"><i class="fa fa-refresh"></i>Reset</a>
                  <?php }?>
                </div>
              </div>
            </form>
        </div>
      </div>
      <div class="card product-table-card">
        <div class="card-header product-list-toolbar gap-2">
          <div>
            <h5 class="card-title">Product Catalogue</h5>
            <small class="text-muted"><?=number_format(count($rows))?> products listed</small>
          </div>
          <div id="product-list-search-slot" class="product-list-search-slot"></div>
          <div id="product-bulk-actions" class="product-bulk-actions d-none">
            <select class="form-select form-select-sm" id="bulk-product-option">
              <option value="" selected>Select Option</option>
              <option value="purchase_order">Add to purchase order</option>
              <option value="transfer">Add to transfer</option>
              <option value="delete">Delete item</option>
            </select>
          </div>
        </div>
        <div class="card-body">
          <div class="dt-responsive table-responsive product-table-wrap">
            <table id="simpletable" class="table table-striped table-bordered nowrap product-catalogue-table" data-export-skip-first="1">
              <colgroup>
                <col class="col-select">
                <col class="col-active">
                <col class="col-sku">
                <col class="col-product-name">
                <col class="col-offers">
                <col class="col-vol">
                <col class="col-retail">
                <col class="col-stock">
                <col class="col-supplier">
                <col class="col-barcode">
                <col class="col-delete">
              </colgroup>
              <thead>
                <tr>
                  <th scope="col" data-dt-order="disable"><input type="checkbox" id="select-all-products" title="Select all products"></th>
                  <th scope="col"><?=(($status != '')?'<u>Active</u>':'Active')?></th>
                  <th scope="col">SKU</th>
                  <th scope="col">Product Name</th>
                  <th scope="col">Offers</th>
                  <th scope="col">Vol_id</th>
                  <th scope="col">Retail</th>
                  <th scope="col">Stock</th>
                  <!-- <th scope="col"><?=(($brand_id != '')?'<u>Brand</u>':'Brand')?></th> -->
                  <th scope="col"><?=(($supplier_id != '')?'<u>Supplier</u>':'Supplier')?></th>
                  <th scope="col">Barcode</th>
                  <th scope="col">Delete</th>
                </tr>
              </thead>
              <tbody>
                <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                  <tr>
                    <td class="select-cell">
                      <input type="checkbox" class="product-row-select" value="<?=$row->id?>" data-supplier-id="<?=$row->supplier_id?>" data-product-name="<?=htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8')?>">
                    </td>
                    <td class="nowrap-cell active-cell">
                      <?php if($row->status){?>
                        <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-success btn-sm product-status-btn" title="Activate <?=$module['title']?>"><i class="fa fa-check"></i></a>
                      <?php } else {?>
                        <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-warning btn-sm product-status-btn" title="Deactivate <?=$module['title']?>"><i class="fa fa-times"></i></a>
                      <?php }?>
                    </td>
                    <td class="nowrap-cell char-cell" title="<?=$row->sku?>">
                      <a class="product-sku-link" href="<?=url('admin/products/edit/'.Helper::encoded($row->id))?>"><?=$row->sku?></a>
                      <!-- <a href="javascript:void(0);" onclick="openAdminPINModal(<?=$row->id?>);"><?=$row->sku?></a> -->
                    </td>
                    <td class="ellipsis-cell char-cell" title="<?=$row->name?>"><?=$row->name?></td>
                    <td class="variety-cell">
                      <!-- Discount Vouchers -->
                        <?php $discountVouchers = ProductDiscountVoucher::select('id', 'voucher_code', 'retail_discounted_price', 'status')->where('product_id', $row->id)->where('status', '!=', 3)->get(); ?>
                        <ul class="promo-list">
                          <?php if(count($discountVouchers) > 0){?>
                            <small style="font-weight: bold; text-decoration:underline;">Discount Vouchers</small>
                            <?php foreach($discountVouchers as $discountVoucher){?>
                              <li>
                                <span class="promo-text" title="<?=$discountVoucher->voucher_code?> : $<?=number_format($discountVoucher->retail_discounted_price,2)?>"><?=$discountVoucher->voucher_code?> : $<?=number_format($discountVoucher->retail_discounted_price,2)?></span>
                                <div class="promo-switch form-check form-switch" style="display: inline;">
                                  <input class="form-check-input mt-0" type="checkbox" role="switch" id="discount_voucher_switch" name="discount_voucher_switch" value="<?= $discountVoucher->id?>" <?=(($discountVoucher->status)?'checked':'')?>>
                                </div>
                              </li>
                            <?php }?>
                          <?php }?>
                        </ul>
                      <!-- Discount Vouchers -->
                      <!-- Multiple Buys -->
                        <?php $multipleBuys = ProductMultipleBuy::select('id', 'first_barcode', 'second_barcode', 'barcode_discount_type', 'discount_amount', 'discounted_amount', 'status')->where('product_id', $row->id)->where('status', '!=', 3)->get(); ?>
                        <ul class="promo-list">
                          <?php if(count($multipleBuys) > 0){?>
                            <small style="font-weight: bold; text-decoration:underline;">Multiple Buys</small>
                            <?php foreach($multipleBuys as $multipleBuy){?>
                              <li>
                                <span class="promo-text" title="<?= $multipleBuy->first_barcode?> and <?= $multipleBuy->second_barcode?> = true, then <?= (($multipleBuy->barcode_discount_type == 'PERCENTAGE')?$multipleBuy->discount_amount . '%':'$' . $multipleBuy->discount_amount)?> ($<?= $multipleBuy->discounted_amount?>)"><?= $multipleBuy->first_barcode?> and <?= $multipleBuy->second_barcode?> = true, then <?= (($multipleBuy->barcode_discount_type == 'PERCENTAGE')?$multipleBuy->discount_amount . '%':'$' . $multipleBuy->discount_amount)?> ($<?= $multipleBuy->discounted_amount?>)</span>
                                <div class="promo-switch form-check form-switch" style="display: inline;">
                                  <input class="form-check-input mt-0" type="checkbox" role="switch" id="multiplebuy_switch" name="multiplebuy_switch" value="<?= $multipleBuy->id?>" <?=(($multipleBuy->status)?'checked':'')?>>
                                </div>
                              </li>
                            <?php }?>
                            <br><br>
                          <?php }?>
                        </ul>
                      <!-- Multiple Buys -->
                    </td>
                    <td class="nowrap-cell char-cell" title="<?=$row->size_name?> <?=$row->unit_name?>"><?=$row->size_id?></td>
                    <td class="nowrap-cell char-cell" title="$<?=number_format($row->retail_price_inc_tax,2)?>">$<?=number_format($row->retail_price_inc_tax,2)?></td>
                    <td class="nowrap-cell char-cell" title="<?=$row->shop_stock?>"><?=$row->shop_stock?></td>
                    <!-- <td><?=$row->brand_name?></td> -->
                    <td class="ellipsis-cell char-cell" title="<?=$row->supplier_name?>"><?=$row->supplier_name?></td>
                    <td class="ellipsis-cell nowrap-cell barcode-cell char-cell" title="<?=$row->barcode?>"><?=$row->barcode?></td>
                    <td class="nowrap-cell delete-cell">
                      <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm product-status-btn js-delete-product" title="Delete <?=$module['title']?>" data-product-id="<?=Helper::encoded($row->id)?>" data-product-name="<?=htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8')?>"><i class="fa fa-trash"></i></a>
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

<!-- Admin PIN Modal -->
<div class="modal fade" id="adminpinmodal" tabindex="-1" aria-labelledby="adminpinmodalLabel" aria-hidden="true">
    
</div>
<script type="text/javascript">
  var otp_inputs = document.querySelectorAll(".otp__digit");
  var mykey = "0123456789".split("");
  otp_inputs.forEach((_) => {
    _.addEventListener("keyup", handle_next_input);
  });
  function handle_next_input(event) {
    let current = event.target;
    let index = parseInt(current.classList[1].split("__")[2]);
    current.value = event.key;

    if (event.keyCode == 8 && index > 1) {
      current.previousElementSibling.focus();
    }
    if (index < 4 && mykey.indexOf("" + event.key + "") != -1) {
      var next = current.nextElementSibling;
      next.focus();
    }
    var _finalKey = "";
    for (let { value } of otp_inputs) {
      _finalKey += value;
    }
    if (_finalKey.length == 4) {
      document.querySelector("#_otp").classList.replace("_notok", "_ok");
      document.querySelector("#_otp").innerText = _finalKey;
    } else {
      document.querySelector("#_otp").classList.replace("_ok", "_notok");
      document.querySelector("#_otp").innerText = _finalKey;
    }
  }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
  function openAdminPINModal(productId){
    var modalHTML = '';
    var actionurl = '<?=url("/admin/products/validate-admin-pin-product")?>';
    modalHTML = '<div class="modal-dialog  modal-dialog-centered">\
                  <div class="modal-content">\
                    <div class="modal-header p-0">\
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\
                    </div>\
                    <form method="POST" action="'+actionurl+'" id="myForm">\
                      @csrf\
                      <input type="hidden" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222">\
                      <input type="hidden" name="product_id" id="product_id" value="'+productId+'">\
                      <input type="text" style="display:none">\
                      <input type="password" style="display:none">\
                      <div class="modal-body">\
                          <h1 class="modal-title fs-4 text-center w-100 text-black mb-2" id="adminpinmodalLabel">Enter Your Admin Pin</h1>\
                          <div class="otp-input-fields">\
                            <input type="password" class="otp__digit otp__field__1" autocomplete="off" name="pin1" id="pin1" autocomplete="new-password">\
                            <input type="password" class="otp__digit otp__field__2" autocomplete="off" name="pin2" id="pin2" autocomplete="new-password">\
                            <input type="password" class="otp__digit otp__field__3" autocomplete="off" name="pin3" id="pin3" autocomplete="new-password">\
                            <input type="password" class="otp__digit otp__field__4" autocomplete="off" name="pin4" id="pin4" autocomplete="new-password">\
                          </div>\
                          <div class="mt-4 d-flex justify-content-center">\
                            <button class="btn btn-green text-black px-4 validate">Submit</button>\
                          </div>\
                      </div>\
                    </form>\
                  </div>\
                </div>';
    $('#adminpinmodal').html(modalHTML);
    $('#adminpinmodal').modal('show');
  }
  function openAdminPINModal2(productId){
    var modalHTML = '';
    var actionurl = '<?=url("/admin/products/validate-admin-pin-export")?>';
    modalHTML = '<div class="modal-dialog  modal-dialog-centered">\
                  <div class="modal-content">\
                    <div class="modal-header p-0">\
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\
                    </div>\
                    <form method="POST" action="'+actionurl+'" id="myForm">\
                      @csrf\
                      <input type="hidden" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222">\
                      <div class="modal-body">\
                          <h1 class="modal-title fs-4 text-center w-100 text-black mb-2" id="adminpinmodalLabel">Enter Your Admin Pin</h1>\
                          <div class="otp-input-fields">\
                            <input type="password" class="otp__digit otp__field__1" autocomplete="off" name="pin1" id="pin5">\
                            <input type="password" class="otp__digit otp__field__2" autocomplete="off" name="pin2" id="pin6">\
                            <input type="password" class="otp__digit otp__field__3" autocomplete="off" name="pin3" id="pin7">\
                            <input type="password" class="otp__digit otp__field__4" autocomplete="off" name="pin4" id="pin8">\
                          </div>\
                          <div class="mt-4 d-flex justify-content-center">\
                            <button class="btn btn-green text-black px-4 validate">Submit</button>\
                          </div>\
                      </div>\
                    </form>\
                  </div>\
                </div>';
    $('#adminpinmodal').html(modalHTML);
    $('#adminpinmodal').modal('show');
  }
  function openProductDeleteModal(productId, productName){
    var modalHTML = '';
    var actionurl = '<?=url("/admin/products/delete")?>/' + productId;
    var safeProductName = $('<div>').text(productName).html();
    modalHTML = '<div class="modal-dialog modal-dialog-centered">\
                  <div class="modal-content">\
                    <div class="modal-header p-0">\
                      <button type="button" class="product-modal-close ms-auto" data-bs-dismiss="modal" aria-label="Close">&times;</button>\
                    </div>\
                    <form method="POST" action="'+actionurl+'" id="deleteProductForm">\
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">\
                      <input type="text" style="display:none">\
                      <input type="password" style="display:none">\
                      <div class="modal-body">\
                          <h1 class="modal-title fs-4 text-center w-100 text-black mb-2" id="adminpinmodalLabel">Enter Admin Password</h1>\
                          <p class="text-center mb-3">Delete '+safeProductName+'?</p>\
                          <div class="otp-input-fields">\
                            <input type="password" class="otp__digit otp__field__1" inputmode="numeric" pattern="[0-9]" maxlength="1" autocomplete="off" name="pin1" id="delete_pin1" autofocus required>\
                            <input type="password" class="otp__digit otp__field__2" inputmode="numeric" pattern="[0-9]" maxlength="1" autocomplete="off" name="pin2" id="delete_pin2" required>\
                            <input type="password" class="otp__digit otp__field__3" inputmode="numeric" pattern="[0-9]" maxlength="1" autocomplete="off" name="pin3" id="delete_pin3" required>\
                            <input type="password" class="otp__digit otp__field__4" inputmode="numeric" pattern="[0-9]" maxlength="1" autocomplete="off" name="pin4" id="delete_pin4" required>\
                          </div>\
                          <div class="mt-4 d-flex justify-content-center gap-2">\
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>\
                            <button type="submit" class="btn btn-danger px-4">Delete</button>\
                          </div>\
                      </div>\
                    </form>\
                  </div>\
                </div>';
    $('#adminpinmodal').html(modalHTML);
    $('#adminpinmodal').modal('show');
    setTimeout(function() {
      $('#delete_pin1').focus();
    }, 300);
  }
  function openBulkProductDeleteModal(productIds){
    var modalHTML = '';
    var actionurl = '<?=url("/admin/products/bulk-delete")?>';
    var hiddenProductIds = productIds.map(function(productId) {
      return '<input type="hidden" name="product_ids[]" value="'+productId+'">';
    }).join('');
    modalHTML = '<div class="modal-dialog modal-dialog-centered">\
                  <div class="modal-content">\
                    <div class="modal-header p-0">\
                      <button type="button" class="product-modal-close ms-auto" data-bs-dismiss="modal" aria-label="Close">&times;</button>\
                    </div>\
                    <form method="POST" action="'+actionurl+'" id="bulkDeleteProductForm">\
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">\
                      '+hiddenProductIds+'\
                      <input type="text" style="display:none">\
                      <input type="password" style="display:none">\
                      <div class="modal-body">\
                          <h1 class="modal-title fs-4 text-center w-100 text-black mb-2">Enter Admin Password</h1>\
                          <p class="text-center mb-3">Delete '+productIds.length+' selected product(s)?</p>\
                          <div class="otp-input-fields">\
                            <input type="password" class="otp__digit otp__field__1" inputmode="numeric" pattern="[0-9]" maxlength="1" autocomplete="off" name="pin1" id="bulk_delete_pin1" autofocus required>\
                            <input type="password" class="otp__digit otp__field__2" inputmode="numeric" pattern="[0-9]" maxlength="1" autocomplete="off" name="pin2" id="bulk_delete_pin2" required>\
                            <input type="password" class="otp__digit otp__field__3" inputmode="numeric" pattern="[0-9]" maxlength="1" autocomplete="off" name="pin3" id="bulk_delete_pin3" required>\
                            <input type="password" class="otp__digit otp__field__4" inputmode="numeric" pattern="[0-9]" maxlength="1" autocomplete="off" name="pin4" id="bulk_delete_pin4" required>\
                          </div>\
                          <div class="mt-4 d-flex justify-content-center gap-2">\
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>\
                            <button type="submit" class="btn btn-danger px-4">Delete</button>\
                          </div>\
                      </div>\
                    </form>\
                  </div>\
                </div>';
    $('#adminpinmodal').html(modalHTML);
    $('#adminpinmodal').modal('show');
    setTimeout(function() {
      $('#bulk_delete_pin1').focus();
    }, 300);
  }
</script>
<script>
  $(document).on('input', '.otp__digit', function () {
      // Allow only digits and limit to 1 character
      this.value = this.value.replace(/\D/g, '').slice(0, 1);

      const form = $(this).closest('form');
      const inputs = form.find('.otp__digit');
      const index = inputs.index(this);

      // Move to next input automatically
      if (this.value && index < inputs.length - 1) {
          inputs.eq(index + 1).focus();
      }

      // Auto-submit if all 4 digits are filled
      const allFilled = inputs.toArray().every(inp => $(inp).val().length === 1);
      if (allFilled && form.attr('id') !== 'deleteProductForm' && form.attr('id') !== 'bulkDeleteProductForm') {
          form.find('.validate').prop('disabled', true); // prevent double submission
          form.submit();
      }
  });

  $(document).on('keyup', '.otp__digit', function (e) {
      const inputs = $(this).closest('form').find('.otp__digit');
      const index = inputs.index(this);

      // Move backward on backspace if empty
      if (e.key === 'Backspace' && !$(this).val() && index > 0) {
          inputs.eq(index - 1).focus();
      }
  });
</script>
<script>
  $(document).on('change', '.form-check-input', function() {
      var base_url = '<?=url('/')?>';
      let switchId = $(this).attr('id');              // e.g. "multiplebuy_switch"
      let id = $(this).attr('value');              // e.g. "multiplebuy_switch"
      let status = $(this).is(':checked') ? 1 : 0;    // 1 = ON, 0 = OFF
      
      if(switchId == 'multiplebuy_switch'){
        url = base_url + '/admin/products/update-multibuy-switch-status';
      } else {
        url = base_url + '/admin/products/update-discountvoucher-switch-status';
      }
      $.ajax({
          url: url,           // your API endpoint
          type: 'POST',
          data: {
              "_token": "{{ csrf_token() }}",
              id: id,
              status: status,
          },
          beforeSend: function() {
              // toastAlert('warning', 'Updating switch status...');
          },
          success: function(response) {
              // console.log('Switch updated successfully:', response);
              toastAlert("success", response.message);
          },
          error: function(xhr) {
              console.error('Error updating switch:', xhr.responseText);
          }
      });
  });
  $(document).on('click', '.js-delete-product', function() {
      openProductDeleteModal($(this).data('product-id'), $(this).data('product-name'));
  });
  function moveProductSearchFilter() {
      var searchSlot = $('#product-list-search-slot');
      var tableWrapper = $('#simpletable_wrapper');
      if (!searchSlot.length || !tableWrapper.length) {
          return false;
      }

      var filter = tableWrapper.find('.dt-search, .dataTables_filter').first();
      if (!filter.length) {
          return false;
      }

      searchSlot.append(filter);
      filter.addClass('product-table-search');
      if (!filter.find('.product-table-search-icon').length) {
          filter.prepend('<i class="fa fa-search product-table-search-icon"></i>');
      }
      filter.find('label').each(function() {
          $(this).contents().filter(function() {
              return this.nodeType === 3;
          }).remove();

          if ($(this).find('input').length) {
              $(this).addClass('product-search-label');
          } else {
              $(this).addClass('d-none');
          }
      });
      filter.find('input').attr('placeholder', 'Search products, SKU, barcode');
      return true;
  }
  function selectedProductCheckboxes() {
      return $('.product-row-select:checked');
  }
  function selectedProductIds() {
      return selectedProductCheckboxes().map(function() {
          return $(this).val();
      }).get();
  }
  function showProductBulkMessage(type, message) {
      if (typeof toastAlert === 'function') {
          toastAlert(type, message);
      } else {
          alert(message.replace(/<br>/g, '\n'));
      }
  }
  function updateProductBulkActions() {
      var selectedCount = selectedProductCheckboxes().length;
      $('#product-bulk-actions').toggleClass('d-none', selectedCount <= 0);
      if (selectedCount <= 0) {
          $('#bulk-product-option').val('');
      }

      var visibleCheckboxes = $('.product-row-select:visible');
      var visibleChecked = $('.product-row-select:visible:checked');
      $('#select-all-products').prop('checked', visibleCheckboxes.length > 0 && visibleCheckboxes.length === visibleChecked.length);
  }
  function openSelectedProductsPurchaseOrder(productIds) {
      var supplierIds = [];
      selectedProductCheckboxes().each(function() {
          var supplierId = String($(this).data('supplier-id') || '');
          if (supplierId && supplierId !== '0' && supplierIds.indexOf(supplierId) === -1) {
              supplierIds.push(supplierId);
          }
      });

      if (supplierIds.length !== 1) {
          showProductBulkMessage('warning', 'Please select products from one supplier only.');
          return;
      }

      window.open('<?=url("/admin/purchase-orders/add")?>?product_ids=' + encodeURIComponent(productIds.join(',')), '_blank');
  }
  function openSelectedProductsTransfer(productIds) {
      window.open('<?=url("/admin/products/transfer-selected")?>?product_ids=' + encodeURIComponent(productIds.join(',')), '_blank');
  }
  $(document).ready(function() {
      var searchMoveAttempts = 0;
      var searchMoveTimer = setInterval(function() {
          searchMoveAttempts++;
          if (moveProductSearchFilter() || searchMoveAttempts >= 10) {
              clearInterval(searchMoveTimer);
          }
      }, 250);
  });
  $(document).on('change', '.product-row-select', function() {
      updateProductBulkActions();
  });
  $(document).on('change', '#select-all-products', function() {
      $('.product-row-select:visible').prop('checked', $(this).is(':checked'));
      updateProductBulkActions();
  });
  $(document).on('change', '#bulk-product-option', function() {
      var option = $(this).val();
      var productIds = selectedProductIds();

      if (!option) {
          return;
      }
      if (productIds.length <= 0) {
          showProductBulkMessage('warning', 'Please select at least one product.');
          $(this).val('');
          updateProductBulkActions();
          return;
      }

      if (option === 'purchase_order') {
          openSelectedProductsPurchaseOrder(productIds);
      } else if (option === 'transfer') {
          openSelectedProductsTransfer(productIds);
      } else if (option === 'delete') {
          if (confirm('Are you sure you want to delete the selected product(s)?')) {
              openBulkProductDeleteModal(productIds);
          }
      }

      $(this).val('');
  });
</script>
