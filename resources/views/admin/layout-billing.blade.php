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
<body class="admin-billing-ui">
    <!-- Loader -->
    <div id="loader">
        <div class="spinner"></div>
    </div>
    <header class="header-box d-flex align-items-center">
        <?=$header?>
    </header>
    <section class="info-body">
        <div class="container-fluid">
            <?php if(session('success_message')){?>
                <div class="alert alert-success alert-dismissible autohide" role="alert">
                <?=session('success_message')?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php }?>
            <?php if(session('error_message')){?>
                <div class="alert alert-danger alert-dismissible autohide" role="alert">
                <?=session('error_message')?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php }?>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function updateClock() {
                let now = new Date();
                let hours = now.getHours().toString().padStart(2, '0');
                let minutes = now.getMinutes().toString().padStart(2, '0');
                let seconds = now.getSeconds().toString().padStart(2, '0');
                $("#clock").text(hours + ":" + minutes + ":" + seconds);
            }

            // Update clock immediately and then every second
            updateClock();
            setInterval(updateClock, 1000);
        });
    </script>
</body>
</html>
