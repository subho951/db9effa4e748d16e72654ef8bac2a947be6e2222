<?php
use App\Helpers\Helper;
?>
<div class="auth-premium-shell">
   <div class="card auth-premium-card">
      <div class="auth-premium-top">
         <div class="auth-brand">
            <span class="auth-brand-mark"><?=strtoupper(substr($generalSetting->site_name, 0, 1))?></span>
            <span>
               <span class="auth-brand-name"><?=$generalSetting->site_name?></span>
               <span class="auth-brand-caption">Verification</span>
            </span>
         </div>
         <h1 class="auth-premium-title"><?=$page_header?></h1>
         <p class="auth-premium-subtitle">Enter the 4-digit verification code sent to your registered email.</p>
      </div>
      <div class="auth-premium-body">
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
         <form id="formAuthentication" action="" method="POST">
            @csrf
            <div class="mb-4">
               <label class="form-label">Verification Code</label>
               <div class="auth-pin-grid">
                  <input type="text" class="form-control auth-pin-input" name="otp1" id="box1" maxlength="1" autocomplete="off" autocapitalize="off" spellcheck="false" autofocus onkeypress="return isNumber(event)" oninput="moveToNext(this, 'box2')" required>
                  <input type="text" class="form-control auth-pin-input" name="otp2" id="box2" maxlength="1" autocomplete="off" autocapitalize="off" spellcheck="false" onkeypress="return isNumber(event)" oninput="moveToNext(this, 'box3')" required>
                  <input type="text" class="form-control auth-pin-input" name="otp3" id="box3" maxlength="1" autocomplete="off" autocapitalize="off" spellcheck="false" onkeypress="return isNumber(event)" oninput="moveToNext(this, 'box4')" required>
                  <input type="text" class="form-control auth-pin-input" name="otp4" id="box4" maxlength="1" autocomplete="off" autocapitalize="off" spellcheck="false" onkeypress="return isNumber(event)" required>
               </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-4">
               <a class="auth-secondary-link" href="<?=url('admin/')?>">Back to sign in</a>
               <a class="auth-secondary-link" href="<?=url('admin/resendOtp/' . Helper::encoded($email))?>">Resend code</a>
            </div>
            <button class="btn auth-primary-btn d-grid w-100" type="submit">Verify Code</button>
         </form>
      </div>
   </div>
</div>
