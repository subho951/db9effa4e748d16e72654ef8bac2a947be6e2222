<?php
use App\Helpers\Helper;
$controllerRoute                = $module['controller_route'];
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
<style type="text/css">
    .choices__list--multiple .choices__item {
        background-color: #d81636;
        border: 1px solid #d81636;
    }
    .error { color: red; }
    .badge {
        display: inline-flex;
        align-items: center;
        margin: 2px;
        background-color: #132144;
    }
    .badge .remove {
        cursor: pointer;
        margin-left: 5px;
    }
</style>
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
      $shipment_type                        = $row->shipment_type;
      $type                                 = $row->type;
      $name                                 = $row->name;
      $form_element_type                    = $row->form_element_type;
      $is_notification                      = $row->is_notification;
      $notification_after_booking_date      = $row->notification_after_booking_date;
      $options                              = $row->options;
    } else {
      $shipment_type                        = '';
      $type                                 = '';
      $name                                 = '';
      $form_element_type                    = '';
      $is_notification                      = '';
      $notification_after_booking_date      = '';
      $options                              = '';
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
              <label for="name" class="col-md-2 col-lg-2 col-form-label">Name</label>
              <div class="col-md-10 col-lg-10">
                <textarea name="name" class="form-control" id="name" required><?=$name?></textarea>
              </div>
            </div>
            <div class="row mb-3">
              <label for="form_element_type" class="col-md-2 col-lg-2 col-form-label">Form Element Type</label>
              <div class="col-md-10 col-lg-10">
                <select name="form_element_type" class="form-control" id="form_element_type" required>
                  <option value="textbox">textbox</option>
                  <option value="datebox">datebox</option>
                  <option value="select">select</option>
                  <option value="checkbox">checkbox</option>
                  <option value="radio">radio</option>
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <label for="options" class="col-md-2 col-lg-2 col-form-label">Options</label>
              <div class="col-md-10 col-lg-10">
                <input type="text" id="input-tags" class="form-control">
                <textarea class="form-control" name="options" id="options" style="display:none;"><?=$options?></textarea>
                <small class="text-primary">Enter options with comma separated</small>
                <div id="badge-container">
                    <?php
                    if($options != ''){
                        $deal_keywords = explode(",", $options);
                        if(!empty($deal_keywords)){
                        for($k=0;$k<count($deal_keywords);$k++){
                    ?>
                        <span class="badge"><?=$deal_keywords[$k]?> <span class="remove" data-tag="<?=$deal_keywords[$k]?>">&times;</span></span>
                    <?php } }
                    }
                    ?>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <label for="is_notification" class="col-md-2 col-lg-2 col-form-label">Is Date Notification</label>
              <div class="col-md-10 col-lg-10">
                  <input type="radio" id="is_notification1" name="is_notification" value="1" <?=(($is_notification == 1)?'checked':'')?>>
                  <label for="is_notification1">YES</label>
                  <input type="radio" id="is_notification2" name="is_notification" value="0" <?=(($is_notification == 0)?'checked':'')?>>
                  <label for="is_notification2">NO</label>
              </div>
            </div>
            <div class="row mb-3" id="noti-date-num" style="display:none;">
              <label for="notification_after_booking_date" class="col-md-2 col-lg-2 col-form-label">Notification After Booking Date</label>
              <div class="col-md-10 col-lg-10">
                <input type="number" name="notification_after_booking_date" class="form-control" id="notification_after_booking_date" value="<?=$notification_after_booking_date?>">
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
  $(document).ready(function() {
    var tagsArray = [];
    var beforeData = $('#options').val();
    if(beforeData.length > 0){
      tagsArray = beforeData.split(',');
    }
    $('#input-tags').on('input', function() {
        var input = $(this).val();
        if (input.includes(',')) {
            var tags = input.split(',');
            tags.forEach(function(tag) {
                tag = tag.trim();
                if (tag.length > 0 && !tagsArray.includes(tag)) {
                    tagsArray.push(tag);
                    $('#badge-container').append(
                        '<span class="badge">' + tag + ' <span class="remove" data-tag="' + tag + '">&times;</span></span>'
                    );
                }
            });
            $('#options').val(tagsArray);
            // console.log(tagsArray);
            $(this).val('');
        }
    });
    // console.log(tagsArray);
    $(document).on('click', '.remove', function() {
        var tag = $(this).data('tag');
        tagsArray = tagsArray.filter(function(item) {
            return item !== tag;
        });
        $(this).parent().remove();
        $('#keywords').val(tagsArray);
        // console.log(tagsArray);
    });
  });
</script>
<script type="text/javascript">
  $(function(){
    const selectedValue = '<?=$shipment_type?>';
    if (selectedValue === "Export") {
      $('#type-row').show();
      $('input[name="type"]').attr('required', true);
    } else {
      $('#type-row').hide();
      $('input[name="type"]').removeAttr('required');
    }

    const is_notification = '<?=$is_notification?>';
    if (is_notification == 1) {
      $('#noti-date-num').show();
      $('input[name="notification_after_booking_date"]').attr('required', true);
    } else {
      $('#noti-date-num').hide();
      $('input[name="notification_after_booking_date"]').removeAttr('required');
    }

    $('input[name="shipment_type"]').on('change', function() {
      const selectedValue = $('input[name="shipment_type"]:checked').val();
      if (selectedValue === "Export") {
        $('#type-row').show();
        $('input[name="type"]').attr('required', true);
      } else {
        $('#type-row').hide();
        $('input[name="type"]').removeAttr('required');
      }
    });

    $('input[name="is_notification"]').on('change', function() {
      const is_notification = $('input[name="is_notification"]:checked').val();
      if (is_notification == 1) {
        $('#noti-date-num').show();
        $('input[name="notification_after_booking_date"]').attr('required', true);
      } else {
        $('#noti-date-num').hide();
        $('input[name="notification_after_booking_date"]').removeAttr('required');
      }
    });
  })
</script>