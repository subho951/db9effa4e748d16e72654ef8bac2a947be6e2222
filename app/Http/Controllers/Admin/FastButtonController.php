<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\FastButton;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
class FastButtonController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Fast Button',
            'controller'        => 'FastButtonController',
            'controller_route'  => 'fast-buttons',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'fast-button.list';
            $data['rows']                   = DB::table('fast_buttons')
                                                ->join('products', 'fast_buttons.product_id', '=', 'products.id')
                                                ->select('fast_buttons.*', 'products.name as product_name')
                                                ->where('fast_buttons.status', '!=', 3)
                                                ->orderBy('fast_buttons.id', 'DESC')
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
                    'product_id'              => 'required',
                    'qty'                     => 'required'
                ];
                if($this->validate($request, $rules)){
                    $checkData = FastButton::where('product_id', '=', $postData['product_id'])->where('status', '!=', 3)->first();
                    if(!$checkData){
                        $getProduct = Product::select('name', 'retail_price_inc_tax')->where('id', '=', $postData['product_id'])->first();
                        $fields = [
                            'product_id'        => $postData['product_id'],
                            'name'              => (($getProduct)?$getProduct->name:''),
                            'price'             => (($getProduct)?$getProduct->retail_price_inc_tax:0),
                            'qty'               => $postData['qty'],
                        ];
                        FastButton::insert($fields);
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
            $page_name                      = 'fast-button.add-edit';
            $data['row']                    = [];
            $data['products']               = Product::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'fast-button.add-edit';
            $data['row']                    = FastButton::where($this->data['primary_key'], '=', $id)->first();
            $data['products']               = Product::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'product_id'              => 'required',
                    'qty'                     => 'required'
                ];
                if($this->validate($request, $rules)){
                    $checkData = FastButton::where('product_id', '=', $postData['product_id'])->where('status', '!=', 3)->where('id', '!=', $id)->first();
                    if(!$checkData){
                        $getProduct = Product::select('name', 'retail_price_inc_tax')->where('id', '=', $postData['product_id'])->first();
                        $fields = [
                            'product_id'        => $postData['product_id'],
                            'name'              => (($getProduct)?$getProduct->name:''),
                            'price'             => (($getProduct)?$getProduct->retail_price_inc_tax:0),
                            'qty'               => $postData['qty'],
                        ];
                        FastButton::where($this->data['primary_key'], '=', $id)->update($fields);
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
            FastButton::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Deleted Successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = FastButton::find($id);
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
