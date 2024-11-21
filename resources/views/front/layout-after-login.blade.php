<?php
   use Illuminate\Support\Facades\Route;;
   $routeName    = Route::current();
   $pageName     = explode("/", $routeName->uri());
   $pageSegment  = $pageName[1];
   $pageFunction = ((count($pageName)>2)?$pageName[2]:'');
   ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <?=$head?>
   </head>
   <body>
      <header class="header-box d-flex align-items-center">
         <?=$header?>
      </header>
      <?=$maincontent?>
      <script src="<?=env('FRONT_ASSETS_URL')?>/assets/vendor/js/bootstrap.js"></script>
      <script src="<?=env('FRONT_ASSETS_URL')?>/assets/vendor/js/script.js"></script>
   </body>
</html>