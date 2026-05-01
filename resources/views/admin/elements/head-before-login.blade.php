<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
<title><?=$title?></title>
<meta name="description" content="<?=$generalSetting->meta_description?>" />
<meta name="keywords" content="<?=$generalSetting->meta_title?>">
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="<?=env('UPLOADS_URL')?><?=$generalSetting->site_favicon?>" />
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com/">
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/fonts/boxicons.css" />
<!-- Core CSS -->
<link rel="stylesheet" href="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/css/core.css" class="template-customizer-core-css" />
<link rel="stylesheet" href="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
<link rel="stylesheet" href="<?=env('ADMIN_ASSETS_URL')?>/assets/css/demo.css" />
<!-- Vendors CSS -->
<link rel="stylesheet" href="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
<!-- Page CSS -->
<!-- Page -->
<link rel="stylesheet" href="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/css/pages/page-auth.css">
<style>
   body {
      min-height: 100vh;
      background:
         radial-gradient(circle at 14% 18%, rgba(37, 99, 235, .18), transparent 28%),
         radial-gradient(circle at 86% 12%, rgba(15, 118, 110, .18), transparent 30%),
         linear-gradient(135deg, #eef2f7 0%, #f8fafc 46%, #eef6f5 100%);
      font-family: "Public Sans", sans-serif;
   }
   .auth-premium-shell {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 16px;
   }
   .auth-premium-card {
      width: 100%;
      max-width: 440px;
      border: 0;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 24px 70px rgba(15, 23, 42, .16);
   }
   .auth-premium-top {
      padding: 28px 28px 22px;
      background: linear-gradient(135deg, #111827 0%, #24415c 56%, #0f766e 100%);
      color: #fff;
      position: relative;
      overflow: hidden;
   }
   .auth-premium-top::after {
      content: "";
      position: absolute;
      right: -60px;
      bottom: -80px;
      width: 210px;
      height: 210px;
      border-radius: 50%;
      background: rgba(255, 255, 255, .12);
   }
   .auth-brand {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 26px;
   }
   .auth-brand-mark {
      width: 44px;
      height: 44px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      color: #fff;
      background: rgba(255, 255, 255, .16);
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .16);
      text-transform: uppercase;
   }
   .auth-brand-name {
      color: #fff;
      font-size: 18px;
      font-weight: 800;
      line-height: 1.1;
      max-width: 250px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
   }
   .auth-brand-caption {
      color: rgba(226, 232, 240, .72);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
      margin-top: 5px;
   }
   .auth-premium-title {
      position: relative;
      z-index: 1;
      color: #fff;
      margin: 0 0 8px;
      font-size: 25px;
      font-weight: 800;
      letter-spacing: 0;
   }
   .auth-premium-subtitle {
      position: relative;
      z-index: 1;
      color: rgba(226, 232, 240, .78);
      margin: 0;
      line-height: 1.6;
   }
   .auth-premium-body {
      padding: 28px;
      background: #fff;
   }
   .auth-premium-body .form-label {
      font-weight: 700;
      color: #374151;
      margin-bottom: 8px;
   }
   .auth-premium-body .form-control {
      border-radius: 8px;
      border-color: #d9e2ec;
      min-height: 46px;
      color: #111827;
      box-shadow: none;
   }
   .auth-premium-body .form-control:focus {
      border-color: #2563eb !important;
      box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
   }
   .auth-input-icon {
      position: relative;
   }
   .auth-input-icon .form-control {
      padding-left: 44px;
   }
   .auth-input-icon i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 18px;
   }
   .auth-pin-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
   }
   .auth-pin-input {
      height: 58px;
      text-align: center;
      font-size: 24px;
      font-weight: 800;
      padding: 0 !important;
      letter-spacing: 0;
   }
   .auth-primary-btn {
      min-height: 46px;
      border: 0;
      border-radius: 8px;
      color: #fff;
      font-weight: 800;
      background: linear-gradient(135deg, #2563eb, #0f766e);
      box-shadow: 0 14px 28px rgba(15, 118, 110, .22);
   }
   .auth-primary-btn:hover,
   .auth-primary-btn:focus {
      color: #fff !important;
      transform: translateY(-1px);
      box-shadow: 0 16px 32px rgba(15, 118, 110, .28);
   }
   .auth-secondary-link {
      color: #2563eb;
      font-weight: 800;
   }
   .auth-secondary-link:hover {
      color: #0f766e;
   }
   .auth-help-row {
      display: flex;
      justify-content: center;
      gap: 6px;
      color: #64748b;
      font-size: 14px;
      margin-top: 18px;
   }
   .auth-note {
      border: 1px solid #e2e8f0;
      background: #f8fafc;
      border-radius: 8px;
      color: #64748b;
      padding: 12px 14px;
      font-size: 13px;
      line-height: 1.5;
   }
   .auth-premium-body .alert {
      border: 0;
      border-radius: 8px;
      font-weight: 600;
   }
   @media (max-width: 575px) {
      .auth-premium-card {
         max-width: 100%;
      }
      .auth-premium-top,
      .auth-premium-body {
         padding: 24px 20px;
      }
      .auth-pin-grid {
         gap: 8px;
      }
      .auth-pin-input {
         height: 52px;
         font-size: 22px;
      }
   }
</style>
<!-- Helpers -->
<script src="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/js/helpers.js"></script>
<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
<!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
<script src="<?=env('ADMIN_ASSETS_URL')?>/assets/js/config.js"></script>
