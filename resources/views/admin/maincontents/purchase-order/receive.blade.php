<?php
use App\Helpers\Helper;

$controllerRoute = $module['controller_route'];
$currencySymbol = (($row->currency_symbol ?? '') != ''?$row->currency_symbol:'$');
$escapedCurrencySymbol = htmlspecialchars((string)$currencySymbol, ENT_QUOTES, 'UTF-8');
$deliveryCost = (float)old('delivery_cost', ($row->delivery_cost ?? 0));
$poTotalQty = max(0, (int)$poItems->sum('qty'));
$savedDeliveryPerUnit = ($poTotalQty > 0?$deliveryCost / $poTotalQty:0);
?>
<style>
  .receive-table input {
    min-width: 105px;
  }
  .receive-summary {
    background: #f8fafc;
    border: 1px solid #e5edf5;
    border-radius: 8px;
    padding: 14px;
  }
  .receive-summary strong,
  .receive-summary span {
    display: block;
  }
  .updated-unit-price {
    background: #ecfdf5 !important;
    color: #0f766e;
    font-weight: 700;
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
              This purchase order is closed. Its stock has already been received into the warehouse.
            </div>
          <?php } else {?>
            <div class="alert alert-info">
              Confirm or edit each quantity and purchase price, apply any delivery cost, then click Receive to add all units to warehouse stock.
            </div>
          <?php }?>
          <div class="mb-3">
            <strong><?=$row->po_no?></strong>
            <span class="text-muted"> | <?=$row->supplier_name?> | Order date: <?=$row->order_date?> | Currency: <?=$escapedCurrencySymbol?></span>
          </div>
          <form method="POST" action="" id="receiveGoodsForm">
            @csrf
            <input type="hidden" name="delivery_cost" id="delivery_cost" value="<?=number_format($deliveryCost, 2, '.', '')?>">
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label class="form-label" for="receive_date">Receive Date</label>
                <input type="date" class="form-control receive-input" name="receive_date" id="receive_date" value="<?=old('receive_date', date('Y-m-d'))?>" <?=($stockAlreadyReceived?'disabled':'')?> required>
              </div>
              <div class="col-md-4 receive-summary">
                <strong>Total Units: <span id="receive_total_qty">0</span></strong>
                <span>Delivery ex GST: <?=$escapedCurrencySymbol?><span id="delivery_total" class="d-inline">0.00</span></span>
              </div>
              <div class="col-md-4 receive-summary">
                <strong>Delivery per Unit</strong>
                <span><?=$escapedCurrencySymbol?><span id="delivery_per_unit" class="d-inline">0.00</span></span>
              </div>
            </div>
            <div class="dt-responsive table-responsive">
              <table class="table table-striped table-bordered nowrap receive-table">
                <thead>
                  <tr>
                    <th scope="col">Supplier SKU</th>
                    <th scope="col">Our SKU</th>
                    <th scope="col">Product</th>
                    <th scope="col">Current Warehouse</th>
                    <th scope="col">Receive Qty</th>
                    <th scope="col">Purchase Unit Price ex GST (<?=$escapedCurrencySymbol?>)</th>
                    <th scope="col">Delivery / Unit (<?=$escapedCurrencySymbol?>)</th>
                    <th scope="col">Updated Unit Price ex GST (<?=$escapedCurrencySymbol?>)</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(count($poItems)>0){ foreach($poItems as $poItem){
                    $product = $products[$poItem->item_id] ?? null;
                    $warehouseStock = (($product)?$product->warehouse_stock:0);
                    $defaultBaseCost = ($stockAlreadyReceived?max(0, (float)$poItem->cost_price - $savedDeliveryPerUnit):(float)$poItem->cost_price);
                    $baseCost = (float)old('base_cost_price.'.$poItem->id, $defaultBaseCost);
                    $receiveQty = (int)old('qty.'.$poItem->id, $poItem->qty);
                  ?>
                    <tr class="receive-row">
                      <td><?=$poItem->supplier_sku?></td>
                      <td><?=$poItem->merchant_sku?></td>
                      <td><?=$poItem->item_name?></td>
                      <td><?=$warehouseStock?></td>
                      <td>
                        <input type="number" min="1" step="1" class="form-control receive-input po-qty" name="qty[<?=$poItem->id?>]" value="<?=$receiveQty?>" <?=($stockAlreadyReceived?'disabled':'')?> required>
                      </td>
                      <td>
                        <input type="number" min="0" step="0.01" class="form-control receive-input base-cost-price" name="base_cost_price[<?=$poItem->id?>]" value="<?=number_format($baseCost, 2, '.', '')?>" <?=($stockAlreadyReceived?'disabled':'')?> required>
                      </td>
                      <td><?=$escapedCurrencySymbol?><span class="delivery-allocation"><?=number_format($savedDeliveryPerUnit, 2)?></span></td>
                      <td>
                        <input type="text" class="form-control updated-unit-price" value="<?=number_format($baseCost + $savedDeliveryPerUnit, 2, '.', '')?>" readonly>
                      </td>
                    </tr>
                  <?php } } else {?>
                    <tr><td colspan="8" class="text-center">No items are available to receive.</td></tr>
                  <?php }?>
                </tbody>
              </table>
            </div>
            <div class="mt-3 d-flex flex-wrap gap-2">
              <?php if(!$stockAlreadyReceived && count($poItems)>0){?>
                <button type="button" class="btn btn-primary" id="openDeliveryModal"><i class="fa fa-truck"></i>&nbsp;Delivery</button>
                <button type="submit" class="btn btn-success" id="receiveStockButton"><i class="fa fa-check-circle"></i>&nbsp;Receive</button>
              <?php }?>
              <a href="<?=url('admin/' . $controllerRoute . '/list/')?>" class="btn btn-outline-secondary">Back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if(!$stockAlreadyReceived){?>
<div class="modal fade" id="deliveryCostModal" tabindex="-1" aria-labelledby="deliveryCostModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deliveryCostModalLabel">Delivery Amount ex GST</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label for="delivery_cost_input" class="form-label">Delivery Amount (<?=$escapedCurrencySymbol?>)</label>
        <input type="number" step="0.01" min="0" class="form-control" id="delivery_cost_input" value="<?=number_format($deliveryCost, 2, '.', '')?>">
        <small class="text-muted">This amount will be divided equally across every unit in the purchase order.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="applyDeliveryCost">OK</button>
      </div>
    </div>
  </div>
</div>
<?php }?>

