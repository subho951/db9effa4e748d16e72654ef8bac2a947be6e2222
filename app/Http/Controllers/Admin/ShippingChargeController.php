<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\ShippingCharge;
use App\Models\Location;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
class ShippingChargeController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Shipping Charge',
            'controller'        => 'ShippingChargeController',
            'controller_route'  => 'shipping-charges',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'shipping-charge.list';
            $data['rows']                   = DB::table('shipping_charges')
                                                ->join('locations', 'shipping_charges.location_id', '=', 'locations.id')
                                                ->select('shipping_charges.*', 'locations.name as location_name')
                                                ->where('shipping_charges.status', '!=', 3)
                                                ->orderBy('shipping_charges.id', 'DESC')
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
                    'location_id'                   => 'required',
                    'default_charge'                => 'required',
                    'level2_charge'                 => 'required',
                    'level3_charge'                 => 'required',
                    'status'                        => 'required',
                ];
                if($this->validate($request, $rules)){
                    $checkData = ShippingCharge::where('location_id', 'LIKE', '%'.$postData['location_id'].'%')->where('status', '!=', 3)->first();
                    if(!$checkData){
                        $fields = [
                            'location_id'               => $postData['location_id'],
                            'default_charge'            => $postData['default_charge'],
                            'level2_charge'             => $postData['level2_charge'],
                            'level3_charge'             => $postData['level3_charge'],
                            'status'                    => $postData['status'],
                        ];
                        ShippingCharge::insert($fields);
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
            $page_name                      = 'shipping-charge.add-edit';
            $data['row']                    = [];
            $data['locations']              = Location::select('id', 'name')->where('status', '=', 1)->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'shipping-charge.add-edit';
            $data['row']                    = ShippingCharge::where($this->data['primary_key'], '=', $id)->first();
            $data['locations']              = Location::select('id', 'name')->where('status', '=', 1)->get();
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'location_id'                   => 'required',
                    'default_charge'                => 'required',
                    'level2_charge'                 => 'required',
                    'level3_charge'                 => 'required',
                    'status'                        => 'required',
                ];
                if($this->validate($request, $rules)){
                    $checkData = ShippingCharge::where('location_id', 'LIKE', '%'.$postData['location_id'].'%')->where('status', '!=', 3)->where('id', '!=', $id)->first();
                    if(!$checkData){
                        $fields = [
                            'location_id'               => $postData['location_id'],
                            'default_charge'            => $postData['default_charge'],
                            'level2_charge'             => $postData['level2_charge'],
                            'level3_charge'             => $postData['level3_charge'],
                            'status'                    => $postData['status'],
                        ];
                        ShippingCharge::where($this->data['primary_key'], '=', $id)->update($fields);
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
            ShippingCharge::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Deleted Successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = ShippingCharge::find($id);
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
