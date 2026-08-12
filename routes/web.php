<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/env-test', function () {
    // return env('APP_NAME', 'Default Name');
    return env('DB_DATABASE', 'Default Name');
    // return env('DB_USERNAME', 'Default Name');
});
Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return 'Database connection is successful!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
/* Front Panel */
    // before login
        Route::match(['get', 'post'], '/', 'App\Http\Controllers\FrontController@login');
    // before login
    // authentication
        Route::match(['get', 'post'], 'signin', 'App\Http\Controllers\FrontController@signin');
        Route::match(['get', 'post'], 'signin/{id}', 'App\Http\Controllers\FrontController@signin');
        Route::match(['get', 'post'], 'signout', 'App\Http\Controllers\FrontController@signout');
    // authentication
    // after login
        Route::group(['prefix' => 'user', 'middleware' => ['admin']], function () {
            Route::match(['get','post'], '/dashboard', 'App\Http\Controllers\FrontController@dashboard');
            // Route::match(['get','post'], '/take-order', 'App\Http\Controllers\FrontController@takeOrder');
            Route::get('/signout', 'App\Http\Controllers\FrontController@logout');
            /* billing */
                Route::get('billing/list', 'App\Http\Controllers\BillingController@list');
                Route::post('billing/billing-item-return', 'App\Http\Controllers\BillingController@billingItemReturn');
                Route::get('billing/billing-item/{id}', 'App\Http\Controllers\BillingController@billingItem');
                Route::get('billing/product-suggestions', 'App\Http\Controllers\BillingController@productSuggestions');
                Route::post('billing/add-to-cart', 'App\Http\Controllers\BillingController@addToCart');
                Route::post('billing/item-delete', 'App\Http\Controllers\BillingController@itemDelete');
                Route::post('billing/billing-change-status', 'App\Http\Controllers\BillingController@billingChangeStatus');
                Route::post('billing/billing-update-qty', 'App\Http\Controllers\BillingController@billingUpdateQty');
                Route::post('billing/billing-select-delivery-address', 'App\Http\Controllers\BillingController@billingSelectDeliveryAddress');
                Route::get('billing/billing-delivery-address/{id}', 'App\Http\Controllers\BillingController@billingDeliveryAddress');
                Route::get('billing/billing-payment/{id}', 'App\Http\Controllers\BillingController@billingPayment');
                Route::post('billing/save-delivery-address', 'App\Http\Controllers\BillingController@saveDeliveryAddress');
                Route::post('billing/billing-select-payment-mode', 'App\Http\Controllers\BillingController@billingSelectPaymentMode');
                Route::post('billing/place-order', 'App\Http\Controllers\BillingController@placeOrder');
                Route::get('billing/billing-search/{id}', 'App\Http\Controllers\BillingController@billingSearch');
                Route::post('billing/search-result', 'App\Http\Controllers\BillingController@searchResult');
                Route::post('billing/search-product-add-to-cart', 'App\Http\Controllers\BillingController@searchProductAddToCart');
                Route::get('billing/billing-shortcuts/{id}', 'App\Http\Controllers\BillingController@billingShortcuts');
                Route::post('billing/validate-admin-pin', 'App\Http\Controllers\BillingController@validateAdminPin');
                Route::post('billing/billing-price-update', 'App\Http\Controllers\BillingController@billingPriceUpdate');

                Route::get('billing/billing-recall', 'App\Http\Controllers\BillingController@billingRecall');
                Route::get('billing/billing-ongoing', 'App\Http\Controllers\BillingController@billingOngoing');
                Route::get('billing/past-orders', 'App\Http\Controllers\BillingController@pastOrders');
                Route::get('billing/billing-invoice/{id}', 'App\Http\Controllers\BillingController@billingInvoice');
                Route::get('billing/billing-invoice-email/{id}', 'App\Http\Controllers\BillingController@billingInvoiceEmail');
                Route::post('billing/print-delivery-order', 'App\Http\Controllers\BillingController@printDeliveryOrder');
                Route::get('billing/billing-pdf-invoice/{id}', 'App\Http\Controllers\BillingController@billingPDFInvoice');
                Route::get('billing/billing-print-receipt/{id}', 'App\Http\Controllers\BillingController@billingPrintReceipt');
            /* billing */
        });
    // after login
