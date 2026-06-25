<?php
use Illuminate\Support\Facades\Route;;
$routeName    = Route::current();
$pageName     = explode("/", $routeName->uri());
$pageSegment  = $pageName[1];
$pageFunction = ((count($pageName)>2)?$pageName[2]:'');
// dd($routeName);
if(!empty($parameters)){
  if (array_key_exists("id1",$parameters)){
    $pId1 = Helper::decoded($parameters['id1']);
  } else {
    $pId1 = Helper::decoded($parameters['id']);
  }
  if(count($parameters) > 1){
    $pId2 = Helper::decoded($parameters['id2']);
  }
}
$user_type = session('type');
?>
<style type="text/css">
   #layout-menu {
      background: linear-gradient(180deg, #111827 0%, #172033 48%, #0f172a 100%) !important;
      border-right: 1px solid rgba(255, 255, 255, .08);
      box-shadow: 14px 0 35px rgba(15, 23, 42, .16);
   }
   #layout-menu .app-brand {
      min-height: 84px;
      padding: 18px 18px 14px;
      margin-bottom: 4px;
      border-bottom: 1px solid rgba(255, 255, 255, .08);
   }
   #layout-menu .app-brand-link {
      width: 100%;
      display: flex;
      align-items: center;
      gap: 12px;
      color: #fff;
   }
   .brand-mark {
      width: 42px;
      height: 42px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
      color: #fff;
      font-size: 18px;
      font-weight: 800;
      background: linear-gradient(135deg, #2563eb, #0f766e);
      box-shadow: 0 12px 24px rgba(37, 99, 235, .26);
      text-transform: uppercase;
   }
   .brand-copy {
      min-width: 0;
      display: flex;
      flex-direction: column;
      line-height: 1.1;
   }
   #layout-menu .app-brand-text {
      color: #fff !important;
      margin-left: 0 !important;
      font-size: 18px !important;
      letter-spacing: .01em;
      max-width: 170px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
   }
   .brand-caption {
      color: rgba(226, 232, 240, .62);
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .12em;
      margin-top: 5px;
      text-transform: uppercase;
   }
   #layout-menu .layout-menu-toggle {
      color: rgba(255, 255, 255, .76) !important;
      border-radius: 8px;
   }
   #layout-menu .menu-inner {
      padding: 10px 12px 18px !important;
   }
   #layout-menu .menu-inner-shadow {
      display: none;
   }
   #layout-menu .menu-item {
      margin: 3px 0;
   }
   #layout-menu .menu-link {
      min-height: 42px;
      margin: 0;
      border-radius: 8px;
      color: rgba(226, 232, 240, .76) !important;
      font-weight: 600;
      letter-spacing: .01em;
      transition: background-color .18s ease, color .18s ease, transform .18s ease, box-shadow .18s ease;
   }
   #layout-menu .menu-link:hover {
      color: #fff !important;
      background: rgba(255, 255, 255, .08) !important;
      transform: translateX(2px);
   }
   #layout-menu .menu-icon {
      width: 34px;
      height: 34px;
      margin-right: 10px !important;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #cbd5e1 !important;
      background: rgba(255, 255, 255, .07);
      font-size: 14px;
   }
   #layout-menu .menu-item.active > .menu-link,
   #layout-menu .menu-item.open > .menu-link {
      color: #fff !important;
      background: linear-gradient(135deg, rgba(37, 99, 235, .95), rgba(15, 118, 110, .95)) !important;
      box-shadow: 0 12px 24px rgba(15, 118, 110, .24);
   }
   #layout-menu .menu-item.active > .menu-link .menu-icon,
   #layout-menu .menu-item.open > .menu-link .menu-icon {
      color: #fff !important;
      background: rgba(255, 255, 255, .16);
   }
   #layout-menu .menu-toggle::after {
      color: rgba(226, 232, 240, .7) !important;
   }
   #layout-menu .menu-sub {
      margin: 6px 0 8px 17px !important;
      padding: 6px 0 6px 12px !important;
      border-left: 1px solid rgba(148, 163, 184, .24);
      background: transparent !important;
   }
   #layout-menu .menu-sub .menu-item {
      margin: 2px 0;
   }
   #layout-menu .menu-sub .menu-link {
      min-height: 34px;
      padding-left: 14px !important;
      color: rgba(203, 213, 225, .72) !important;
      font-size: 12.5px !important;
      font-weight: 600;
      border-radius: 7px;
   }
   #layout-menu .menu-sub .menu-link::before {
      display: none !important;
   }
   #layout-menu .menu-sub .menu-item.active > .menu-link {
      color: #fff !important;
      background: rgba(255, 255, 255, .11) !important;
      box-shadow: none;
   }
   #layout-menu .menu-inner > .menu-item:nth-last-child(2) {
      margin-top: 12px;
      padding-top: 12px;
      border-top: 1px solid rgba(255, 255, 255, .08);
   }
   #layout-menu .menu-inner > .menu-item:last-child > .menu-link {
      color: #fecaca !important;
   }
   #layout-menu .menu-inner > .menu-item:last-child > .menu-link:hover {
      background: rgba(220, 38, 38, .16) !important;
   }
   #layout-menu .menu-inner::-webkit-scrollbar {
      width: 6px;
   }
   #layout-menu .menu-inner::-webkit-scrollbar-thumb {
      background: rgba(148, 163, 184, .34);
      border-radius: 999px;
   }
   .menu-sub .menu-item .menu-link{
      font-size: 13px;
   }

   .modal-backdrop.show
   {
      z-index: 9;
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
      position: absolute; left: 90px; top: 8px;
  }
  @media(max-width: 767px) {
    div.dt-container div.dt-layout-row {
      display: flex !important;
      width: 100%;
      justify-content: space-between;
      align-items: center;
    }
    .buttons-export {
      top: 11px;
  }
  }
  @media(max-width: 575px) {
    div.dt-container div.dt-layout-row {
      flex-wrap: wrap;
    }
  }
