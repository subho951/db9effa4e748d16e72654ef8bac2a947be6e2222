<?php
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Helpers\Helper;

$controllerRoute                = $module['controller_route'];
$prefillItems                   = $prefillItems ?? collect();
$prefillSupplierId              = $prefillSupplierId ?? '';
$selectedSupplierId             = $selectedSupplierId ?? $prefillSupplierId;
$supplierProducts               = $supplierProducts ?? collect();
$hasPrefillItems                = (count($prefillItems) > 0);
?>
<style>
  .invoice-footer span {
      font-weight: 600;
      font-size: 15px;
  }

  .invoice-footer h6 {
      margin-bottom: 0;
  }
  .supplier-product-select-table th:first-child,
  .supplier-product-select-table td:first-child {
      width: 44px;
      text-align: center;
      vertical-align: middle;
  }
</style>
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light"><a href="<?= url('admin/dashboard') ?>">Dashboard</a> /</span>
    <span class="text-muted fw-light"><a href="<?= url('admin/' . $controllerRoute . '/list/') ?>"><?= $module['title'] ?> List</a> /</span>
    <?= $page_header ?>
    <?php if($row){?>
      <a href="<?=url('admin/purchase-orders/receive/' . Helper::encoded($row->id))?>" class="btn btn-outline-success btn-sm float-end"><i class="fa fa-truck-loading"></i>&nbsp;Receive</a>
    <?php }?>
  </h4>
  <div class="row">
    <?php
    if ($row) {
      $order_date           = $row->order_date;
      $currency_symbol      = (($row->currency_symbol ?? '') != ''?$row->currency_symbol:'$');
      $delivery_id          = $row->delivery_id;
      $supplier_id          = $row->supplier_id;

      $s_street_address1    = $row->s_street_address1;
      $s_street_address2    = $row->s_street_address2;
      $s_city               = $row->s_city;
      $s_state              = $row->s_state;
      $s_postcode           = $row->s_postcode;
      $s_country            = $row->s_country;

      $status               = $row->status;
      $total_lines          = $row->total_lines;
      $total_quantity       = $row->total_quantity;
      $subtotal             = $row->subtotal;
      $tax_total            = $row->tax_total;
      $total_inc_tax        = $row->total_inc_tax;
      $note                 = $row->note;
    } else {
      $order_date           = '';
      $currency_symbol      = $currencySymbol ?? '$';
      $delivery_id          = '';
      $supplier_id          = $selectedSupplierId;

      $s_street_address1    = '';
      $s_street_address2    = '';
      $s_city               = '';
      $s_state              = '';
      $s_postcode           = '';
      $s_country            = 'Australia';
      $status               = 0;
      $uId                  = '';

      $status               = 1;
      $total_lines          = 0;
      $total_quantity       = 0;
      $subtotal             = 0;
      $tax_total            = 0;
      $total_inc_tax        = 0;
      $note                 = 0;

      if ($hasPrefillItems) {
        $total_lines = count($prefillItems);
        $total_quantity = count($prefillItems);
        $subtotal = 0;
        $tax_total = 0;
        $total_inc_tax = 0;
        foreach($prefillItems as $prefillItem) {
          $lineSubtotal = $prefillItem->cost_price_ex_tax;
          $lineTax = ($lineSubtotal * $prefillItem->cost_price_tax) / 100;
          $subtotal += $lineSubtotal;
          $tax_total += $lineTax;
          $total_inc_tax += ($lineSubtotal + $lineTax);
        }
      }
    }
    ?>
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <small class="text-danger">Star (*) marked fields are mandatory</small>
          <form id="filterForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="order_date" class="form-label">Order Date <small class="text-danger">*</small></label>
                <input class="form-control" type="date" id="order_date" name="order_date" value="<?= $order_date ?>" required autofocus />
              </div>

              <div class="mb-3 col-md-6">
                <label for="currency_symbol" class="form-label">Currency Symbol or Code <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="currency_symbol" name="currency_symbol" maxlength="3" value="<?=htmlspecialchars((string)$currency_symbol, ENT_QUOTES, 'UTF-8')?>" required>
                <small class="text-muted">Up to 3 characters, for example $, USD, GBP or JPY.</small>
              </div>
              <div class="mb-3 col-md-6">
                <label for="username" class="form-label d-block">Status <small class="text-danger">*</small></label>
                <div class="form-check form-check-inline mt-3">
                  <input name="status" class="form-check-input" type="radio" value="1" id="status1" <?= (($status == 1) ? 'checked' : '') ?> required />
                  <label class="form-check-label" for="status1">
                    Active
                  </label>
                </div>
                <div class="form-check form-check-inline mt-3">
                  <input name="status" class="form-check-input" type="radio" value="0" id="status2" <?= (($status == 0) ? 'checked' : '') ?> required />
                  <label class="form-check-label" for="status2">
                    Deactive
                  </label>
                </div>
              </div>

              <h5 class="mt-3">Shipping Details</h5>
              <div class="mb-3 col-md-6">
                <label for="s_street_address1" class="form-label">Street Address 1 <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="s_street_address1" name="s_street_address1" value="<?= $s_street_address1 ?>" />
              </div>
              <div class="mb-3 col-md-6">
                <label for="s_street_address2" class="form-label">Street Address 2</label>
                <input class="form-control" type="text" id="s_street_address2" name="s_street_address2" value="<?= $s_street_address2 ?>" />
              </div>
              <div class="mb-3 col-md-6">
                <label for="s_city" class="form-label">City <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="s_city" name="s_city" value="<?= $s_city ?>" required />
              </div>

              <div class="mb-3 col-md-6">
                <label for="s_state" class="form-label">State <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="s_state" name="s_state" value="<?= $s_state ?>" required />
              </div>
              <div class="mb-3 col-md-6">
                <label for="s_postcode" class="form-label">Postcode <small class="text-danger">*</small></label>
                <input class="form-control" type="text" id="s_postcode" name="s_postcode" value="<?= $s_postcode ?>" required />
              </div>
              <div class="mb-3 col-md-6">
                <label for="s_country" class="form-label">Country <small class="text-danger">*</small></label>
                <select name="s_country" class="form-select" id="s_country" required>
                  <option value="" selected>Select Country</option>
                  <?php if ($couns) {
                    foreach ($couns as $coun) { ?>
                      <option value="<?= $coun->country ?>" <?= (($coun->country == $s_country) ? 'selected' : '') ?>><?= $coun->country ?></option>
                  <?php }
                  } ?>
                </select>
              </div>              

              <div class="mb-3 col-md-6">
                <label for="delivery_id" class="form-label">Delivery Location <small class="text-danger">*</small></label>
                <select name="delivery_id" class="form-select" id="delivery_id" required>
                  <option value="" selected>Select Delivery Location</option>
                  <?php if ($deliveryLocations) {
                    foreach ($deliveryLocations as $deliveryLocation) { ?>
                      <option value="<?= $deliveryLocation->id ?>" <?= (($deliveryLocation->id == $delivery_id) ? 'selected' : '') ?>><?= $deliveryLocation->name ?> | <?= $deliveryLocation->address ?> | <?= $deliveryLocation->phone ?></option>
                  <?php }
                  } ?>
                </select>
              </div>

              <div class="mb-3 col-md-6">
                <label for="supplier_id" class="form-label">Supplier <small class="text-danger">*</small></label>
                <select name="supplier_id" class="form-select" id="supplier_id" required>
                  <option value="" selected>Select Supplier</option>
                  <?php if ($suppliers) {
                    foreach ($suppliers as $supp) { ?>
                      <option value="<?= $supp->id ?>" <?= (($supp->id == $supplier_id) ? 'selected' : '') ?>><?= $supp->name ?> | <?= $supp->supplier_code ?> | <?= $supp->phone ?></option>
                  <?php }
                  } ?>
                </select>
              </div>
            </div>

            <?php if(!$row && $supplier_id != '' && !$hasPrefillItems){?>
              <div class="row">
                <div class="col-md-12">
                  <h5 class="mt-3">Supplier Products</h5>
                  <div class="dt-responsive table-responsive">
                    <table class="table table-striped table-bordered nowrap supplier-product-select-table">
                      <thead>
                        <tr>
                          <th scope="col">
                            <input type="checkbox" class="form-check-input" id="supplier_product_select_all">
                          </th>
                          <th scope="col">Product</th>
                          <th scope="col">Supplier SKU</th>
                          <th scope="col">Merchant SKU</th>
                          <th scope="col">Barcode</th>
                          <th scope="col">Cost ex GST</th>
                          <th scope="col">GST</th>
                          <th scope="col">Stock</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if(count($supplierProducts) > 0){ foreach($supplierProducts as $supplierProduct){
                          $productName = (($supplierProduct->supplier_product_name != '')?$supplierProduct->supplier_product_name:$supplierProduct->name);
                          $supplierSku = (($supplierProduct->supplier_sku != '')?$supplierProduct->supplier_sku:$supplierProduct->sku);
                          $stockTotal = ((int)$supplierProduct->shop_stock + (int)$supplierProduct->warehouse_stock);
                        ?>
                          <tr>
                            <td>
                              <input type="checkbox" class="form-check-input supplier-product-checkbox" value="<?= $supplierProduct->id ?>">
                            </td>
                            <td><?= htmlspecialchars((string)$productName, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)$supplierSku, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)$supplierProduct->sku, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string)$supplierProduct->barcode, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="currency-symbol"><?=htmlspecialchars((string)$currency_symbol, ENT_QUOTES, 'UTF-8')?></span><?= number_format((float)$supplierProduct->cost_price_ex_tax, 2) ?></td>
                            <td><?= number_format((float)$supplierProduct->cost_price_tax, 2) ?>%</td>
                            <td><?= $stockTotal ?></td>
                          </tr>
                        <?php } } else {?>
                          <tr>
                            <td colspan="8" class="text-center">No products found for this supplier.</td>
                          </tr>
                        <?php }?>
                      </tbody>
                    </table>
                  </div>
                  <div class="mt-2 mb-3">
                    <button type="button" class="btn btn-primary" id="proceedSupplierProducts" <?=(count($supplierProducts) <= 0?'disabled':'')?>>
                      <i class="fa fa-arrow-right"></i>&nbsp;&nbsp;Proceed
                    </button>
                  </div>
                </div>
              </div>
            <?php }?>

            <?php if ($row || $hasPrefillItems) { ?>
              <div class="row">
                <div class="mb-3 col-md-2">
                  <h6 style="font-weight: bold;">Items</h6>
                </div>
                <div class="mb-3 col-md-1">
                  <h6 style="font-weight: bold;">Supplier SKU</h6>
                </div>
                <div class="mb-3 col-md-1">
                  <h6 style="font-weight: bold;">Merchant SKU</h6>
                </div>
                <div class="mb-3 col-md-3">
                  <h6 style="font-weight: bold;">Product name</h6>
                </div>
                <div class="mb-3 col-md-1">
                  <h6 style="font-weight: bold;">Order QTY</h6>
                </div>
                <div class="mb-3 col-md-1">
                  <h6 style="font-weight: bold;">Unit price (<span class="currency-symbol"><?=htmlspecialchars((string)$currency_symbol, ENT_QUOTES, 'UTF-8')?></span>)</h6>
                </div>
                <div class="mb-3 col-md-1">
                  <h6 style="font-weight: bold;">Tax rate (%)</h6>
                </div>
                <div class="mb-3 col-md-1">
                  <h6 style="font-weight: bold;">Line total (<span class="currency-symbol"><?=htmlspecialchars((string)$currency_symbol, ENT_QUOTES, 'UTF-8')?></span>)</h6>
                </div>
                <div class="mb-3 col-md-1">
                  <h6 style="font-weight: bold;">Action</h6>
                </div>
              </div>

              <div class="row">
                <div class="mb-3 col-md-12 text-center">
                  <a href="javascript:void(0);" class="btn btn-success add_button" title="Add row">
                    <i class="fa fa-plus-circle"></i>&nbsp;Add Item
                  </a>
                </div>
              </div>

              <div class="field_wrapper">
                <?php
                $po_items = (($row)?PurchaseOrderItem::where('purchase_order_id', '=', $id)->get():[]);
                if($row && $po_items){ $sl=101; foreach($po_items as $po_item){
                ?>
                  <div class="row" style="border:1px solid #04163d1f; padding:10px; border-radius:10px;margin-bottom:5px;">
                    <div class="mb-3 col-md-2">
                      <select name="item_id[]" class="form-select" id="item_id_<?= $sl?>" required onchange="getItemInfo(this.value, <?= $sl?>);">
                        <option value="" selected>Select Items</option>
                        <?php if ($items) {
                          foreach ($items as $item) { ?>
                          <option value="<?= $item->id ?>" <?= (($item->id == $po_item->item_id)?'selected':'') ?>><?= $item->name ?></option>
                        <?php }
                        } ?>
                      </select>
                      <span class="row-loader d-none" id="loader_<?= $sl?>">
                        <i class="fa fa-spinner fa-spin"></i>
                      </span>
                    </div>
                    <div class="mb-3 col-md-1">
                      <span id="supplier_sku_text_<?= $sl?>"><?= $po_item->supplier_sku ?></span>
                      <input type="hidden" name="supplier_sku[]" id="supplier_sku_val_<?= $sl?>" value="<?= $po_item->supplier_sku ?>">
                    </div>
                    <div class="mb-3 col-md-1">
                      <span id="merchant_sku_text_<?= $sl?>"><?= $po_item->merchant_sku ?></span>
                      <input type="hidden" name="merchant_sku[]" id="merchant_sku_val_<?= $sl?>" value="<?= $po_item->merchant_sku ?>">
                    </div>
                    <div class="mb-3 col-md-3">
                      <span id="item_name_text_<?= $sl?>"><?= $po_item->item_name ?></span>
                      <input type="hidden" name="item_name[]" id="item_name_val_<?= $sl?>" value="<?= $po_item->item_name ?>">
                    </div>
                    <div class="mb-3 col-md-1">
                      <input type="text" class="form-control" name="qty[]" maxlength="4" id="qty_val_<?= $sl?>" required value="<?= $po_item->qty ?>">
                    </div>
                    <div class="mb-3 col-md-1">
                      <input type="text" class="form-control" name="cost_price[]" id="cost_price_val_<?= $sl?>" value="<?= $po_item->cost_price ?>">
                    </div>
                    <div class="mb-3 col-md-1">
                      <span id="tax_percent_text_<?= $sl?>"><?= $po_item->tax_percent ?></span>
                      <input type="hidden" name="tax_percent[]" id="tax_percent_val_<?= $sl?>" value="<?= $po_item->tax_percent ?>">
                      <input type="hidden" name="tax_amount[]" id="tax_amount_val_<?= $sl?>" value="<?= $po_item->tax_amount ?>">
                    </div>
                    <div class="mb-3 col-md-1">
                      <span id="total_inc_tax_text_<?= $sl?>"><?= $po_item->total_inc_tax ?></span>
                      <input type="hidden" class="form-control" name="total_inc_tax[]" id="total_inc_tax_val_<?= $sl?>" value="<?= $po_item->total_inc_tax ?>">
                    </div>
                    <div class="mb-3 col-md-1">
                      <a href="javascript:void(0);" class="btn btn-danger remove_button" title="Remove row">
                        <i class="fa fa-minus-circle"></i>
                      </a>
                    </div>
                  </div>
                <?php $sl++; } } elseif($hasPrefillItems){ $sl=201; foreach($prefillItems as $prefillItem){
                    $supplierSku = (($prefillItem->supplier_sku != '')?$prefillItem->supplier_sku:$prefillItem->sku);
                    $merchantSku = $prefillItem->sku;
                    $itemName = (($prefillItem->supplier_product_name != '')?$prefillItem->supplier_product_name:$prefillItem->name);
                    $costPrice = $prefillItem->cost_price_ex_tax;
                    $taxPercent = $prefillItem->cost_price_tax;
                    $taxAmount = ($costPrice * $taxPercent) / 100;
                    $lineTotal = ($costPrice + $taxAmount);
                ?>
                  <div class="row" style="border:1px solid #04163d1f; padding:10px; border-radius:10px;margin-bottom:5px;">
                    <div class="mb-3 col-md-2">
                      <select name="item_id[]" class="form-select" id="item_id_<?= $sl?>" required onchange="getItemInfo(this.value, <?= $sl?>);">
                        <option value="" selected>Select Items</option>
                        <?php if ($items) {
                          foreach ($items as $item) { ?>
                          <option value="<?= $item->id ?>" <?= (($item->id == $prefillItem->id)?'selected':'') ?>><?= $item->name ?></option>
                        <?php }
                        } ?>
                      </select>
                      <span class="row-loader d-none" id="loader_<?= $sl?>">
                        <i class="fa fa-spinner fa-spin"></i>
                      </span>
                    </div>
                    <div class="mb-3 col-md-1">
                      <span id="supplier_sku_text_<?= $sl?>"><?= $supplierSku ?></span>
                      <input type="hidden" name="supplier_sku[]" id="supplier_sku_val_<?= $sl?>" value="<?= $supplierSku ?>">
                    </div>
                    <div class="mb-3 col-md-1">
                      <span id="merchant_sku_text_<?= $sl?>"><?= $merchantSku ?></span>
                      <input type="hidden" name="merchant_sku[]" id="merchant_sku_val_<?= $sl?>" value="<?= $merchantSku ?>">
                    </div>
                    <div class="mb-3 col-md-3">
                      <span id="item_name_text_<?= $sl?>"><?= $itemName ?></span>
                      <input type="hidden" name="item_name[]" id="item_name_val_<?= $sl?>" value="<?= $itemName ?>">
                    </div>
                    <div class="mb-3 col-md-1">
                      <input type="text" class="form-control" name="qty[]" maxlength="4" id="qty_val_<?= $sl?>" required value="1">
                    </div>
                    <div class="mb-3 col-md-1">
                      <input type="text" class="form-control" name="cost_price[]" id="cost_price_val_<?= $sl?>" value="<?= $costPrice ?>">
                    </div>
                    <div class="mb-3 col-md-1">
                      <span id="tax_percent_text_<?= $sl?>"><?= $taxPercent ?>%</span>
                      <input type="hidden" name="tax_percent[]" id="tax_percent_val_<?= $sl?>" value="<?= $taxPercent ?>">
                      <input type="hidden" name="tax_amount[]" id="tax_amount_val_<?= $sl?>" value="<?= number_format($taxAmount, 2, '.', '') ?>">
                    </div>
                    <div class="mb-3 col-md-1">
                      <span id="total_inc_tax_text_<?= $sl?>"><?= number_format($lineTotal, 2, '.', '') ?></span>
                      <input type="hidden" class="form-control" name="total_inc_tax[]" id="total_inc_tax_val_<?= $sl?>" value="<?= number_format($lineTotal, 2, '.', '') ?>">
                    </div>
                    <div class="mb-3 col-md-1">
                      <a href="javascript:void(0);" class="btn btn-danger remove_button" title="Remove row">
                        <i class="fa fa-minus-circle"></i>
                      </a>
                    </div>
                  </div>
                <?php $sl++; } }?>
              </div>

              <div class="row">
                <div class="mb-3 col-md-6 text-center">

                </div>
                <div class="invoice-footer mb-3 col-md-4 text-center">
                  <h6 style="font-weight: bold;">Total Lines</h6>
                </div>
                <div class="invoice-footer mb-3 col-md-2 text-center">
                  <span id="total_lines_text"><?= $total_lines ?></span>
                  <input type="hidden" name="total_lines" id="total_lines_val" value="<?= $total_lines ?>">
                </div>
              </div>

              <div class="row">
                <div class="mb-3 col-md-6 text-center">

                </div>
                <div class="invoice-footer mb-3 col-md-4 text-center">
                  <h6 style="font-weight: bold;">Total Bottles</h6>
                </div>
                <div class="invoice-footer mb-3 col-md-2 text-center">
                  <span id="total_quantity_text"><?= $total_quantity ?></span>
                  <input type="hidden" name="total_quantity" id="total_quantity_val" value="<?= $total_quantity ?>">
                </div>
              </div>

              <div class="row">
                <div class="mb-3 col-md-6 text-center">

                </div>
                <div class="invoice-footer mb-3 col-md-4 text-center">
                  <h6 style="font-weight: bold;">Ex GST</h6>
                </div>
                <div class="invoice-footer mb-3 col-md-2 text-center">
                  <span id="subtotal_text"><span class="currency-symbol"><?=htmlspecialchars((string)$currency_symbol, ENT_QUOTES, 'UTF-8')?></span><?= $subtotal ?></span>
                  <input type="hidden" name="subtotal" id="subtotal_val" value="<?= $subtotal ?>">
                </div>
              </div>

              <div class="row">
                <div class="mb-3 col-md-6 text-center">

                </div>
                <div class="invoice-footer mb-3 col-md-4 text-center">
                  <h6 style="font-weight: bold;">GST</h6>
                </div>
                <div class="invoice-footer mb-3 col-md-2 text-center">
                  <span id="tax_total_text"><span class="currency-symbol"><?=htmlspecialchars((string)$currency_symbol, ENT_QUOTES, 'UTF-8')?></span><?= $tax_total ?></span>
                  <input type="hidden" name="tax_total" id="tax_total_val" value="<?= $tax_total ?>">
                </div>
              </div>

              <div class="row">
                <div class="mb-3 col-md-6 text-center">

                </div>
                <div class="invoice-footer mb-3 col-md-4 text-center">
                  <h6 style="font-weight: bold;">Total inc GST</h6>
                </div>
                <div class="invoice-footer mb-3 col-md-2 text-center">
                  <span id="total_inc_tax_text"><span class="currency-symbol"><?=htmlspecialchars((string)$currency_symbol, ENT_QUOTES, 'UTF-8')?></span><?= $total_inc_tax ?></span>
                  <input type="hidden" name="total_inc_tax_val" id="total_inc_tax_val" value="<?= $total_inc_tax ?>">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <label for="note" class="form-label">Note</label>
                  <textarea class="form-control" id="note" name="note" rows="3"><?= $note ?></textarea>
                </div>
              </div>

              <div class="mt-2">
                <button type="submit" class="btn btn-primary me-2"><i class="fa fa-paper-plane"></i>&nbsp;&nbsp;<?= (($row) ? 'Save' : 'Add') ?></button>
              </div>
            <?php } ?>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
  const purchaseOrderAddUrl = '<?= url('admin/' . $controllerRoute . '/add') ?>';
  const supplierItemsUrl = '<?= url('admin/' . $controllerRoute . '/supplier-items') ?>';
  const purchaseOrderIsEdit = <?=($row?'true':'false')?>;
  let supplierItems = <?=json_encode($items->map(function($item){
    return ['id' => $item->id, 'name' => $item->name];
  })->values(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)?>;

  function clearPurchaseOrderItemRow(row) {
    row.find('span[id^="supplier_sku_text_"], span[id^="merchant_sku_text_"], span[id^="item_name_text_"], span[id^="tax_percent_text_"], span[id^="total_inc_tax_text_"]').text('');
    row.find('input[name="supplier_sku[]"], input[name="merchant_sku[]"], input[name="item_name[]"], input[name="qty[]"], input[name="cost_price[]"], input[name="tax_percent[]"], input[name="tax_amount[]"], input[name="total_inc_tax[]"]').val('');
  }

  function populateSupplierItemSelect(select, selectedItemId) {
    const itemSelect = $(select);
    const selectedId = String(selectedItemId || '');
    itemSelect.empty().append(new Option('Select Items', ''));

    supplierItems.forEach(function(item) {
      itemSelect.append(new Option(item.name, item.id, false, String(item.id) === selectedId));
    });

    if (selectedId && itemSelect.val() !== selectedId) {
      clearPurchaseOrderItemRow(itemSelect.closest('.row'));
    }
  }

  function refreshSupplierItemSelects() {
    $('select[name="item_id[]"]').each(function() {
      populateSupplierItemSelect(this, this.value);
    });
    recalculateInvoiceFooter();
  }

  document.getElementById('supplier_id').addEventListener('change', function() {
    if (!purchaseOrderIsEdit) {
      window.location.href = this.value
        ? purchaseOrderAddUrl + '?supplier_id=' + encodeURIComponent(this.value) + '&currency_symbol=' + encodeURIComponent($('#currency_symbol').val() || '$')
        : purchaseOrderAddUrl;
      return;
    }

    const supplierId = this.value;
    if (!supplierId) {
      supplierItems = [];
      refreshSupplierItemSelects();
      return;
    }

    $.getJSON(supplierItemsUrl, { supplier_id: supplierId })
      .done(function(items) {
        supplierItems = items;
        refreshSupplierItemSelects();
      })
      .fail(function() {
        supplierItems = [];
        refreshSupplierItemSelects();
        showSupplierProductMessage('error', 'Unable to load products for the selected supplier.');
      });
  });

  function showSupplierProductMessage(type, message) {
    if (typeof toastAlert === 'function') {
      toastAlert(type, message);
      return;
    }
    alert(message.replace(/<br\s*\/?>/gi, "\n"));
  }

  $(document).on('change', '#supplier_product_select_all', function() {
    $('.supplier-product-checkbox').prop('checked', this.checked);
  });

  $(document).on('change', '.supplier-product-checkbox', function() {
    const totalProducts = $('.supplier-product-checkbox').length;
    const selectedProducts = $('.supplier-product-checkbox:checked').length;
    $('#supplier_product_select_all').prop('checked', totalProducts > 0 && totalProducts === selectedProducts);
  });

  $(document).on('click', '#proceedSupplierProducts', function() {
    const productIds = $('.supplier-product-checkbox:checked').map(function() {
      return this.value;
    }).get();

    if (productIds.length <= 0) {
      showSupplierProductMessage('warning', 'Please select at least one product.');
      return;
    }

    window.location.href = purchaseOrderAddUrl + '?product_ids=' + encodeURIComponent(productIds.join(',')) + '&currency_symbol=' + encodeURIComponent($('#currency_symbol').val() || '$');
  });
</script>
<script>
  $(document).ready(function() {
    var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper

    var x = 1; //Initial field counter is 1

    // Once add button is clicked
    $(addButton).click(function() {
      //Check maximum number of input fields
      if (x < maxField) {
        var fieldHTML = `<div class="row" style="border:1px solid #04163d1f; padding:10px; border-radius:10px;margin-bottom:5px;">
                          <div class="mb-3 col-md-2">
                            <select name="item_id[]" class="form-select" id="item_id_${x}" required onchange="getItemInfo(this.value, ${x});">
                              <option value="" selected>Select Items</option>
                            </select>
                            <span class="row-loader d-none" id="loader_${x}">
                              <i class="fa fa-spinner fa-spin"></i>
                            </span>
                          </div>
                          <div class="mb-3 col-md-1">
                            <span id="supplier_sku_text_${x}"></span>
                            <input type="hidden" name="supplier_sku[]" id="supplier_sku_val_${x}">
                          </div>
                          <div class="mb-3 col-md-1">
                            <span id="merchant_sku_text_${x}"></span>
                            <input type="hidden" name="merchant_sku[]" id="merchant_sku_val_${x}">
                          </div>
                          <div class="mb-3 col-md-3">
                            <span id="item_name_text_${x}"></span>
                            <input type="hidden" name="item_name[]" id="item_name_val_${x}">
                          </div>
                          <div class="mb-3 col-md-1">
                            <input type="text" class="form-control" name="qty[]" maxlength="4" id="qty_val_${x}" required>
                          </div>
                          <div class="mb-3 col-md-1">
                            <input type="text" class="form-control" name="cost_price[]" id="cost_price_val_${x}">
                          </div>
                          <div class="mb-3 col-md-1">
                            <span id="tax_percent_text_${x}"></span>
                            <input type="hidden" name="tax_percent[]" id="tax_percent_val_${x}">
                            <input type="hidden" name="tax_amount[]" id="tax_amount_val_${x}">
                          </div>
                          <div class="mb-3 col-md-1">
                            <span id="total_inc_tax_text_${x}"></span>
                            <input type="hidden" class="form-control" name="total_inc_tax[]" id="total_inc_tax_val_${x}">
                          </div>
                          <div class="mb-3 col-md-1">
                            <a href="javascript:void(0);" class="btn btn-danger remove_button" title="Remove row">
                              <i class="fa fa-minus-circle"></i>
                            </a>
                          </div>
                        </div>`; //New input field html

        $(wrapper).append(fieldHTML); //Add field html
        populateSupplierItemSelect($(wrapper).find('select[name="item_id[]"]').last(), '');
        x++; //Increase field counter
      } else {
        alert('A maximum of ' + maxField + ' fields are allowed to be added. ');
      }
    });

    // Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e) {
      e.preventDefault();
      $(this).closest('.row').remove();
      recalculateInvoiceFooter();
    });
  });

  function getItemInfo(item_id, row) {
    // let selectedItems = [];

    // if (selectedItems.includes(item_id)) {
    //     alert('This item is already selected!');
    //     $('#item_id_' + row).val('');
    //     return;
    // }

    // selectedItems.push(item_id);

    if (!item_id) return;

    $('#loader_' + row).removeClass('d-none');
    let base_url = '<?= url('/admin') ?>';
    $.ajax({
        url: base_url + "/get-item-info",
        type: "GET",
        data: {
          item_id: item_id,
          supplier_id: $('#supplier_id').val()
        },
        dataType: "json",

        success: function (res) {
            $('#supplier_sku_text_' + row).text(res.supplier_sku);
            $('#supplier_sku_val_' + row).val(res.supplier_sku);

            $('#merchant_sku_text_' + row).text(res.merchant_sku);
            $('#merchant_sku_val_' + row).val(res.merchant_sku);

            $('#item_name_text_' + row).text(res.name);
            $('#item_name_val_' + row).val(res.name);

            $('#qty_val_' + row).val(1);
            $('#cost_price_val_' + row).val(res.cost_price);

            $('#tax_percent_text_' + row).text(res.tax_percent + '%');
            $('#tax_percent_val_' + row).val(res.tax_percent);
            $('#tax_amount_val_' + row).val(res.tax_amount);

            $('#total_inc_tax_text_' + row).text(res.total_inc_tax);
            $('#total_inc_tax_val_' + row).val(res.total_inc_tax);

            // Disable selected item
            // $('#item_id_' + row).prop('disabled', true);

            // 🔥 force row calc + footer update
            $('#qty_val_' + row).trigger('change');
            recalculateInvoiceFooter();
        },

        error: function () {
            const itemSelect = $('#item_id_' + row);
            itemSelect.val('');
            clearPurchaseOrderItemRow(itemSelect.closest('.row'));
            recalculateInvoiceFooter();
            showSupplierProductMessage('error', 'That product is not available for the selected supplier.');
        },

        complete: function () {
            $('#loader_' + row).addClass('d-none');
        }
    });
  }

  $(document).on(
    'keyup change',
    'input[name="qty[]"], input[name="cost_price[]"]',
    function () {

        let row = $(this).closest('.row');

        let qty        = parseFloat(row.find('input[name="qty[]"]').val()) || 0;
        let price      = parseFloat(row.find('input[name="cost_price[]"]').val()) || 0;
        let taxPercent = parseFloat(row.find('input[name="tax_percent[]"]').val()) || 0;

        let subTotal  = qty * price;
        let taxAmount = (subTotal * taxPercent) / 100;
        let total     = subTotal + taxAmount;

        // Row updates
        row.find('input[name="tax_amount[]"]').val(taxAmount.toFixed(2));
        row.find('input[name="total_inc_tax[]"]').val(total.toFixed(2));
        row.find('span[id^="total_inc_tax_text_"]').text(total.toFixed(2));

        // ✅ Footer update
        recalculateInvoiceFooter();
    }
);

