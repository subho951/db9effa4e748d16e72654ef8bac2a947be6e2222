<?php
use App\Models\ProductDiscountVoucher;
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
<style>
    #checkAllBtn:hover, #uncheckAllBtn:hover {
        background-color: #f0f0f0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 10px;
        text-align: left;
    }
    input[type="checkbox"] {
        cursor: pointer;
    }
</style>
<style type="text/css">
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
  table>tbody>tr>th, table>tbody>tr>td {
      padding: 1px 5px !important;
      font-size: 12px !important;
  }
</style>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light"><a href="<?=url('admin/dashboard')?>">Dashboard</a> /</span> <?=$page_header?>
  </h4>
  <div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="" enctype="multipart/form-data">
                    <input type="hidden" name="mode" value="product_search">
                    @csrf
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
                          <select class="form-control" name="discount_type">
                            <option value="" selected>Select Discount Type</option>
                            <option value="Percentage" <?=(($discount_type == 'Percentage')?'selected':'')?>>Percentage</option>
                            <option value="Flat" <?=(($discount_type == 'Flat')?'selected':'')?>>Flat</option>
                          </select>
                        </div>
                        <div class="col-lg-3 col-md-3">
                          <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i>&nbsp;Submit</button>
                          <?php if($is_search){?>
                            <a href="<?=url('admin/' . $controllerRoute . '/generate-product-barcode/')?>" class="btn btn-secondary btn-sm"><i class="fa fa-refresh"></i>&nbsp;Reset</a>
                          <?php }?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php if(count($rows)>0){?>
            <div class="card mt-3">
              <div class="card-body">
                <div class="dt-responsive table-responsive mt-3">
                  <!-- Check All / Uncheck All Button -->
                  <button id="checkAllBtn" class="btn btn-primary btn-sm">Check All</button>
                  <button id="uncheckAllBtn" class="btn btn-primary btn-sm">Uncheck All</button>
                  <form method="POST" action="<?=url('admin/products/print-products')?>" enctype="multipart/form-data">
                    <div class="mt-2">
                      <button type="submit" class="btn btn-info btn-sm mt-2 mb-2 print-btn" style="display: none;float: right;"><i class="fa fa-print"></i>&nbsp;Print</button>
                    </div>
                    <input type="hidden" name="mode" value="product_search">
                    @csrf
                    <table class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                            <!-- <th scope="col">#<br><input type="checkbox" id="checkAll"></th> -->
                            <th scope="col">SKU</th>
                            <th scope="col">Tag</th>
                            <th scope="col"><?=(($brand_id != '')?'<u>Brand</u>':'Brand')?></th>
                            <th scope="col">Variety</th>
                            <th scope="col">Vol</th>
                            <th scope="col">Supplier</th>
                            <th scope="col">DiscCode</th>
                            <th scope="col"><?=(($discount_type != '')?'<u>DiscType</u>':'DiscType')?></th>
                            <th scope="col">Disc</th>
                            <th scope="col">SellDisc</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                                <tr>
                                    <td scope="row"><a href="javascript:void(0);" onclick="openAdminPINModal(<?=$row->id?>);"><?=$row->sku?></a></td>
                                    <td><input type="checkbox" class="checkItem" name="product_id[]" value="<?=$row->id?>"></td>
                                    <td><?=$row->brand_name?></td>
                                    <td>
                                      <?php
                                      $discountVouchers = ProductDiscountVoucher::select('voucher_code', 'retail_discounted_price')->where('product_id', $row->id)->where('status', 1)->get();
                                      ?>
                                      <ul>
                                        <?php if($discountVouchers){ foreach($discountVouchers as $discountVoucher){?>
                                          <li><?=$discountVoucher->voucher_code?> : $<?=number_format($discountVoucher->retail_discounted_price,2)?></li>
                                        <?php } }?>
                                      </ul>
                                    </td>
                                    <td><?=$row->size_name?> <?=$row->unit_name?></td>
                                    <td><?=$row->supplier_name?></td>
                                    <td colspan="4">
                                      <table class="table table-striped table-bordered nowrap">
                                        <?php
                                        $discountVouchers = ProductDiscountVoucher::select('voucher_code', 'discount_type', 'retail_discount', 'retail_discounted_price')->where('product_id', $row->id)->where('status', 1)->get();
                                        if($discountVouchers){ foreach($discountVouchers as $discountVoucher){
                                        ?>
                                          <tr>
                                            <td><?=$discountVoucher->voucher_code?></td>
                                            <td><?=$discountVoucher->discount_type?></td>
                                            <td><?=$discountVoucher->retail_discount?></td>
                                            <td><?=$discountVoucher->retail_discounted_price?></td>
                                          </tr>
                                        <?php } }?>
                                      </table>
                                    </td>
                                </tr>
                            <?php } }?>
                        </tbody>
                    </table>
                    <div class="mt-2">
                      <button type="submit" class="btn btn-info btn-sm mt-2 mb-2 print-btn" style="display: none;float: right;"><i class="fa fa-print"></i>&nbsp;Print</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        <?php }?>
    </div>
  </div>
</div>
<!-- Admin PIN Modal -->
<div class="modal fade" id="adminpinmodal" tabindex="-1" aria-labelledby="adminpinmodalLabel" aria-hidden="true">
    
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Check All Functionality
    $('#checkAll').on('change', function() {
        $('.checkItem').prop('checked', $(this).prop('checked'));
        $('.print-btn').show();
    });

    // Check All Button
    $('#checkAllBtn').on('click', function() {
        $('#checkAll').prop('checked', true);  // Check the main checkbox
        $('.checkItem').prop('checked', true); // Check all individual checkboxes
        $('.print-btn').show();
    });

    // Uncheck All Button
    $('#uncheckAllBtn').on('click', function() {
        $('#checkAll').prop('checked', false);  // Uncheck the main checkbox
        $('.checkItem').prop('checked', false); // Uncheck all individual checkboxes
        $('.print-btn').hide();
    });
</script>
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
                      <div class="modal-body">\
                          <h1 class="modal-title fs-4 text-center w-100 text-black mb-2" id="adminpinmodalLabel">Enter Your Admin Pin</h1>\
                          <div class="otp-input-fields">\
                            <input type="password" class="otp__digit otp__field__1" autocomplete="off" name="pin1" id="pin1">\
                            <input type="password" class="otp__digit otp__field__2" autocomplete="off" name="pin2" id="pin2">\
                            <input type="password" class="otp__digit otp__field__3" autocomplete="off" name="pin3" id="pin3">\
                            <input type="password" class="otp__digit otp__field__4" autocomplete="off" name="pin4" id="pin4">\
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
                            <input type="password" class="otp__digit otp__field__1" autocomplete="off" name="pin1" id="pin1">\
                            <input type="password" class="otp__digit otp__field__2" autocomplete="off" name="pin2" id="pin2">\
                            <input type="password" class="otp__digit otp__field__3" autocomplete="off" name="pin3" id="pin3">\
                            <input type="password" class="otp__digit otp__field__4" autocomplete="off" name="pin4" id="pin4">\
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
  $(function(){
    <?php if(session('is_export')){?>
      setTimeout(function hideText() {
          $('.buttons-excel').show();
          $('.buttons-pdf').show();
      }, 1000);
      $('.buttons-export').css('display', 'none');
    <?php } else {?>
      setTimeout(function hideText() {
          $('.buttons-excel').css('display', 'none');
          $('.buttons-pdf').css('display', 'none');
      }, 1000);
      $('.buttons-export').show();
    <?php } ?>
  })
</script>