</style>
<div class="app-brand demo">
   <a href="<?=url('admin/dashboard')?>" class="app-brand-link">
      <!-- <span class="app-brand-logo demo">
         <img src="<?=env('UPLOADS_URL')?><?=$generalSetting->site_logo?>">
      </span> -->
      <span class="brand-mark"><?=strtoupper(substr($generalSetting->site_name, 0, 1))?></span>
      <span class="brand-copy">
         <span class="app-brand-text demo menu-text fw-bold"><?=$generalSetting->site_name?></span>
         <span class="brand-caption">Admin Suite</span>
      </span>
   </a>
   <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
   <i class="bx bx-chevron-left bx-sm align-middle"></i>
   </a>
</div>
<div class="menu-inner-shadow"></div>
<ul class="menu-inner py-1">
   <!-- Dashboards -->
   <li class="menu-item <?=(($pageSegment == 'dashboard')?'active':'')?>">
      <a href="<?=url('admin/dashboard')?>" class="menu-link">
         <i class="menu-icon tf-icons fa fa-home"></i>
         <div data-i18n="Dashboard">Dashboard</div>
      </a>
   </li>
   <!-- POS Application -->
   <li class="menu-item <?=(($pageFunction == 'list')?'active':'')?>">
      <a href="<?=url('admin/billing/list')?>" class="menu-link" target="_blank">
         <!-- <i class="menu-icon tf-icons fa fa-home"></i> -->
         <i class="menu-icon fa-solid fa-file-invoice"></i>
         <div data-i18n="POS Application">POS Application</div>
      </a>
   </li>
   <!-- Access & Permission -->
   <li class="menu-item <?=(($pageSegment == 'modules' || $pageSegment == 'roles' || $pageSegment == 'sub-users' || $pageSegment == 'sale-operators')?'open':'')?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
         <i class="menu-icon tf-icons fa fa-lock"></i>
         <div data-i18n="Access & Permission">Access & Permission</div>
      </a>
      <ul class="menu-sub">
         <li class="menu-item <?=(($pageSegment == 'modules')?'active':'')?>">
            <a href="<?=url('admin/modules/list')?>" class="menu-link">
               <div data-i18n="Modules">Modules</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'roles')?'active':'')?>">
            <a href="<?=url('admin/roles/list')?>" class="menu-link">
               <div data-i18n="Roles">Roles</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'sub-users')?'active':'')?>">
            <a href="<?=url('admin/sub-users/list')?>" class="menu-link">
               <div data-i18n="Sub Users">Sub Users</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'sale-operators')?'active':'')?>">
            <a href="<?=url('admin/sale-operators/list')?>" class="menu-link">
               <div data-i18n="Sale Operators">Sale Operators</div>
            </a>
         </li>
      </ul>
   </li>
   <!-- Masters -->
   <li class="menu-item <?=(($pageSegment == 'locations' || $pageSegment == 'brands' || $pageSegment == 'categories' || $pageSegment == 'suppliers' || $pageSegment == 'delivery-locations' || $pageSegment == 'shipping-charges' || $pageSegment == 'coupons' || $pageSegment == 'fast-buttons' || $pageSegment == 'units' || $pageSegment == 'sizes')?'open':'')?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
         <i class="menu-icon tf-icons fa fa-database"></i>
         <div data-i18n="Masters">Masters</div>
      </a>
      <ul class="menu-sub">
         <!-- <li class="menu-item <?=(($pageSegment == 'locations')?'active':'')?>">
            <a href="<?=url('admin/locations/list')?>" class="menu-link">
               <div data-i18n="Locations">Locations</div>
            </a>
         </li> -->
         <li class="menu-item <?=(($pageSegment == 'brands')?'active':'')?>">
            <a href="<?=url('admin/brands/list')?>" class="menu-link">
               <div data-i18n="Brands">Brands</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'categories')?'active':'')?>">
            <a href="<?=url('admin/categories/list')?>" class="menu-link">
               <div data-i18n="Categories">Categories</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'suppliers')?'active':'')?>">
            <a href="<?=url('admin/suppliers/list')?>" class="menu-link">
               <div data-i18n="Suppliers">Suppliers</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'delivery-locations')?'active':'')?>">
            <a href="<?=url('admin/delivery-locations/list')?>" class="menu-link">
               <div data-i18n="Delivery Locations">Delivery Locations</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'shipping-charges')?'active':'')?>">
            <a href="<?=url('admin/shipping-charges/list')?>" class="menu-link">
               <div data-i18n="Shipping Charges">Shipping Charges</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'coupons')?'active':'')?>">
            <a href="<?=url('admin/coupons/list')?>" class="menu-link">
               <div data-i18n="Discount Coupons">Discount Coupons</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'fast-buttons')?'active':'')?>">
            <a href="<?=url('admin/fast-buttons/list')?>" class="menu-link">
               <div data-i18n="Configure Fast Buttons">Configure Fast Buttons</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'units')?'active':'')?>">
            <a href="<?=url('admin/units/list')?>" class="menu-link">
               <div data-i18n="Units">Units</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'sizes')?'active':'')?>">
            <a href="<?=url('admin/sizes/list')?>" class="menu-link">
               <div data-i18n="Sizes">Sizes</div>
            </a>
         </li>
      </ul>
   </li>
   <!-- Products -->
   <li class="menu-item <?=(($pageSegment == 'products' || $pageSegment == 'purchase-orders')?'open':'')?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
         <i class="menu-icon tf-icons fa-solid fa-cart-shopping"></i>
         <div data-i18n="Products">Products</div>
      </a>
      <ul class="menu-sub">
         <li class="menu-item <?=(($pageSegment == 'products')?'active':'')?>">
            <a href="<?=url('admin/products/list')?>" class="menu-link">
               <div data-i18n="List">List</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageFunction == 'upload-product')?'active':'')?>">
            <a href="<?=url('admin/products/upload-product')?>" class="menu-link">
               <div data-i18n="Upload Products">Upload Products</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageFunction == 'generate-product-barcode')?'active':'')?>">
            <a href="<?=url('admin/products/generate-product-barcode')?>" class="menu-link">
               <div data-i18n="Shelf Tags and Discounts">Shelf Tags and Discounts</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageFunction == 'shelf-tag-list')?'active':'')?>">
            <a href="<?=url('admin/products/shelf-tag-list')?>" class="menu-link">
               <div data-i18n="Shelf Tags List">Shelf Tags List</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'purchase-orders')?'active':'')?>">
            <a href="<?=url('admin/purchase-orders/list')?>" class="menu-link">
               <div data-i18n="Purchase Orders">Purchase Orders</div>
            </a>
         </li>
      </ul>
   </li>
   <!-- Stock -->
   <li class="menu-item <?=(($pageSegment == 'stock')?'active':'')?>">
      <a href="<?=url('admin/stock/warehouse-stock')?>" class="menu-link">
         <i class="menu-icon tf-icons fa fa-list-alt"></i>
         <div data-i18n="Stocks">Stocks</div>
      </a>
   </li>
   <!-- Billing -->
   <li class="menu-item <?=(($pageSegment == 'billing')?'open':'')?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
         <i class="menu-icon tf-icons fa fa-list-alt"></i>
         <div data-i18n="Billing">Billing</div>
      </a>
      <ul class="menu-sub">
         <li class="menu-item <?=(($pageFunction == 'list')?'active':'')?>">
            <a href="<?=url('admin/billing/list')?>" class="menu-link">
               <div data-i18n="New">New</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageFunction == 'past-orders')?'active':'')?>">
            <a href="<?=url('admin/billing/past-orders')?>" class="menu-link">
               <div data-i18n="Past Orders">Past Orders</div>
            </a>
         </li>
      </ul>
   </li>
   <!-- Customers -->
   <li class="menu-item <?=(($pageSegment == 'customer')?'active':'')?>">
      <a href="<?=url('admin/customer/list')?>" class="menu-link">
         <i class="menu-icon tf-icons fa fa-users"></i>
         <div data-i18n="Customers">Customers</div>
      </a>
   </li>
   <!-- Reports -->
   <li class="menu-item <?=(($pageSegment == 'report')?'open':'')?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
         <i class="menu-icon tf-icons fa fa-file"></i>
         <div data-i18n="Reports">Reports</div>
      </a>
      <ul class="menu-sub">
         <!-- <li class="menu-item <?=(($pageFunction == 'advance-search-report')?'active':'')?>">
            <a href="<?=url('admin/report/advance-search-report')?>" class="menu-link">
               <div data-i18n="Advance Search Reports">Advance Search Reports</div>
            </a>
         </li> -->
         <li class="menu-item <?=(($pageFunction == 'sale-report')?'active':'')?>">
            <a href="<?=url('admin/report/sale-report')?>" class="menu-link">
               <div data-i18n="Sale Reports">Sale Reports</div>
            </a>
         </li>
      </ul>
   </li>
   <!-- Login Logs -->
   <li class="menu-item <?=(($pageSegment == 'login-logs')?'active':'')?>">
      <a href="<?=url('admin/login-logs')?>" class="menu-link">
         <i class="menu-icon tf-icons fa fa-sign-in"></i>
         <div data-i18n="Login Logs">Login Logs</div>
      </a>
   </li>
   <!-- Email Logs -->
   <li class="menu-item <?=(($pageSegment == 'email-logs')?'active':'')?>">
      <a href="<?=url('admin/email-logs')?>" class="menu-link">
         <i class="menu-icon tf-icons fa fa-envelope"></i>
         <div data-i18n="Email Logs">Email Logs</div>
      </a>
   </li>
   <!-- Settings -->
   <li class="menu-item <?=(($pageSegment == 'settings')?'active':'')?>">
      <a href="<?=url('admin/settings')?>" class="menu-link">
         <i class="menu-icon tf-icons bx bx-cog"></i>
         <div data-i18n="Settings">Settings</div>
      </a>
   </li>
   <!-- Log Out -->
   <li class="menu-item <?=(($pageSegment == 'logout')?'active':'')?>">
      <a href="<?=url('admin/logout')?>" class="menu-link">
         <i class="menu-icon tf-icons bx bx-power-off"></i>
         <div data-i18n="Log Out">Log Out</div>
      </a>
   </li>
