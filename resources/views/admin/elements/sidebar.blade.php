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
   <li class="menu-item <?=(($pageSegment == 'locations' || $pageSegment == 'brands' || $pageSegment == 'suppliers' || $pageSegment == 'shipping-charges' || $pageSegment == 'coupons' || $pageSegment == 'fast-buttons' || $pageSegment == 'units' || $pageSegment == 'sizes')?'open':'')?>">
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
   <li class="menu-item <?=(($pageSegment == 'products')?'active':'')?>">
      <a href="<?=url('admin/products/list')?>" class="menu-link">
         <i class="menu-icon tf-icons fa fa-list-alt"></i>
         <div data-i18n="Products">Products</div>
      </a>
   </li>
   <!-- Login Logs -->
   <li class="menu-item <?=(($pageSegment == 'login-logs')?'active':'')?>">
      <a href="<?=url('admin/login-logs')?>" class="menu-link">
         <i class="menu-icon tf-icons fa fa-home"></i>
         <div data-i18n="Login Logs">Login Logs</div>
      </a>
   </li>
   <!-- Email Logs -->
   <li class="menu-item <?=(($pageSegment == 'email-logs')?'active':'')?>">
      <a href="<?=url('admin/email-logs')?>" class="menu-link">
         <i class="menu-icon tf-icons fa fa-home"></i>
         <div data-i18n="Email Logs">Email Logs</div>
      </a>
   </li>
</ul>