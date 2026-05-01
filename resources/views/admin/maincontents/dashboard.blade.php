<?php
   $money = function($amount) {
      return '$' . number_format((float) $amount, 2);
   };
   $number = function($value) {
      return number_format((float) $value);
   };
   $trendMap = [];
   if(!empty($salesTrend)){
      foreach($salesTrend as $trend){
         $trendMap[$trend->order_date] = [
            'amount' => (float) $trend->bill_amount,
            'count' => (int) $trend->bill_count,
         ];
      }
   }
   $trendLabels = [];
   $trendAmounts = [];
   $trendCounts = [];
   for($i = 6; $i >= 0; $i--){
      $date = date('Y-m-d', strtotime('-'.$i.' days'));
      $trendLabels[] = date('d M', strtotime($date));
      $trendAmounts[] = $trendMap[$date]['amount'] ?? 0;
      $trendCounts[] = $trendMap[$date]['count'] ?? 0;
   }
   $operatorLabels = [];
   $operatorAmounts = [];
   if(!empty($operatorStats)){
      foreach($operatorStats->take(6) as $operator){
         $operatorLabels[] = $operator->name ?: 'Operator';
         $operatorAmounts[] = (float) $operator->bill_amount;
      }
   }
   $paymentLabels = [];
   $paymentAmounts = [];
   if(!empty($paymentModeStats)){
      foreach($paymentModeStats as $payment){
         $paymentLabels[] = $payment->payment_mode ?: 'Not Set';
         $paymentAmounts[] = (float) $payment->bill_amount;
      }
   }
   if(empty($operatorLabels)){
      $operatorLabels = ['No Sales'];
      $operatorAmounts = [0];
   }
   if(empty($paymentLabels)){
      $paymentLabels = ['No Sales'];
      $paymentAmounts = [0];
   }