</ul>

<!-- Admin PIN Modal -->
<div class="modal fade" id="adminpinmodal" tabindex="-1" aria-labelledby="adminpinmodalLabel" aria-hidden="true">
    
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   function openAdminPINModal(){
      var modalHTML = '';
      var actionurl = '<?=url("/admin/products/validate-admin-pin-product")?>';
      modalHTML = '<div class="modal-dialog modal-dialog-centered">\
                     <div class="modal-content">\
                     <div class="modal-header p-0">\
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\
                     </div>\
                     <form method="POST" action="'+actionurl+'" id="myForm">\
                        @csrf\
                        <input type="hidden" name="key" id="key" value="db9effa4e748d16e72654ef8bac2a947be6e2222">\
                        <input type="text" style="display:none">\
                        <input type="password" style="display:none">\
                        <div class="modal-body">\
                           <h1 class="modal-title fs-4 text-center w-100 text-black mb-2" id="adminpinmodalLabel">Enter Your Admin Pin</h1>\
                           <div class="otp-input-fields">\
                              <input type="password" class="otp__digit otp__field__1" autocomplete="off" name="pin1" id="pin1" autocomplete="new-password" autofocus>\
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

      // Auto focus pin1 AFTER modal renders
      setTimeout(() => {
         $('#pin1').focus();
      }, 300);
   }
</script>
<script>
  $(document).on('input', '.otp__digit', function () {
      // Allow only digits and limit to 1 character
      this.value = this.value.replace(/\D/g, '').slice(0, 1);

      const inputs = $('.otp__digit');
      const index = inputs.index(this);

      // Move to next input automatically
      if (this.value && index < inputs.length - 1) {
          inputs.eq(index + 1).focus();
      }

      // Auto-submit if all 4 digits are filled
      const allFilled = inputs.toArray().every(inp => $(inp).val().length === 1);
      if (allFilled) {
          $('.validate').prop('disabled', true); // prevent double submission
          $('#myForm').submit();
      }
  });

  $(document).on('keyup', '.otp__digit', function (e) {
      const inputs = $('.otp__digit');
      const index = inputs.index(this);

      // Move backward on backspace if empty
      if (e.key === 'Backspace' && !$(this).val() && index > 0) {
          inputs.eq(index - 1).focus();
      }
  });
</script>
