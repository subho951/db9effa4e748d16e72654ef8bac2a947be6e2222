<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div>
                <ul class="d-flex justify-content-end right-bar-list align-items-center flex-wrap">
                    <li>
                        <a href="<?=url('admin/dashboard')?>" class="my-btn btn-sky"><i class="fa fa-arrow-left"></i> Back To Dashboard</a>
                    </li>
                    <li>
                        <i class="bx bx-time"></i><?=date('h:i A')?>
                    </li>
                    <li>
                        <i class="bx bx-user-circle"></i><?=session('name')?>
                    </li>
                    <li>
                        <a href="<?=url('admin/logout')?>" class="my-btn btn-sky">LOG OUT <i class="bx bx-log-in"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>