function recalculateInvoiceFooter() {

    let totalLines = 0;
    let totalQty   = 0;
    let subTotal   = 0;
    let taxTotal   = 0;
    let grandTotal = 0;

    $('.field_wrapper .row').each(function () {

        let qty        = parseFloat($(this).find('input[name="qty[]"]').val()) || 0;
        let price      = parseFloat($(this).find('input[name="cost_price[]"]').val()) || 0;
        let taxPercent = parseFloat($(this).find('input[name="tax_percent[]"]').val()) || 0;

        if (qty > 0) totalLines++;

        let rowSubTotal = qty * price;
        let rowTax      = (rowSubTotal * taxPercent) / 100;
        let rowTotal    = rowSubTotal + rowTax;

        totalQty   += qty;
        subTotal   += rowSubTotal;
        taxTotal   += rowTax;
        grandTotal += rowTotal;
    });

    // ✅ TEXT (formatted)
    $('#total_lines_text').text(totalLines);
    $('#total_quantity_text').text(totalQty);
    $('#subtotal_text').text(formatCurrency(subTotal));
    $('#tax_total_text').text(formatCurrency(taxTotal));
    $('#total_inc_tax_text').text(formatCurrency(grandTotal));

    // ✅ INPUTS (raw numbers for backend)
    $('#total_lines_val').val(totalLines);
    $('#total_quantity_val').val(totalQty);
    $('#subtotal_val').val(subTotal.toFixed(2));
    $('#tax_total_val').val(taxTotal.toFixed(2));
    $('#total_inc_tax_val').val(grandTotal.toFixed(2));
}

