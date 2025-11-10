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
   .menu-sub .menu-item .menu-link{
      font-size: 13px;
   }
</style>
<div class="app-brand demo ">
   <a href="<?=url('admin/dashboard')?>" class="app-brand-link">
      <!-- <span class="app-brand-logo demo">
         <img src="<?=env('UPLOADS_URL')?><?=$generalSetting->site_logo?>">
      </span> -->
      <span class="app-brand-text demo menu-text fw-bold ms-2" style="text-transform: uppercase;font-size: 23px;"><?=$generalSetting->site_name?></span>
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
   <li class="menu-item <?=(($pageSegment == 'locations' || $pageSegment == 'brands' || $pageSegment == 'suppliers' || $pageSegment == 'delivery-locations' || $pageSegment == 'shipping-charges' || $pageSegment == 'coupons' || $pageSegment == 'fast-buttons' || $pageSegment == 'units' || $pageSegment == 'sizes')?'open':'')?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
         <i class="menu-icon tf-icons fa fa-database"></i>
         <div data-i18n="Masters">Masters</div>
      </a>
      <ul class="menu-sub">
         <li class="menu-item <?=(($pageSegment == 'locations')?'active':'')?>">
            <a href="<?=url('admin/locations/list')?>" class="menu-link">
               <div data-i18n="Locations">Locations</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'brands')?'active':'')?>">
            <a href="<?=url('admin/brands/list')?>" class="menu-link">
               <div data-i18n="Brands">Brands</div>
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
   <li class="menu-item <?=(($pageSegment == 'products')?'open':'')?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
         <i class="menu-icon tf-icons fa-solid fa-cart-shopping"></i>
         <div data-i18n="Products">Products</div>
      </a>
      <ul class="menu-sub">
         <li class="menu-item <?=(($pageFunction == 'list')?'active':'')?>">
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
      </ul>
   </li>
   <!-- Stock -->
   <li class="menu-item <?=(($pageSegment == 'stock')?'open':'')?>">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
         <i class="menu-icon tf-icons fa fa-list-alt"></i>
         <div data-i18n="Stocks">Stocks</div>
      </a>
      <ul class="menu-sub">
         <li class="menu-item <?=(($pageFunction == 'warehouse-stock')?'active':'')?>">
            <a href="<?=url('admin/stock/warehouse-stock')?>" class="menu-link">
               <div data-i18n="Warehouse">Warehouse</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageFunction == 'shop-stock')?'active':'')?>">
            <a href="<?=url('admin/stock/shop-stock')?>" class="menu-link">
               <div data-i18n="Shop">Shop</div>
            </a>
         </li>
      </ul>
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
</ul>