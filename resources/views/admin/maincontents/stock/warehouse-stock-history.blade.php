<?php
use App\Helpers\Helper;

$escape = function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$number = function ($value) {
    return number_format((float) $value, 0);
};
$formatDate = function ($value, $format) {
    if (empty($value)) {
        return '-';
    }

    try {
        return date_format(date_create($value), $format);
    } catch (Throwable $e) {
        return '-';
    }
};

$productName = (($product) ? $product->name : 'Product');
$productSku = (($product && isset($product->sku)) ? $product->sku : '');
$currentStock = (($product) ? (int) $product->warehouse_stock : 0);
$stockInTotal = 0;
$stockOutTotal = 0;
foreach ($stocks as $summaryStock) {
    if ($summaryStock->txn_type == 'IN') {
        $stockInTotal += (int) $summaryStock->txn_qty;
    } else {
        $stockOutTotal += (int) $summaryStock->txn_qty;
    }
}
?>
<style type="text/css">
  .stock-history-page {
    color: #24313f;
  }
  .stock-history-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
  }
  .stock-history-heading h4 {
    margin: 0;
    font-size: 24px;
    font-weight: 500;
    color: #2b3137;
  }
  .stock-history-heading small {
    display: block;
    color: #7b8793;
    font-size: 12px;
    margin-top: 4px;
  }
  .stock-history-actions {
    display: inline-flex;
    align-items: center;
    gap: 12px;
  }
  .stock-history-back {
    height: 34px;
    border: 1px solid #d6dee5;
    background: #fff;
    color: #2f80b7;
    border-radius: 2px;
    padding: 0 12px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 12px;
    font-weight: 700;
  }
  .stock-history-back:hover {
    color: #0e5f8c;
    background: #f7fafc;
  }
  .stock-history-shell {
    background: #fff;
    border: 1px solid #dce3e9;
    box-shadow: 0 2px 8px rgba(36, 49, 63, .08);
  }
  .stock-history-filter-row {
    position: relative;
    border-bottom: 1px solid #dce3e9;
  }
  .stock-history-filter-row i {
    position: absolute;
    top: 50%;
    left: 14px;
    transform: translateY(-50%);
    color: #6f7d89;
    font-size: 13px;
    pointer-events: none;
  }
  .stock-history-filter-row input {
    width: 100%;
    height: 38px;
    border: 0;
    border-radius: 0;
    color: #3b4650;
    font-size: 13px;
    padding: 0 14px 0 39px;
    outline: 0;
  }
  .stock-history-filter-row input::placeholder {
    color: #8b97a3;
  }
  .stock-history-summary {
    display: grid;
    grid-template-columns: repeat(4, minmax(150px, 1fr));
    border-bottom: 1px solid #e8edf2;
    background: #fbfcfd;
  }
  .stock-history-summary-item {
    padding: 13px 14px;
    border-right: 1px solid #e8edf2;
  }
  .stock-history-summary-item:last-child {
    border-right: 0;
  }
  .stock-history-summary-item span {
    display: block;
    color: #7b8793;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 3px;
  }
  .stock-history-summary-item strong {
    display: block;
    color: #24313f;
    font-size: 18px;
    font-weight: 600;
  }
  .stock-history-tab-row {
    padding: 32px 14px 18px;
  }
  .stock-history-tab {
    border: 0;
    background: transparent;
    color: #4d5863;
    font-size: 13px;
    padding: 0 12px 10px;
    border-bottom: 1px solid #22b8b0;
  }
  .stock-history-table-wrap {
    overflow-x: auto;
  }
  .stock-history-table {
    width: 100%;
    min-width: 920px;
    margin: 0;
    table-layout: fixed;
    border-collapse: collapse;
  }
  .stock-history-table th {
    border-top: 1px solid #e2e7ec;
    border-bottom: 1px solid #e2e7ec;
    color: #232b34;
    font-size: 12px;
    font-weight: 700;
    padding: 12px 8px;
    background: #fff;
    vertical-align: middle;
  }
  .stock-history-table td {
    border-bottom: 1px solid #e7ebef;
    color: #1f2a33;
    font-size: 12px;
    padding: 10px 8px;
    vertical-align: middle;
  }
  .stock-history-type {
    width: 10%;
  }
  .stock-history-number {
    width: 12%;
    text-align: center;
  }
  .stock-history-note {
    width: 28%;
  }
  .stock-history-date {
    width: 15%;
  }
  .stock-history-badge {
    min-width: 48px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 800;
  }
  .stock-history-badge.in {
    background: #e9f9ed;
    color: #15803d;
  }
  .stock-history-badge.out {
    background: #fff1f1;
    color: #c53030;
  }
  .history-opening {
    background: #edfafd;
  }
  .history-txn-in {
    background: #e9f9ed;
    color: #15803d;
  }
  .history-txn-out {
    background: #fff1f1;
    color: #c53030;
  }
  .history-closing {
    background: #f2fbe9;
  }
  .history-note-text {
    display: block;
    color: #4d5863;
    font-size: 12px;
    line-height: 1.45;
    word-break: break-word;
  }
  .stock-history-empty td {
    text-align: center;
    padding: 30px 14px;
    color: #7b8793;
  }
  .stock-history-legend {
    text-align: center;
    padding: 18px 16px 24px;
    color: #1f2a33;
    font-size: 12px;
  }
  .stock-history-legend span {
    display: inline-block;
    margin: 0 6px;
  }
  .stock-history-legend .legend-item {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin: 0;
  }
  .stock-history-legend .legend-swatch {
    width: 18px;
    height: 12px;
    border: 1px solid rgba(36, 49, 63, .1);
    border-radius: 2px;
  }
  .stock-history-legend .legend-on-hand {
    background: #edfafd;
  }
  .stock-history-legend .legend-committed {
    background: #fff8ed;
  }
  .stock-history-legend .legend-available {
    background: #f2fbe9;
  }
  @media (max-width: 991px) {
    .stock-history-summary {
      grid-template-columns: repeat(2, minmax(150px, 1fr));
    }
    .stock-history-summary-item:nth-child(2n) {
      border-right: 0;
    }
    .stock-history-summary-item:nth-child(-n+2) {
      border-bottom: 1px solid #e8edf2;
    }
  }
  @media (max-width: 767px) {
    .stock-history-heading {
      align-items: stretch;
      flex-direction: column;
    }
    .stock-history-actions {
      justify-content: flex-start;
    }
    .stock-history-summary {
      grid-template-columns: 1fr;
    }
    .stock-history-summary-item,
    .stock-history-summary-item:nth-child(2n) {
      border-right: 0;
      border-bottom: 1px solid #e8edf2;
    }
    .stock-history-summary-item:last-child {
      border-bottom: 0;
    }
    .stock-history-legend {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 9px;
    }
  }
