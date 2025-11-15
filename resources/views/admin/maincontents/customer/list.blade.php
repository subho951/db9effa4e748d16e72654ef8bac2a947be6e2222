<?php

use App\Models\Order;
use App\Helpers\Helper;

$controllerRoute = $module['controller_route'];
?>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light"><a href="<?= url('admin/dashboard') ?>">Dashboard</a> /</span> <?= $page_header ?>
  </h4>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">
            <ul class="nav nav-pills mb-3" role="tablist">
              <li class="nav-item">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab1" aria-controls="tab1" aria-selected="true">Pickup</button>
              </li>
              <li class="nav-item">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab2" aria-controls="tab2" aria-selected="false">Deliver</button>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                <div class="dt-responsive table-responsive">
                  <table id="simpletable" class="table table-striped table-bordered nowrap">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tag</th>
                        <th scope="col">Name</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">Email</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Orders</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (count($rows1) > 0) {
                        $sl = 1;
                        foreach ($rows1 as $row) { ?>
                          <tr>
                            <th scope="row"><?= $sl++ ?></th>
                            <td><?= $row->customer_tag ?></td>
                            <td><?= $row->pickup_name ?></td>
                            <td><?= $row->pickup_phone ?></td>
                            <td><?= $row->pickup_email ?></td>
                            <td><?= date_format(date_create($row->created_at), "M d, Y h:i A") ?></td>
                            <td>
                              <?php
                              $orderCount = Order::where('pickup_phone', '=', $row->pickup_phone)->where('status', 5)->where('delivery_mode', '=', 'Pickup')->count();
                              ?>
                              <a href="<?= url('admin/' . $controllerRoute . '/customer-orders/' . Helper::encoded($row->customer_phone)) ?>" class="btn btn-outline-info btn-sm" title="<?= $module['title'] ?> Orders" target="_blank"><i class="fa fa-list"></i>&nbsp; <?= $orderCount ?> orders</a>
                            </td>
                          </tr>
                        <?php }
                      } else { ?>
                        <tr>
                          <td colspan="7" style="text-align: center;color: red;">No Pickup Records Found !!!</td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="tab-pane fade" id="tab2" role="tabpanel">
                <div class="dt-responsive table-responsive">
                  <table id="simpletable2" class="table table-striped table-bordered nowrap">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tag</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">Name</th>
                        <th scope="col">Address</th>
                        <th scope="col">Email</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Orders</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (count($rows2) > 0) {
                        $sl = 1;
                        foreach ($rows2 as $row) { ?>
                          <tr>
                            <th scope="row"><?= $sl++ ?></th>
                            <td><?= $row->customer_tag ?></td>
                            <td><?= $row->delivery_phone ?></td>
                            <td><?= $row->delivery_name ?></td>
                            <td><?= $row->delivery_address . ' ' . $row->delivery_suburb . ' ' . $row->delivery_state . ' ' . $row->delivery_postcode?></td>
                            <td><?= $row->delivery_email ?></td>
                            <td><?= date_format(date_create($row->created_at), "M d, Y h:i A") ?></td>
                            <td>
                              <?php
                              $orderCount = Order::where('delivery_phone', '=', $row->delivery_phone)->where('status', 5)->where('delivery_mode', '=', 'Deliver')->count();
                              ?>
                              <a href="<?= url('admin/' . $controllerRoute . '/customer-orders/' . Helper::encoded($row->customer_phone)) ?>" class="btn btn-outline-info btn-sm" title="<?= $module['title'] ?> Orders" target="_blank"><i class="fa fa-list"></i>&nbsp; <?= $orderCount ?> orders</a>
                            </td>
                          </tr>
                        <?php }
                      } else { ?>
                        <tr>
                          <td colspan="8" style="text-align: center;color: red;">No Deliver Records Found !!!</td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>