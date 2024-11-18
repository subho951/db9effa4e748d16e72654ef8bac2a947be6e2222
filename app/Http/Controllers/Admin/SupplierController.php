<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Supplier;

use Auth;
use Session;
use Helper;
use Hash;
class SupplierController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Supplier',
            'controller'        => 'SupplierController',
            'controller_route'  => 'suppliers',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'supplier.list';
            $data['rows']                   = Supplier::where('status', '!=', 3)->orderBy('id', 'DESC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $data['module']           = $this->data;
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'name'                          => 'required',
                    'contact_person_name'           => 'required',
                    'email'                         => 'required',
                    'phone'                         => 'required',
                    'address'                       => 'required',
                    'country'                       => 'required',
                    'state'                         => 'required',
                    'city'                          => 'required',
                    'zipcode'                       => 'required',
                    'status'                        => 'required',
                ];
                if($this->validate($request, $rules)){
                    $checkData = Supplier::where('name', 'LIKE', '%'.$postData['name'].'%')->where('status', '!=', 3)->first();
                    if(!$checkData){
                        $fields = [
                            'name'                      => $postData['name'],
                            'contact_person_name'       => $postData['contact_person_name'],
                            'email'                     => $postData['email'],
                            'phone'                     => $postData['phone'],
                            'address'                   => $postData['address'],
                            'country'                   => $postData['country'],
                            'state'                     => $postData['state'],
                            'city'                      => $postData['city'],
                            'locality'                  => $postData['locality'],
                            'street_no'                 => $postData['street_no'],
                            'zipcode'                   => $postData['zipcode'],
                            'latitude'                  => $postData['latitude'],
                            'longitude'                 => $postData['longitude'],
                            'status'                    => $postData['status'],
                        ];
                        Supplier::insert($fields);
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
            $page_name                      = 'supplier.add-edit';
            $data['row']                    = [];
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'supplier.add-edit';
            $data['row']                    = Supplier::where($this->data['primary_key'], '=', $id)->first();
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'name'                          => 'required',
                    'contact_person_name'           => 'required',
                    'email'                         => 'required',
                    'phone'                         => 'required',
                    'address'                       => 'required',
                    'country'                       => 'required',
                    'state'                         => 'required',
                    'city'                          => 'required',
                    'zipcode'                       => 'required',
                    'status'                        => 'required',
                ];
                if($this->validate($request, $rules)){
                    $checkData = Supplier::where('name', 'LIKE', '%'.$postData['name'].'%')->where('status', '!=', 3)->where('id', '!=', $id)->first();
                    if(!$checkData){
                        $fields = [
                            'name'                      => $postData['name'],
                            'contact_person_name'       => $postData['contact_person_name'],
                            'email'                     => $postData['email'],
                            'phone'                     => $postData['phone'],
                            'address'                   => $postData['address'],
                            'country'                   => $postData['country'],
                            'state'                     => $postData['state'],
                            'city'                      => $postData['city'],
                            'locality'                  => $postData['locality'],
                            'street_no'                 => $postData['street_no'],
                            'zipcode'                   => $postData['zipcode'],
                            'latitude'                  => $postData['latitude'],
                            'longitude'                 => $postData['longitude'],
                            'status'                    => $postData['status'],
                        ];
                        Supplier::where($this->data['primary_key'], '=', $id)->update($fields);
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
            Supplier::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Deleted Successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = Supplier::find($id);
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
