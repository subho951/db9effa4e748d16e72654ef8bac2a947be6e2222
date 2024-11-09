<?php
use App\Models\ProcessFlow;
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
<div class="pagetitle">
  <h1><?=$page_header?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=url('admin/dashboard')?>">Home</a></li>
      <li class="breadcrumb-item active"><?=$page_header?></li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
  <div class="row">
    <div class="col-xl-12">
      @if(session('success_message'))
        <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show autohide" role="alert">
          {{ session('success_message') }}
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      @if(session('error_message'))
        <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show autohide" role="alert">
          {{ session('error_message') }}
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
    </div>
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Consignment No.</h5>
                <span><?=(($row)?$row->consignment_no:'')?></span>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Customer Name</h5>
                <span><?=(($row)?$row->customer_name:'')?></span>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Date Of Booking</h5>
                <span><?=(($row)?date_format(date_create($row->booking_date), "M d, Y"):'')?></span>
              </div>
            </div>

            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Type</h5>
                <span><?=(($row)?$row->shipment_type . ''. (($row->type != '')?'('.$row->type.')':''):'')?></span>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Port Of Loading</h5>
                <span><?=(($row)?$row->pol_name:'')?></span>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div style="background-color: beige;padding: 10px;">
                <h5>Port Of Dispatch</h5>
                <span><?=(($row)?$row->pod_name:'')?></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 mt-3">
              <form method="POST" action="">
                @csrf
                <input type="hidden" name="consignment_id" value="<?=(($row)?$row->id:'')?>">
                <table class="table table-striped table-bordered nowrap">
                  <thead>
                    <tr>
                      <th scope="col">Process Flow Name</th>
                      <th scope="col">Date Of Booking</th>
                      <th scope="col">Last Fillup Date</th>
                      <th scope="col">Input Value</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if($consignmentDetails){ foreach($consignmentDetails as $consignmentDetail){?>
                      <?php
                      $getProcessFlow = ProcessFlow::select('name', 'form_element_type', 'options')->where('id', '=', $consignmentDetail->process_flow_id)->first();
                      ?>
                      <tr>
                        <td>
                          <b><?=(($getProcessFlow)?$getProcessFlow->name:'')?></b>
                          <input type="hidden" name="process_flow_id[]" value="<?=$consignmentDetail->process_flow_id?>">
                        </td>
                        <td><?=date_format(date_create($consignmentDetail->booking_date), "M d, Y")?></td>
                        <td><?=date_format(date_create($consignmentDetail->notification_date), "M d, Y")?></td>
                        <td>
                          <?php if($getProcessFlow){?>
                            <?php if($getProcessFlow->form_element_type == 'textbox'){?>
                              <input type="text" class="form-control" name="input_value[<?=$consignmentDetail->process_flow_id?>]" value="<?=$consignmentDetail->input_value?>" placeholder="Enter <?=$getProcessFlow->name?>">
                            <?php }?>
                            <?php if($getProcessFlow->form_element_type == 'select'){?>
                              <?php
                              $options = explode(',', $getProcessFlow->options);
                              ?>
                              <select class="form-control" name="input_value[<?=$consignmentDetail->process_flow_id?>]">
                                <option value="" selected>Select</option>
                                <?php if(!empty($options)){ for($s=0;$s<count($options);$s++){?>
                                  <option value="<?=$options[$s]?>" <?=(($options[$s] == $consignmentDetail->input_value)?'selected':'')?>><?=$options[$s]?></option>
                                <?php } }?>
                              </select>
                            <?php }?>
                            <?php if($getProcessFlow->form_element_type == 'checkbox'){?>
                              <input type="checkbox" name="input_value[<?=$consignmentDetail->process_flow_id?>]" value="<?=$getProcessFlow->name?>" <?=(($consignmentDetail->input_value == $getProcessFlow->name)?'checked':'')?>> <?=$getProcessFlow->name?>
                            <?php }?>
                            <?php if($getProcessFlow->form_element_type == 'radio'){?>
                              <?php
                              $options = explode(',', $getProcessFlow->options);
                              ?>
                              <?php if(!empty($options)){ for($m=0;$m<count($options);$m++){?>
                                <input type="radio" name="input_value[<?=$consignmentDetail->process_flow_id?>]" value="<?=$options[$m]?>" <?=(($consignmentDetail->input_value == $options[$m])?'checked':'')?>> <?=$options[$m]?>
                              <?php } }?>
                            <?php }?>
                            <?php if($getProcessFlow->form_element_type == 'datebox'){?>
                              <input type="date" class="form-control" name="input_value[<?=$consignmentDetail->process_flow_id?>]" value="<?=$consignmentDetail->input_value?>">
                            <?php }?>
                          <?php }?>
                        </td>
                      </tr>
                    <?php } }?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="4" style="text-align:center;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-paper-plane"></i> Submit</button>
                      </th>
                    </tr>
                  </tfoot>
                </table>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>