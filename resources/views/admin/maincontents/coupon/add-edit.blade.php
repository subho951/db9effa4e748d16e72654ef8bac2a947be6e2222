<?php
use App\Helpers\Helper;
$controllerRoute                = $module['controller_route'];
$current_url                    = url()->current();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

<style type="text/css">
    .choices__list--multiple .choices__item {
        background-color: #48974e;
        border: 1px solid #48974e;
    }
</style>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light"><a href="<?=url('admin/dashboard')?>">Dashboard</a> /</span>
    <span class="text-muted fw-light"><a href="<?=url('admin/' . $controllerRoute . '/list/')?>"><?=$module['title']?> List</a> /</span>
    <?=$page_header?>
  </h4>
  <div class="row">
    <?php
    if($row){
      $name                             = $row->name;
      $main_product                     = $row->main_product;
      $discount_nature                  = $row->discount_nature;
      $bundle_products                  = (($row->bundle_products != '')?json_decode($row->bundle_products):[]);
      $discount_type                    = $row->discount_type;
      $discount_amount                  = $row->discount_amount;
      $voucher_code                     = $row->voucher_code;
      $from_date                        = $row->from_date;
      $to_date                          = $row->to_date;
      $status                           = $row->status;
    } else {
      $name                             = '';
      $main_product                     = '';
      $discount_nature                  = 'Voucher';
      $bundle_products                  = [];
      $discount_type                    = '';
      $discount_amount                  = '';
      $voucher_code                     = '';
      $from_date                        = '';
      $to_date                          = '';
      $status                           = '';
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
                 <label for="main_product" class="form-label">Main Product <small class="text-danger">*</small></label>
                 <select name="main_product" class="form-control" id="main_product">
                  <option value="" selected>Select Product</option>
                  <?php if($products){ foreach($products as $product){?>
                  <option value="<?=$product->id?>" <?=(($product->id == $main_product)?'selected':'')?>><?=$product->name?></option>
                  <?php } }?>
                </select>
              </div>

              <div class="mb-3 col-md-6">
                <label for="discount_nature" class="form-label d-block">Discount Nature <small class="text-danger">*</small></label>
                <div class="form-check form-check-inline mt-3">
                  <input name="discount_nature" class="form-check-input" type="radio" value="Voucher" id="discount_nature1" <?=(($discount_nature == 'Voucher')?'checked':'')?> required />
                  <label class="form-check-label" for="discount_nature1">
                    Voucher
                  </label>
                </div>
                <div class="form-check form-check-inline mt-3">
                  <input name="discount_nature" class="form-check-input" type="radio" value="Bundled" id="discount_nature2" <?=(($discount_nature == 'Bundled')?'checked':'')?> required />
                  <label class="form-check-label" for="discount_nature2">
                    Bundled
                  </label>
                </div>
              </div>
              <div class="mb-3 col-md-6" id="voucher_nature">
                 <label for="voucher_code" class="form-label">Voucher Code <small class="text-danger">*</small></label>
                 <input class="form-control no-space" type="text" id="voucher_code" name="voucher_code" value="<?=$voucher_code?>" />
              </div>
              <div class="mb-3 col-md-6" id="bundled_nature" style="display: none;">
                 <label for="choices-multiple-remove-button" class="form-label">Bundled Products</label>
                 <select name="bundle_products[]" class="form-control" id="choices-multiple-remove-button" multiple>
                  <?php if($products){ foreach($products as $product){?>
                  <option value="<?=$product->id?>" <?=((in_array($product->id, $bundle_products))?'selected':'')?>><?=$product->name?></option>
                  <?php } }?>
                </select>
              </div>

              <div class="mb-3 col-md-6">
                <label for="discount_type" class="form-label d-block">Discount Type <small class="text-danger">*</small></label>
                <div class="form-check form-check-inline mt-3">
                  <input name="discount_type" class="form-check-input" type="radio" value="Flat" id="discount_type1" <?=(($discount_type == 'Flat')?'checked':'')?> required />
                  <label class="form-check-label" for="discount_type1">
                    Flat
                  </label>
                </div>
                <div class="form-check form-check-inline mt-3">
                  <input name="discount_type" class="form-check-input" type="radio" value="Percentage" id="discount_type2" <?=(($discount_type == 'Percentage')?'checked':'')?> required />
                  <label class="form-check-label" for="discount_type2">
                    Percentage
                  </label>
                </div>
              </div>
              <div class="mb-3 col-md-6">
                 <label for="discount_amount" class="form-label">Discount Amount <small class="text-danger">*</small></label>
                 <input class="form-control no-space" type="text" id="discount_amount" name="discount_amount" value="<?=$discount_amount?>" required />
              </div>
              
              <div class="mb-3 col-md-3">
                <label for="from_date" class="form-label">From Date <small class="text-danger">*</small></label>
                <input class="form-control" type="date" id="from_date" name="from_date" value="<?=$from_date?>" min="<?=date('Y-m-d')?>" required />
              </div>
              <div class="mb-3 col-md-3">
                <label for="to_date" class="form-label">To Date <small class="text-danger">*</small></label>
                <input class="form-control" type="date" id="to_date" name="to_date" value="<?=$to_date?>" min="<?=date('Y-m-d')?>" required />
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
<script type="text/javascript">
  $(document).ready(function(){    
    var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
        removeItemButton: true,
        maxItemCount:30,
        searchResultLimit:30,
        renderChoiceLimit:30
    });
  });
</script>
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
  $(document).ready(function () {
    // Function to toggle the div based on the selected radio button
    function toggleDiv() {
      const selectedValue = $('input[name="discount_nature"]:checked').val(); // Get the selected radio button value
      if (selectedValue === 'Voucher') {
        $('#voucher_nature').show(); // Show the div
        $('#bundled_nature').hide(); // Hide the div
        $('#voucher_code').attr('required', true);
        $('#choices-multiple-remove-button').attr('required', false);
      } else {
        $('#bundled_nature').show(); // Show the div
        $('#voucher_nature').hide(); // Hide the div
        $('#voucher_code').attr('required', false);
        $('#choices-multiple-remove-button').attr('required', true);
      }
    }

    // Call toggleDiv on page load
    toggleDiv();

    // Attach the onchange event to the radio buttons
    $('input[name="discount_nature"]').on('change', toggleDiv);
  });
</script>