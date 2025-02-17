<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
<style>
    #checkAllBtn:hover, #uncheckAllBtn:hover {
        background-color: #f0f0f0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 10px;
        text-align: left;
    }
    input[type="checkbox"] {
        cursor: pointer;
    }
</style>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light"><a href="<?=url('admin/dashboard')?>">Dashboard</a> /</span> <?=$page_header?>
  </h4>
  <div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="mode" value="product_search">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <select name="brand_id" class="form-control" id="brand_id" required>
                                <option value="" selected>Select Brand</option>
                                <?php if($brands){ foreach($brands as $brand){?>
                                <option value="<?=$brand->id?>" <?=(($brand_id == $brand->id)?'selected':'')?>><?=$brand->name?></option>
                                <?php } }?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary btn-sm me-2">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php if(count($rows)>0){?>
            <div class="card mt-3">
                <div class="card-body">
                    <!-- Check All / Uncheck All Button -->
                    <button id="checkAllBtn" class="btn btn-primary btn-sm">Check All</button>
                    <button id="uncheckAllBtn" class="btn btn-primary btn-sm">Uncheck All</button>
                    <div class="dt-responsive table-responsive mt-3">
                        <form method="POST" action="<?=url('admin/products/print-products')?>" target="_blank" enctype="multipart/form-data">
                            <input type="hidden" name="mode" value="product_search">
                            @csrf
                            <table class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                    <th scope="col">#<br><input type="checkbox" id="checkAll"></th>
                                    <th scope="col">Name</th>
                                    <th scope="col">SKU ID</th>
                                    <th scope="col">Barcode</th>
                                    <th scope="col">Brand</th>
                                    <th scope="col">Supplier</th>
                                    <th scope="col">Cost Price Ex. Tax</th>
                                    <th scope="col">Cost Price Inc. Tax</th>
                                    <th scope="col">Retail Price Inc. Tax</th>
                                    <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(count($rows)>0){ $sl=1; foreach($rows as $row){?>
                                        <tr>
                                            <td scope="row">
                                                <?=$sl++?>
                                                <input type="checkbox" class="checkItem" name="product_id[]" value="<?=$row->id?>">
                                            </td>
                                            <td><?=$row->name?></td>
                                            <td><?=$row->sku?></td>
                                            <td><?=$row->barcode?></td>
                                            <td><?=$row->brand_name?></td>
                                            <td><?=$row->supplier_name?></td>
                                            <td>$<?=number_format($row->cost_price_ex_tax,2)?></td>
                                            <td>$<?=number_format($row->cost_price_inc_tax,2)?></td>
                                            <td>$<?=number_format($row->retail_price_inc_tax,2)?></td>
                                            <td>
                                                <a href="<?=url('admin/' . $controllerRoute . '/edit/'.Helper::encoded($row->id))?>" class="btn btn-outline-primary btn-sm" title="Edit <?=$module['title']?>"><i class="fa fa-edit"></i></a>
                                                <a href="<?=url('admin/' . $controllerRoute . '/delete/'.Helper::encoded($row->id))?>" class="btn btn-outline-danger btn-sm" title="Delete <?=$module['title']?>" onclick="return confirm('Do You Want To Delete This <?=$module['title']?>');"><i class="fa fa-trash"></i></a>
                                                <?php if($row->status){?>
                                                    <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-success btn-sm" title="Activate <?=$module['title']?>"><i class="fa fa-check"></i></a>
                                                <?php } else {?>
                                                    <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-warning btn-sm" title="Deactivate <?=$module['title']?>"><i class="fa fa-times"></i></a>
                                                <?php }?>
                                                <br><br>
                                                <?php if($row->barcode_image_url != ''){?>
                                                    <a target="_blank" href="<?=url('admin/' . $controllerRoute . '/print-barcode/'.Helper::encoded($row->id))?>" class="btn btn-info btn-sm" title="Print <?=$module['title']?> Barcode"><i class="fa-solid fa-barcode"></i>&nbsp;Print Barcode</a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                    <?php } }?>
                                </tbody>
                            </table>
                            <div class="mt-2">
                                <button type="submit" id="print-btn" class="btn btn-success me-2" style="display: none;">Print</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Check All Functionality
    $('#checkAll').on('change', function() {
        $('.checkItem').prop('checked', $(this).prop('checked'));
        $('#print-btn').show();
    });

    // Check All Button
    $('#checkAllBtn').on('click', function() {
        $('#checkAll').prop('checked', true);  // Check the main checkbox
        $('.checkItem').prop('checked', true); // Check all individual checkboxes
        $('#print-btn').show();
    });

    // Uncheck All Button
    $('#uncheckAllBtn').on('click', function() {
        $('#checkAll').prop('checked', false);  // Uncheck the main checkbox
        $('.checkItem').prop('checked', false); // Uncheck all individual checkboxes
        $('#print-btn').hide();
    });
</script>