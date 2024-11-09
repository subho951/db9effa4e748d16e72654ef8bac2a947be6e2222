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
/* Front Panel */
    // before login
        Route::match(['get', 'post'], '/', 'App\Http\Controllers\FrontController@home');
        Route::match(['get', 'post'], '/search-result', 'App\Http\Controllers\FrontController@searchResult');
        Route::match(['get', 'post'], 'page/{id}', 'App\Http\Controllers\FrontController@page');
        Route::match(['get', 'post'], '/faq', 'App\Http\Controllers\FrontController@faq');
        Route::match(['get', 'post'], '/submit-subscriber', 'App\Http\Controllers\FrontController@submitSubscriber');
        Route::match(['get', 'post'], '/contact-us', 'App\Http\Controllers\FrontController@contactUs');
        Route::match(['get', 'post'], '/get-filter/', 'App\Http\Controllers\FrontController@getFilter');
        Route::match(['get', 'post'], '/new-products-list', 'App\Http\Controllers\FrontController@newProductsList');
        Route::match(['get', 'post'], '/new-products-grid', 'App\Http\Controllers\FrontController@newProductsGrid');
        Route::match(['get', 'post'], '/used-products-list', 'App\Http\Controllers\FrontController@usedProductsList');
        Route::match(['get', 'post'], '/used-products-grid', 'App\Http\Controllers\FrontController@usedProductsGrid');
        Route::match(['get', 'post'], '/listing-sorting', 'App\Http\Controllers\FrontController@listingSorting');
        Route::match(['get', 'post'], '/sell-your-machine', 'App\Http\Controllers\FrontController@sellYourMachine');
        // Route::match(['get', 'post'], '/sell-products-grid', 'App\Http\Controllers\FrontController@sellProductsGrid');
        Route::match(['get', 'post'], 'product-details/{id}', 'App\Http\Controllers\FrontController@productDetails');
        Route::match(['get', 'post'], 'contact-for-sales-inquiry/{id}', 'App\Http\Controllers\FrontController@contactForSalesInquiry');
        Route::match(['get', 'post'], 'report-listing/{id}', 'App\Http\Controllers\FrontController@reportListing');
        Route::match(['get', 'post'], 'make-wishlist/{id}/{id2}', 'App\Http\Controllers\FrontController@makeWishlist');
    // before login
    // authentication
        Route::match(['get', 'post'], 'signin', 'App\Http\Controllers\FrontController@signin');
        Route::match(['get', 'post'], 'signin/{id}', 'App\Http\Controllers\FrontController@signin');
        Route::match(['get', 'post'], 'signout', 'App\Http\Controllers\FrontController@signout');
        Route::match(['get', 'post'], 'forgot-password', 'App\Http\Controllers\FrontController@forgotPassword');
        Route::match(['get', 'post'], 'validate-otp/{id}', 'App\Http\Controllers\FrontController@validateOTP');
        Route::match(['get', 'post'], 'reset-password/{id}', 'App\Http\Controllers\FrontController@resetPassword');
        Route::match(['get', 'post'], 'signup', 'App\Http\Controllers\FrontController@signup');
        Route::match(['get', 'post'], 'signup-validate-otp/{id}', 'App\Http\Controllers\FrontController@signupValidateOTP');
    // authentication
    // after login
        Route::group(['prefix' => 'user', 'middleware' => ['user']], function () {
            Route::match(['get','post'], '/dashboard', 'App\Http\Controllers\FrontController@dashboard');
            Route::match(['get','post'], '/update-profile', 'App\Http\Controllers\FrontController@updateProfile');
            Route::match(['get','post'], '/change-password', 'App\Http\Controllers\FrontController@changePassword');
            Route::match(['get','post'], '/post-listing-ad', 'App\Http\Controllers\FrontController@postListingAd');
            Route::match(['get','post'], '/my-listing-ad', 'App\Http\Controllers\FrontController@myListingAd');
            Route::match(['get','post'], '/my-listing-ad-edit/{id}', 'App\Http\Controllers\FrontController@myListingAdEdit');
            Route::match(['get','post'], '/my-listing-ad-delete-single-image/{id}/{id2}', 'App\Http\Controllers\FrontController@myListingAdDeleteSingleImage');
            Route::match(['get','post'], '/my-listing-ad-delete/{id}', 'App\Http\Controllers\FrontController@myListingAdDelete');
            Route::match(['get','post'], '/my-listing-ad-change-status/{id}', 'App\Http\Controllers\FrontController@myListingAdChangeStatus');
            Route::match(['get','post'], '/my-listing-ad-change-availability/{id}', 'App\Http\Controllers\FrontController@myListingAdChangeAvailability');
            Route::match(['get','post'], '/submit-listing-ad-request', 'App\Http\Controllers\FrontController@submitListingAdRequest');
            Route::match(['get','post'], '/my-listing-ad-request', 'App\Http\Controllers\FrontController@myListingAdRequest');
            Route::match(['get','post'], '/saved-comparison', 'App\Http\Controllers\FrontController@savedComparison');
            Route::match(['get','post'], '/wishlist', 'App\Http\Controllers\FrontController@wishlist');
            Route::match(['get','post'], '/remove-wishlist/{id}', 'App\Http\Controllers\FrontController@removeWishlist');
            Route::match(['get','post'], '/my-sales-enquires', 'App\Http\Controllers\FrontController@mySalesEnquires');
            Route::match(['get','post'], '/my-reported-listing', 'App\Http\Controllers\FrontController@myReportedListing');
            Route::get('/signout', 'App\Http\Controllers\FrontController@signout');
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
           
            /* setting */
                Route::get('settings', 'UserController@settings');
                Route::post('profile-settings', 'UserController@profile_settings');
                Route::post('general-settings', 'UserController@general_settings');
                Route::post('change-password', 'UserController@change_password');
                Route::post('email-settings', 'UserController@email_settings');
                Route::post('email-template', 'UserController@email_template');
                Route::post('sms-settings', 'UserController@sms_settings');
                Route::post('footer-settings', 'UserController@footer_settings');
                Route::post('seo-settings', 'UserController@seo_settings');
                Route::post('payment-settings', 'UserController@payment_settings');
                Route::post('signature-settings', 'UserController@signature_settings');
            /* setting */
            /* access & permission */
                /* module */
                    Route::get('module/list', 'ModuleController@list');
                    Route::match(['get', 'post'], 'module/add', 'ModuleController@add');
                    Route::match(['get', 'post'], 'module/edit/{id}', 'ModuleController@edit');
                    Route::get('module/delete/{id}', 'ModuleController@delete');
                    Route::get('module/change-status/{id}', 'ModuleController@change_status');
                /* module */
                /* sub users */
                    Route::get('sub-user/list', 'SubUserController@list');
                    Route::match(['get', 'post'], 'sub-user/add', 'SubUserController@add');
                    Route::match(['get', 'post'], 'sub-user/edit/{id}', 'SubUserController@edit');
                    Route::get('sub-user/delete/{id}', 'SubUserController@delete');
                    Route::get('sub-user/change-status/{id}', 'SubUserController@change_status');
                /* sub users */
                /* give access */
                    Route::get('access/list', 'AccessController@list');
                    Route::match(['get', 'post'], 'access/add', 'AccessController@add');
                    Route::match(['get', 'post'], 'access/edit/{id}', 'AccessController@edit');
                    Route::get('access/delete/{id}', 'AccessController@delete');
                    Route::get('access/change-status/{id}', 'AccessController@change_status');
                /* give access */
            /* access & permission */
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
            /* pol */
                Route::get('pol/list', 'PolController@list');
                Route::match(['get', 'post'], 'pol/add', 'PolController@add');
                Route::match(['get', 'post'], 'pol/edit/{id}', 'PolController@edit');
                Route::get('pol/delete/{id}', 'PolController@delete');
                Route::get('pol/change-status/{id}', 'PolController@change_status');
            /* pol */
            /* pod */
                Route::get('pod/list', 'PodController@list');
                Route::match(['get', 'post'], 'pod/add', 'PodController@add');
                Route::match(['get', 'post'], 'pod/edit/{id}', 'PodController@edit');
                Route::get('pod/delete/{id}', 'PodController@delete');
                Route::get('pod/change-status/{id}', 'PodController@change_status');
            /* pod */
            /* process flow */
                Route::get('process-flow/list', 'ProcessFlowController@list');
                Route::match(['get', 'post'], 'process-flow/add', 'ProcessFlowController@add');
                Route::match(['get', 'post'], 'process-flow/edit/{id}', 'ProcessFlowController@edit');
                Route::get('process-flow/delete/{id}', 'ProcessFlowController@delete');
                Route::get('process-flow/change-status/{id}', 'ProcessFlowController@change_status');
            /* process flow */
            /* consignment */
                Route::get('consignment/list', 'ConsignmentController@list');
                Route::match(['get', 'post'], 'consignment/add', 'ConsignmentController@add');
                Route::match(['get', 'post'], 'consignment/edit/{id}', 'ConsignmentController@edit');
                Route::get('consignment/delete/{id}', 'ConsignmentController@delete');
                Route::get('consignment/change-status/{id}', 'ConsignmentController@change_status');
                Route::post('consignment/get-process-flow', 'ConsignmentController@getProcessFlow');
                Route::match(['get', 'post'], 'consignment/process-flow-details/{id}', 'ConsignmentController@process_flow_details');
            /* consignment */
        });
    });
/* Admin Panel */