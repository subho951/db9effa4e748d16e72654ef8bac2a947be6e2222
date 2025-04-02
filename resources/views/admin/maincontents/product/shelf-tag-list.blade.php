<?php
use App\Helpers\Helper;
$controllerRoute      = $module['controller_route'];
$current_url          = url()->current();
?>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light"><a href="<?=url('admin/dashboard')?>">Dashboard</a> /</span> <?=$page_header?>
  </h4>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <div class="dt-responsive table-responsive">
            <table id="simpletable" class="table table-striped table-bordered nowrap">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Sequence No.</th>
                  <th scope="col">File</th>
                  <th scope="col">Send Email</th>
                </tr>
              </thead>
              <tbody>
                <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                  <tr>
                    <th scope="row"><?=$sl++?></th>
                    <td><?=$row->sequence_no?></td>
                    <td><a href="<?=env('UPLOADS_URL') . 'shelf_tags/' . $row->filename?>" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-eye"></i>&nbsp;View File</a></td>
                    <td>
                      <?=(($row->emails != '')?implode(", ", json_decode($row->emails)):'')?>
                      <form method="POST" action="">
                        @csrf
                        <input type="hidden" name="mode" value="send_email">
                        <input type="hidden" name="shilf_tag_id" name="shilf_tag_id" value="<?=$row->id?>">
                        <input type="hidden" name="filename" name="filename" value="<?=$row->filename?>">
                        <div class="form-group">
                          <textarea class="form-control" name="emails" required></textarea>
                          <small style="font-size:10px; color: red;">Enter email by comma separated</small>
                        </div>
                        <div class="form-group">
                          <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-envelope"></i>&nbsp;Send Email</button>
                        </div>
                      </form>
                    </td>
                  </tr>
                <?php } }?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>