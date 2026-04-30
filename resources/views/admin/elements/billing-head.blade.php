<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
<title><?=$title?></title>
<meta name="description" content="<?=$generalSetting->meta_description?>" />
<meta name="keywords" content="<?=$generalSetting->meta_title?>">
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="<?=env('UPLOADS_URL')?><?=$generalSetting->site_favicon?>" />
<link href="<?=env('ADMIN_ASSETS_URL')?>/billing_assets/vendor/css/core.css" rel="stylesheet">
<link rel="stylesheet" href="<?=env('ADMIN_ASSETS_URL')?>/billing_assets/vendor/fonts/boxicons.css">
<link rel="stylesheet" href="<?=env('ADMIN_ASSETS_URL')?>/billing_assets/vendor/css/all.min.css">
<link rel="stylesheet" href="<?=env('ADMIN_ASSETS_URL')?>/billing_assets/vendor/css/style.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
<style type="text/css">
	.toast-success {
	  background-color: #000;
	  color: #28a745 !important;
	}
	.toast-error {
	  background-color: #000;
	  color: #dc3545 !important;
	}
	.toast-warning {
	  background-color: #000;
	  color: #ffc107 !important;
	}
	.toast-info {
	  background-color: #000;
	  color: #007bff !important;
	}
    .toast-bottom-center {
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
    }
</style>
<style>
    /* Loader Styling */
    #loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #ccc;
        border-top: 5px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
<!-- Main jQuery -->
<script src="https://market.ecoex.market/inc/js/jquery-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
<script type="text/javascript">
    function toastAlert(type, message, redirectStatus = false, redirectUrl = ''){
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-bottom-center",
            "preventDuplicates": false,
            "showDuration": "5000",
            "hideDuration": "1000000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
        toastr[type](message);
        if(redirectStatus){        
            setTimeout(function(){ window.location = redirectUrl; }, 5000);
        }
    }
</script>