</style>

<div class="container-xxl flex-grow-1 container-p-y stock-history-page">
  <div class="stock-history-heading">
    <div>
      <h4>Warehouse stock history</h4>
      <small><?=$escape($productName)?><?=($productSku != '' ? ' / SKU: ' . $escape($productSku) : '')?></small>
    </div>
    <div class="stock-history-actions">
      <a href="<?=url('admin/stock/warehouse-stock')?>" class="stock-history-back"><i class="fa fa-arrow-left"></i> Back to inventory</a>
    </div>
  </div>

  <div class="stock-history-shell">
    <div class="stock-history-filter-row">
      <i class="fa fa-filter"></i>
      <input type="text" class="js-stock-history-filter" placeholder="Filter by type, note, date, quantity..." autocomplete="off">
    </div>

    <div class="stock-history-summary">
      <div class="stock-history-summary-item">
        <span>Current warehouse stock</span>
        <strong><?=$number($currentStock)?></strong>
      </div>
      <div class="stock-history-summary-item">
        <span>Total stock in</span>
        <strong><?=$number($stockInTotal)?></strong>
      </div>
      <div class="stock-history-summary-item">
        <span>Total stock out</span>
        <strong><?=$number($stockOutTotal)?></strong>
      </div>
      <div class="stock-history-summary-item">
        <span>Transactions</span>
        <strong><?=number_format(count($stocks))?></strong>
      </div>
    </div>

    <div class="stock-history-tab-row">
      <button type="button" class="stock-history-tab">All</button>
    </div>

    <div class="stock-history-table-wrap">
      <table class="stock-history-table">
        <colgroup>
          <col class="stock-history-type">
          <col class="stock-history-number">
          <col class="stock-history-number">
          <col class="stock-history-number">
          <col class="stock-history-note">
          <col class="stock-history-date">
          <col class="stock-history-date">
        </colgroup>
        <thead>
          <tr>
            <th scope="col">Type</th>
            <th scope="col">Opening</th>
            <th scope="col">Txn</th>
            <th scope="col">Closing</th>
            <th scope="col">Note</th>
            <th scope="col">Stock Date</th>
            <th scope="col">Timestamp</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($stocks) > 0) { foreach ($stocks as $stock) {
              $isIn = ($stock->txn_type == 'IN');
              $stockDate = (isset($stock->stock_date) ? $stock->stock_date : $stock->created_at);
              $searchText = strtolower(trim($stock->txn_type . ' ' . $stock->opening_qty . ' ' . $stock->txn_qty . ' ' . $stock->closing_qty . ' ' . $stock->note . ' ' . $stockDate . ' ' . $stock->created_at));
          ?>
            <tr class="stock-history-row" data-search="<?=$escape($searchText)?>">
              <td><span class="stock-history-badge <?=($isIn ? 'in' : 'out')?>"><?=$escape($stock->txn_type)?></span></td>
              <td class="stock-history-number history-opening"><?=$number($stock->opening_qty)?></td>
              <td class="stock-history-number <?=($isIn ? 'history-txn-in' : 'history-txn-out')?>"><?=($isIn ? '+' : '-')?><?=$number($stock->txn_qty)?></td>
              <td class="stock-history-number history-closing"><?=$number($stock->closing_qty)?></td>
              <td><span class="history-note-text"><?=$escape(($stock->note != '') ? $stock->note : '-')?></span></td>
              <td><?=$formatDate($stockDate, 'M d, Y')?></td>
              <td><?=$formatDate($stock->created_at, 'M d, Y h:i A')?></td>
            </tr>
          <?php } } ?>
          <tr class="stock-history-empty" style="<?=((count($stocks) > 0) ? 'display: none;' : '')?>">
            <td colspan="7">No transactions available</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="stock-history-legend">
      <span class="legend-item"><span class="legend-swatch legend-on-hand"></span>On hand</span>
      <span>-</span>
      <span class="legend-item"><span class="legend-swatch legend-committed"></span>Committed</span>
      <span>=</span>
      <span class="legend-item"><span class="legend-swatch legend-available"></span>Available inventory</span>
      <i class="fa fa-circle-info"></i>
    </div>
  </div>
</div>

<script type="text/javascript">
  (function($) {
    $(document).on('input', '.js-stock-history-filter', function() {
      var keyword = ($(this).val() || '').toLowerCase().trim();
      var visibleCount = 0;

      $('.stock-history-row').each(function() {
        var isMatch = !keyword || String($(this).data('search') || '').indexOf(keyword) !== -1;
        $(this).toggle(isMatch);
        if (isMatch) {
          visibleCount++;
        }
      });

      $('.stock-history-empty').toggle(visibleCount === 0);
    });
  })(jQuery);
</script>
