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
      $product_id              = $row->product_id;
      $qty                     = $row->qty;
      $status                  = $row->status;
    } else {
      $product_id              = '';
      $qty                     = '';
      $status                  = '';
    }
    ?>
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <small class="text-danger">Star (*) marked fields are mandatory</small>
          <form method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="mb-3 col-md-12">
                 <label for="product_id" class="form-label">Product <small class="text-danger">*</small></label>
                 <select name="product_id" class="form-control" id="product_id" required>
                  <option value="" selected>Select Product</option>
                  <?php if($products){ foreach($products as $product){?>
                  <option value="<?=$product->id?>" <?=(($product->id == $product_id)?'selected':'')?>><?=$product->name?></option>
                  <?php } }?>
                </select>
              </div>
              
              <div class="mb-3 col-md-6">
                 <label for="qty" class="form-label">Quantity <small class="text-danger">*</small></label>
                 <input class="form-control no-space" type="number" min="1" id="qty" name="qty" value="<?=$qty?>" required />
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
      } else {
        $('#bundled_nature').show(); // Show the div
        $('#voucher_nature').hide(); // Hide the div
      }
    }

    // Call toggleDiv on page load
    toggleDiv();

    // Attach the onchange event to the radio buttons
    $('input[name="discount_nature"]').on('change', toggleDiv);
  });
</script>