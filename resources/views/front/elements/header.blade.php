<div class="container-fluid">
   <div class="row">
      <div class="col-md-12">
         <div>
            <ul class="d-flex justify-content-end right-bar-list align-items-center">
               <li>
                  <i class="bx bx-time"></i><?=date('h:i A')?>
               </li>
               <li>
                  <i class="bx bx-user-circle"></i><?=session('name')?><br>
                  <?=session('username')?>
               </li>
               <li>
                  <a href="<?=url('user/signout')?>" class="my-btn btn-sky">LOG OUT <i class="bx bx-log-in"></i></a>
               </li>
            </ul>
         </div>
      </div>
   </div>
</div>