<div class="auth-premium-shell">
   <div class="card auth-premium-card">
      <div class="auth-premium-top">
         <div class="auth-brand">
            <span class="auth-brand-mark"><?=strtoupper(substr($generalSetting->site_name, 0, 1))?></span>
            <span>
               <span class="auth-brand-name"><?=$generalSetting->site_name?></span>
               <span class="auth-brand-caption">Admin Access</span>
            </span>
         </div>
         <h1 class="auth-premium-title">Welcome Back</h1>
         <p class="auth-premium-subtitle">Sign in with your username and secure 4-digit PIN to continue.</p>
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
                  <i class="bx bx-user"></i>
                  <input type="text" class="form-control" id="email" name="username" placeholder="Enter username" required autofocus>
               </div>
            </div>
            <div class="mb-4">
               <label class="form-label">Admin PIN</label>
               <div class="auth-pin-grid">
                  <input type="password" class="form-control auth-pin-input" name="otp1" id="box1" maxlength="1" autocomplete="off" autocapitalize="off" spellcheck="false" onkeypress="return isNumber(event)" oninput="moveToNext(this, 'box2')" required>
                  <input type="password" class="form-control auth-pin-input" name="otp2" id="box2" maxlength="1" autocomplete="off" autocapitalize="off" spellcheck="false" onkeypress="return isNumber(event)" oninput="moveToNext(this, 'box3')" required>
                  <input type="password" class="form-control auth-pin-input" name="otp3" id="box3" maxlength="1" autocomplete="off" autocapitalize="off" spellcheck="false" onkeypress="return isNumber(event)" oninput="moveToNext(this, 'box4')" required>
                  <input type="password" class="form-control auth-pin-input" name="otp4" id="box4" maxlength="1" autocomplete="off" autocapitalize="off" spellcheck="false" onkeypress="return isNumber(event)" required>
               </div>
            </div>
            <button class="btn auth-primary-btn d-grid w-100" type="submit">Sign In</button>
            <div class="auth-help-row">
               <span>Forgot your PIN?</span>
               <a class="auth-secondary-link" href="<?=url('admin/forgot-password/')?>">Recover access</a>
            </div>
         </form>
      </div>
   </div>
</div>
