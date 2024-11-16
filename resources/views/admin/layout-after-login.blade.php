<?php
   use Illuminate\Support\Facades\Route;;
   $routeName    = Route::current();
   $pageName     = explode("/", $routeName->uri());
   $pageSegment  = $pageName[1];
   $pageFunction = ((count($pageName)>2)?$pageName[2]:'');
   ?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact " dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
   <head>
      <?=$head?>
   </head>
   <body>
      <!-- Layout wrapper -->
      <div class="layout-wrapper layout-content-navbar">
         <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" style="background-color: <?=$generalSetting->header_color?> !important;">
               <?=$sidebar?>
            </aside>
            <!-- / Menu -->
            <!-- Layout container -->
            <div class="layout-page">
               <!-- Navbar -->
               <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar" style="background-color: <?=$generalSetting->sidebar_color?> !important;">
                  <?=$header?>
               </nav>
               <!-- / Navbar -->
               <!-- Content wrapper -->
               <div class="content-wrapper">
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
                  <!-- Content -->
                  <?=$maincontent?>
                  <!-- / Content -->
                  <!-- Footer -->
                  <footer class="content-footer footer bg-footer-theme">
                     <?=$footer?>
                  </footer>
                  <!-- / Footer -->
                  <div class="content-backdrop fade"></div>
               </div>
               <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
         </div>
         <!-- Overlay -->
         <div class="layout-overlay layout-menu-toggle"></div>
      </div>
      <!-- / Layout wrapper -->
      <!-- Core JS -->
      <!-- build:js assets/vendor/js/core.js -->
      <script src="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/libs/jquery/jquery.js"></script>
      <script src="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/libs/popper/popper.js"></script>
      <script src="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/js/bootstrap.js"></script>
      <script src="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
      <script src="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/js/menu.js"></script>
      <!-- endbuild -->
      <!-- Vendors JS -->
      <script src="<?=env('ADMIN_ASSETS_URL')?>/assets/vendor/libs/apex-charts/apexcharts.js"></script>
      <!-- Main JS -->
      <script src="<?=env('ADMIN_ASSETS_URL')?>/assets/js/main.js"></script>
      <!-- Page JS -->
      <script src="<?=env('ADMIN_ASSETS_URL')?>/assets/js/dashboards-analytics.js"></script>
      <!-- Place this tag in your head or just before your close body tag. -->
      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
      <script>
         $(function(){
            $('.autohide').delay(5000).fadeOut('slow');
         })
         function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
         }
      </script>
      <script>
         function moveToNext(currentBox, nextBoxId) {
            if (currentBox.value.length === currentBox.maxLength) {
               document.getElementById(nextBoxId).focus();
            }
         }
      </script>
      <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.1.0/ckeditor5.css" />
      <script type="importmap">
        {
            "imports": {
                "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.1.0/ckeditor5.js",
                "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.1.0/"
            }
        }
      </script>
      <script type="module">
        import {
            ClassicEditor,
            Essentials,
            Bold,
            Italic,
            Strikethrough,
            Subscript,
            Superscript,
            CodeBlock,
            Font,
            Link,
            List,
            Paragraph,
            Image,
            ImageCaption,
            ImageResize,
            ImageStyle,
            ImageToolbar,
            LinkImage,
            PictureEditing,
            ImageUpload,
            CloudServices,
            CKBox,
            CKBoxImageEdit,
            SourceEditing,
            ImageInsert
        } from 'ckeditor5';

        for (let i = 0; i <= 15; i++) {
          ClassicEditor
            .create( document.querySelector( '#ckeditor' + i ), {
              plugins: [ Essentials, Bold, Italic, Strikethrough, Subscript, Superscript, CodeBlock, Font, Link, List, Paragraph, Image, ImageToolbar, ImageCaption, ImageStyle, ImageResize, LinkImage, PictureEditing, ImageUpload, CloudServices, CKBox, CKBoxImageEdit, SourceEditing, ImageInsert ],
              toolbar: {
                items: [
                  'undo', 'redo',
                  '|',
                  'heading',
                  '|',
                  'sourceEditing',
                  '|',
                  'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor', 'formatPainter',
                  '|',
                  'bold', 'italic', 'strikethrough', 'subscript', 'superscript', 'code',
                  '|',
                  'link', 'uploadImage', 'blockQuote', 'codeBlock',
                  '|',
                  'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent',
                  '|',
                  'ckbox', 'ckboxImageEdit', 'toggleImageCaption', 'imageTextAlternative', 'ckboxImageEdit',
                  '|',
                  'imageStyle:block',
                  'imageStyle:side',
                  '|',
                  'toggleImageCaption',
                  'imageTextAlternative',
                  '|',
                  'linkImage', 'insertImage', 'insertImageViaUrl'
                ]
              },
              menuBar: {
                isVisible: true
              }
            })
            .then( /* ... */ )
            .catch( /* ... */ );
        }
      </script>
   </body>
</html>
<!-- beautify ignore:end -->