function formatCurrency(amount) {
    return ($('#currency_symbol').val() || '$') + amount.toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

$(document).on('input', '#currency_symbol', function() {
    const symbol = this.value || '$';
    $('.currency-symbol').text(symbol);
    recalculateInvoiceFooter();
});
</script>
<script>
  const fieldOrder = [
      'select[name="item_id[]"]',
      'input[name="qty[]"]',
      'input[name="cost_price[]"]'
  ];
  $(document).on('keydown', 'select, input', function (e) {

      if (e.key !== 'Enter') return;

      e.preventDefault();

      let row = $(this).closest('.row');
      let inputs = row.find(fieldOrder.join(','));
      let index = inputs.index(this);

      if (index < inputs.length - 1) {
          inputs.eq(index + 1).focus();
      } else {
          focusNextRow(row);
      }
  });
  function focusNextRow(currentRow) {

      let nextRow = currentRow.next('.row');

      if (nextRow.length) {
          nextRow.find('select[name="item_id[]"]').focus();
      } else {
          $('.add_button').trigger('click');

          setTimeout(() => {
              $('.field_wrapper .row:last')
                  .find('select[name="item_id[]"]')
                  .focus();
          }, 100);
      }
  }
  $(document).on('keydown', function (e) {

      if (e.key === '+') {
          e.preventDefault();
          $('.add_button').trigger('click');

          setTimeout(() => {
              $('.field_wrapper .row:last')
                  .find('select[name="item_id[]"]')
                  .focus();
          }, 100);
      }
  });
  $(document).on('keydown', 'input, select', function (e) {

      if (e.key === 'Delete') {

          let row = $(this).closest('.row');
          row.remove();

          recalculateInvoiceFooter();

          let prevRow = row.prev('.row');
          if (prevRow.length) {
              prevRow.find('input[name="qty[]"]').focus();
          }
      }
  });
  $(document).on('keydown', 'input[name="qty[]"]', function (e) {

      let row = $(this).closest('.row');

      if (e.key === 'ArrowDown') {
          e.preventDefault();
          row.next('.row')?.find('input[name="qty[]"]').focus();
      }

      if (e.key === 'ArrowUp') {
          e.preventDefault();
          row.prev('.row')?.find('input[name="qty[]"]').focus();
      }
  });
  $(document).on('focus', 'input[name="qty[]"]', function () {
      this.select();
  });
</script>
