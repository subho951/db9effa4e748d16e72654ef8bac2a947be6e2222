<?php
$controllerRoute = $module['controller_route'];
?>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light"><a href="<?=url('admin/dashboard')?>">Dashboard</a> /</span>
    <span class="text-muted fw-light"><a href="<?=url('admin/' . $controllerRoute . '/list/')?>"><?=$module['title']?> List</a> /</span>
    <?=$page_header?>
  </h4>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <form method="POST" action="">
            @csrf
            <div class="row align-items-end g-3 mb-3">
              <div class="col-md-4">
                <label class="form-label" for="transfer_direction">Transfer Direction <small class="text-danger">*</small></label>
                <select class="form-select" name="transfer_direction" id="transfer_direction" required>
                  <option value="WAREHOUSE_TO_SHOP" selected>Warehouse to Shop</option>
                  <option value="SHOP_TO_WAREHOUSE">Shop to Warehouse</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label" for="stock_date">Transfer Date <small class="text-danger">*</small></label>
                <input type="date" class="form-control" name="stock_date" id="stock_date" value="<?=date('Y-m-d')?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label" for="note">Note</label>
                <input type="text" class="form-control" name="note" id="note" placeholder="Optional note">
              </div>
            </div>
            <div class="dt-responsive table-responsive">
              <table class="table table-striped table-bordered nowrap">
                <thead>
                  <tr>
                    <th scope="col">SKU</th>
                    <th scope="col">Product</th>
                    <th scope="col">Supplier</th>
                    <th scope="col">Shop Stock</th>
                    <th scope="col">Warehouse Stock</th>
                    <th scope="col">Transfer Qty</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(count($rows)>0){ foreach($rows as $row){?>
                    <tr>
                      <td><?=$row->sku?></td>
                      <td><?=$row->name?></td>
                      <td><?=$row->supplier_name?></td>
                      <td><?=$row->shop_stock?></td>
                      <td><?=$row->warehouse_stock?></td>
                      <td>
                        <input type="hidden" name="product_ids[]" value="<?=$row->id?>">
                        <input type="number" class="form-control" name="transfer_qty[<?=$row->id?>]" min="0" max="<?=$row->warehouse_stock?>" value="0">
                      </td>
                    </tr>
                  <?php } }?>
                </tbody>
              </table>
            </div>
            <div class="mt-3 d-flex gap-2">
              <button type="submit" class="btn btn-primary"><i class="fa fa-exchange-alt"></i>&nbsp;Transfer</button>
              <a href="<?=url('admin/' . $controllerRoute . '/list/')?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).on('change', '#transfer_direction', function() {
    var isWarehouseToShop = $(this).val() === 'WAREHOUSE_TO_SHOP';
    $('input[name^="transfer_qty"]').each(function() {
      var row = $(this).closest('tr');
      var maxQty = isWarehouseToShop ? row.find('td').eq(4).text() : row.find('td').eq(3).text();
      $(this).attr('max', parseInt(maxQty, 10) || 0);
    });
  });
</script>
