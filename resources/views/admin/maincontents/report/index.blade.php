<?php
$activeReport = $activeReport ?? 'sales-by-items';
$reportRows = $reportRows ?? [];
$tableHeaders = $tableHeaders ?? [];
$quickSearchPlaceholder = [
  'sales-by-items' => 'Find by product name',
  'sales-transactions' => 'Find by order number',
  'register' => 'Find by register',
  'payments' => 'Find by order number and/or customer name',
  'customers' => 'Find by customer group (tiers) name',
  'custom-reports' => 'Find by order number and/or customer name',
][$activeReport] ?? 'Find report records';
?>
<style type="text/css">
  .sales-report-page {
    color: #2f3542;
  }
  .sales-report-shell {
    background: #fff;
    border: 1px solid #e8edf3;
    box-shadow: 0 8px 30px rgba(31, 45, 61, .06);
  }
  .sales-report-topbar {
    min-height: 68px;
    padding: 16px 20px;
    border-bottom: 1px solid #edf1f5;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
  }
  .sales-report-title {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #2e3440;
  }
  .sales-report-actions,
  .sales-report-date {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .sales-report-date {
    min-height: 38px;
    padding: 0 12px;
    border: 1px solid #e6edf5;
    background: #fff;
  }
  .sales-report-date input {
    border: 0;
    outline: none;
    min-width: 124px;
    color: #3b4252;
    font-size: 12px;
  }
  .sales-report-date span {
    color: #9aa3af;
    font-size: 12px;
  }
  .report-icon-button {
    width: 38px;
    height: 38px;
    border: 1px solid #e6edf5;
    background: #fff;
    color: #506071;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .report-icon-button:hover {
    color: #21b8b0;
    border-color: #21b8b0;
  }
  .report-export-button {
    height: 38px;
    border: 0;
    padding: 0 16px;
    background: #27c3bd;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .report-export-button:hover,
  .report-export-button:focus {
    color: #fff;
    background: #20aaa5;
  }
  .sales-report-body {
    padding: 18px 20px;
  }
  .report-table-search i {
    color: #697386;
  }
  .report-tabs {
    display: flex;
    overflow-x: auto;
    border-top: 1px solid #e4e9ef;
    border-bottom: 1px solid #e4e9ef;
    background: #f7f8fa;
  }
  .report-tab {
    min-width: 128px;
    min-height: 64px;
    padding: 0 16px;
    border-right: 1px solid #e4e9ef;
    color: #21b8b0;
    font-size: 11px;
    font-weight: 800;
    line-height: 1.08;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    text-decoration: none;
  }
  .report-tab:hover {
    color: #159c96;
    background: #fff;
  }
  .report-tab.active {
    background: #fff;
    color: #16b8b1;
    box-shadow: inset 0 -3px 0 #89e0dc;
  }
  .report-table-toolbar {
    padding: 22px 0 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }
  .report-table-search {
    width: min(440px, 100%);
    min-height: 38px;
    border: 1px solid #dfe7ef;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 12px;
  }
  .report-table-toolbar-sales-transactions .report-table-search,
  .report-table-toolbar-register .report-table-search,
  .report-table-toolbar-payments .report-table-search,
  .report-table-toolbar-customers .report-table-search,
  .report-table-toolbar-custom-reports .report-table-search {
    width: min(260px, 100%);
  }
  .report-table-search input {
    border: 0;
    outline: none;
    width: 100%;
    font-size: 13px;
  }
  .report-table-options {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    font-size: 12px;
    color: #2f3542;
  }
  .report-status-filter-menu {
    min-width: 240px;
    padding: 14px 16px;
    border: 1px solid #dfe4ea;
    box-shadow: 0 8px 22px rgba(31, 45, 61, .15);
  }
  .report-status-filter-menu .form-check {
    min-height: 32px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 9px;
    color: #2f3542;
    font-size: 14px;
  }
  .report-status-filter-menu .form-check:last-child {
    margin-bottom: 0;
  }
  .report-status-filter-menu .form-check-input {
    width: 18px;
    height: 18px;
    margin: 0;
    border-color: #5c6880;
  }
  .report-status-filter-menu .form-check-input:checked {
    background-color: #5b6882;
    border-color: #5b6882;
  }
  .report-table-options .form-check-input {
    border-color: #dfe7ef;
  }
  .report-table-wrap {
    overflow-x: auto;
    border-top: 1px solid #e8edf3;
  }
  .report-table {
    min-width: 1020px;
    margin: 0;
    color: #2f3542;
  }
  .report-table-sales-transactions,
  .report-table-payments,
  .report-table-register,
  .report-table-customers {
    min-width: 760px;
  }
  .report-table-custom-reports {
    min-width: 900px;
  }
  .report-table thead th {
    white-space: nowrap;
    font-size: 12px;
    font-weight: 800;
    padding: 12px 10px !important;
    border-bottom: 1px solid #e8edf3;
    background: #fff;
  }
  .report-table tbody td {
    font-size: 12px !important;
    padding: 12px 10px !important;
    vertical-align: middle;
    border-bottom: 1px solid #eef2f6;
  }
  .report-table .empty-row {
    height: 62px;
    color: #2f3542;
    text-align: center;
  }
  .report-table-footer {
    min-height: 52px;
    padding: 12px 0 0;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #808a98;
    font-size: 12px;
  }
  .report-table-footer select {
    width: 50px;
    height: 32px;
    border: 1px solid #dfe7ef;
    color: #2f3542;
    font-size: 12px;
  }
  .analytics-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(126px, 1fr));
    gap: 22px 18px;
    width: 100%;
    padding: 28px 0 14px;
  }
  .analytics-card {
    min-height: 70px;
    padding: 12px 12px 9px;
    background: #f6f7f9;
    border: 1px solid #f0f2f5;
  }
  .analytics-card-value {
    margin: 0;
    font-size: 20px;
    line-height: 1.1;
    font-weight: 800;
  }
  .analytics-card-label,
  .analytics-card-change-label,
  .analytics-card-change {
    margin: 0;
    color: #a3abb8;
    font-size: 10px;
    line-height: 1.2;
    font-weight: 800;
    text-transform: uppercase;
  }
  .analytics-card-label {
    margin-top: 2px;
  }
  .analytics-card-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: 15px;
  }
  .analytics-card-teal .analytics-card-value { color: #12aec7; }
  .analytics-card-blue .analytics-card-value { color: #4f73c9; }
  .analytics-card-purple .analytics-card-value { color: #884ac0; }
  .analytics-card-orange .analytics-card-value { color: #bc8a2d; }
  .analytics-card-gold .analytics-card-value { color: #c28d23; }
  .analytics-card-violet .analytics-card-value { color: #7770a6; }
  .analytics-card-olive .analytics-card-value { color: #a6b338; }
  .analytics-card-green .analytics-card-value { color: #12b976; }
  .analytics-card-sky .analytics-card-value { color: #3d94df; }
  .analytics-card-slate .analytics-card-value { color: #4b617e; }
  @media (max-width: 991px) {
    .sales-report-topbar {
      align-items: flex-start;
      flex-direction: column;
    }
    .sales-report-actions,
    .sales-report-date {
      width: 100%;
    }
    .sales-report-date input {
      flex: 1 1 120px;
    }
    .analytics-grid {
      grid-template-columns: repeat(2, minmax(126px, 1fr));
    }
  }
  @media (max-width: 575px) {
    .analytics-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="container-xxl flex-grow-1 container-p-y sales-report-page">
  <form method="GET" action="<?=url('admin/report/' . $activeReport)?>" id="reportFilterForm">
    <input type="hidden" name="mode" value="report">
    <div class="sales-report-shell">
      <div class="sales-report-topbar">
        <h4 class="sales-report-title">Sales</h4>
        <div class="sales-report-actions">
          <button type="button" class="report-icon-button" id="todayFilter" title="Today">
            <i class="fa fa-calendar-day"></i>
          </button>
          <div class="sales-report-date">
            <i class="fa fa-calendar-days"></i>
            <input type="date" name="from_date" id="from_date" value="<?=$from_date?>" required>
            <span>to</span>
            <input type="date" name="to_date" id="to_date" value="<?=$to_date?>" required>
          </div>
          <button type="submit" class="report-icon-button" title="Generate">
            <i class="fa fa-magnifying-glass"></i>
          </button>
          <?php if($is_search){?>
            <a href="<?=url('admin/report/' . $activeReport)?>" class="report-icon-button" title="Reset">
              <i class="fa fa-rotate-right"></i>
            </a>
          <?php }?>
          <div class="dropdown">
            <button type="button" class="report-export-button dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fa fa-chart-simple"></i> EXPORT
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><button type="button" class="dropdown-item" id="printReport"><i class="fa fa-print me-2"></i>Print</button></li>
              <li><button type="button" class="dropdown-item" id="exportCsv"><i class="fa fa-file-csv me-2"></i>CSV</button></li>
            </ul>
          </div>
        </div>
      </div>

      <div class="sales-report-body">
        <div class="report-tabs">
          <?php foreach($reportTabs as $reportSlug => $reportTab){?>
            <a href="<?=url('admin/report/' . $reportSlug)?>" class="report-tab <?=(($activeReport == $reportSlug)?'active':'')?>">
              <?=$reportTab['label']?>
            </a>
          <?php }?>
        </div>

        <?php if($activeReport != 'detail-analytics'){?>
        <div class="report-table-toolbar report-table-toolbar-<?=$activeReport?>">
          <div class="report-table-search">
            <i class="fa fa-magnifying-glass"></i>
            <input type="text" name="report_search" id="tableQuickSearch" value="<?=$report_search?>" placeholder="<?=$quickSearchPlaceholder?>">
          </div>
          <?php if($activeReport == 'register'){?>
          <div class="report-table-options">
            <label class="form-check d-flex align-items-center gap-2 m-0">
              <input class="form-check-input m-0" type="checkbox" name="include_deleted" value="1" <?=(($include_deleted == '1')?'checked':'')?>>
              <span>Include deleted</span>
            </label>
          </div>
          <?php }?>
          <?php if($activeReport == 'sales-by-items'){?>
          <div class="report-table-options">
            <label class="form-check d-flex align-items-center gap-2 m-0">
              <input class="form-check-input m-0" type="checkbox" name="group_by_transaction" value="1">
              <span>Group by sale transaction</span>
            </label>
            <label class="form-check d-flex align-items-center gap-2 m-0">
              <input class="form-check-input m-0" type="checkbox" name="omit_filtered_items" value="1">
              <span>Omit filtered items</span>
            </label>
            <div class="dropdown">
              <button type="button" class="report-icon-button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Status filters">
                <i class="fa fa-filter"></i>
              </button>
              <div class="dropdown-menu dropdown-menu-end report-status-filter-menu">
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" name="sale_status[]" value="all" checked>
                  <span>All</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" name="sale_status[]" value="completed_sales" checked>
                  <span>Completed sales</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" name="sale_status[]" value="refunded" checked>
                  <span>Refunded</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" name="sale_status[]" value="backorder" checked>
                  <span>Backorder</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" name="sale_status[]" value="on_account" checked>
                  <span>On Account</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" name="sale_status[]" value="exchanged" checked>
                  <span>Exchanged</span>
                </label>
                <label class="form-check">
                  <input class="form-check-input" type="checkbox" name="sale_status[]" value="partial_fulfilled" checked>
                  <span>Partial fulfilled</span>
                </label>
              </div>
            </div>
          </div>
          <?php }?>
        </div>
        <?php }?>

        <div id="printReportArea">
          <?php if($activeReport == 'detail-analytics'){?>
            <div class="analytics-grid">
              <?php foreach($reportRows as $card){?>
                <div class="analytics-card <?=$card['class']?>">
                  <p class="analytics-card-value"><?=$card['value']?></p>
                  <p class="analytics-card-label"><?=$card['label']?> <i class="fa fa-circle-info"></i></p>
                  <div class="analytics-card-meta">
                    <p class="analytics-card-change-label">Change</p>
                    <p class="analytics-card-change"><?=$card['change']?></p>
                  </div>
                </div>
              <?php }?>
            </div>
          <?php } else {?>
          <div class="report-table-wrap">
            <table class="table report-table report-table-<?=$activeReport?>" id="reportDataTable">
              <thead>
                <tr>
                  <?php foreach($tableHeaders as $header){?>
                    <th><?=$header?></th>
                  <?php }?>
                </tr>
              </thead>
              <tbody>
                <?php if(count($reportRows)>0){ foreach($reportRows as $row){?>
                  <tr>
                    <?php foreach($row as $cell){?>
                      <td><?=$cell?></td>
                    <?php }?>
                  </tr>
                <?php } } else {?>
                  <tr>
                    <td class="empty-row" colspan="<?=count($tableHeaders)?>">No records found</td>
                  </tr>
                <?php }?>
              </tbody>
            </table>
          </div>
          <?php }?>
        </div>

        <?php if($activeReport != 'detail-analytics'){?>
        <div class="report-table-footer">
          <select>
            <option>8</option>
            <option>15</option>
            <option>25</option>
            <option>50</option>
          </select>
          <span>per page</span>
        </div>
        <?php }?>
      </div>
    </div>
  </form>
</div>

<script>
  (function () {
    var todayFilter = document.getElementById('todayFilter');
    var fromDate = document.getElementById('from_date');
    var toDate = document.getElementById('to_date');
    var reportForm = document.getElementById('reportFilterForm');
    var quickSearch = document.getElementById('tableQuickSearch');
    var reportTable = document.getElementById('reportDataTable');
    var printReport = document.getElementById('printReport');
    var exportCsv = document.getElementById('exportCsv');
    var allStatusFilter = document.querySelector('input[name="sale_status[]"][value="all"]');
    var statusFilters = document.querySelectorAll('input[name="sale_status[]"]:not([value="all"])');

    if (todayFilter && fromDate && toDate && reportForm) {
      todayFilter.addEventListener('click', function () {
        var today = new Date().toISOString().slice(0, 10);
        fromDate.value = today;
        toDate.value = today;
        reportForm.submit();
      });
    }

    if (quickSearch && reportTable) {
      quickSearch.addEventListener('input', function () {
        var term = quickSearch.value.toLowerCase();
        var rows = reportTable.querySelectorAll('tbody tr');
        rows.forEach(function (row) {
          if (row.querySelector('.empty-row')) {
            return;
          }
          row.style.display = row.textContent.toLowerCase().indexOf(term) === -1 ? 'none' : '';
        });
      });
    }

    if (allStatusFilter && statusFilters.length) {
      allStatusFilter.addEventListener('change', function () {
        statusFilters.forEach(function (filter) {
          filter.checked = allStatusFilter.checked;
        });
      });

      statusFilters.forEach(function (filter) {
        filter.addEventListener('change', function () {
          allStatusFilter.checked = Array.prototype.every.call(statusFilters, function (item) {
            return item.checked;
          });
        });
      });
    }

    if (printReport) {
      printReport.addEventListener('click', function () {
        var printContents = document.getElementById('printReportArea').outerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
      });
    }

    if (exportCsv && reportTable) {
      exportCsv.addEventListener('click', function () {
        var csvRows = [];
        reportTable.querySelectorAll('tr').forEach(function (row) {
          var cells = Array.prototype.slice.call(row.querySelectorAll('th, td'));
          csvRows.push(cells.map(function (cell) {
            return '"' + cell.textContent.trim().replace(/"/g, '""') + '"';
          }).join(','));
        });

        var blob = new Blob([csvRows.join("\n")], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = '<?=$activeReport?>.csv';
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      });
    }
  })();
</script>