/* Front Panel */
/* Admin Panel */
    Route::prefix('/admin')->namespace('App\Http\Controllers\Admin')->group(function(){
        Route::match(['get', 'post'], '/', 'UserController@login');
        Route::match(['get','post'],'/forgot-password', 'UserController@forgotPassword');
        Route::match(['get','post'],'/validateOtp/{id}', 'UserController@validateOtp');
        Route::match(['get','post'],'/resendOtp/{id}', 'UserController@resendOtp');
        Route::match(['get','post'],'/changePassword/{id}', 'UserController@changePassword');
        Route::group(['middleware' => ['admin']], function(){
            Route::get('dashboard', 'UserController@dashboard');
            Route::get('logout', 'UserController@logout');
            Route::get('email-logs', 'UserController@emailLogs');
            Route::match(['get','post'],'/email-logs/details/{email}', 'UserController@emailLogsDetails');
            Route::get('login-logs', 'UserController@loginLogs');
            Route::match(['get','post'], '/common-delete-image/{id1}/{id2}/{id3}/{id4}/{id5}', 'UserController@commonDeleteImage');
            /* setting */
                Route::get('settings', 'UserController@settings');
                Route::post('profile-settings', 'UserController@profile_settings');
                Route::post('general-settings', 'UserController@general_settings');
                Route::post('change-password', 'UserController@change_password');
                Route::post('email-settings', 'UserController@email_settings');
                Route::post('email-template', 'UserController@email_template');
                Route::post('sms-settings', 'UserController@sms_settings');
                Route::post('application-settings', 'UserController@sms_settings');
                Route::post('color-settings', 'UserController@color_settings');
                Route::post('seo-settings', 'UserController@seo_settings');
                // Route::post('footer-settings', 'UserController@footer_settings');
                // Route::post('payment-settings', 'UserController@payment_settings');
                // Route::post('signature-settings', 'UserController@signature_settings');
            /* setting */
            /* access & permission */
                /* module */
                    Route::get('modules/list', 'ModuleController@list');
                    Route::match(['get', 'post'], 'modules/add', 'ModuleController@add');
                    Route::match(['get', 'post'], 'modules/edit/{id}', 'ModuleController@edit');
                    Route::get('modules/delete/{id}', 'ModuleController@delete');
                    Route::get('modules/change-status/{id}', 'ModuleController@change_status');
                /* module */
                /* role */
                    Route::get('roles/list', 'RoleController@list');
                    Route::match(['get', 'post'], 'roles/add', 'RoleController@add');
                    Route::match(['get', 'post'], 'roles/edit/{id}', 'RoleController@edit');
                    Route::get('roles/delete/{id}', 'RoleController@delete');
                    Route::get('roles/change-status/{id}', 'RoleController@change_status');
                /* module */
                /* sub users */
                    Route::get('sub-users/list', 'SubUserController@list');
                    Route::match(['get', 'post'], 'sub-users/add', 'SubUserController@add');
                    Route::match(['get', 'post'], 'sub-users/edit/{id}', 'SubUserController@edit');
                    Route::get('sub-users/delete/{id}', 'SubUserController@delete');
                    Route::get('sub-users/change-status/{id}', 'SubUserController@change_status');
                /* sub users */
                /* sale operator */
                    Route::get('sale-operators/list', 'SaleOperatorController@list');
                    Route::match(['get', 'post'], 'sale-operators/add', 'SaleOperatorController@add');
                    Route::match(['get', 'post'], 'sale-operators/edit/{id}', 'SaleOperatorController@edit');
                    Route::get('sale-operators/delete/{id}', 'SaleOperatorController@delete');
                    Route::get('sale-operators/change-status/{id}', 'SaleOperatorController@change_status');
                /* sale operator */
            /* access & permission */
            /* masters */
                /* locations */
                    Route::get('locations/list', 'LocationController@list');
                    Route::match(['get', 'post'], 'locations/add', 'LocationController@add');
                    Route::match(['get', 'post'], 'locations/edit/{id}', 'LocationController@edit');
                    Route::get('locations/delete/{id}', 'LocationController@delete');
                    Route::get('locations/change-status/{id}', 'LocationController@change_status');
                /* locations */
                /* brands */
                    Route::get('brands/list', 'BrandController@list');
                    Route::match(['get', 'post'], 'brands/add', 'BrandController@add');
                    Route::match(['get', 'post'], 'brands/edit/{id}', 'BrandController@edit');
                    Route::get('brands/delete/{id}', 'BrandController@delete');
                    Route::get('brands/change-status/{id}', 'BrandController@change_status');
                /* brands */
                /* categories */
                    Route::get('categories/list', 'ProductCategoryController@list');
                    Route::match(['get', 'post'], 'categories/add', 'ProductCategoryController@add');
                    Route::match(['get', 'post'], 'categories/edit/{id}', 'ProductCategoryController@edit');
                    Route::get('categories/delete/{id}', 'ProductCategoryController@delete');
                    Route::get('categories/change-status/{id}', 'ProductCategoryController@change_status');
                /* categories */
                /* suppliers */
                    Route::get('suppliers/list', 'SupplierController@list');
                    Route::match(['get', 'post'], 'suppliers/add', 'SupplierController@add');
                    Route::match(['get', 'post'], 'suppliers/edit/{id}', 'SupplierController@edit');
                    Route::get('suppliers/delete/{id}', 'SupplierController@delete');
                    Route::get('suppliers/change-status/{id}', 'SupplierController@change_status');
                /* suppliers */
                /* Delivery Location */
                    Route::get('delivery-locations/list', 'DeliveryLocationController@list');
                    Route::match(['get', 'post'], 'delivery-locations/add', 'DeliveryLocationController@add');
                    Route::match(['get', 'post'], 'delivery-locations/edit/{id}', 'DeliveryLocationController@edit');
                    Route::get('delivery-locations/delete/{id}', 'DeliveryLocationController@delete');
                    Route::get('delivery-locations/change-status/{id}', 'DeliveryLocationController@change_status');
                /* Delivery Location */
                /* shipping charges */
                    Route::get('shipping-charges/list', 'ShippingChargeController@list');
                    Route::match(['get', 'post'], 'shipping-charges/add', 'ShippingChargeController@add');
                    Route::match(['get', 'post'], 'shipping-charges/edit/{id}', 'ShippingChargeController@edit');
                    Route::get('shipping-charges/delete/{id}', 'ShippingChargeController@delete');
                    Route::get('shipping-charges/change-status/{id}', 'ShippingChargeController@change_status');
                /* shipping charges */
                /* coupons */
                    Route::get('coupons/list', 'CouponController@list');
                    Route::match(['get', 'post'], 'coupons/add', 'CouponController@add');
                    Route::match(['get', 'post'], 'coupons/edit/{id}', 'CouponController@edit');
                    Route::get('coupons/delete/{id}', 'CouponController@delete');
                    Route::get('coupons/change-status/{id}', 'CouponController@change_status');
                /* coupons */
                /* fast buttons */
                    Route::get('fast-buttons/list', 'FastButtonController@list');
                    Route::match(['get', 'post'], 'fast-buttons/add', 'FastButtonController@add');
                    Route::match(['get', 'post'], 'fast-buttons/edit/{id}', 'FastButtonController@edit');
                    Route::get('fast-buttons/delete/{id}', 'FastButtonController@delete');
                    Route::get('fast-buttons/change-status/{id}', 'FastButtonController@change_status');
                /* fast buttons */
                /* units */
                    Route::get('units/list', 'UnitController@list');
                    Route::match(['get', 'post'], 'units/add', 'UnitController@add');
                    Route::match(['get', 'post'], 'units/edit/{id}', 'UnitController@edit');
                    Route::get('units/delete/{id}', 'UnitController@delete');
                    Route::get('units/change-status/{id}', 'UnitController@change_status');
                /* units */
                /* sizes */
                    Route::get('sizes/list', 'SizeController@list');
                    Route::match(['get', 'post'], 'sizes/add', 'SizeController@add');
                    Route::match(['get', 'post'], 'sizes/edit/{id}', 'SizeController@edit');
                    Route::get('sizes/delete/{id}', 'SizeController@delete');
                    Route::get('sizes/change-status/{id}', 'SizeController@change_status');
                /* sizes */
            /* masters */
            /* products */
                Route::get('products/list', 'ProductController@list');
                Route::match(['get', 'post'], 'products/add', 'ProductController@add');
                Route::match(['get', 'post'], 'products/edit/{id}', 'ProductController@edit');
                Route::match(['get', 'post'], 'products/delete/{id}', 'ProductController@delete');
                Route::post('products/bulk-delete', 'ProductController@bulkDelete');
                Route::get('products/change-status/{id}', 'ProductController@change_status');
                Route::get('products/get-suggestions', 'ProductController@getSuggestions');
                Route::get('products/select-suggestions', 'ProductController@selectSuggestions');
                Route::get('products/get-barcode-suggestions', 'ProductController@getBarcodeSuggestions');
                Route::get('products/select-barcode-suggestions', 'ProductController@selectBarcodeSuggestions');
                Route::match(['get', 'post'], 'products/upload-product', 'ProductController@uploadProduct');
                Route::match(['get', 'post'], 'products/delete-upload-product/{id}', 'ProductController@deleteUploadProduct');
                Route::get('products/print-barcode/{id}', 'ProductController@printBarcode');
                Route::get('products/generate-product-barcode', 'ProductController@generateProductBarcode');
                Route::post('products/generate-product-barcode', 'ProductController@generateProductBarcode');
                Route::get('products/shelf-tag-list', 'ProductController@shelfTagList');
                Route::post('products/shelf-tag-list', 'ProductController@shelfTagList');
                Route::post('products/print-products', 'ProductController@printProducts');
                Route::post('products/validate-admin-pin-product', 'ProductController@validateAdminPinProduct');
                Route::post('products/validate-admin-pin-export', 'ProductController@validateAdminPinExport');
                Route::post('products/update-discountvoucher-switch-status', 'ProductController@updateDiscountVoucherStatus');
                Route::post('products/update-discountoffer-switch-status', 'ProductController@updateMultiBuyStatus');
                Route::post('products/update-multibuy-switch-status', 'ProductController@updateMultiBuyStatus');
                Route::match(['get', 'post'], 'products/transfer-selected', 'ProductController@transferSelected');
            /* products */
            /* purchase orders */
                Route::get('purchase-orders/list', 'PurchaseOrderController@list');
                Route::match(['get', 'post'], 'purchase-orders/add', 'PurchaseOrderController@add');
                Route::match(['get', 'post'], 'purchase-orders/edit/{id}', 'PurchaseOrderController@edit');
                Route::match(['get', 'post'], 'purchase-orders/receive/{id}', 'PurchaseOrderController@receive');
                Route::get('purchase-orders/supplier-items', 'PurchaseOrderController@getSupplierItems');
                Route::get('/get-item-info', 'PurchaseOrderController@getItemInfo');
            /* purchase orders */
            /* customer */
                Route::get('customer/list', 'CustomerController@list');
                // Route::match(['get', 'post'], 'customer/add', 'CustomerController@add');
                // Route::match(['get', 'post'], 'customer/edit/{id}', 'CustomerController@edit');
                // Route::get('customer/delete/{id}', 'CustomerController@delete');
                Route::get('customer/customer-orders/{id}', 'CustomerController@customerOrders');
                Route::get('customer/order-details/{id}', 'CustomerController@orderDetails');
            /* customer */
            /* page */
                Route::get('page/list', 'PageController@list');
                Route::match(['get', 'post'], 'page/add', 'PageController@add');
                Route::match(['get', 'post'], 'page/edit/{id}', 'PageController@edit');
                Route::get('page/delete/{id}', 'PageController@delete');
                Route::get('page/change-status/{id}', 'PageController@change_status');
            /* page */
            /* billing */
                Route::get('billing/list', 'BillingController@list');
                Route::get('billing/product-suggestions', [\App\Http\Controllers\Admin\BillingController::class, 'productSuggestions']);
                Route::post('billing/billing-item-return', 'BillingController@billingItemReturn');
                Route::get('billing/billing-item/{id}', 'BillingController@billingItem');
                Route::post('billing/add-to-cart', 'BillingController@addToCart');
                Route::post('billing/item-delete', 'BillingController@itemDelete');
                Route::post('billing/billing-change-status', 'BillingController@billingChangeStatus');
                Route::post('billing/billing-update-qty', 'BillingController@billingUpdateQty');
                Route::post('billing/billing-select-delivery-address', 'BillingController@billingSelectDeliveryAddress');
                Route::get('billing/billing-delivery-address/{id}', 'BillingController@billingDeliveryAddress');
                Route::get('billing/billing-payment/{id}', 'BillingController@billingPayment');
                Route::post('billing/save-delivery-address', 'BillingController@saveDeliveryAddress');
                Route::post('billing/billing-select-payment-mode', 'BillingController@billingSelectPaymentMode');
                Route::post('billing/place-order', 'BillingController@placeOrder');
                Route::get('billing/billing-search/{id}', 'BillingController@billingSearch');
                Route::post('billing/search-result', 'BillingController@searchResult');
                Route::post('billing/search-product-add-to-cart', 'BillingController@searchProductAddToCart');
                Route::get('billing/billing-shortcuts/{id}', 'BillingController@billingShortcuts');
                Route::post('billing/validate-admin-pin', 'BillingController@validateAdminPin');
                Route::post('billing/billing-price-update', 'BillingController@billingPriceUpdate');

                Route::get('billing/billing-recall', 'BillingController@billingRecall');
                Route::get('billing/billing-ongoing', 'BillingController@billingOngoing');
                Route::get('billing/past-orders', 'BillingController@pastOrders');
                Route::get('billing/billing-invoice/{id}', 'BillingController@billingInvoice');
                Route::get('billing/billing-invoice-email/{id}', 'BillingController@billingInvoiceEmail');
                Route::get('billing/billing-pdf-invoice/{id}', 'BillingController@billingPDFInvoice');
                Route::get('billing/billing-print-receipt/{id}', 'BillingController@billingPrintReceipt');
            /* billing */
            /* stock */
                Route::get('stock/warehouse-stock', 'StockController@warehouseStock');
                Route::post('stock/manage-warehouse-stock', 'StockController@manageWarehouseStock');
                Route::get('stock/warehouse-stock-history/{id}', 'StockController@warehouseStockHistory');
                Route::get('stock/shop-stock', 'StockController@shopStock');
                Route::get('stock/shop-stock-history/{id}', 'StockController@shopStockHistory');
            /* stock */
            /* reports */
                Route::match(['get', 'post'], 'report/advance-search-report', 'ReportController@advanceSearchReport');
                Route::match(['get', 'post'], 'report/sales-by-items', 'ReportController@salesByItems');
                Route::match(['get', 'post'], 'report/sales-transactions', 'ReportController@salesTransactions');
                Route::match(['get', 'post'], 'report/register', 'ReportController@register');
                Route::match(['get', 'post'], 'report/payments', 'ReportController@payments');
                Route::match(['get', 'post'], 'report/customers', 'ReportController@customers');
                Route::match(['get', 'post'], 'report/custom-reports', 'ReportController@customReports');
                Route::match(['get', 'post'], 'report/detail-analytics', 'ReportController@detailAnalytics');
                Route::match(['get', 'post'], 'report/sale-report', 'ReportController@saleReport');
            /* reports */
        });
    });
/* Admin Panel */
