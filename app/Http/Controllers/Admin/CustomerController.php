<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Order;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
class CustomerController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Customer',
            'controller'        => 'CustomerController',
            'controller_route'  => 'customer',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'customer.list';
            $data['rows1']                  = Order::select('delivery_mode', 'customer_tag', 'pickup_name', 'pickup_email', 'pickup_phone', 'customer_name', 'customer_phone', 'customer_email')
                                                ->where('status', '=', 5)
                                                ->where('delivery_mode', '=', 'Pickup')
                                                ->groupBy('pickup_phone')
                                                ->orderBy('id', 'DESC')
                                                ->get();
            $data['rows2']                  = Order::select('delivery_mode', 'customer_tag', 'delivery_name', 'delivery_phone', 'delivery_email', 'delivery_address', 'delivery_suburb', 'delivery_state', 'delivery_postcode', 'customer_name', 'customer_phone', 'customer_email')
                                                ->where('status', '=', 5)
                                                ->where('delivery_mode', '=', 'Deliver')
                                                ->groupBy('delivery_phone')
                                                ->orderBy('id', 'DESC')
                                                ->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* customer orders */
        public function customerOrders($customer_phone){
            $customer_phone                 = Helper::decoded($customer_phone);
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Orders List : ' . $customer_phone;
            $page_name                      = 'customer.order-list';
            $data['rows1']                   = Order::select('id', 'order_no', 'order_time', 'order_time', 'payment_mode', 'net_amount', 'payment_status', 'payment_date_time', 'payment_amount')
                                                ->where('customer_phone', '=', $customer_phone)
                                                ->where('delivery_mode', '=', 'Take')
                                                ->where('status', '=', 5)
                                                ->orderBy('id', 'DESC')
                                                ->get();
            $data['rows2']                   = Order::select('id', 'pickup_name', 'pickup_email', 'pickup_phone', 'order_no', 'order_time', 'order_time', 'payment_mode', 'net_amount', 'payment_status', 'payment_date_time', 'payment_amount')
                                                ->where('customer_phone', '=', $customer_phone)
                                                ->where('delivery_mode', '=', 'Pickup')
                                                ->where('status', '=', 5)
                                                ->orderBy('id', 'DESC')
                                                ->get();
            $data['rows3']                   = Order::select('id', 'delivery_name', 'delivery_phone', 'delivery_email', 'delivery_address', 'delivery_suburb', 'delivery_state', 'delivery_postcode', 'order_no', 'order_time', 'order_time', 'payment_mode', 'net_amount', 'payment_status', 'payment_date_time', 'payment_amount')
                                                ->where('customer_phone', '=', $customer_phone)
                                                ->where('delivery_mode', '=', 'Deliver')
                                                ->where('status', '=', 5)
                                                ->orderBy('id', 'DESC')
                                                ->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* customer orders */
    /* customer orders  details */
        public function orderDetails($order_id){
            $order_id                       = Helper::decoded($order_id);
            $data['module']                 = $this->data;
            $data['getOrderDetail']         = Order::where('id', '=', $order_id)->first();
            return view('admin.maincontents.billing.print-invoice', $data);
        }
    /* customer orders  details */
}
