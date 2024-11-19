<?php
use App\Helpers\Helper;
$controllerRoute                = $module['controller_route'];
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
      $location_id                      = $row->location_id;
      $default_charge                   = $row->default_charge;
      $level2_charge                    = $row->level2_charge;
      $level3_charge                    = $row->level3_charge;
      $status                           = $row->status;
    } else {
      $location_id                      = '';
      $default_charge                   = '';
      $level2_charge                    = '';
      $level3_charge                    = '';
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
                 <label for="location_id" class="form-label">Location <small class="text-danger">*</small></label>
                 <select name="location_id" class="form-control" id="location_id" required>
                  <option value="" selected>Select Location</option>
                  <?php if($locations){ foreach($locations as $location){?>
                  <option value="<?=$location->id?>" <?=(($location->id == $location_id)?'selected':'')?>><?=$location->name?></option>
                  <?php } }?>
                </select>
              </div>
              <div class="mb-3 col-md-6">
                 <label for="default_charge" class="form-label">Default Charge <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="default_charge" name="default_charge" value="<?=$default_charge?>" required />
              </div>
              <div class="mb-3 col-md-6">
                 <label for="level2_charge" class="form-label">Level 2 Charge <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="level2_charge" name="level2_charge" value="<?=$level2_charge?>" required />
              </div>
              <div class="mb-3 col-md-6">
                 <label for="level3_charge" class="form-label">Level 3 Charge <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="level3_charge" name="level3_charge" value="<?=$level3_charge?>" required />
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