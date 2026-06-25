<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;

use Helper;

class ProductCategoryController extends Controller
{
    public function __construct()
    {
        $this->data = array(
            'title'             => 'Category',
            'controller'        => 'ProductCategoryController',
            'controller_route'  => 'categories',
            'primary_key'       => 'id',
        );
    }

    public function list(){
        $data['module']                 = $this->data;
        $title                          = $this->data['title'].' List';
        $page_name                      = 'category.list';
        $data['rows']                   = ProductCategory::where('status', '!=', 3)->orderBy('name', 'ASC')->get();
        echo $this->admin_after_login_layout($title,$page_name,$data);
    }

    public function add(Request $request){
        $data['module']           = $this->data;
        if($request->isMethod('post')){
            $postData = $request->all();
            $rules = [
                'name'                      => 'required',
            ];
            if($this->validate($request, $rules)){
                $checkData = ProductCategory::where('name', '=', $postData['name'])->where('status', '!=', 3)->first();
                if(!$checkData){
                    $fields = [
                        'name'                    => $postData['name'],
                        'status'                  => $postData['status'],
                    ];
                    ProductCategory::insert($fields);
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
        $page_name                      = 'category.add-edit';
        $data['row']                    = [];
        echo $this->admin_after_login_layout($title,$page_name,$data);
    }

    public function edit(Request $request, $id){
        $data['module']                 = $this->data;
        $id                             = Helper::decoded($id);
        $title                          = $this->data['title'].' Update';
        $page_name                      = 'category.add-edit';
        $data['row']                    = ProductCategory::where($this->data['primary_key'], '=', $id)->first();
        if($request->isMethod('post')){
            $postData = $request->all();
            $rules = [
                'name'                      => 'required',
            ];
            if($this->validate($request, $rules)){
                $checkData = ProductCategory::where('name', '=', $postData['name'])->where('status', '!=', 3)->where('id', '!=', $id)->first();
                if(!$checkData){
                    $fields = [
                        'name'                    => $postData['name'],
                        'status'                  => $postData['status'],
                    ];
                    ProductCategory::where($this->data['primary_key'], '=', $id)->update($fields);
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

    public function delete(Request $request, $id){
        $id                             = Helper::decoded($id);
        $fields = [
            'status'             => 3
        ];
        ProductCategory::where($this->data['primary_key'], '=', $id)->update($fields);
        return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Deleted Successfully !!!');
    }

    public function change_status(Request $request, $id){
        $id                             = Helper::decoded($id);
        $model                          = ProductCategory::find($id);
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
}
