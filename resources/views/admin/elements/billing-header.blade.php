<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div>
                <ul class="d-flex justify-content-between justify-content-sm-end right-bar-list align-items-center">
                    <?php if(session('type') == 'MA'){?>
                        <li>
                            <a href="<?=url('admin/dashboard')?>" class="my-btn btn-sky"><i class="fa fa-arrow-left"></i><span class="d-none d-sm-block"> Back To Dashboard</span></a>
                        </li>
                    <?php }?>
                    <li>
                        <i class="bx bx-time"></i><span id="clock" style="font-weight:bold;"><?=date('h:i:s')?></span>
                    </li>
                    <li>
                        <i class="bx bx-user-circle"></i><span style="font-weight:bold;"><?=session('name')?></span>
                    </li>
                    <?php if(session('type') != 'MA'){?>
                        <li>
                            <a href="<?=url('admin/logout')?>" class="my-btn btn-sky">SIGN OUT <i class="bx bx-log-in"></i></a>
                        </li>
                    <?php } else {?>
                        <li>
                            <a href="javascript:void(0);" class="my-btn btn-sky" onclick="window.close();">CLOSE&nbsp;&nbsp;<i class="fa-solid fa-xmark"></i></a>
                        </li>
                    <?php }?>
                </ul>
            </div>
        </div>
    </div>
</div>