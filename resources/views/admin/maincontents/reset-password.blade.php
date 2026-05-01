<div class="auth-premium-shell">
   <div class="card auth-premium-card">
      <div class="auth-premium-top">
         <div class="auth-brand">
            <span class="auth-brand-mark"><?=strtoupper(substr($generalSetting->site_name, 0, 1))?></span>
            <span>
               <span class="auth-brand-name"><?=$generalSetting->site_name?></span>
               <span class="auth-brand-caption">Secure Reset</span>
            </span>
         </div>
         <h1 class="auth-premium-title"><?=$page_header?></h1>
         <p class="auth-premium-subtitle">Create a new 4-digit admin PIN for this account.</p>
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
               <label for="new_password" class="form-label">New PIN</label>
               <div class="auth-input-icon">
                  <i class="bx bx-lock-alt"></i>
                  <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter 4-digit PIN" maxlength="4" minlength="4" onkeypress="return isNumber(event)" required autofocus>
               </div>
            </div>
            <div class="mb-4">
               <label for="confirm_password" class="form-label">Confirm PIN</label>
               <div class="auth-input-icon">
                  <i class="bx bx-check-shield"></i>
                  <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm 4-digit PIN" maxlength="4" minlength="4" onkeypress="return isNumber(event)" required>
               </div>
            </div>
            <div class="auth-note mb-4">Choose a PIN that is easy for authorized users to remember and difficult for others to guess.</div>
            <button class="btn auth-primary-btn d-grid w-100" type="submit">Reset PIN</button>
         </form>
      </div>
   </div>
</div>