?>
<style>
   .dashboard-content-wrapper {
      justify-content: flex-start !important;
   }
   .dashboard-page {
      color: #1f2937;
      padding-bottom: 0 !important;
      flex-grow: 0 !important;
   }
   .dashboard-page.container-p-y:not([class^=pb-]):not([class*=" pb-"]) {
      padding-bottom: 0 !important;
   }
   .dashboard-hero {
      background: linear-gradient(135deg, #111827 0%, #24415c 55%, #0f766e 100%);
      border-radius: 8px;
      padding: 26px;
      color: #fff;
      box-shadow: 0 16px 40px rgba(15, 23, 42, .16);
      position: relative;
      overflow: hidden;
   }
   .dashboard-hero::after {
      content: "";
      position: absolute;
      inset: auto -70px -90px auto;
      width: 260px;
      height: 260px;
      background: rgba(255, 255, 255, .12);
      border-radius: 50%;
   }
   .dashboard-hero h2 {
      color: #fff;
      font-weight: 700;
      margin-bottom: 8px;
   }
   .dashboard-hero p {
      color: rgba(255, 255, 255, .76);
      max-width: 740px;
   }
   .hero-metric {
      border-left: 1px solid rgba(255, 255, 255, .22);
      padding-left: 22px;
   }
   .hero-metric .value {
      color: #fff;
      font-size: 28px;
      font-weight: 700;
      line-height: 1.2;
   }
   .hero-metric .label {
      color: rgba(255, 255, 255, .68);
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .08em;
   }
   .metric-card {
      border: 0;
      border-radius: 8px;
      box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
      height: 100%;
   }
   .metric-card .card-body {
      padding: 20px;
   }
   .metric-icon {
      width: 42px;
      height: 42px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
   }
   .metric-label {
      color: #6b7280;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .08em;
      margin-bottom: 8px;
   }
   .metric-value {
      color: #111827;
      font-size: 24px;
      font-weight: 700;
      line-height: 1.15;
      margin-bottom: 4px;
   }
   .metric-note {
      color: #6b7280;
      font-size: 13px;
   }
   .panel-card {
      border: 0;
      border-radius: 8px;
      box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
      height: 100%;
   }
   .recent-bills-card {
      height: auto !important;
   }
   .recent-bills-card .card-body {
      padding-bottom: 16px;
   }
   .panel-card .card-header {
      background: #fff;
      border-bottom: 1px solid #eef2f7;
      padding: 18px 20px;
   }
   .panel-card .card-title {
      margin: 0;
      color: #111827;
      font-weight: 700;
   }
   .panel-card .card-subtitle {
      color: #6b7280;
      font-size: 13px;
      margin-top: 3px;
   }
   .mini-stat {
      background: #f8fafc;
      border: 1px solid #eef2f7;
      border-radius: 8px;
      padding: 14px;
   }
   .mini-stat span {
      color: #6b7280;
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: .06em;
   }
   .mini-stat strong {
      display: block;
      color: #111827;
      font-size: 20px;
      margin-top: 5px;
   }
   .dashboard-table {
      margin-bottom: 0;
   }
   .dashboard-table th {
      color: #6b7280;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .06em;
      border-top: 0;
      white-space: nowrap;
   }
   .dashboard-table td {
      vertical-align: middle;
      color: #374151;
   }
   .dashboard-table-scroll {
      overflow-y: auto;
      overflow-x: auto;
   }
   .dashboard-table-scroll thead th {
      position: sticky;
      top: 0;
      z-index: 2;
      background: #fff;
      box-shadow: 0 1px 0 #eef2f7;
   }
   .stock-scroll {
      max-height: 360px;
   }
   .recent-bills-scroll {
      max-height: 430px;
   }
   .stock-pill {
      display: inline-flex;
      min-width: 56px;
      justify-content: center;
      border-radius: 6px;
      padding: 4px 8px;
      font-weight: 700;
      font-size: 12px;
   }
   .stock-ok { background: #ecfdf5; color: #047857; }
   .stock-low { background: #fffbeb; color: #b45309; }
   .stock-out { background: #fef2f2; color: #b91c1c; }
   .rank-badge {
      width: 28px;
      height: 28px;
      border-radius: 8px;
      background: #eef2ff;
      color: #3730a3;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 12px;
   }
   .progress {
      height: 7px;
      background-color: #eef2f7;
   }
   @media (max-width: 767px) {
      .dashboard-hero {
         padding: 20px;
      }
      .hero-metric {
         border-left: 0;
         border-top: 1px solid rgba(255, 255, 255, .22);
         padding-left: 0;
         padding-top: 14px;
         margin-top: 14px;
      }
   }
</style>

<div class="container-xxl container-p-y dashboard-page">
   <div class="dashboard-hero mb-4">
      <div class="row align-items-center g-4">
         <div class="col-lg-7">
            <div class="badge bg-white text-dark mb-3">Live business overview</div>
            <h2>Admin Dashboard</h2>
            <p class="mb-0">Sales, stock, customers, products, and operator performance are now pulled directly from billing and inventory data.</p>
         </div>
         <div class="col-lg-5">
            <div class="row g-3">
               <div class="col-sm-6">
                  <div class="hero-metric">
                     <div class="value"><?=$money($todayBillsAmount)?></div>
                     <div class="label">Today sales</div>
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="hero-metric">
                     <div class="value"><?=$money($monthBillsAmount)?></div>
                     <div class="label">This month</div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="row g-4 mb-4">
      <div class="col-xl-3 col-md-6">
         <div class="card metric-card">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-start">
                  <div>
                     <div class="metric-label">Total Bills</div>
                     <div class="metric-value"><?=$number($totalBillsCount)?></div>
                     <div class="metric-note"><?=$money($totalBillsAmount)?> billed</div>
                  </div>
                  <div class="metric-icon bg-label-primary text-primary"><i class="fa-solid fa-receipt"></i></div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-xl-3 col-md-6">
         <div class="card metric-card">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-start">
                  <div>
                     <div class="metric-label">Sale Operators</div>
                     <div class="metric-value"><?=$number($totalSaleOperators)?></div>
                     <div class="metric-note"><?=$number($activeSaleOperators)?> active operators</div>
                  </div>
                  <div class="metric-icon bg-label-success text-success"><i class="fa-solid fa-user-tie"></i></div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-xl-3 col-md-6">
         <div class="card metric-card">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-start">
                  <div>
                     <div class="metric-label">Products</div>
                     <div class="metric-value"><?=$number($totalProducts)?></div>
                     <div class="metric-note"><?=$number($activeProducts)?> active SKUs</div>
                  </div>
                  <div class="metric-icon bg-label-info text-info"><i class="fa-solid fa-boxes-stacked"></i></div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-xl-3 col-md-6">
         <div class="card metric-card">
            <div class="card-body">
               <div class="d-flex justify-content-between align-items-start">
                  <div>
                     <div class="metric-label">Customers</div>
                     <div class="metric-value"><?=$number($customerCount)?></div>
                     <div class="metric-note">unique billed phones</div>
                  </div>
                  <div class="metric-icon bg-label-warning text-warning"><i class="fa-solid fa-users"></i></div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="row g-4 mb-4">
      <div class="col-xl-3 col-md-6">
         <div class="mini-stat">
            <span>Brands</span>
            <strong><?=$number($totalBrands)?></strong>
         </div>
      </div>
      <div class="col-xl-3 col-md-6">
         <div class="mini-stat">
            <span>Suppliers</span>
            <strong><?=$number($totalSuppliers)?></strong>
         </div>
      </div>
      <div class="col-xl-3 col-md-6">
         <div class="mini-stat">
            <span>Shop / Warehouse Stock</span>
            <strong><?=$number($shopStockQty)?> / <?=$number($warehouseStockQty)?></strong>
         </div>
      </div>
      <div class="col-xl-3 col-md-6">
         <div class="mini-stat">
            <span>Stock Alerts</span>
            <strong><?=$number($lowStockCount)?> low, <?=$number($outOfStockCount)?> out</strong>
         </div>
      </div>
   </div>

   <div class="row g-4 mb-4">
      <div class="col-xl-8">
         <div class="card panel-card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
               <div>
                  <h5 class="card-title">Sales Trend</h5>
                  <div class="card-subtitle">Completed bill amount and bill count for the last 7 days</div>
               </div>
               <span class="badge bg-label-primary"><?=$number($todayBillsCount)?> bills today</span>
            </div>
            <div class="card-body">
               <div id="dashboardSalesTrendChart" style="min-height: 320px;"></div>
            </div>
         </div>
      </div>
      <div class="col-xl-4">
         <div class="card panel-card">
            <div class="card-header">
               <h5 class="card-title">Payment Mix</h5>
               <div class="card-subtitle">Collected amount by payment mode</div>
            </div>
            <div class="card-body">
               <div id="dashboardPaymentChart" style="min-height: 315px;"></div>
            </div>
         </div>
      </div>
   </div>

   <div class="row g-4 mb-4">
      <div class="col-xl-7">
         <div class="card panel-card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
               <div>
                  <h5 class="card-title">Sale Operator Wise Count</h5>
                  <div class="card-subtitle">Completed bills and revenue by sale operator</div>
               </div>
               <a href="<?=url('admin/sale-operators/list')?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Operators</a>
            </div>
            <div class="card-body">
               <div id="dashboardOperatorChart" style="min-height: 230px;"></div>
               <div class="table-responsive mt-3">
                  <table class="table dashboard-table">
                     <thead>
                        <tr>
                           <th>Operator</th>
                           <th class="text-end">Bills</th>
                           <th class="text-end">Amount</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if(count($operatorStats)>0){ foreach($operatorStats as $operator){ ?>
                           <tr>
                              <td class="fw-medium"><?=$operator->name?></td>
                              <td class="text-end"><?=$number($operator->bill_count)?></td>
                              <td class="text-end"><?=$money($operator->bill_amount)?></td>
                           </tr>
                        <?php } } else { ?>
                           <tr><td colspan="3" class="text-center text-muted">No sale operators found.</td></tr>
                        <?php } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
      <div class="col-xl-5">
         <div class="card panel-card">
            <div class="card-header">
               <h5 class="card-title">Product Wise Stock</h5>
               <div class="card-subtitle">Lowest stock products need attention first</div>
            </div>
            <div class="card-body">
               <div class="table-responsive dashboard-table-scroll stock-scroll">
                  <table class="table dashboard-table">
                     <thead>
                        <tr>
                           <th>Product</th>
                           <th class="text-end">Shop</th>
                           <th class="text-end">Warehouse</th>
                           <th class="text-end">Total</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if(count($productStockRows)>0){ foreach($productStockRows as $product){ 
                           $stockClass = ($product->total_stock <= 0) ? 'stock-out' : (($product->total_stock <= 5) ? 'stock-low' : 'stock-ok');
                        ?>
                           <tr>
                              <td>
                                 <div class="fw-medium"><?=($product->receipt_short_name ?: $product->name)?></div>
                                 <small class="text-muted"><?=$product->sku?></small>
                              </td>
                              <td class="text-end"><?=$number($product->shop_stock)?></td>
                              <td class="text-end"><?=$number($product->warehouse_stock)?></td>
                              <td class="text-end"><span class="stock-pill <?=$stockClass?>"><?=$number($product->total_stock)?></span></td>
                           </tr>
                        <?php } } else { ?>
                           <tr><td colspan="4" class="text-center text-muted">No products found.</td></tr>
                        <?php } ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="row g-4 mb-4">
      <div class="col-xl-4">
         <div class="card panel-card">
            <div class="card-header">
               <h5 class="card-title">Top Products</h5>
               <div class="card-subtitle">Best sellers by quantity</div>
            </div>
            <div class="card-body">
               <?php if(count($topProducts)>0){ $rank = 1; $maxQty = max(1, (float) $topProducts->max('total_qty')); foreach($topProducts as $product){ 
                  $percent = min(100, (((float) $product->total_qty / $maxQty) * 100));
               ?>
                  <div class="d-flex align-items-center mb-4">
                     <span class="rank-badge me-3"><?=$rank++?></span>
                     <div class="flex-grow-1">
                        <div class="d-flex justify-content-between gap-2">
                           <span class="fw-medium text-truncate"><?=($product->receipt_short_name ?: $product->name)?></span>
                           <span class="fw-bold"><?=$number($product->total_qty)?></span>
                        </div>
                        <div class="progress mt-2">
                           <div class="progress-bar bg-primary" style="width: <?=$percent?>%"></div>
                        </div>
                     </div>
                  </div>
               <?php } } else { ?>
                  <p class="text-muted mb-0">No completed bill item data available.</p>
               <?php } ?>
            </div>
         </div>
      </div>
      <div class="col-xl-4">
         <div class="card panel-card">
            <div class="card-header">
               <h5 class="card-title">Delivery Mix</h5>
               <div class="card-subtitle">Completed bills by delivery mode</div>
            </div>
            <div class="card-body">
               <?php if(count($deliveryModeStats)>0){ foreach($deliveryModeStats as $delivery){ ?>
                  <div class="d-flex align-items-center justify-content-between border-bottom py-3">
                     <div>
                        <div class="fw-medium"><?=$delivery->delivery_mode ?: 'Not Set'?></div>
                        <small class="text-muted"><?=$number($delivery->bill_count)?> bills</small>
                     </div>
                     <div class="fw-bold"><?=$money($delivery->bill_amount)?></div>
                  </div>
               <?php } } else { ?>
                  <p class="text-muted mb-0">No delivery data available.</p>
               <?php } ?>
            </div>
         </div>
      </div>
      <div class="col-xl-4">
         <div class="card panel-card">
            <div class="card-header">
               <h5 class="card-title">Open Work</h5>
               <div class="card-subtitle">Bills that may need follow-up</div>
            </div>
            <div class="card-body">
               <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                  <div>
                     <div class="fw-medium">Ongoing Bills</div>
                     <small class="text-muted">Created but not finalized</small>
                  </div>
                  <span class="badge bg-label-warning fs-6"><?=$number($ongoingBillsCount)?></span>
               </div>
               <div class="d-flex align-items-center justify-content-between">
                  <div>
                     <div class="fw-medium">Recalled Bills</div>
                     <small class="text-muted">Saved for recall</small>
                  </div>
                  <span class="badge bg-label-info fs-6"><?=$number($recallBillsCount)?></span>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="card panel-card recent-bills-card">
      <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
         <div>
            <h5 class="card-title">Recent Bills</h5>
            <div class="card-subtitle">Latest completed billing activity</div>
         </div>
         <a href="<?=url('admin/billing/past-orders')?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-clock-rotate-left me-1"></i>Past Orders</a>
      </div>
      <div class="card-body">
         <div class="table-responsive dashboard-table-scroll recent-bills-scroll">
            <table class="table dashboard-table">
               <thead>
                  <tr>
                     <th>Bill</th>
                     <th>Customer</th>
                     <th>Operator</th>
                     <th>Mode</th>
                     <th>Payment</th>
                     <th class="text-end">Amount</th>
                  </tr>
               </thead>
               <tbody>
                  <?php if(count($recentBills)>0){ foreach($recentBills as $bill){ ?>
                     <tr>
                        <td>
                           <div class="fw-medium"><?=$bill->order_no?></div>
                           <small class="text-muted"><?=date_format(date_create($bill->order_date), "M d, Y")?> <?=($bill->order_time ? date_format(date_create($bill->order_time), "h:i A") : '')?></small>
                        </td>
                        <td>
                           <div><?=$bill->customer_name ?: 'Walk-in Customer'?></div>
                           <small class="text-muted"><?=$bill->customer_phone?></small>
                        </td>
                        <td><?=$operatorNames[$bill->operator_id] ?? 'N/A'?></td>
                        <td><span class="badge bg-label-secondary"><?=$bill->delivery_mode ?: 'N/A'?></span></td>
                        <td><span class="badge bg-label-primary"><?=$bill->payment_mode ?: 'N/A'?></span></td>
                        <td class="text-end fw-bold"><?=$money($bill->net_amount)?></td>
                     </tr>
                  <?php } } else { ?>
                     <tr><td colspan="6" class="text-center text-muted">No completed bills found.</td></tr>
                  <?php } ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>

<script>
   document.addEventListener('DOMContentLoaded', function () {
      var salesTrendEl = document.querySelector('#dashboardSalesTrendChart');
      if (salesTrendEl && window.ApexCharts) {
         new ApexCharts(salesTrendEl, {
            chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'Public Sans, sans-serif' },
            series: [
               { name: 'Sales Amount', data: <?=json_encode($trendAmounts)?> },
               { name: 'Bill Count', data: <?=json_encode($trendCounts)?> }
            ],
            xaxis: { categories: <?=json_encode($trendLabels)?> },
            stroke: { curve: 'smooth', width: 3 },
            colors: ['#2563eb', '#0f766e'],
            fill: { type: 'gradient', gradient: { shadeIntensity: .2, opacityFrom: .24, opacityTo: .04 } },
            dataLabels: { enabled: false },
            grid: { borderColor: '#eef2f7' },
            yaxis: [
               { labels: { formatter: function(value) { return '$' + Number(value).toLocaleString(); } } },
               { opposite: true, labels: { formatter: function(value) { return Math.round(value); } } }
            ],
            tooltip: {
               shared: true,
               y: [
                  { formatter: function(value) { return '$' + Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); } },
                  { formatter: function(value) { return Math.round(value) + ' bills'; } }
               ]
            }
         }).render();
      }

      var operatorEl = document.querySelector('#dashboardOperatorChart');
      if (operatorEl && window.ApexCharts) {
         new ApexCharts(operatorEl, {
            chart: { type: 'bar', height: 230, toolbar: { show: false }, fontFamily: 'Public Sans, sans-serif' },
            series: [{ name: 'Amount', data: <?=json_encode($operatorAmounts)?> }],
            xaxis: { categories: <?=json_encode($operatorLabels)?> },
            plotOptions: { bar: { borderRadius: 5, columnWidth: '44%' } },
            colors: ['#0f766e'],
            dataLabels: { enabled: false },
            grid: { borderColor: '#eef2f7' },
            yaxis: { labels: { formatter: function(value) { return '$' + Number(value).toLocaleString(); } } },
            tooltip: { y: { formatter: function(value) { return '$' + Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); } } }
         }).render();
      }

      var paymentEl = document.querySelector('#dashboardPaymentChart');
      if (paymentEl && window.ApexCharts) {
         new ApexCharts(paymentEl, {
            chart: { type: 'donut', height: 315, fontFamily: 'Public Sans, sans-serif' },
            labels: <?=json_encode($paymentLabels)?>,
            series: <?=json_encode($paymentAmounts)?>,
            colors: ['#2563eb', '#0f766e', '#f59e0b', '#64748b', '#dc2626'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: false },
            plotOptions: { pie: { donut: { size: '68%' } } },
            tooltip: { y: { formatter: function(value) { return '$' + Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); } } }
         }).render();
      }
   });
</script>
