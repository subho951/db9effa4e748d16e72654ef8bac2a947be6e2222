<?php
use App\Helpers\Helper;

$controllerRoute = $module['controller_route'];
?>
<style>
  .receive-table input {
    min-width: 90px;
  }
  .receive-summary {
    background: #f8fafc;
    border: 1px solid #e5edf5;
    border-radius: 8px;
    padding: 14px;
  }
  .receive-summary strong {
    display: block;
    color: #111827;
  }
  .landed-cost {
    font-weight: 700;
    color: #0f766e;
  }
</style>
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
          <?php if($stockAlreadyReceived){?>
            <div class="alert alert-warning">
              Stock has already been received for this purchase order. This screen is read-only to prevent duplicate stock entry.
            </div>
          <?php }?>
          <div class="mb-3">
            <strong><?=$row->po_no?></strong>
            <span class="text-muted"> | <?=$row->supplier_name?> | Order date: <?=$row->order_date?></span>
          </div>
          <form method="POST" action="" id="receiveGoodsForm">
            @csrf
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label class="form-label" for="receive_date">Receive Date</label>
                <input type="date" class="form-control receive-input" name="receive_date" id="receive_date" value="<?=date('Y-m-d')?>" <?=($stockAlreadyReceived?'disabled':'')?> required>
              </div>
              <div class="col-md-4">
                <label class="form-label" for="delivery_cost">Delivery Cost</label>
                <input type="number" step="0.01" min="0" class="form-control receive-input" name="delivery_cost" id="delivery_cost" value="0" <?=($stockAlreadyReceived?'disabled':'')?> required>
              </div>
              <div class="col-md-4 receive-summary">
                <strong>Total Items: <span id="receive_total_qty">0</span></strong>
                <span>Delivery per item: $<span id="delivery_per_item">0.00</span></span>
              </div>
            </div>
            <div class="dt-responsive table-responsive">
              <table class="table table-striped table-bordered nowrap receive-table">
                <thead>
                  <tr>
                    <th scope="col">Supplier SKU</th>
                    <th scope="col">Our SKU</th>
                    <th scope="col">Product</th>
                    <th scope="col">Current Shop</th>
                    <th scope="col">Current Warehouse</th>
                    <th scope="col">PO Qty</th>
                    <th scope="col">Cost ex GST</th>
                    <th scope="col">Shop Qty</th>
                    <th scope="col">Warehouse Qty</th>
                    <th scope="col">New Cost ex GST</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(count($poItems)>0){ foreach($poItems as $poItem){
                    $product = $products[$poItem->item_id] ?? null;
                    $shopStock = (($product)?$product->shop_stock:0);
                    $warehouseStock = (($product)?$product->warehouse_stock:0);
                  ?>
                    <tr class="receive-row">
                      <td><?=$poItem->supplier_sku?></td>
                      <td><?=$poItem->merchant_sku?></td>
                      <td><?=$poItem->item_name?></td>
                      <td><?=$shopStock?></td>
                      <td><?=$warehouseStock?></td>
                      <td>
                        <input type="hidden" name="item_id[<?=$poItem->id?>]" value="<?=$poItem->item_id?>">
                        <input type="hidden" class="tax-percent" name="tax_percent[<?=$poItem->id?>]" value="<?=$poItem->tax_percent?>">
                        <input type="number" min="1" class="form-control receive-input po-qty" name="qty[<?=$poItem->id?>]" value="<?=$poItem->qty?>" <?=($stockAlreadyReceived?'disabled':'')?> required>
                      </td>
                      <td>
                        <input type="number" min="0" step="0.01" class="form-control receive-input cost-price" name="cost_price[<?=$poItem->id?>]" value="<?=$poItem->cost_price?>" <?=($stockAlreadyReceived?'disabled':'')?> required>
                      </td>
                      <td>
                        <input type="number" min="0" class="form-control receive-input shop-qty" name="shop_qty[<?=$poItem->id?>]" value="0" <?=($stockAlreadyReceived?'disabled':'')?> required>
                      </td>
                      <td>
                        <input type="number" min="0" class="form-control receive-input warehouse-qty" name="warehouse_qty[<?=$poItem->id?>]" value="<?=$poItem->qty?>" <?=($stockAlreadyReceived?'disabled':'')?> required>
                      </td>
                      <td>$<span class="landed-cost"><?=number_format($poItem->cost_price, 2)?></span></td>
                    </tr>
                  <?php } }?>
                </tbody>
              </table>
            </div>
            <div class="mt-3 d-flex flex-wrap gap-2">
              <?php if(!$stockAlreadyReceived){?>
                <button type="button" class="btn btn-primary" id="validateReceiveGoods"><i class="fa fa-check"></i>&nbsp;OK</button>
                <button type="submit" class="btn btn-success d-none" id="addStockButton"><i class="fa fa-plus-circle"></i>&nbsp;Add Stock</button>
              <?php }?>
              <a href="<?=url('admin/' . $controllerRoute . '/list/')?>" class="btn btn-outline-secondary">Back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  function receiveGoodsSummary(showErrors) {
    var totalQty = 0;
    var errors = [];
    var deliveryCost = parseFloat($('#delivery_cost').val()) || 0;

    if (deliveryCost < 0) {
      errors.push('Delivery cost can not be negative.');
    }

    $('.receive-row').each(function() {
      var row = $(this);
      var qty = parseInt(row.find('.po-qty').val(), 10) || 0;
      var cost = parseFloat(row.find('.cost-price').val()) || 0;
      var shopQty = parseInt(row.find('.shop-qty').val(), 10) || 0;
      var warehouseQty = parseInt(row.find('.warehouse-qty').val(), 10) || 0;
      var productName = row.find('td').eq(2).text();

      if (qty <= 0) {
        errors.push(productName + ' quantity must be greater than zero.');
      }
      if (cost < 0) {
        errors.push(productName + ' cost can not be negative.');
      }
      if (shopQty < 0 || warehouseQty < 0) {
        errors.push(productName + ' allocation can not be negative.');
      }
      if ((shopQty + warehouseQty) !== qty) {
        errors.push(productName + ' shop + warehouse quantity must equal PO quantity.');
      }

      totalQty += qty;
    });

    if (totalQty <= 0) {
      errors.push('Total received quantity must be greater than zero.');
    }

    var deliveryPerItem = totalQty > 0 ? (deliveryCost / totalQty) : 0;
    $('.receive-row').each(function() {
      var row = $(this);
      var cost = parseFloat(row.find('.cost-price').val()) || 0;
      row.find('.landed-cost').text((cost + deliveryPerItem).toFixed(2));
    });
    $('#receive_total_qty').text(totalQty);
    $('#delivery_per_item').text(deliveryPerItem.toFixed(2));

    if (showErrors && errors.length > 0) {
      if (typeof toastAlert === 'function') {
        toastAlert('error', errors.join('<br>'));
      } else {
        alert(errors.join("\n"));
      }
    }

    return errors.length === 0;
  }

  $(document).on('input change', '.receive-input', function() {
    $('#addStockButton').addClass('d-none');
    receiveGoodsSummary(false);
  });
  $(document).on('click', '#validateReceiveGoods', function() {
    if (receiveGoodsSummary(true)) {
      $('#addStockButton').removeClass('d-none');
      if (typeof toastAlert === 'function') {
        toastAlert('success', 'Receiving quantities validated. You can now add stock.');
      }
    }
  });
  $(document).on('submit', '#receiveGoodsForm', function(e) {
    if (!receiveGoodsSummary(true)) {
      e.preventDefault();
      return false;
    }
    return confirm('Add stock for this purchase order now?');
  });
  $(document).ready(function() {
    receiveGoodsSummary(false);
  });
</script>
