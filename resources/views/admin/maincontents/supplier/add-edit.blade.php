<?php
use App\Helpers\Helper;
$controllerRoute                = $module['controller_route'];
?>
<script>
   let autocomplete;
   let address1Field;
   let address2Field;
   let postalField;
   
   function initAutocomplete() {
   address1Field = document.querySelector("#address");
   address2Field = document.querySelector("#street_no");
   postalField = document.querySelector("#zipcode");
   autocomplete = new google.maps.places.Autocomplete(address1Field, {
   componentRestrictions: { country: ["aus"] },
   fields: ["address_components", "geometry", "formatted_address"],
   types: ["address"],
   });
   // address1Field.focus();
   autocomplete.addListener("place_changed", fillInAddress);
   }
   
   function fillInAddress() {
   const place = autocomplete.getPlace();
   let address1 = "";
   let postcode = "";
   for (const component of place.address_components) {
   const componentType = component.types[0];
   switch (componentType) {
     case "postal_code": {
       postcode = `${component.long_name}${postcode}`;
       break;
     }
     case "postal_code_suffix": {
       postcode = `${postcode}-${component.long_name}`;
       break;
     }
     case "street_number": {
       document.querySelector("#street_no").value = component.long_name;
       break;
     }
     case "route": {
       document.querySelector("#locality").value = component.long_name;
       break;
     }
     case "locality": {
       document.querySelector("#city").value = component.long_name;
       break;
     }
     case "administrative_area_level_1": {
       document.querySelector("#state").value = component.short_name;
       break;
     }
     case "country":
       document.querySelector("#country").value = component.short_name;
       break;
    }
   }
   address1Field.value = place.formatted_address;
   postalField.value = postcode;
   document.querySelector("#latitude").value = place.geometry.location.lat();
   document.querySelector("#longitude").value = place.geometry.location.lng();
   address2Field.focus();
   }
   window.initAutocomplete = initAutocomplete;
</script>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light"><a href="<?=url('admin/dashboard')?>">Dashboard</a> /</span>
    <span class="text-muted fw-light"><a href="<?=url('admin/' . $controllerRoute . '/list/')?>"><?=$module['title']?> List</a> /</span>
    <?=$page_header?>
  </h4>
  <div class="row">
    <?php
    if($row){
      $name                         = $row->name;
      $contact_person_name          = $row->contact_person_name;
      $email                        = $row->email;
      $phone                        = $row->phone;
      $address                      = $row->address;
      $country                      = $row->country;
      $state                        = $row->state;
      $city                         = $row->city;
      $locality                     = $row->locality;
      $street_no                    = $row->street_no;
      $zipcode                      = $row->zipcode;
      $latitude                     = $row->latitude;
      $longitude                    = $row->longitude;
      $status                       = $row->status;
    } else {
      $name                         = '';
      $contact_person_name          = '';
      $email                        = '';
      $phone                        = '';
      $address                      = '';
      $country                      = '';
      $state                        = '';
      $city                         = '';
      $locality                     = '';
      $street_no                    = '';
      $zipcode                      = '';
      $latitude                     = '';
      $longitude                    = '';
      $status                       = '';
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
                 <input class="form-control" type="text" id="name" name="name" value="<?=$name?>" required autofocus />
              </div>
              <div class="mb-3 col-md-6">
                 <label for="contact_person_name" class="form-label">Contact Person Name <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="contact_person_name" name="contact_person_name" value="<?=$contact_person_name?>" required />
              </div>

              <div class="mb-3 col-md-6">
                 <label for="email" class="form-label">Email <small class="text-danger">*</small></label>
                 <input class="form-control" type="email" id="email" name="email" value="<?=$email?>" required />
              </div>
              <div class="mb-3 col-md-6">
                 <label for="phone" class="form-label">Phone <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="phone" name="phone" value="<?=$phone?>" required />
              </div>

              <div class="mb-3 col-md-6">
                 <label for="address" class="form-label">Address <small class="text-danger">*</small></label>
                 <textarea class="form-control" id="address" name="address" required><?=$address?></textarea>
                 <input type="hidden" id="latitude" name="latitude" value="<?=$latitude?>" />
                 <input type="hidden" id="longitude" name="longitude" value="<?=$longitude?>" />
              </div>
              <div class="mb-3 col-md-6">
                 <label for="country" class="form-label">Country <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="country" name="country" value="<?=$country?>" required />
              </div>

              <div class="mb-3 col-md-6">
                 <label for="state" class="form-label">State <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="state" name="state" value="<?=$state?>" required />
              </div>
              <div class="mb-3 col-md-6">
                 <label for="city" class="form-label">City <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="city" name="city" value="<?=$city?>" required />
              </div>

              <div class="mb-3 col-md-6">
                 <label for="locality" class="form-label">Locality</label>
                 <input class="form-control" type="text" id="locality" name="locality" value="<?=$locality?>" />
              </div>
              <div class="mb-3 col-md-6">
                 <label for="street_no" class="form-label">Street No.</label>
                 <input class="form-control" type="text" id="street_no" name="street_no" value="<?=$street_no?>" />
              </div>

              <div class="mb-3 col-md-6">
                 <label for="zipcode" class="form-label">Zipcode <small class="text-danger">*</small></label>
                 <input class="form-control" type="text" id="zipcode" name="zipcode" value="<?=$zipcode?>" required />
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
<?php
  $google_map_api_code = $generalSetting->google_map_api_code;
?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?=$google_map_api_code?>&libraries=places&callback=initAutocomplete&libraries=places&v=weekly"></script>