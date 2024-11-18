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
   <a href="index-2.html" class="app-brand-link">
      <span class="app-brand-logo demo">
         <img src="<?=env('UPLOADS_URL')?><?=$generalSetting->site_logo?>">
      </span>
      <!-- <span class="app-brand-text demo menu-text fw-bold ms-2"><?=$generalSetting->site_name?></span> -->
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
   <!-- Masters -->
   <li class="menu-item <?=(($pageSegment == 'locations' || $pageSegment == 'brands' || $pageSegment == 'suppliers' || $pageSegment == 'shipping-charges' || $pageSegment == 'coupons' || $pageSegment == 'fast-buttons')?'open':'')?>">
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
            <a href="javascript:void(0);" class="menu-link">
               <div data-i18n="Discount Coupons">Discount Coupons</div>
            </a>
         </li>
         <li class="menu-item <?=(($pageSegment == 'fast-buttons')?'active':'')?>">
            <a href="javascript:void(0);" class="menu-link">
               <div data-i18n="Configuure Fast Buttons">Configuure Fast Buttons</div>
            </a>
         </li>
      </ul>
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