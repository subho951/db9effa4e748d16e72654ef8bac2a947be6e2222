<?php
use App\Helpers\Helper;
$controllerRoute                = $module['controller_route'];
?>
<div class="pagetitle">
  <h1><?=$page_header?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=url('admin/dashboard')?>">Home</a></li>
      <li class="breadcrumb-item active"><a href="<?=url('admin/' . $controllerRoute . '/list/')?>"><?=$module['title']?> List</a></li>
      <li class="breadcrumb-item active"><?=$page_header?></li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section profile">
  <div class="row">
    <div class="col-xl-12">
      @if(session('success_message'))
        <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show autohide" role="alert">
          {{ session('success_message') }}
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      @if(session('error_message'))
        <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show autohide" role="alert">
          {{ session('error_message') }}
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
    </div>
    <?php
    if($row){
      $shipment_type        = $row->shipment_type;
      $type                 = $row->type;
      $customer_id          = $row->customer_id;
      $pol                  = $row->pol;
      $pod                  = $row->pod;
      $booking_date         = $row->booking_date;
    } else {
      $shipment_type        = '';
      $type                 = '';
      $customer_id          = '';
      $pol                  = '';
      $pod                  = '';
      $booking_date         = '';
    }
    ?>
    <div class="col-xl-12">
      <div class="card">
        <div class="card-body pt-3">
          <form method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="row mb-3">
              <label for="shipment_type" class="col-md-2 col-lg-2 col-form-label">Shipment Type</label>
              <div class="col-md-10 col-lg-10">
                  <input type="radio" id="shipment_type1" name="shipment_type" value="Export" <?=(($shipment_type == 'Export')?'checked':'')?>>
                  <label for="shipment_type1">Export</label>
                  <input type="radio" id="shipment_type2" name="shipment_type" value="Import" <?=(($shipment_type == 'Import')?'checked':'')?>>
                  <label for="shipment_type2">Import</label>
              </div>
            </div>
            <div class="row mb-3" id="type-row" style="display: none;">
              <label for="type" class="col-md-2 col-lg-2 col-form-label">Type</label>
              <div class="col-md-10 col-lg-10">
                  <input type="radio" id="type1" name="type" value="FCL" <?=(($type == 'FCL')?'checked':'')?>>
                  <label for="type1">FCL</label>
                  <input type="radio" id="type2" name="type" value="LCL" <?=(($type == 'LCL')?'checked':'')?>>
                  <label for="type2">LCL</label>
                  <input type="radio" id="type3" name="type" value="LCL CO LOAD" <?=(($type == 'LCL CO LOAD')?'checked':'')?>>
                  <label for="type3">LCL CO LOAD</label>
              </div>
            </div>
            <div class="row mb-3">
              <label for="customer_id" class="col-md-2 col-lg-2 col-form-label">Consignee Name</label>
              <div class="col-md-10 col-lg-10">
                <select name="customer_id" class="form-control" id="customer_id" required>
                  <option value="" selected>Select Consignee Name</option>
                  <?php if($customers){ foreach($customers as $customer){?>
                  <option value="<?=$customer->id?>" <?=(($customer->id == $customer_id)?'selected':'')?>><?=$customer->name?></option>
                  <?php } }?>
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <label for="pol" class="col-md-2 col-lg-2 col-form-label">Port Of Loading</label>
              <div class="col-md-10 col-lg-10">
                <select name="pol" class="form-control" id="pol" required>
                  <option value="" selected>Select Port Of Loading</option>
                  <?php if($pols){ foreach($pols as $polRow){?>
                  <option value="<?=$polRow->id?>" <?=(($polRow->id == $pol)?'selected':'')?>><?=$polRow->name?></option>
                  <?php } }?>
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <label for="pod" class="col-md-2 col-lg-2 col-form-label">Port Of Discharge</label>
              <div class="col-md-10 col-lg-10">
                <select name="pod" class="form-control" id="pod" required>
                  <option value="" selected>Select Port Of Loading</option>
                  <?php if($pods){ foreach($pods as $podRow){?>
                  <option value="<?=$podRow->id?>" <?=(($podRow->id == $pod)?'selected':'')?>><?=$podRow->name?></option>
                  <?php } }?>
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <label for="booking_date" class="col-md-2 col-lg-2 col-form-label">Booking Date</label>
              <div class="col-md-10 col-lg-10">
                <input type="date" name="booking_date" class="form-control" id="booking_date" value="<?=$booking_date?>" required>
              </div>
            </div>

            <div class="row mb-3" id="process-flow-dates" style="display:none;">
              <div class="col-md-12">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Options</th>
                      <th>Date Number</th>
                    </tr>
                  </thead>
                  <tbody id="process-flow-html">
                    
                  </tbody>
                </table>
              </div>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary"><?=(($row)?'Save':'Add')?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type="text/javascript">
  $(function(){
    const selectedValue = '<?=$shipment_type?>';
    const selectedValue2 = '<?=$type?>';
    if (selectedValue === "Export") {
      $('#type-row').show();
      $('input[name="type"]').attr('required', true);
    } else {
      $('#type-row').hide();
      $('input[name="type"]').removeAttr('required');
    }

    var base_url        = '<?=url('/')?>';
    $.ajax({
      type: 'POST',
      url: base_url + "/admin/consignment/get-process-flow", // Replace with your server endpoint
      data: {"_token": "{{ csrf_token() }}", shipment_type:selectedValue, selectedValue2:selectedValue2},
      success: function(res) {
        // console.log(res.status);
          // res = $.parseJSON(res);
          if(res.status){
            $('#process-flow-dates').show();
            $('#process-flow-html').html(res.data.processDateHTML);
          } else {
            $('#process-flow-dates').hide();
            $('#process-flow-html').html('');
          }
      },
      error: function(xhr, status, error) {
        console.error('Error:', error); // Handle errors
      }
    });

    $('input[name="shipment_type"]').on('change', function() {
      const selectedValue = $('input[name="shipment_type"]:checked').val();
      if (selectedValue === "Export") {
        $('#type-row').show();
        $('input[name="type"]').attr('required', true);
      } else {
        $('#type-row').hide();
        $('input[name="type"]').removeAttr('required');
      }
      if(selectedValue == 'Import'){
        var base_url        = '<?=url('/')?>';
        $.ajax({
          type: 'POST',
          url: base_url + "/admin/consignment/get-process-flow", // Replace with your server endpoint
          data: {"_token": "{{ csrf_token() }}", shipment_type:selectedValue},
          success: function(res) {
            // console.log(res.status);
              // res = $.parseJSON(res);
              if(res.status){
                $('#process-flow-dates').show();
                $('#process-flow-html').html(res.data.processDateHTML);
              } else {
                $('#process-flow-dates').hide();
                $('#process-flow-html').html('');
              }
          },
          error: function(xhr, status, error) {
            console.error('Error:', error); // Handle errors
          }
        });
      }
    });
    $('input[name="type"]').on('change', function() {
      var base_url        = '<?=url('/')?>';
      const selectedValue = $('input[name="shipment_type"]:checked').val();
      const selectedValue2 = $('input[name="type"]:checked').val();
      $.ajax({
        type: 'POST',
        url: base_url + "/admin/consignment/get-process-flow", // Replace with your server endpoint
        data: {"_token": "{{ csrf_token() }}", shipment_type:selectedValue, selectedValue2:selectedValue2},
        success: function(res) {
          // console.log(res.status);
            // res = $.parseJSON(res);
            if(res.status){
              $('#process-flow-dates').show();
              $('#process-flow-html').html(res.data.processDateHTML);
            } else {
              $('#process-flow-dates').hide();
              $('#process-flow-html').html('');
            }
        },
        error: function(xhr, status, error) {
          console.error('Error:', error); // Handle errors
        }
      });
    });
  })
</script>