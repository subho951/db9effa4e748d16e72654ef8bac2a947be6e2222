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
      componentRestrictions: {
        country: ["aus"]
      },
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
    <span class="text-muted fw-light"><a href="<?= url('admin/dashboard') ?>">Dashboard</a> /</span>
    <span class="text-muted fw-light"><a href="<?= url('admin/' . $controllerRoute . '/list/') ?>"><?= $module['title'] ?> List</a> /</span>
    <?= $page_header ?>
  </h4>
  <div class="row">
    <?php
    if ($row) {
      $supplier_code                = $row->supplier_code;
      $name                         = $row->name;
      $currency                     = $row->currency;
      $primary_lead_time            = $row->primary_lead_time;
      $secondary_lead_time          = $row->secondary_lead_time;
      $notes                        = $row->notes;

      $phone                        = $row->phone;
      $email                        = $row->email;
      $email2                       = $row->email2;
      $fax                          = $row->fax;
      $website                      = $row->website;

      $b_street_address1            = $row->b_street_address1;
      $b_street_address2            = $row->b_street_address2;
      $b_city                       = $row->b_city;
      $b_state                      = $row->b_state;
      $b_postcode                   = $row->b_postcode;
      $b_country                    = $row->b_country;

      $s_street_address1            = $row->s_street_address1;
      $s_street_address2            = $row->s_street_address2;
      $s_city                       = $row->s_city;
      $s_state                      = $row->s_state;
      $s_postcode                   = $row->s_postcode;
      $s_country                    = $row->s_country;
      $status                       = $row->status;
    } else {
      $supplier_code                = '';
      $name                         = '';
      $currency                     = 'Australian Dollar';
      $primary_lead_time            = 0;
      $secondary_lead_time          = 0;
      $notes                        = '';

      $phone                        = '';
      $email                        = '';
      $email2                       = '';
      $fax                          = '';
      $website                      = '';

      $b_street_address1            = '';
      $b_street_address2            = '';
      $b_city                       = '';
      $b_state                      = '';
      $b_postcode                   = '';
      $b_country                    = 'Australia';

      $s_street_address1            = '';
      $s_street_address2            = '';
      $s_city                       = '';
      $s_state                      = '';
      $s_postcode                   = '';
      $s_country                    = 'Australia';
      $status                       = 0;
    }
    ?>
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <small class="text-danger">Star (*) marked fields are mandatory</small>
          <form method="POST" action="" enctype="multipart/form-data">
            @csrf

            <h5>Business Details</h5>
            <div class="row">
              <div class="mb-3 col-md-4">
                <label for="supplier_code" class="form-label">Supplier Code <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="supplier_code" name="supplier_code" value="<?= $supplier_code ?>" required autofocus />
              </div>
              <div class="mb-3 col-md-4">
                <label for="name" class="form-label">Company <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="name" name="name" value="<?= $name ?>" required />
              </div>
              <div class="mb-3 col-md-4">
                <label for="currency" class="form-label">Currency <small class="text-danger">*</small></label>
                <select name="currency" class="form-select" id="currency" required>
                  <option value="" selected>Select Currency</option>
                  <?php if ($couns) {
                    foreach ($couns as $coun) { ?>
                      <option value="<?= $coun->currency_name ?>" <?= (($coun->currency_name == $currency) ? 'selected' : '') ?>><?= $coun->currency_name ?> (<?= $coun->currency_code ?>)</option>
                  <?php }
                  } ?>
                </select>
              </div>

              <div class="mb-3 col-md-2">
                <label for="primary_lead_time" class="form-label">Primary Lead Time <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="primary_lead_time" name="primary_lead_time" value="<?= $primary_lead_time ?>" required />
              </div>
              <div class="mb-3 col-md-2">
                <label for="secondary_lead_time" class="form-label">Secondary Lead Time <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="secondary_lead_time" name="secondary_lead_time" value="<?= $secondary_lead_time ?>" required />
              </div>
              <div class="mb-3 col-md-4">
                <label for="notes" class="form-label">Notes</label>
                <input class="form-control" type="text" id="notes" name="notes" value="<?= $notes ?>" />
              </div>
              <div class="mb-3 col-md-4">
                <label for="username" class="form-label d-block">Status <small class="text-danger">*</small></label>
                <div class="form-check form-check-inline mt-3">
                  <input name="status" class="form-check-input" type="radio" value="1" id="status1" <?= (($status == 1) ? 'checked' : '') ?> required />
                  <label class="form-check-label" for="status1">
                    Active
                  </label>
                </div>
                <div class="form-check form-check-inline mt-3">
                  <input name="status" class="form-check-input" type="radio" value="0" id="status2" <?= (($status == 0) ? 'checked' : '') ?> required />
                  <label class="form-check-label" for="status2">
                    Deactive
                  </label>
                </div>
              </div>
            </div>

            <h5 class="mt-3">Contact Details</h5>
            <div class="row">
              <div class="mb-3 col-md-4">
                <label for="phone" class="form-label">Phone <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="phone" name="phone" value="<?= $phone ?>" required />
              </div>
              <div class="mb-3 col-md-4">
                <label for="email" class="form-label">Email 1<small class="text-danger">*</small></label>
                <input class="form-control" type="email" id="email" name="email" value="<?= $email ?>" required />
              </div>
              <div class="mb-3 col-md-4">
                <label for="email2" class="form-label">Email 2</label>
                <input class="form-control" type="email" id="email2" name="email2" value="<?= $email2 ?>" />
              </div>

              <div class="mb-3 col-md-4">
                <label for="fax" class="form-label">Fax</label>
                <input class="form-control" type="text" id="fax" name="fax" value="<?= $fax ?>" required />
              </div>
              <div class="mb-3 col-md-4">
                <label for="website" class="form-label">Website</label>
                <input class="form-control" type="text" id="website" name="website" value="<?= $website ?>" required />
              </div>
            </div>

            <h5 class="mt-3">Billing Details</h5>
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="b_street_address1" class="form-label">Street Address 1 <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="b_street_address1" name="b_street_address1" value="<?= $b_street_address1 ?>" required />
              </div>
              <div class="mb-3 col-md-6">
                <label for="b_street_address2" class="form-label">Street Address 2</label>
                <input class="form-control" type="text" id="b_street_address2" name="b_street_address2" value="<?= $b_street_address2 ?>" required />
              </div>
              <div class="mb-3 col-md-6">
                <label for="b_city" class="form-label">City <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="b_city" name="b_city" value="<?= $b_city ?>" required />
              </div>

              <div class="mb-3 col-md-6">
                <label for="b_state" class="form-label">State <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="b_state" name="b_state" value="<?= $b_state ?>" required />
              </div>
              <div class="mb-3 col-md-6">
                <label for="b_postcode" class="form-label">Postcode <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="b_postcode" name="b_postcode" value="<?= $b_postcode ?>" required />
              </div>
              <div class="mb-3 col-md-6">
                <label for="b_country" class="form-label">Country <small class="text-danger">*</small></label>
                <select name="b_country" class="form-select" id="b_country" required>
                  <option value="" selected>Select Country</option>
                  <?php if ($couns) {
                    foreach ($couns as $coun) { ?>
                      <option value="<?= $coun->country ?>" <?= (($coun->country == $b_country) ? 'selected' : '') ?>><?= $coun->country ?></option>
                  <?php }
                  } ?>
                </select>
              </div>
            </div>

            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="same_as_billing">
              <label class="form-check-label" for="same_as_billing">
                Shipping address same as billing
              </label>
            </div>

            <h5 class="mt-3">Shipping Details</h5>
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="s_street_address1" class="form-label">Street Address 1 <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="s_street_address1" name="s_street_address1" value="<?= $s_street_address1 ?>" />
              </div>
              <div class="mb-3 col-md-6">
                <label for="s_street_address2" class="form-label">Street Address 2</label>
                <input class="form-control" type="text" id="s_street_address2" name="s_street_address2" value="<?= $s_street_address2 ?>" />
              </div>
              <div class="mb-3 col-md-6">
                <label for="s_city" class="form-label">City <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="s_city" name="s_city" value="<?= $s_city ?>" required />
              </div>

              <div class="mb-3 col-md-6">
                <label for="s_state" class="form-label">State <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="s_state" name="s_state" value="<?= $s_state ?>" required />
              </div>
              <div class="mb-3 col-md-6">
                <label for="s_postcode" class="form-label">Postcode <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="s_postcode" name="s_postcode" value="<?= $s_postcode ?>" required />
              </div>
              <div class="mb-3 col-md-6">
                <label for="s_country" class="form-label">Country <small class="text-danger">*</small></label>
                <select name="s_country" class="form-select" id="s_country" required>
                  <option value="" selected>Select Country</option>
                  <?php if ($couns) {
                    foreach ($couns as $coun) { ?>
                      <option value="<?= $coun->country ?>" <?= (($coun->country == $s_country) ? 'selected' : '') ?>><?= $coun->country ?></option>
                  <?php }
                  } ?>
                </select>
              </div>
            </div>

        </div>
        <div class="mt-2">
          <button type="submit" class="btn btn-primary me-2"><?= (($row) ? 'Save' : 'Add') ?></button>
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
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= $google_map_api_code ?>&libraries=places&callback=initAutocomplete&libraries=places&v=weekly"></script>
<script>
document.getElementById('same_as_billing').addEventListener('change', function () {

    if (this.checked) {
        // COPY billing → shipping
        document.getElementById('s_street_address1').value     = document.getElementById('b_street_address1').value;
        document.getElementById('s_street_address2').value    = document.getElementById('b_street_address2').value;
        document.getElementById('s_city').value  = document.getElementById('b_city').value;
        document.getElementById('s_state').value     = document.getElementById('b_state').value;
        document.getElementById('s_postcode').value    = document.getElementById('b_postcode').value;
        document.getElementById('s_country').value  = document.getElementById('b_country').value;

    } else {
        // CLEAR shipping address
        document.getElementById('s_street_address1').value     = '';
        document.getElementById('s_street_address2').value    = '';
        document.getElementById('s_city').value  = '';
        document.getElementById('s_state').value     = '';
        document.getElementById('s_postcode').value    = '';
        document.getElementById('s_country').value  = '';
    }
});
</script>