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
    <!-- Loader -->
    <div id="loader">
        <div class="spinner"></div>
    </div>
    <header class="header-box d-flex align-items-center">
        <?=$header?>
    </header>
    <section class="info-body">
        <div class="container-fluid">
            <?=$maincontent?>
        </div>
    </section>
    <script src="<?=env('ADMIN_ASSETS_URL')?>/billing_assets/vendor/js/bootstrap.js"></script>
    <script src="<?=env('ADMIN_ASSETS_URL')?>/billing_assets/vendor/js/script.js"></script>
    <script>
        // Hide loader when page is fully loaded
        window.onload = function() {
            document.getElementById("loader").style.display = "none";
        };
    </script>
</body>
</html>