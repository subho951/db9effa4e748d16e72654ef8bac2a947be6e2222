<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Supplier;
use App\Models\Country;

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
                    'email'                         => 'required',
                    'phone'                         => 'required',
                    'status'                        => 'required',
                ];
                if($this->validate($request, $rules)){
                    $checkData = Supplier::where('name', '=', $postData['name'])->where('status', '!=', 3)->first();
                    if(!$checkData){
                        if($postData['email2'] != ''){
                            if($postData['email'] == $postData['email2']){
                                return redirect()->back()->with('error_message', 'First Email and 2nd Email cannot be same !!!');
                            }
                        }

                        /* logo */
                            $imageFile      = $request->file('logo');
                            if($imageFile != ''){
                                $imageName      = $imageFile->getClientOriginalName();
                                $uploadedFile   = $this->upload_single_file('logo', $imageName, 'supplier', 'image');
                                if($uploadedFile['status']){
                                    $logo = $uploadedFile['newFilename'];
                                } else {
                                    return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                                }
                            } else {
                                $logo = '';
                            }
                        /* logo */

                        $fields = [
                            'supplier_code'                         => $postData['supplier_code'],
                            'name'                                  => $postData['name'],
                            'currency'                              => $postData['currency'],
                            'primary_lead_time'                     => $postData['primary_lead_time'],
                            'secondary_lead_time'                   => $postData['secondary_lead_time'],
                            'notes'                                 => $postData['notes'],
                            'footers'                               => $postData['footers'],
                            'logo'                                  => $logo,
                            'phone'                                 => $postData['phone'],
                            'email'                                 => $postData['email'],
                            'email2'                                => $postData['email2'],
                            'fax'                                   => $postData['fax'],
                            'website'                               => $postData['website'],
                            'b_street_address1'                     => $postData['b_street_address1'],
                            'b_street_address2'                     => $postData['b_street_address2'],
                            'b_city'                                => $postData['b_city'],
                            'b_state'                               => $postData['b_state'],
                            'b_postcode'                            => $postData['b_postcode'],
                            'b_country'                             => $postData['b_country'],
                            's_street_address1'                     => $postData['s_street_address1'],
                            's_street_address2'                     => $postData['s_street_address2'],
                            's_city'                                => $postData['s_city'],
                            's_state'                               => $postData['s_state'],
                            's_postcode'                            => $postData['s_postcode'],
                            's_country'                             => $postData['s_country'],
                            'status'                                => $postData['status'],
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
            $data['couns']                  = Country::select('country', 'currency_name', 'currency_code')->where('status', '=', 1)->orderBy('country', 'ASC')->get();
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
            $data['couns']                  = Country::select('country', 'currency_name', 'currency_code')->where('status', '=', 1)->orderBy('country', 'ASC')->get();
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'name'                          => 'required',
                    'email'                         => 'required',
                    'phone'                         => 'required',
                    'status'                        => 'required',
                ];
                if($this->validate($request, $rules)){
                    $checkData = Supplier::where('name', '=', $postData['name'])->where('status', '!=', 3)->where('id', '!=', $id)->first();
                    if(!$checkData){
                        if($postData['email2'] != ''){
                            if($postData['email'] == $postData['email2']){
                                return redirect()->back()->with('error_message', 'First Email and 2nd Email cannot be same !!!');
                            }
                        }

                        /* logo */
                            $imageFile      = $request->file('logo');
                            if($imageFile != ''){
                                $imageName      = $imageFile->getClientOriginalName();
                                $uploadedFile   = $this->upload_single_file('logo', $imageName, 'supplier', 'image');
                                if($uploadedFile['status']){
                                    $logo = $uploadedFile['newFilename'];
                                } else {
                                    return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                                }
                            } else {
                                $logo = $data['row']->logo;
                            }
                        /* logo */

                        $fields = [
                            'supplier_code'                         => $postData['supplier_code'],
                            'name'                                  => $postData['name'],
                            'currency'                              => $postData['currency'],
                            'primary_lead_time'                     => $postData['primary_lead_time'],
                            'secondary_lead_time'                   => $postData['secondary_lead_time'],
                            'notes'                                 => $postData['notes'],
                            'footers'                               => $postData['footers'],
                            'logo'                                  => $logo,
                            'phone'                                 => $postData['phone'],
                            'email'                                 => $postData['email'],
                            'email2'                                => $postData['email2'],
                            'fax'                                   => $postData['fax'],
                            'website'                               => $postData['website'],
                            'b_street_address1'                     => $postData['b_street_address1'],
                            'b_street_address2'                     => $postData['b_street_address2'],
                            'b_city'                                => $postData['b_city'],
                            'b_state'                               => $postData['b_state'],
                            'b_postcode'                            => $postData['b_postcode'],
                            'b_country'                             => $postData['b_country'],
                            's_street_address1'                     => $postData['s_street_address1'],
                            's_street_address2'                     => $postData['s_street_address2'],
                            's_city'                                => $postData['s_city'],
                            's_state'                               => $postData['s_state'],
                            's_postcode'                            => $postData['s_postcode'],
                            's_country'                             => $postData['s_country'],
                            'status'                                => $postData['status'],
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
