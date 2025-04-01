<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div>
                <ul class="d-flex justify-content-end right-bar-list align-items-center flex-wrap">
                    <!-- <li>
                        <a href="<?=url('user/dashboard')?>" class="my-btn btn-sky"><i class="fa fa-arrow-left"></i> Back To Dashboard</a>
                    </li> -->
                    <li>
                        <i class="bx bx-time"></i><span id="clock" style="font-weight:bold;"><?=date('h:i:s')?></span>
                    </li>
                    <li>
                        <i class="bx bx-user-circle"></i><span style="font-weight:bold;"><?=session('name')?></span>
                    </li>
                    <li>
                        <a href="<?=url('user/signout')?>" class="my-btn btn-sky">SIGN OUT <i class="bx bx-log-in"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>