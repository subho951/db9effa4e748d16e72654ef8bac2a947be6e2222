<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralSetting;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductDiscountVoucher;
use App\Models\ProductMultipleBuy;
use App\Models\Supplier;
use App\Models\Size;
use App\Models\Unit;

use Auth;
use Session;
use Helper;
use Hash;
class ReportController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Report',
            'controller'        => 'ReportController',
            'controller_route'  => 'report',
            'primary_key'       => 'id',
        );
    }
    /* advance search report */
        public function advanceSearchReport(Request $request){
            $data['module']                 = $this->data;
            $title                          = 'Advance Search ' . $this->data['title'];
            $page_name                      = 'report.advance-search-report';
            $data['brands']                 = Brand::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['suppliers']              = Supplier::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();

            $data['brand_id']               = '';
            $data['supplier_id']            = '';
            $data['from_date']              = '';
            $data['to_date']                = '';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* advance search report */
    /* sale report */
        public function saleReport(Request $request){
            $this->salesByItems($request);
        }
    /* sale report */

    /* report pages */
        public function salesByItems(Request $request){
            $this->reportPage($request, 'sales-by-items');
        }

        public function salesTransactions(Request $request){
            $this->reportPage($request, 'sales-transactions');
        }

        public function register(Request $request){
            $this->reportPage($request, 'register');
        }

        public function payments(Request $request){
            $this->reportPage($request, 'payments');
        }

        public function customers(Request $request){
            $this->reportPage($request, 'customers');
        }

        public function customReports(Request $request){
            $this->reportPage($request, 'custom-reports');
        }

        public function detailAnalytics(Request $request){
            $this->reportPage($request, 'detail-analytics');
        }

        private function reportPage(Request $request, $activeReport){
            $data['module']                 = $this->data;
            $reportTabs                     = $this->reportTabs();
            $activeReport                   = array_key_exists($activeReport, $reportTabs) ? $activeReport : 'sales-by-items';
            $title                          = 'Sales';
            $page_name                      = 'report.index';
            $data['brands']                 = Brand::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['suppliers']              = Supplier::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['operators']              = Admin::select('id', 'name')->where('status', '=', 1)->where('type', '=', 'SO')->orderBy('name', 'ASC')->get();

            $defaultRange                   = $this->defaultReportDateRange();
            $data['brand_id']               = '';
            $data['supplier_id']            = '';
            $data['from_date']              = $this->parseReportDate($request->input('from_date'), $defaultRange['from_date']);
            $data['to_date']                = $this->parseReportDate($request->input('to_date'), $defaultRange['to_date']);
            if (strtotime($data['from_date']) > strtotime($data['to_date'])) {
                $dateSwap = $data['from_date'];
                $data['from_date'] = $data['to_date'];
                $data['to_date'] = $dateSwap;
            }
            $data['delivery_mode']          = $request->input('delivery_mode', '');
            $data['payment_mode']           = $request->input('payment_mode', '');
            $data['operator_id']            = $request->input('operator_id', '');
            $data['report_search']          = trim($request->input('report_search', ''));
            $data['include_deleted']        = $request->input('include_deleted', '');
            $data['reportTabs']             = $reportTabs;
            $data['activeReport']           = $activeReport;
            $data['reportTitle']            = $reportTabs[$activeReport]['label'];
            $data['tableHeaders']           = $reportTabs[$activeReport]['headers'];
            $data['is_search']              = ($request->isMethod('get') && $request->has('mode')) ? 1 : 0;
            $data['rows']                   = $this->filteredOrders($request, $data['from_date'], $data['to_date']);
            $data['reportRows']             = $this->reportRows($activeReport, $data['rows'], $request);
            $data['response']               = $data['reportRows'];
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }

        private function reportTabs(){
            return [
                'sales-by-items' => [
                    'label' => 'Sales by items',
                    'headers' => ['Item', 'SKU', 'Barcode', 'Quantity sold', 'Total Quantity', 'Sales (Ex. tax)', 'Order discounts', 'Purchase cost'],
                ],
                'sales-transactions' => [
                    'label' => 'Sales Transactions',
                    'headers' => ['Order #', 'Date', 'Customer', 'Items (Quantity)', 'Status', 'Payment Types', 'Order total', 'User'],
                ],
                'register' => [
                    'label' => 'Register',
                    'headers' => ['Register (Outlet)', 'Sales (inc)', 'Sales (Ex. tax)', 'Refunds', 'Order discounts'],
                ],
                'payments' => [
                    'label' => 'Payments',
                    'headers' => ['Order #', 'Date', 'Customer', 'Order total', 'Outstanding'],
                ],
                'customers' => [
                    'label' => 'Customers',
                    'headers' => ['Group (tiers)', 'Standard discount', 'Sales (Ex. tax)', 'Order discounts', 'Refunds'],
                ],
                'custom-reports' => [
                    'label' => 'Custom Reports',
                    'headers' => ['Order #', 'Date', 'Customer', 'Title', 'Notes', 'Qty', 'Cost price', 'Discount', 'Tax', 'Order total', 'User'],
                ],
                'detail-analytics' => [
                    'label' => 'Detail Analytics',
                    'headers' => [],
                ],
            ];
        }

        private function defaultReportDateRange(){
            $today = Carbon::today()->toDateString();
            $range = Order::where('status', 5)
                ->selectRaw('MIN(order_date) as from_date, MAX(order_date) as to_date')
                ->first();

            return [
                'from_date' => $this->parseReportDate(($range ? $range->from_date : null), $today),
                'to_date' => $this->parseReportDate(($range ? $range->to_date : null), $today),
            ];
        }

        private function parseReportDate($date, $fallback){
            if (trim((string) $date) == '') {
                return $fallback;
            }

            try {
                return Carbon::parse($date)->toDateString();
            } catch (\Exception $e) {
                return $fallback;
            }
        }

        private function reportTaxPercent(){
            $generalSetting = GeneralSetting::find(1);
            return ($generalSetting) ? (float) $generalSetting->tax_percent : 0;
        }

        private function reportTaxMultiplier(){
            return 1 + ($this->reportTaxPercent() / 100);
        }

        private function amountExTax($amount){
            $taxMultiplier = $this->reportTaxMultiplier();
            return ($taxMultiplier > 0) ? ((float) $amount / $taxMultiplier) : (float) $amount;
        }

        private function orderIds($orders){
            return $orders->pluck('id')->filter()->unique()->values();
        }

        private function reportLineSummary($orderIds){
            $summary = [
                'positive_subtotal' => 0,
                'refunds' => 0,
                'line_discounts' => 0,
                'cogs' => 0,
            ];

            if ($orderIds->isEmpty()) {
                return $summary;
            }

            $lineSummary = DB::table('order_details')
                ->leftJoin('products', 'order_details.item_id', '=', 'products.id')
                ->whereIn('order_details.order_id', $orderIds)
                ->selectRaw('COALESCE(SUM(CASE WHEN order_details.subtotal >= 0 AND order_details.price >= 0 THEN order_details.subtotal ELSE 0 END), 0) as positive_subtotal')
                ->selectRaw('COALESCE(SUM(CASE WHEN order_details.subtotal < 0 OR order_details.price < 0 THEN ABS(order_details.subtotal) ELSE 0 END), 0) as refunds')
                ->selectRaw('COALESCE(SUM(CASE WHEN order_details.subtotal >= 0 AND order_details.price >= 0 THEN order_details.discount_amount ELSE 0 END), 0) as line_discounts')
                ->selectRaw('COALESCE(SUM(CASE WHEN order_details.subtotal >= 0 AND order_details.price >= 0 THEN COALESCE(products.cost_price_ex_tax, products.cost_price_inc_tax, 0) * order_details.qty ELSE 0 END), 0) as cogs')
                ->first();

            if ($lineSummary) {
                $summary['positive_subtotal'] = (float) $lineSummary->positive_subtotal;
                $summary['refunds'] = (float) $lineSummary->refunds;
                $summary['line_discounts'] = (float) $lineSummary->line_discounts;
                $summary['cogs'] = (float) $lineSummary->cogs;
            }

            return $summary;
        }

        private function reportSummary($orders){
            $orderIds = $this->orderIds($orders);
            $lineSummary = $this->reportLineSummary($orderIds);
            $discounts = (float) $orders->sum('discount_amount');
            $salesInc = max(0, $lineSummary['positive_subtotal'] - $discounts);
            $refunds = $lineSummary['refunds'];
            $netSalesInc = max(0, $salesInc - $refunds);
            $salesEx = $this->amountExTax($salesInc);
            $netSalesEx = $this->amountExTax($netSalesInc);
            $cogs = $lineSummary['cogs'];
            $grossProfit = $netSalesEx - $cogs;

            return [
                'sales_inc' => $salesInc,
                'sales_ex' => $salesEx,
                'refunds' => $refunds,
                'discounts' => $discounts,
                'net_sales_inc' => $netSalesInc,
                'net_sales_ex' => $netSalesEx,
                'cogs' => $cogs,
                'gross_profit' => $grossProfit,
                'margin' => ($netSalesEx > 0) ? (($grossProfit / $netSalesEx) * 100) : 0,
                'net_sales_tax' => max(0, $netSalesInc - $netSalesEx),
                'shipping' => (float) $orders->sum('delivery_amount'),
            ];
        }

        private function filteredOrders(Request $request, $fromDate, $toDate){
            return Order::query()
                ->where('status', 5)
                ->when($fromDate, function ($query, $fromDate) {
                    $query->whereDate('order_date', '>=', $fromDate);
                })
                ->when($toDate, function ($query, $toDate) {
                    $query->whereDate('order_date', '<=', $toDate);
                })
                ->when($request->input('delivery_mode'), function ($query, $deliveryMode) {
                    $query->where('delivery_mode', $deliveryMode);
                })
                ->when($request->input('payment_mode'), function ($query, $paymentMode) {
                    $query->where('payment_mode', $paymentMode);
                })
                ->when($request->input('operator_id'), function ($query, $operatorId) {
                    $query->where('operator_id', $operatorId);
                })
                ->orderBy('id', 'DESC')
                ->get();
        }

        private function reportRows($activeReport, $orders, Request $request){
            switch ($activeReport) {
                case 'sales-transactions':
                    return $this->salesTransactionRows($orders, $request);
                case 'register':
                    return $this->registerRows($orders, $request);
                case 'payments':
                    return $this->paymentRows($orders, $request);
                case 'customers':
                    return $this->customerRows($orders, $request);
                case 'detail-analytics':
                    return $this->detailAnalyticsRows($orders, $request);
                case 'custom-reports':
                    return $this->customReportRows($orders, $request);
                case 'sales-by-items':
                default:
                    return $this->salesByItemRows($orders, $request);
            }
        }

        private function salesByItemRows($orders, Request $request){
            $orderIds = $this->orderIds($orders);
            if ($orderIds->isEmpty()) {
                return [];
            }

            $query = DB::table('order_details')
                ->leftJoin('products', 'order_details.item_id', '=', 'products.id')
                ->whereIn('order_details.order_id', $orderIds);

            $search = trim($request->input('report_search', ''));
            if ($search !== '') {
                $like = '%' . $search . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('products.name', 'LIKE', $like)
                        ->orWhere('products.receipt_short_name', 'LIKE', $like)
                        ->orWhere('products.sku', 'LIKE', $like)
                        ->orWhere('products.barcode', 'LIKE', $like)
                        ->orWhere('products.supplier_product_name', 'LIKE', $like);
                });
            }

            $rows = $query
                ->selectRaw('order_details.item_id')
                ->selectRaw("COALESCE(NULLIF(products.receipt_short_name, ''), products.name, CONCAT('Product #', order_details.item_id)) as item_name")
                ->selectRaw("COALESCE(products.sku, '') as sku")
                ->selectRaw("COALESCE(products.barcode, '') as barcode")
                ->selectRaw('COALESCE(SUM(CASE WHEN order_details.subtotal >= 0 AND order_details.price >= 0 THEN order_details.qty ELSE 0 END), 0) as quantity_sold')
                ->selectRaw('COALESCE(MAX(COALESCE(products.shop_stock, 0) + COALESCE(products.warehouse_stock, 0)), 0) as total_quantity')
                ->selectRaw('COALESCE(SUM(CASE WHEN order_details.subtotal >= 0 AND order_details.price >= 0 THEN (order_details.subtotal - order_details.discount_amount) ELSE 0 END), 0) as sales_inc')
                ->selectRaw('COALESCE(SUM(CASE WHEN order_details.subtotal >= 0 AND order_details.price >= 0 THEN order_details.discount_amount ELSE 0 END), 0) as order_discounts')
                ->selectRaw('COALESCE(SUM(CASE WHEN order_details.subtotal >= 0 AND order_details.price >= 0 THEN COALESCE(products.cost_price_ex_tax, products.cost_price_inc_tax, 0) * order_details.qty ELSE 0 END), 0) as purchase_cost')
                ->groupBy('order_details.item_id', 'products.receipt_short_name', 'products.name', 'products.sku', 'products.barcode')
                ->orderBy('item_name', 'ASC')
                ->get();

            $formattedRows = [];
            foreach ($rows as $row) {
                $formattedRows[] = [
                    $row->item_name,
                    $row->sku,
                    $row->barcode,
                    $this->formatQuantity($row->quantity_sold),
                    $this->formatQuantity($row->total_quantity),
                    $this->formatMoney($this->amountExTax($row->sales_inc)),
                    $this->formatMoney($row->order_discounts),
                    $this->formatMoney($row->purchase_cost),
                ];
            }

            return $formattedRows;
        }

        private function salesTransactionRows($orders, Request $request){
            $operatorNames = $this->operatorNames($orders);
            $search = strtolower(trim($request->input('report_search', '')));
            $orderIds = $this->orderIds($orders);
            $itemSummary = $orderIds->isEmpty()
                ? collect([])
                : OrderDetail::selectRaw('order_id')
                    ->selectRaw('SUM(CASE WHEN subtotal >= 0 AND price >= 0 THEN 1 ELSE 0 END) as items_count')
                    ->selectRaw('SUM(CASE WHEN subtotal >= 0 AND price >= 0 THEN qty ELSE 0 END) as total_qty')
                    ->whereIn('order_id', $orderIds)
                    ->groupBy('order_id')
                    ->get()
                    ->keyBy('order_id');
            $rows = [];
            foreach ($orders as $order) {
                $orderNo = $order->order_no ?: '';
                if ($search !== '' && strpos(strtolower($orderNo), $search) === false) {
                    continue;
                }

                $summary = $itemSummary->get($order->id);
                $itemsCount = $summary ? (int) $summary->items_count : 0;
                $totalQty = $summary ? (float) $summary->total_qty : 0;

                $rows[] = [
                    $orderNo,
                    $this->formatDate($order->order_date ?: $order->created_at),
                    $this->customerName($order),
                    $itemsCount . ' (' . $this->formatQuantity($totalQty) . ')',
                    $this->orderStatusLabel($order->status),
                    $order->payment_mode ?: '',
                    $this->formatMoney($order->net_amount),
                    $operatorNames[$order->operator_id] ?? '',
                ];
            }
            return $rows;
        }

        private function registerRows($orders, Request $request){
            $search = strtolower(trim($request->input('report_search', '')));
            $orderOperatorIds = $orders->pluck('operator_id')->filter()->unique()->values()->all();
            $operatorQuery = Admin::query()
                ->where(function ($query) use ($orderOperatorIds) {
                    $query->where('type', '=', 'SO');
                    if (!empty($orderOperatorIds)) {
                        $query->orWhereIn('id', $orderOperatorIds);
                    }
                });

            if ($request->input('include_deleted') != '1') {
                $operatorQuery->where('status', '=', 1);
            }

            $operators = $operatorQuery->orderBy('id', 'ASC')->get();
            $operatorIds = $operators->pluck('id')->values()->all();
            foreach ($orderOperatorIds as $operatorId) {
                if (!in_array($operatorId, $operatorIds)) {
                    $operatorIds[] = $operatorId;
                }
            }

            $ordersByOperator = $orders->groupBy(function ($order) {
                return (int) ($order->operator_id ?: 0);
            });
            $operatorsById = $operators->keyBy('id');
            $rows = [];
            $registerSl = 1;
            foreach ($operatorIds as $operatorId) {
                $operator = $operatorsById->get($operatorId);
                $registerName = ($operator && trim((string) $operator->name) != '')
                    ? $operator->name . '/Event Shop'
                    : 'Cash Register ' . $registerSl . '/Event Shop';
                $registerSl++;

                if ($search !== '' && strpos(strtolower($registerName), $search) === false) {
                    continue;
                }

                $registerOrders = $ordersByOperator->get((int) $operatorId, collect([]));
                $summary = $this->reportSummary($registerOrders);

                $rows[] = [
                    $registerName,
                    $this->formatMoney($summary['sales_inc']),
                    $this->formatMoney($summary['sales_ex']),
                    $this->formatMoney($summary['refunds']),
                    $this->formatMoney($summary['discounts']),
                ];
            }
            return $rows;
        }

        private function paymentRows($orders, Request $request){
            $search = strtolower(trim($request->input('report_search', '')));
            $rows = [];
            foreach ($orders as $order) {
                $orderNo = $order->order_no ?: '';
                $customerName = $this->customerName($order);
                $haystack = strtolower($orderNo . ' ' . $customerName);
                if ($search !== '' && strpos($haystack, $search) === false) {
                    continue;
                }

                $orderTotal = (float) ($order->net_amount ?: 0);
                $paidAmount = (float) ($order->payment_amount ?: (($order->payment_status) ? $orderTotal : 0));
                $outstanding = max($orderTotal - $paidAmount, 0);

                $rows[] = [
                    $orderNo,
                    $this->formatDate($order->order_date ?: $order->created_at),
                    $customerName,
                    $this->formatMoney($orderTotal),
                    $this->formatMoney($outstanding),
                ];
            }
            return $rows;
        }

        private function customerRows($orders, Request $request){
            $search = strtolower(trim($request->input('report_search', '')));
            $ordersByGroup = $orders->groupBy(function ($order) {
                $groupName = trim($order->customer_tag ?: '');
                return $groupName !== '' ? $groupName : 'Retail (default)';
            });

            $rows = [];
            if ($ordersByGroup->isEmpty()) {
                $ordersByGroup = collect(['Retail (default)' => collect([])]);
            }

            foreach ($ordersByGroup as $groupName => $groupOrders) {
                if ($search !== '' && strpos(strtolower($groupName), $search) === false) {
                    continue;
                }

                $summary = $this->reportSummary($groupOrders);

                $rows[] = [
                    $groupName,
                    '0%',
                    $this->formatMoney($summary['sales_ex']),
                    $this->formatMoney($summary['discounts']),
                    $this->formatMoney($summary['refunds']),
                ];
            }
            return $rows;
        }

        private function customReportRows($orders, Request $request){
            $search = strtolower(trim($request->input('report_search', '')));
            $orderIds = $this->orderIds($orders);
            if ($orderIds->isEmpty()) {
                return [];
            }

            $query = DB::table('order_details')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->leftJoin('products', 'order_details.item_id', '=', 'products.id')
                ->leftJoin('admins', 'orders.operator_id', '=', 'admins.id')
                ->whereIn('orders.id', $orderIds);

            if ($search !== '') {
                $like = '%' . $search . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('orders.order_no', 'LIKE', $like)
                        ->orWhere('orders.customer_name', 'LIKE', $like)
                        ->orWhere('orders.pickup_name', 'LIKE', $like)
                        ->orWhere('orders.delivery_name', 'LIKE', $like);
                });
            }

            $details = $query
                ->select([
                    'orders.order_no',
                    'orders.order_date',
                    'orders.created_at as order_created_at',
                    'orders.customer_name',
                    'orders.pickup_name',
                    'orders.delivery_name',
                    'orders.note',
                    'orders.net_amount as order_total',
                    'admins.name as operator_name',
                    'order_details.price',
                    'order_details.qty',
                    'order_details.discount_amount',
                    'order_details.subtotal',
                    'products.name as product_name',
                    'products.receipt_short_name',
                    'products.cost_price_ex_tax',
                    'products.cost_price_inc_tax',
                ])
                ->orderBy('orders.id', 'DESC')
                ->orderBy('order_details.id', 'ASC')
                ->get();

            $rows = [];
            foreach ($details as $detail) {
                $lineInc = max(0, ((float) $detail->subtotal - (float) $detail->discount_amount));
                $lineTax = max(0, $lineInc - $this->amountExTax($lineInc));
                $rows[] = [
                    $detail->order_no ?: '',
                    $this->formatDate($detail->order_date ?: $detail->order_created_at),
                    $this->customerName($detail),
                    $detail->receipt_short_name ?: ($detail->product_name ?: ''),
                    $detail->note ?: '',
                    $this->formatQuantity($detail->qty),
                    $this->formatMoney($detail->cost_price_ex_tax ?: $detail->cost_price_inc_tax),
                    $this->formatMoney($detail->discount_amount),
                    $this->formatMoney($lineTax),
                    $this->formatMoney($detail->order_total),
                    $detail->operator_name ?: '',
                ];
            }

            return $rows;
        }

        private function detailAnalyticsRows($orders, Request $request){
            $summary = $this->reportSummary($orders);

            return [
                ['label' => 'Sales (inc)', 'value' => $this->formatMoney($summary['sales_inc']), 'change' => '0.00%', 'class' => 'analytics-card-teal'],
                ['label' => 'Sales (Ex)', 'value' => $this->formatMoney($summary['sales_ex']), 'change' => '0.00%', 'class' => 'analytics-card-blue'],
                ['label' => 'Refunds', 'value' => $this->formatMoney($summary['refunds']), 'change' => '0.00%', 'class' => 'analytics-card-purple'],
                ['label' => 'Discounts', 'value' => $this->formatMoney($summary['discounts']), 'change' => '0.00%', 'class' => 'analytics-card-orange'],
                ['label' => 'Net Sales', 'value' => $this->formatMoney($summary['net_sales_inc']), 'change' => '0.00%', 'class' => 'analytics-card-gold'],
                ['label' => 'COGS', 'value' => $this->formatMoney($summary['cogs']), 'change' => '0.00%', 'class' => 'analytics-card-violet'],
                ['label' => 'Gross Profit', 'value' => $this->formatMoney($summary['gross_profit']), 'change' => '0.00%', 'class' => 'analytics-card-olive'],
                ['label' => 'Margin %', 'value' => number_format($summary['margin'], 2), 'change' => '0.00%', 'class' => 'analytics-card-green'],
                ['label' => 'Net Sales Tax', 'value' => $this->formatMoney($summary['net_sales_tax']), 'change' => '0.00%', 'class' => 'analytics-card-sky'],
                ['label' => 'Surcharge / Shipping', 'value' => $this->formatMoney($summary['shipping']), 'change' => '0.00%', 'class' => 'analytics-card-slate'],
            ];
        }

        private function operatorNames($orders){
            $operatorIds = $orders->pluck('operator_id')->filter()->unique()->values();
            if ($operatorIds->isEmpty()) {
                return [];
            }
            return Admin::whereIn('id', $operatorIds)->pluck('name', 'id')->toArray();
        }

        private function customerName($order){
            return $order->customer_name ?: ($order->pickup_name ?: ($order->delivery_name ?: ''));
        }

        private function formatDate($value){
            if (!$value) {
                return '';
            }
            if ($value instanceof \DateTimeInterface) {
                return $value->format('M d, Y');
            }
            return date_format(date_create($value), 'M d, Y');
        }

        private function formatQuantity($value){
            return fmod((float) $value, 1.0) == 0.0 ? (string) (int) $value : number_format((float) $value, 2);
        }

        private function formatMoney($value){
            return '$' . number_format((float) $value, 2);
        }

        private function orderStatusLabel($status){
            $labels = [
                1 => 'Ongoing',
                2 => 'Payment pending',
                3 => 'Backorder',
                4 => 'Refunded',
                5 => 'Completed sales',
            ];

            return $labels[$status] ?? '';
        }
    /* report pages */
}
