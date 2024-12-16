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
            Route::match(['get','post'], '/take-order', 'App\Http\Controllers\FrontController@takeOrder');
            Route::get('/signout', 'App\Http\Controllers\FrontController@logout');
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
                /* suppliers */
                    Route::get('suppliers/list', 'SupplierController@list');
                    Route::match(['get', 'post'], 'suppliers/add', 'SupplierController@add');
                    Route::match(['get', 'post'], 'suppliers/edit/{id}', 'SupplierController@edit');
                    Route::get('suppliers/delete/{id}', 'SupplierController@delete');
                    Route::get('suppliers/change-status/{id}', 'SupplierController@change_status');
                /* suppliers */
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
                Route::get('products/delete/{id}', 'ProductController@delete');
                Route::get('products/change-status/{id}', 'ProductController@change_status');
                Route::get('products/get-suggestions', 'ProductController@getSuggestions');
                Route::get('products/select-suggestions', 'ProductController@selectSuggestions');
                Route::get('products/get-barcode-suggestions', 'ProductController@getBarcodeSuggestions');
                Route::get('products/select-barcode-suggestions', 'ProductController@selectBarcodeSuggestions');
                Route::match(['get', 'post'], 'products/upload-product', 'ProductController@uploadProduct');
                Route::match(['get', 'post'], 'products/delete-upload-product/{id}', 'ProductController@deleteUploadProduct');
            /* customer */
            /* customer */
                Route::get('customer/list', 'CustomerController@list');
                Route::match(['get', 'post'], 'customer/add', 'CustomerController@add');
                Route::match(['get', 'post'], 'customer/edit/{id}', 'CustomerController@edit');
                Route::get('customer/delete/{id}', 'CustomerController@delete');
                Route::get('customer/change-status/{id}', 'CustomerController@change_status');
            /* customer */
            /* page */
                Route::get('page/list', 'PageController@list');
                Route::match(['get', 'post'], 'page/add', 'PageController@add');
                Route::match(['get', 'post'], 'page/edit/{id}', 'PageController@edit');
                Route::get('page/delete/{id}', 'PageController@delete');
                Route::get('page/change-status/{id}', 'PageController@change_status');
            /* page */
        });
    });
/* Admin Panel */