<script>
  const receiveCurrencySymbol = <?=json_encode($currencySymbol, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)?>;

  function showReceiveMessage(type, message) {
    if (typeof toastAlert === 'function') {
      toastAlert(type, message);
      return;
    }
    alert(message.replace(/<br\s*\/?>/gi, "\n"));
  }

  function showDeliveryCostModal() {
    const modalElement = document.getElementById('deliveryCostModal');
    if (window.bootstrap && window.bootstrap.Modal) {
      window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
    } else if ($.fn && typeof $.fn.modal === 'function') {
      $('#deliveryCostModal').modal('show');
    }
  }

  function hideDeliveryCostModal() {
    const modalElement = document.getElementById('deliveryCostModal');
    if (window.bootstrap && window.bootstrap.Modal) {
      window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
    } else if ($.fn && typeof $.fn.modal === 'function') {
      $('#deliveryCostModal').modal('hide');
    }
  }

  function receiveGoodsSummary(showErrors) {
    let totalQty = 0;
    let errors = [];
    const deliveryCost = parseFloat($('#delivery_cost').val()) || 0;

    $('.receive-row').each(function() {
      const row = $(this);
      const qty = parseInt(row.find('.po-qty').val(), 10) || 0;
      const baseCost = parseFloat(row.find('.base-cost-price').val());
      const productName = row.find('td').eq(2).text().trim();

      if (qty <= 0) {
        errors.push(productName + ' quantity must be greater than zero.');
      }
      if (isNaN(baseCost) || baseCost < 0) {
        errors.push(productName + ' purchase price can not be negative.');
      }
      totalQty += qty;
    });

    if (totalQty <= 0) {
      errors.push('Total received quantity must be greater than zero.');
    }

    const deliveryPerUnit = totalQty > 0 ? deliveryCost / totalQty : 0;
    $('.receive-row').each(function() {
      const row = $(this);
      const baseCost = parseFloat(row.find('.base-cost-price').val()) || 0;
      row.find('.delivery-allocation').text(deliveryPerUnit.toFixed(2));
      row.find('.updated-unit-price').val((baseCost + deliveryPerUnit).toFixed(2));
    });

    $('#receive_total_qty').text(totalQty);
    $('#delivery_total').text(deliveryCost.toFixed(2));
    $('#delivery_per_unit').text(deliveryPerUnit.toFixed(2));

    if (showErrors && errors.length > 0) {
      showReceiveMessage('error', errors.join('<br>'));
    }
    return errors.length === 0;
  }

  $(document).on('click', '#openDeliveryModal', function() {
    $('#delivery_cost_input').val((parseFloat($('#delivery_cost').val()) || 0).toFixed(2));
    showDeliveryCostModal();
    window.setTimeout(function() { $('#delivery_cost_input').focus().select(); }, 250);
  });

  $(document).on('click', '#applyDeliveryCost', function() {
    const deliveryCost = parseFloat($('#delivery_cost_input').val());
    if (isNaN(deliveryCost) || deliveryCost < 0) {
      showReceiveMessage('error', 'Please enter a valid delivery amount ex GST.');
      return;
    }
    $('#delivery_cost').val(deliveryCost.toFixed(2));
    if (receiveGoodsSummary(true)) {
      hideDeliveryCostModal();
      showReceiveMessage('success', 'Delivery cost has been allocated equally per unit and the PO unit prices have been updated.');
    }
  });

  $(document).on('input change', '.receive-input', function() {
    receiveGoodsSummary(false);
  });

  $(document).on('submit', '#receiveGoodsForm', function(e) {
    if (!receiveGoodsSummary(true)) {
      e.preventDefault();
      return false;
    }
    if (!confirm('Receive this purchase order into warehouse stock? This will close the PO and cannot be undone.')) {
      e.preventDefault();
      return false;
    }
    $('#receiveStockButton').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Receiving...');
    return true;
  });

  $(document).ready(function() {
    receiveGoodsSummary(false);
  });
</script>
