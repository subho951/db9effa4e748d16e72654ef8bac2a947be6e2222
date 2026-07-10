<?php
use App\Helpers\Helper;

$current_url = url()->current();
$escape = function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$number = function ($value) {
    return number_format((float) $value, 0);
};

$totalItems = count($rows);
$totalWarehouseStock = 0;
$totalShopStock = 0;
$totalWastageStock = 0;
foreach ($rows as $summaryRow) {
    $totalWarehouseStock += (int) $summaryRow->warehouse_stock;
    $totalShopStock += (int) $summaryRow->shop_stock;
    $totalWastageStock += (int) ($summaryRow->wastage_stock ?? 0);
}
?>
<style type="text/css">
  .warehouse-inventory-page {
    color: #24313f;
    max-width: none !important;
    width: 100%;
    padding-left: 8px !important;
    padding-right: 8px !important;
    padding-top: 12px !important;
  }
  .warehouse-inventory-page .page-heading-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
  }
  .warehouse-inventory-page h4 {
    margin: 0;
    font-size: 24px;
    font-weight: 500;
    color: #2b3137;
  }
  .warehouse-page-actions {
    display: inline-flex;
    align-items: center;
    gap: 22px;
    color: #2f80b7;
    font-size: 12px;
    font-weight: 700;
  }
  .warehouse-page-actions a,
  .warehouse-page-actions button {
    border: 0;
    background: transparent;
    color: #2f80b7;
    padding: 0;
    font: inherit;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .warehouse-reference-shell {
    background: #fff;
    border: 1px solid #dce3e9;
    box-shadow: 0 2px 8px rgba(36, 49, 63, .08);
  }
  .warehouse-filter-row {
    display: grid;
    grid-template-columns: minmax(280px, 1fr) auto;
    border-bottom: 1px solid #dce3e9;
  }
  .warehouse-search-field {
    position: relative;
    min-height: 38px;
  }
  .warehouse-search-field i {
    position: absolute;
    top: 50%;
    left: 14px;
    transform: translateY(-50%);
    color: #6f7d89;
    font-size: 13px;
    pointer-events: none;
  }
  .warehouse-search-field input {
    width: 100%;
    height: 38px;
    border: 0;
    border-radius: 0;
    color: #3b4650;
    font-size: 13px;
    padding: 0 14px 0 39px;
    outline: 0;
  }
  .warehouse-search-field input::placeholder {
    color: #8b97a3;
  }
  .warehouse-more-filter {
    min-width: 126px;
    border: 0;
    border-left: 1px solid #dce3e9;
    background: #fff;
    color: #2f80b7;
    font-size: 11px;
    font-weight: 800;
    padding: 0 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 38px;
  }
  .warehouse-filter-panel {
    display: none;
    grid-template-columns: repeat(3, minmax(160px, 1fr));
    gap: 12px;
    padding: 12px 14px;
    border-bottom: 1px solid #e8edf2;
    background: #fbfcfd;
  }
  .warehouse-filter-panel.is-open {
    display: grid;
  }
  .warehouse-filter-panel .form-control,
  .warehouse-filter-panel .form-select {
    min-height: 36px;
    border-radius: 2px;
    border-color: #cfd8df;
    font-size: 12px;
  }
  .warehouse-summary-strip {
    display: grid;
    grid-template-columns: repeat(4, minmax(130px, 1fr));
    gap: 0;
    border-bottom: 1px solid #e8edf2;
    background: #fbfcfd;
  }
  .warehouse-summary-item {
    padding: 12px 14px;
    border-right: 1px solid #e8edf2;
  }
  .warehouse-summary-item:last-child {
    border-right: 0;
  }
  .warehouse-summary-item span {
    display: block;
    color: #7b8793;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 3px;
  }
  .warehouse-summary-item strong {
    display: block;
    color: #24313f;
    font-size: 18px;
    font-weight: 600;
  }
  .warehouse-tab-row {
    padding: 42px 14px 18px;
  }
  .warehouse-tab {
    border: 0;
    background: transparent;
    color: #4d5863;
    font-size: 13px;
    padding: 0 12px 10px;
    border-bottom: 1px solid #22b8b0;
  }
  .warehouse-table-wrap {
    overflow-x: hidden;
  }
  .warehouse-inventory-table {
    width: 100%;
    min-width: 0;
    margin: 0;
    table-layout: fixed;
    border-collapse: collapse;
  }
  .warehouse-inventory-table th {
    border-top: 1px solid #e2e7ec;
    border-bottom: 1px solid #e2e7ec;
    color: #232b34;
    font-size: 12px;
    font-weight: 700;
    padding: 10px 6px;
    background: #fff;
    vertical-align: middle;
  }
  .warehouse-inventory-table th .sort-hint {
    float: right;
    color: #c4ccd3;
    font-size: 11px;
    margin-top: 2px;
  }
  .warehouse-inventory-table td {
    border-bottom: 1px solid #e7ebef;
    color: #1f2a33;
    font-size: 12px;
    padding: 9px 6px;
    vertical-align: middle;
    min-height: 54px;
  }
  .warehouse-col-item {
    width: 23%;
  }
  .warehouse-col-stock,
  .warehouse-col-committed,
  .warehouse-col-available {
    width: 10%;
  }
  .warehouse-col-edit {
    width: 18%;
  }
  .warehouse-col-location {
    width: 15%;
  }
  .warehouse-col-wastage {
    width: 7%;
  }
  .warehouse-col-history {
    width: 7%;
  }
  .inventory-item-name {
    color: #2d7fab;
    font-weight: 500;
    line-height: 1.35;
    display: inline-block;
  }
  .inventory-item-meta {
    display: block;
    color: #8a95a1;
    font-size: 11px;
    line-height: 1.45;
  }
  .stock-cell,
  .awaiting-cell,
  .location-cell,
  .history-cell {
    text-align: center;
  }
  .stock-on-hand {
    background: #edfafd;
  }
  .stock-committed {
    background: #fff8ed;
  }
  .stock-available {
    background: #f2fbe9;
  }
  .awaiting-cell {
    background: #f2fbe9;
    color: #2d7fab;
  }
  .wastage-cell {
    background: #fff1f1;
    color: #c53030;
    text-align: center;
  }
  .stock-cell span,
  .awaiting-cell span,
  .wastage-cell span {
    display: inline-block;
    min-width: 34px;
    font-weight: 500;
  }
  .inventory-edit-control {
    display: grid;
    grid-template-columns: auto minmax(44px, 1fr) auto;
    align-items: center;
    gap: 0;
    max-width: 198px;
  }
  .stock-mode-group {
    display: inline-flex;
    align-items: center;
  }
  .stock-mode-btn,
  .stock-save-btn {
    height: 28px;
    border: 1px solid #c5ccd2;
    background: #fff;
    color: #2c353d;
    font-size: 11px;
    font-weight: 700;
    padding: 0 9px;
    border-radius: 0;
  }
  .stock-mode-btn:first-child {
    background: #e7e7e7;
  }
  .stock-mode-btn.active {
    background: #d8d8d8;
    border-color: #aeb7bf;
  }
  .stock-mode-btn + .stock-mode-btn {
    border-left: 0;
  }
  .stock-qty-input {
    height: 28px;
    min-width: 44px;
    border: 1px solid #cfd6dc;
    border-left: 0;
    border-right: 0;
    text-align: center;
    color: #26313b;
    outline: 0;
    font-size: 12px;
    padding: 0 6px;
  }
  .stock-save-btn {
    min-width: 45px;
    color: #a0a9b1;
    background: #eef1f4;
  }
  .stock-save-btn:not(:disabled) {
    color: #fff;
    background: #2f80b7;
    border-color: #2f80b7;
  }
  .stock-save-btn.is-saving {
    opacity: .75;
  }
  .location-cell strong,
  .location-cell small {
    display: block;
    line-height: 1.35;
  }
  .location-cell strong {
    color: #1f2a33;
    font-size: 12px;
    font-weight: 500;
  }
  .location-cell small {
    color: #7b8793;
    font-size: 11px;
  }
  .return-stock-btn {
    height: 24px;
    border: 1px solid #c5ccd2;
    background: #fff;
    color: #2f80b7;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin-top: 7px;
    padding: 0 7px;
    font-size: 10px;
    font-weight: 800;
  }
  .return-stock-btn:hover {
    background: #eef8fc;
    color: #0e5f8c;
  }
  .return-stock-btn:disabled {
    background: #f8fafb;
    color: #b6bec7;
    cursor: not-allowed;
  }
  .history-cell {
    white-space: nowrap;
  }
  .history-link {
    width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #2d7fab;
    border: 0;
    background: transparent;
    border-radius: 50%;
    margin: 0;
  }
  .history-link:hover {
    background: #eaf4fa;
    color: #0e5f8c;
  }
  .warehouse-row-success td {
    animation: warehouseSuccess 2.8s ease forwards;
  }
  .warehouse-row-error td {
    animation: warehouseError 2.8s ease forwards;
  }
  @keyframes warehouseSuccess {
    0% { box-shadow: inset 0 0 0 999px rgba(34, 184, 176, .14); }
    100% { box-shadow: inset 0 0 0 999px rgba(34, 184, 176, 0); }
  }
  @keyframes warehouseError {
    0% { box-shadow: inset 0 0 0 999px rgba(220, 53, 69, .13); }
    100% { box-shadow: inset 0 0 0 999px rgba(220, 53, 69, 0); }
  }
  .warehouse-pagination-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 16px 18px;
    color: #8a95a1;
    font-size: 12px;
  }
  .warehouse-page-size {
    display: inline-flex;
    align-items: center;
    gap: 7px;
  }
  .warehouse-page-size select {
    width: 54px;
    height: 28px;
    border: 1px solid #cfd8df;
    border-radius: 2px;
    color: #1f2a33;
    font-size: 12px;
    padding: 0 7px;
  }
  .warehouse-pager {
    display: inline-flex;
    align-items: center;
  }
  .warehouse-page-btn {
    min-width: 28px;
    height: 28px;
    border: 1px solid #d9e0e6;
    border-left: 0;
    background: #fff;
    color: #61707e;
    font-size: 12px;
  }
  .warehouse-page-btn:first-child {
    border-left: 1px solid #d9e0e6;
  }
  .warehouse-page-btn.active {
    background: #22384d;
    border-color: #22384d;
    color: #fff;
  }
  .warehouse-page-btn:disabled {
    color: #b6bec7;
    background: #f8fafb;
  }
  .warehouse-formula {
    text-align: center;
    padding: 0 16px 24px;
    color: #1f2a33;
    font-size: 12px;
  }
  .warehouse-formula span {
    display: inline-block;
    margin: 0 6px;
  }
  .warehouse-formula .legend-item {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin: 0;
  }
  .warehouse-formula .legend-swatch {
    width: 18px;
    height: 12px;
    border: 1px solid rgba(36, 49, 63, .1);
    border-radius: 2px;
  }
  .warehouse-formula .legend-on-hand {
    background: #edfafd;
  }
  .warehouse-formula .legend-committed {
    background: #fff8ed;
  }
  .warehouse-formula .legend-available {
    background: #f2fbe9;
  }
  .warehouse-empty-row td {
    text-align: center;
    padding: 30px 14px;
    color: #7b8793;
  }
  .stock-return-modal .modal-dialog {
    max-width: 480px;
  }
  .stock-return-modal .modal-content {
    border: 1px solid #dce3e9;
    border-radius: 2px;
    box-shadow: 0 18px 42px rgba(36, 49, 63, .18);
  }
  .stock-return-modal .modal-header {
    border-bottom: 1px solid #e8edf2;
    padding: 14px 16px;
  }
  .stock-return-modal .modal-title {
    color: #24313f;
    font-size: 16px;
    font-weight: 700;
  }
  .stock-return-modal .btn-close {
    background: transparent;
    border: 0;
    color: #6f7d89;
    opacity: 1;
  }
  .stock-return-modal .modal-body {
    padding: 16px;
  }
  .stock-return-modal .return-qty-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }
  .stock-return-modal .return-calculation {
    border: 1px solid #e8edf2;
    background: #fbfcfd;
    color: #4d5863;
    font-size: 12px;
    padding: 9px 10px;
    margin-bottom: 14px;
  }
  .stock-return-modal .return-calculation strong {
    color: #24313f;
  }
  .stock-return-modal .return-product-meta {
    background: #fbfcfd;
    border: 1px solid #e8edf2;
    padding: 10px 12px;
    margin-bottom: 14px;
  }
  .stock-return-modal .return-product-meta strong {
    display: block;
    color: #2d7fab;
    font-size: 13px;
    line-height: 1.35;
  }
  .stock-return-modal .return-product-meta span {
    display: block;
    color: #7b8793;
    font-size: 12px;
    margin-top: 3px;
  }
  .stock-return-modal label {
    color: #4d5863;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 6px;
  }
  .stock-return-modal .form-control {
    border-radius: 2px;
    border-color: #cfd8df;
    font-size: 13px;
  }
  .stock-return-modal .modal-footer {
    border-top: 1px solid #e8edf2;
    padding: 12px 16px;
  }
  .stock-return-modal .return-submit-btn {
    height: 34px;
    border: 1px solid #2f80b7;
    background: #2f80b7;
    color: #fff;
    border-radius: 2px;
    padding: 0 16px;
    font-size: 12px;
    font-weight: 800;
  }
  .stock-return-modal .return-submit-btn:disabled {
    opacity: .72;
  }
  .stock-return-modal .return-cancel-btn {
    height: 34px;
    border: 1px solid #cfd8df;
    background: #fff;
    color: #4d5863;
    border-radius: 2px;
    padding: 0 14px;
    font-size: 12px;
    font-weight: 700;
  }
  @media (max-width: 991px) {
    .warehouse-summary-strip,
    .warehouse-filter-panel {
      grid-template-columns: repeat(2, minmax(130px, 1fr));
    }
    .warehouse-summary-item {
      border-right: 0;
      border-bottom: 1px solid #e8edf2;
    }
    .warehouse-summary-item:last-child {
      border-bottom: 0;
    }
  }
  @media (max-width: 767px) {
    .warehouse-inventory-page .page-heading-row,
    .warehouse-pagination-row {
      align-items: stretch;
      flex-direction: column;
    }
    .warehouse-filter-row {
      grid-template-columns: 1fr;
    }
    .warehouse-more-filter {
      border-left: 0;
      border-top: 1px solid #dce3e9;
      justify-content: flex-start;
    }
    .warehouse-page-actions {
      justify-content: space-between;
      gap: 12px;
    }
    .warehouse-summary-strip,
    .warehouse-filter-panel,
    .stock-return-modal .return-qty-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="container-xxl flex-grow-1 container-p-y warehouse-inventory-page">
  <div class="page-heading-row">
    <div>
      <h4>My inventory</h4>
    </div>
    <div class="warehouse-page-actions">
      <a href="<?=url('admin/dashboard')?>" title="Dashboard"><i class="fa fa-graduation-cap"></i></a>
      <button type="button" title="Settings"><i class="fa fa-gear"></i></button>
      <button type="button" title="Export">EXPORT <i class="fa fa-angle-down"></i></button>
    </div>
  </div>

  <div class="warehouse-reference-shell">
    <div class="warehouse-filter-row">
      <div class="warehouse-search-field">
        <i class="fa fa-filter"></i>
        <input type="text" placeholder="Filter by product name, brand, product type, supplier, season, tag..." id="myInput" autocomplete="off">
      </div>
      <button type="button" class="warehouse-more-filter" id="warehouse-more-filter" aria-expanded="false">
        MORE FILTERS <i class="fa fa-angle-up"></i>
      </button>
    </div>

    <div class="warehouse-filter-panel" id="warehouse-filter-panel">
      <select class="form-select" id="warehouse-stock-filter" aria-label="Filter by stock level">
        <option value="all" selected>All stock levels</option>
        <option value="in_stock">Available stock</option>
        <option value="low_stock">Low stock (1-5)</option>
        <option value="out_stock">No warehouse stock</option>
      </select>
      <input type="text" class="form-control" id="warehouse-brand-filter" placeholder="Brand">
      <input type="text" class="form-control" id="warehouse-supplier-filter" placeholder="Supplier">
    </div>

    <div class="warehouse-summary-strip">
      <div class="warehouse-summary-item">
        <span>Inventory items</span>
        <strong><?=number_format($totalItems)?></strong>
      </div>
      <div class="warehouse-summary-item">
        <span>Warehouse available</span>
        <strong id="warehouse-total-available"><?=$number($totalWarehouseStock)?></strong>
      </div>
      <div class="warehouse-summary-item">
        <span>Shop stock</span>
        <strong id="warehouse-total-shop"><?=$number($totalShopStock)?></strong>
      </div>
      <div class="warehouse-summary-item">
        <span>Wastage</span>
        <strong id="warehouse-total-wastage"><?=$number($totalWastageStock)?></strong>
      </div>
    </div>

    <div class="warehouse-tab-row">
      <button type="button" class="warehouse-tab">All</button>
    </div>

    <div class="warehouse-table-wrap">
      <table class="warehouse-inventory-table">
        <colgroup>
          <col class="warehouse-col-item">
          <col class="warehouse-col-stock">
          <col class="warehouse-col-committed">
          <col class="warehouse-col-available">
          <col class="warehouse-col-edit">
          <col class="warehouse-col-location">
          <col class="warehouse-col-wastage">
          <col class="warehouse-col-history">
        </colgroup>
        <thead>
          <tr>
            <th scope="col">Inventory items <i class="fa fa-sort sort-hint"></i></th>
            <th scope="col">On hand <i class="fa fa-sort sort-hint"></i></th>
            <th scope="col">Committed <i class="fa fa-sort sort-hint"></i></th>
            <th scope="col">Available <i class="fa fa-sort sort-hint"></i></th>
            <th scope="col">Edit available qty</th>
            <th scope="col">Location/s</th>
            <th scope="col">Wastage <i class="fa fa-sort sort-hint"></i></th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody id="item-list">
          <?php if ($totalItems > 0) { foreach ($rows as $row) {
              $warehouseStock = (int) $row->warehouse_stock;
              $shopStock = (int) $row->shop_stock;
              $wastageStock = (int) ($row->wastage_stock ?? 0);
              $onHandStock = $warehouseStock + $shopStock;
              $searchText = strtolower(trim($row->name . ' ' . $row->sku . ' ' . $row->barcode . ' ' . $row->brand_name . ' ' . $row->supplier_name . ' ' . $row->size_name . ' ' . $row->unit_name));
          ?>
            <tr class="warehouse-product-row productList"
                id="product-row-<?=$row->id?>"
                data-product-id="<?=$row->id?>"
                data-search="<?=$escape($searchText)?>"
                data-brand="<?=$escape(strtolower($row->brand_name))?>"
                data-supplier="<?=$escape(strtolower($row->supplier_name))?>"
                data-warehouse-stock="<?=$warehouseStock?>"
                data-shop-stock="<?=$shopStock?>"
                data-wastage-stock="<?=$wastageStock?>">
              <td>
                <span class="inventory-item-name"><?=$escape($row->name)?></span>
                <span class="inventory-item-meta">SKU: <?=$escape($row->sku)?></span>
                <?php if ($row->barcode != '') { ?>
                  <span class="inventory-item-meta">Barcode: <?=$escape($row->barcode)?></span>
                <?php } ?>
              </td>
              <td class="stock-cell stock-on-hand"><span id="on-hand-<?=$row->id?>"><?=$number($onHandStock)?></span></td>
              <td class="stock-cell stock-committed"><span id="shop-stock-<?=$row->id?>"><?=$number($shopStock)?></span></td>
              <td class="stock-cell stock-available"><span id="warehouse-stock-<?=$row->id?>"><?=$number($warehouseStock)?></span></td>
              <td>
                <div class="inventory-edit-control"
                     data-product-id="<?=$row->id?>"
                     data-product-name="<?=$escape($row->name)?>"
                     data-product-sku="<?=$escape($row->sku)?>">
                  <div class="stock-mode-group">
                    <button type="button" class="stock-mode-btn js-stock-mode active" data-mode="ADD" title="Add warehouse stock">ADD</button>
                    <button type="button" class="stock-mode-btn js-stock-mode" data-mode="SET" title="Set warehouse available quantity">SET</button>
                  </div>
                  <input type="number" class="stock-qty-input js-stock-qty" min="1" step="1" inputmode="numeric" aria-label="Stock quantity">
                  <button type="button" class="stock-save-btn js-stock-save" disabled>SAVE</button>
                </div>
              </td>
              <td class="location-cell">
                <strong>Warehouse</strong>
                <small>Shop: <span id="location-shop-<?=$row->id?>"><?=$number($shopStock)?></span></small>
                <button type="button"
                        class="return-stock-btn js-open-return-stock"
                        data-product-id="<?=$row->id?>"
                        data-product-name="<?=$escape($row->name)?>"
                        data-product-sku="<?=$escape($row->sku)?>"
                        data-shop-stock="<?=$shopStock?>"
                        <?=($shopStock <= 0 ? 'disabled' : '')?>>
                  <i class="fa fa-rotate-left"></i> RETURN
                </button>
              </td>
              <td class="wastage-cell"><span id="wastage-stock-<?=$row->id?>"><?=$number($wastageStock)?></span></td>
              <td class="history-cell">
                <a href="<?=url('admin/stock/warehouse-stock-history/' . Helper::encoded($row->id))?>" target="_blank" class="history-link" title="Warehouse history"><i class="fa fa-clock"></i></a>
                <a href="<?=url('admin/stock/shop-stock-history/' . Helper::encoded($row->id))?>" target="_blank" class="history-link" title="Shop history"><i class="fa fa-shop"></i></a>
              </td>
            </tr>
          <?php } } ?>
          <tr class="warehouse-empty-row" id="warehouse-empty-row" style="display: none;">
            <td colspan="8">No inventory items found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="warehouse-pagination-row">
      <label class="warehouse-page-size">
        <select id="warehouse-page-size">
          <option value="8" selected>8</option>
          <option value="16">16</option>
          <option value="32">32</option>
          <option value="64">64</option>
        </select>
        <span>per page</span>
      </label>
      <div class="warehouse-range" id="warehouse-range">0 of 0</div>
      <div class="warehouse-pager" id="warehouse-pager"></div>
    </div>

    <div class="warehouse-formula">
      <span class="legend-item"><span class="legend-swatch legend-on-hand"></span>On hand</span>
      <span>-</span>
      <span class="legend-item"><span class="legend-swatch legend-committed"></span>Committed</span>
      <span>=</span>
      <span class="legend-item"><span class="legend-swatch legend-available"></span>Available inventory</span>
      <i class="fa fa-circle-info"></i>
    </div>
  </div>
</div>

<div class="modal fade stock-return-modal" id="return-stock-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="returnStockForm">
        @csrf
        <input type="hidden" id="return_product_id" name="product_id">
        <div class="modal-header">
          <h5 class="modal-title">Return shop stock to warehouse</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
        </div>
        <div class="modal-body">
          <div class="return-product-meta">
            <strong id="return_product_name">Product</strong>
            <span id="return_product_stock">Shop stock: 0</span>
          </div>
          <div class="return-qty-grid mb-3">
            <div>
              <label for="return_txn_qty">Return Qty</label>
              <input type="number" class="form-control" id="return_txn_qty" name="txn_qty" min="0" step="1" inputmode="numeric">
            </div>
            <div>
              <label for="return_wastage_qty">Wastage Qty</label>
              <input type="number" class="form-control" id="return_wastage_qty" name="wastage_qty" min="0" step="1" inputmode="numeric" value="0">
            </div>
          </div>
          <div class="return-calculation">
            Shop stock deduction: <strong id="return_total_deduct">0</strong>
            <span class="text-muted">= Return <span id="return_calc_return">0</span> + Wastage <span id="return_calc_wastage">0</span></span>
          </div>
          <div class="mb-0">
            <label for="return_note">Return Note</label>
            <textarea class="form-control" id="return_note" name="note" rows="3" placeholder="Reason or reference for returning stock" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="return-cancel-btn" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="return-submit-btn" id="return_stock_submit">RETURN</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  (function($) {
    var currentPage = 1;
    var baseUrl = <?=json_encode(url('/'))?>;
    var projectKey = <?=json_encode(env('PROJECT_KEY'))?>;

    function parseStockValue(value) {
      var number = parseInt(value, 10);
      return isNaN(number) ? 0 : number;
    }

    function formatStockValue(value) {
      return parseStockValue(value).toLocaleString('en-US');
    }

    function todayForStock() {
      var date = new Date();
      var month = String(date.getMonth() + 1).padStart(2, '0');
      var day = String(date.getDate()).padStart(2, '0');
      return date.getFullYear() + '-' + month + '-' + day;
    }

    function rowMatchesFilters($row) {
      var keyword = ($('#myInput').val() || '').toLowerCase().trim();
      var brand = ($('#warehouse-brand-filter').val() || '').toLowerCase().trim();
      var supplier = ($('#warehouse-supplier-filter').val() || '').toLowerCase().trim();
      var stockFilter = $('#warehouse-stock-filter').val() || 'all';
      var warehouseStock = parseStockValue($row.attr('data-warehouse-stock'));

      if (keyword && String($row.attr('data-search') || '').indexOf(keyword) === -1) {
        return false;
      }
      if (brand && String($row.attr('data-brand') || '').indexOf(brand) === -1) {
        return false;
      }
      if (supplier && String($row.attr('data-supplier') || '').indexOf(supplier) === -1) {
        return false;
      }
      if (stockFilter === 'in_stock' && warehouseStock <= 0) {
        return false;
      }
      if (stockFilter === 'low_stock' && (warehouseStock <= 0 || warehouseStock > 5)) {
        return false;
      }
      if (stockFilter === 'out_stock' && warehouseStock > 0) {
        return false;
      }

      return true;
    }

    function renderPager(totalPages) {
      var pagerHtml = '';
      var startPage = Math.max(1, currentPage - 2);
      var endPage = Math.min(totalPages, startPage + 4);
      startPage = Math.max(1, endPage - 4);

      function pageButton(page, label, disabled, active) {
        var classes = 'warehouse-page-btn' + (active ? ' active' : '');
        return '<button type="button" class="' + classes + '" data-page="' + page + '"' + (disabled ? ' disabled' : '') + '>' + label + '</button>';
      }

      pagerHtml += pageButton(1, '&laquo;', currentPage === 1, false);
      pagerHtml += pageButton(Math.max(1, currentPage - 1), '&lsaquo;', currentPage === 1, false);
      for (var page = startPage; page <= endPage; page++) {
        pagerHtml += pageButton(page, page, false, page === currentPage);
      }
      pagerHtml += pageButton(Math.min(totalPages, currentPage + 1), '&rsaquo;', currentPage === totalPages, false);
      pagerHtml += pageButton(totalPages, '&raquo;', currentPage === totalPages, false);

      $('#warehouse-pager').html(pagerHtml);
    }

    function renderWarehouseRows() {
      var $rows = $('#item-list .warehouse-product-row');
      var pageSize = parseStockValue($('#warehouse-page-size').val()) || 8;
      var $matchedRows = $rows.filter(function() {
        return rowMatchesFilters($(this));
      });
      var totalRows = $matchedRows.length;
      var totalPages = Math.max(1, Math.ceil(totalRows / pageSize));

      if (currentPage > totalPages) {
        currentPage = totalPages;
      }

      var startIndex = (currentPage - 1) * pageSize;
      var endIndex = startIndex + pageSize;

      $rows.hide();
      $matchedRows.slice(startIndex, endIndex).show();
      $('#warehouse-empty-row').toggle(totalRows === 0);

      if (totalRows === 0) {
        $('#warehouse-range').text('0 of 0');
      } else {
        $('#warehouse-range').text((startIndex + 1) + ' - ' + Math.min(endIndex, totalRows) + ' of ' + totalRows);
      }

      renderPager(totalPages);
    }

    function updateSaveState($control) {
      var rawQuantity = String($control.find('.js-stock-qty').val() || '').trim();
      var quantity = parseStockValue(rawQuantity);
      var mode = $control.find('.js-stock-mode.active').data('mode') || 'ADD';
      var canSave = rawQuantity !== '' && (mode === 'SET' ? quantity >= 0 : quantity > 0);
      $control.find('.js-stock-save').prop('disabled', !canSave);
    }

    function syncModeInput($control) {
      var mode = $control.find('.js-stock-mode.active').data('mode') || 'ADD';
      $control.find('.js-stock-qty').attr('min', mode === 'SET' ? '0' : '1');
      updateSaveState($control);
    }

    function setInlineControlBusy($control, isBusy) {
      $control.toggleClass('is-busy', isBusy);
      $control.find('button, input').prop('disabled', isBusy);
      $control.find('.js-stock-save').toggleClass('is-saving', isBusy).text(isBusy ? '...' : 'SAVE');
      if (!isBusy) {
        updateSaveState($control);
      }
    }

    function highlightRow(productID, statusClass) {
      var $row = $('#product-row-' + productID);
      $row.removeClass('warehouse-row-success warehouse-row-error');
      window.setTimeout(function() {
        $row.addClass(statusClass);
      }, 10);
      window.setTimeout(function() {
        $row.removeClass(statusClass);
      }, 3000);
    }

    function updateTotals() {
      var totalWarehouse = 0;
      var totalShop = 0;
      var totalWastage = 0;

      $('#item-list .warehouse-product-row').each(function() {
        var $row = $(this);
        totalWarehouse += parseStockValue($row.attr('data-warehouse-stock'));
        totalShop += parseStockValue($row.attr('data-shop-stock'));
        totalWastage += parseStockValue($row.attr('data-wastage-stock'));
      });

      $('#warehouse-total-available').text(formatStockValue(totalWarehouse));
      $('#warehouse-total-shop').text(formatStockValue(totalShop));
      $('#warehouse-total-wastage').text(formatStockValue(totalWastage));
    }

    function refreshStockColumns(productID, responseData) {
      var $row = $('#product-row-' + productID);
      var warehouseClosingQty = responseData.warehouse_closing_qty;
      var shopClosingQty = responseData.shop_closing_qty;
      var wastageClosingQty = responseData.wastage_closing_qty;

      if (typeof warehouseClosingQty === 'undefined') {
        warehouseClosingQty = responseData.closing_qty;
      }
      if (typeof warehouseClosingQty !== 'undefined') {
        $row.attr('data-warehouse-stock', parseStockValue(warehouseClosingQty));
      }
      if (typeof shopClosingQty !== 'undefined') {
        $row.attr('data-shop-stock', parseStockValue(shopClosingQty));
      }
      if (typeof wastageClosingQty !== 'undefined') {
        $row.attr('data-wastage-stock', parseStockValue(wastageClosingQty));
      }

      var warehouseStock = parseStockValue($row.attr('data-warehouse-stock'));
      var shopStock = parseStockValue($row.attr('data-shop-stock'));
      var wastageStock = parseStockValue($row.attr('data-wastage-stock'));
      var onHandStock = warehouseStock + shopStock;

      $('#on-hand-' + productID).text(formatStockValue(onHandStock));
      $('#warehouse-stock-' + productID).text(formatStockValue(warehouseStock));
      $('#shop-stock-' + productID).text(formatStockValue(shopStock));
      $('#location-shop-' + productID).text(formatStockValue(shopStock));
      $('#wastage-stock-' + productID).text(formatStockValue(wastageStock));
      $row.find('.js-open-return-stock')
        .attr('data-shop-stock', shopStock)
        .data('shop-stock', shopStock)
        .prop('disabled', shopStock <= 0);
      updateTotals();
    }

    function setReturnModalBusy(isBusy) {
      $('#returnStockForm').find('button, input, textarea').prop('disabled', isBusy);
      $('#return_stock_submit').text(isBusy ? '...' : 'RETURN');
      if (!isBusy) {
        updateReturnCalculation();
      }
    }

    function updateReturnCalculation() {
      var productID = $('#return_product_id').val();
      var shopStock = parseStockValue($('#product-row-' + productID).attr('data-shop-stock'));
      var returnQty = parseStockValue($('#return_txn_qty').val());
      var wastageQty = parseStockValue($('#return_wastage_qty').val());
      var totalDeductQty = returnQty + wastageQty;

      $('#return_total_deduct').text(formatStockValue(totalDeductQty));
      $('#return_calc_return').text(formatStockValue(returnQty));
      $('#return_calc_wastage').text(formatStockValue(wastageQty));
      $('#return_stock_submit').prop('disabled', totalDeductQty <= 0 || totalDeductQty > shopStock);
    }

    function showReturnStockModal() {
      var modalElement = document.getElementById('return-stock-modal');
      if (!modalElement) {
        return;
      }

      if (window.bootstrap && window.bootstrap.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
        return;
      }

      if ($.fn && typeof $.fn.modal === 'function') {
        $('#return-stock-modal').modal('show');
        return;
      }

      modalElement.style.display = 'block';
      modalElement.removeAttribute('aria-hidden');
      modalElement.setAttribute('aria-modal', 'true');
      modalElement.classList.add('show');
      document.body.classList.add('modal-open');
      if (!document.querySelector('.modal-backdrop.stock-return-backdrop')) {
        var backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show stock-return-backdrop';
        document.body.appendChild(backdrop);
      }
    }

    function hideReturnStockModal() {
      var modalElement = document.getElementById('return-stock-modal');
      if (!modalElement) {
        return;
      }

      if (window.bootstrap && window.bootstrap.Modal) {
        window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
        return;
      }

      if ($.fn && typeof $.fn.modal === 'function') {
        $('#return-stock-modal').modal('hide');
        return;
      }

      modalElement.style.display = 'none';
      modalElement.setAttribute('aria-hidden', 'true');
      modalElement.removeAttribute('aria-modal');
      modalElement.classList.remove('show');
      document.body.classList.remove('modal-open');
      $('.stock-return-backdrop').remove();
    }

    function openReturnStockModal($button) {
      var productID = $button.data('product-id');
      var productName = String($button.data('product-name') || 'Product');
      var productSKU = String($button.data('product-sku') || '');
      var shopStock = parseStockValue($('#product-row-' + productID).attr('data-shop-stock'));

      if (shopStock <= 0) {
        toastAlert('error', 'No shop stock available to return.');
        return;
      }

      $('#return_product_id').val(productID);
      $('#return_product_name').text(productName + (productSKU ? ' (' + productSKU + ')' : ''));
      $('#return_product_stock').text('Shop stock: ' + formatStockValue(shopStock));
      $('#return_txn_qty').attr('max', shopStock).val('');
      $('#return_wastage_qty').attr('max', shopStock).val('0');
      $('#return_note').val('');
      updateReturnCalculation();
      showReturnStockModal();
      window.setTimeout(function() {
        $('#return_txn_qty').focus();
      }, 300);
    }

    function submitReturnStock() {
      var productID = $('#return_product_id').val();
      var $row = $('#product-row-' + productID);
      var shopStock = parseStockValue($row.attr('data-shop-stock'));
      var quantity = parseStockValue($('#return_txn_qty').val());
      var wastageQty = parseStockValue($('#return_wastage_qty').val());
      var totalDeductQty = quantity + wastageQty;
      var note = String($('#return_note').val() || '').trim();

      if (totalDeductQty <= 0) {
        toastAlert('error', 'Please enter return or wastage quantity.');
        return;
      }
      if (totalDeductQty > shopStock) {
        toastAlert('error', 'You have only ' + formatStockValue(shopStock) + ' shop stock. Return plus wastage can\'t be more than available shop stock.');
        return;
      }
      if (!note) {
        toastAlert('error', 'Please enter return note.');
        return;
      }

      setReturnModalBusy(true);

      $.ajax({
        url: baseUrl + '/admin/stock/manage-warehouse-stock',
        type: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          key: projectKey,
          product_id: productID,
          txn_type: 'SHOP_TO_WAREHOUSE',
          stock_date: todayForStock(),
          txn_qty: quantity,
          wastage_qty: wastageQty,
          note: note
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.status) {
            toastAlert('success', response.message);
            refreshStockColumns(productID, response.data);
            hideReturnStockModal();
            highlightRow(productID, 'warehouse-row-success');
            renderWarehouseRows();
          } else {
            toastAlert('error', response.message);
            highlightRow(productID, 'warehouse-row-error');
          }
        },
        error: function() {
          toastAlert('error', 'Error occurred. Please try again.');
          highlightRow(productID, 'warehouse-row-error');
        },
        complete: function() {
          setReturnModalBusy(false);
        }
      });
    }

    function submitInlineWarehouseStock($control) {
      var productID = $control.data('product-id');
      var rawQuantity = String($control.find('.js-stock-qty').val() || '').trim();
      var quantity = parseStockValue(rawQuantity);
      var selectedMode = $control.find('.js-stock-mode.active').data('mode') || 'ADD';
      var currentWarehouseStock = parseStockValue($('#product-row-' + productID).attr('data-warehouse-stock'));
      var txnType = 'IN';
      var note = 'Inline warehouse stock add';

      if (rawQuantity === '' || (selectedMode === 'ADD' && quantity <= 0)) {
        toastAlert('error', 'Please enter stock quantity.');
        return;
      }

      if (selectedMode === 'SET') {
        if (quantity === currentWarehouseStock) {
          toastAlert('info', 'Warehouse stock already matches this quantity.');
          return;
        }

        txnType = quantity > currentWarehouseStock ? 'IN' : 'OUT';
        note = 'Inline warehouse stock set to ' + quantity;
        quantity = Math.abs(quantity - currentWarehouseStock);
      }

      setInlineControlBusy($control, true);

      $.ajax({
        url: baseUrl + '/admin/stock/manage-warehouse-stock',
        type: 'POST',
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          key: projectKey,
          product_id: productID,
          txn_type: txnType,
          stock_date: todayForStock(),
          txn_qty: quantity,
          note: note
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.status) {
            toastAlert('success', response.message);
            refreshStockColumns(productID, response.data);
            $control.find('.js-stock-qty').val('');
            highlightRow(productID, 'warehouse-row-success');
            renderWarehouseRows();
          } else {
            toastAlert('error', response.message);
            highlightRow(productID, 'warehouse-row-error');
          }
        },
        error: function() {
          toastAlert('error', 'Error occurred. Please try again.');
          highlightRow(productID, 'warehouse-row-error');
        },
        complete: function() {
          setInlineControlBusy($control, false);
        }
      });
    }

    $(document).ready(function() {
      renderWarehouseRows();
    });

    $(document).on('input change', '#myInput, #warehouse-brand-filter, #warehouse-supplier-filter, #warehouse-stock-filter, #warehouse-page-size', function() {
      currentPage = 1;
      renderWarehouseRows();
    });

    $(document).on('click', '#warehouse-more-filter', function() {
      var $panel = $('#warehouse-filter-panel');
      var isOpen = !$panel.hasClass('is-open');
      $panel.toggleClass('is-open', isOpen);
      $(this).attr('aria-expanded', isOpen ? 'true' : 'false');
      $(this).find('i').toggleClass('fa-angle-up', !isOpen).toggleClass('fa-angle-down', isOpen);
    });

    $(document).on('click', '.warehouse-page-btn', function() {
      var page = parseStockValue($(this).data('page'));
      if (!page || $(this).prop('disabled')) {
        return;
      }
      currentPage = page;
      renderWarehouseRows();
    });

    $(document).on('click', '.js-stock-mode', function() {
      var $control = $(this).closest('.inventory-edit-control');
      $control.find('.js-stock-mode').removeClass('active');
      $(this).addClass('active');
      syncModeInput($control);
    });

    $(document).on('input', '.js-stock-qty', function() {
      updateSaveState($(this).closest('.inventory-edit-control'));
    });

    $(document).on('click', '.js-stock-save', function() {
      submitInlineWarehouseStock($(this).closest('.inventory-edit-control'));
    });

    $(document).on('click', '.js-open-return-stock', function(e) {
      e.preventDefault();
      openReturnStockModal($(this));
    });

    $(document).on('input', '#return_txn_qty, #return_wastage_qty', function() {
      updateReturnCalculation();
    });

    $(document).on('click', '#return-stock-modal [data-bs-dismiss="modal"]', function() {
      hideReturnStockModal();
    });

    $(document).on('submit', '#returnStockForm', function(e) {
      e.preventDefault();
      submitReturnStock();
    });
  })(jQuery);
</script>
