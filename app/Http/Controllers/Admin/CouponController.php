<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Coupon;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
class CouponController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Discount Coupon',
            'controller'        => 'CouponController',
            'controller_route'  => 'coupons',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'coupon.list';
            $data['rows']                   = DB::table('coupons')
                                                ->leftjoin('products', 'coupons.main_product', '=', 'products.id')
                                                ->select('coupons.*', 'products.name as main_product_name')
                                                ->where('coupons.status', '!=', 3)
                                                ->orderBy('coupons.id', 'DESC')
                                                ->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $data['module']           = $this->data;
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    // 'main_product'              => 'required',
                    'name'                      => 'required',
                    'discount_nature'           => 'required',
                    'discount_type'             => 'required',
                    'discount_amount'           => 'required',
                    // 'voucher_code'              => 'required',
                    'from_date'                 => 'required',
                    'to_date'                   => 'required'
                ];
                if($this->validate($request, $rules)){
                    $checkData = Coupon::where('name', 'LIKE', '%'.$postData['name'].'%')->where('status', '!=', 3)->first();
                    if(!$checkData){
                        $fields = [
                            'name'                  => $postData['name'],
                            'main_product'          => (($postData['main_product'] != '')?$postData['main_product']:0),
                            'discount_nature'       => $postData['discount_nature'],
                            'bundle_products'       => ((array_key_exists("bundle_products",$postData))?json_encode($postData['bundle_products']):json_encode(array())),
                            'discount_type'         => $postData['discount_type'],
                            'discount_amount'       => $postData['discount_amount'],
                            'voucher_code'          => $postData['voucher_code'],
                            'from_date'             => $postData['from_date'],
                            'to_date'               => $postData['to_date'],
                        ];
                        // Helper::pr($fields);
                        Coupon::insert($fields);
                        return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Inserted Successfully !!!');
                    } else {
                        return redirect()->back()->with('error_message', $this->data['title'].' Already Exists !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'coupon.add-edit';
            $data['row']                    = [];
            $data['products']               = Product::select('id', 'name')->where('status', '=', 1)->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'coupon.add-edit';
            $data['row']                    = Coupon::where($this->data['primary_key'], '=', $id)->first();
            $data['products']               = Product::select('id', 'name')->where('status', '=', 1)->get();
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    // 'main_product'              => 'required',
                    'name'                      => 'required',
                    'discount_nature'           => 'required',
                    'discount_type'             => 'required',
                    'discount_amount'           => 'required',
                    // 'voucher_code'              => 'required',
                    'from_date'                 => 'required',
                    'to_date'                   => 'required'
                ];
                if($this->validate($request, $rules)){
                    $checkData = Coupon::where('name', 'LIKE', '%'.$postData['name'].'%')->where('status', '!=', 3)->where('id', '!=', $id)->first();
                    if(!$checkData){
                        $fields = [
                            'name'                  => $postData['name'],
                            'main_product'          => (($postData['main_product'] != '')?$postData['main_product']:0),
                            'discount_nature'       => $postData['discount_nature'],
                            'bundle_products'       => ((array_key_exists("bundle_products",$postData))?json_encode($postData['bundle_products']):json_encode(array())),
                            'discount_type'         => $postData['discount_type'],
                            'discount_amount'       => $postData['discount_amount'],
                            'voucher_code'          => $postData['voucher_code'],
                            'from_date'             => $postData['from_date'],
                            'to_date'               => $postData['to_date'],
                        ];
                        Coupon::where($this->data['primary_key'], '=', $id)->update($fields);
                        return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Updated Successfully !!!');
                    } else {
                        return redirect()->back()->with('error_message', $this->data['title'].' Already Exists !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* edit */
    /* delete */
        public function delete(Request $request, $id){
            $id                             = Helper::decoded($id);
            $fields = [
                'status'             => 3
            ];
            Coupon::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Deleted Successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = Coupon::find($id);
            if ($model->status == 1)
            {
                $model->status  = 0;
                $msg            = 'Deactivated';
            } else {
                $model->status  = 1;
                $msg            = 'Activated';
            }            
            $model->save();
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' '.$msg.' Successfully !!!');
        }
    /* change status */
}
