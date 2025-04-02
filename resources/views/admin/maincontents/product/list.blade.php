<?php
use App\Models\ProductDiscountVoucher;
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
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
    .validate {
      border-radius: 20px;
      height: 40px;
      background-color: #01CA6A;
      border: 1px solid #01CA6A;
      width: 140px
  }
  .buttons-export {
      padding: 2px 20px;
      background-color: #04163d;
      color: #FFF;
      border-radius: 50px;
      border: 2px solid #04163d;
      transition: all .3s ease-in-out;
      box-shadow: 0 9px 20px -10px #a5a5a5;
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
          <h5 class="card-title">
            <a href="<?=url('admin/' . $controllerRoute . '/add/')?>" class="btn btn-outline-success btn-sm float-right">Add <?=$module['title']?></a>
            <a href="<?=url('admin/' . $controllerRoute . '/upload-product/')?>" class="btn btn-outline-success btn-sm float-right">Upload <?=$module['title']?></a>
            <form method="GET" action="" style="border: 1px solid #04163d24;padding: 10px;border-radius: 10px;margin-top: 10px;">
              <input type="hidden" name="mode" value="filter">
              <div class="row">
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
                <div class="col-lg-3 col-md-3">
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i>&nbsp;Submit</button>
                  <?php if($is_search){?>
                    <a href="<?=url('admin/' . $controllerRoute . '/list/')?>" class="btn btn-secondary btn-sm"><i class="fa fa-refresh"></i>&nbsp;Reset</a>
                  <?php }?>
                </div>
              </div>
            </form>
          </h5>
          <div class="dt-responsive table-responsive">
            <button class="dt-button buttons-export" tabindex="0" aria-controls="simpletable" type="button" onclick="openAdminPINModal2();"><span>Export</span></button>
            <table id="simpletable" class="table table-striped table-bordered nowrap">
              <thead>
                <tr>
                  <th scope="col"><?=(($status != '')?'<u>Active</u>':'Active')?></th>
                  <th scope="col">SKU</th>
                  <th scope="col">Barcode</th>
                  <th scope="col">Stock</th>
                  <th scope="col"><?=(($brand_id != '')?'<u>Brand</u>':'Brand')?></th>
                  <th scope="col">Variety</th>
                  <th scope="col">Vol</th>
                  <th scope="col"><?=(($supplier_id != '')?'<u>Supplier</u>':'Supplier')?></th>
                  <th scope="col">Retail</th>
                </tr>
              </thead>
              <tbody>
                <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                  <tr>
                    <td>
                      <?php if($row->status){?>
                        <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-success btn-sm" title="Activate <?=$module['title']?>"><i class="fa fa-check"></i></a>
                      <?php } else {?>
                        <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-warning btn-sm" title="Deactivate <?=$module['title']?>"><i class="fa fa-times"></i></a>
                      <?php }?>
                    </td>
                    <td>
                      <a href="javascript:void(0);" onclick="openAdminPINModal(<?=$row->id?>);"><?=$row->sku?></a>
                    </td>
                    <td><?=$row->barcode?></td>
                    <td><?=$row->shop_stock?></td>
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
                    <td>$<?=number_format($row->retail_price_inc_tax,2)?></td>
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