<div class="auth-premium-shell">
   <div class="card auth-premium-card">
      <div class="auth-premium-top">
         <div class="auth-brand">
            <span class="auth-brand-mark"><?=strtoupper(substr($generalSetting->site_name, 0, 1))?></span>
            <span>
               <span class="auth-brand-name"><?=$generalSetting->site_name?></span>
               <span class="auth-brand-caption">PIN Recovery</span>
            </span>
         </div>
         <h1 class="auth-premium-title"><?=$page_header?></h1>
         <p class="auth-premium-subtitle">Enter your username and we will send a verification code to the registered email.</p>
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
               <label for="email" class="form-label">Username</label>
               <div class="auth-input-icon">
                  <i class="bx bx-user-check"></i>
                  <input type="text" class="form-control" id="email" name="username" placeholder="Enter username" required autofocus>
               </div>
            </div>
            <div class="auth-note mb-4">For security, the reset code is sent only to the email address linked to this admin account.</div>
            <button class="btn auth-primary-btn d-grid w-100" type="submit">Send Verification Code</button>
            <div class="auth-help-row">
               <span>Remembered your PIN?</span>
               <a class="auth-secondary-link" href="<?=url('admin/')?>">Back to sign in</a>
            </div>
         </form>
      </div>
   </div>
</div>
