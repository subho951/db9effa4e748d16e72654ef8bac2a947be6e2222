<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Size;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
class ProductController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Product',
            'controller'        => 'ProductController',
            'controller_route'  => 'products',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'product.list';
            $data['rows']                   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name')
                                                ->where('products.status', '!=', 3)
                                                ->orderBy('products.id', 'DESC')
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
                    'sku'                       => 'required',
                    'name'                      => 'required',
                    'receipt_short_name'        => 'required',
                    'shelf_tag_short_name'      => 'required',
                    'barcode'                   => 'required',
                    'brand_id'                  => 'required',
                    'supplier_id'               => 'required',
                    'cost_price_ex_tax'         => 'required',
                    'cost_price_inc_tax'        => 'required',
                    'retail_price_inc_tax'      => 'required',
                    'status'                    => 'required',
                ];
                if($this->validate($request, $rules)){
                    $checkData = Product::where('name', 'LIKE', '%'.$postData['name'].'%')->where('status', '!=', 3)->first();
                    if(!$checkData){
                        /* cover image */
                            $imageFile      = $request->file('cover_image');
                            if($imageFile != ''){
                                $imageName      = $imageFile->getClientOriginalName();
                                $uploadedFile   = $this->upload_single_file('cover_image', $imageName, 'product', 'image');
                                if($uploadedFile['status']){
                                    $cover_image = $uploadedFile['newFilename'];
                                } else {
                                    return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                                }
                            } else {
                                $cover_image = '';
                            }
                        /* cover image */
                        $fields = [
                            'sku'                       => $postData['sku'],
                            'name'                      => $postData['name'],
                            'receipt_short_name'        => $postData['receipt_short_name'],
                            'shelf_tag_short_name'      => $postData['shelf_tag_short_name'],
                            'barcode'                   => $postData['barcode'],
                            'brand_id'                  => $postData['brand_id'],
                            'supplier_id'               => $postData['supplier_id'],
                            'cost_price_ex_tax'         => $postData['cost_price_ex_tax'],
                            'cost_price_inc_tax'        => $postData['cost_price_inc_tax'],
                            'retail_price_inc_tax'      => $postData['retail_price_inc_tax'],
                            'cover_image'               => $cover_image,
                            'stock'                     => $postData['stock'],
                            'status'                    => $postData['status'],
                        ];
                        Product::insert($fields);
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
            $page_name                      = 'product.add-edit';
            $data['row']                    = [];
            $data['brands']                 = Brand::select('id', 'name')->where('status', '=', 1)->get();
            $data['suppliers']              = Supplier::select('id', 'name')->where('status', '=', 1)->get();
            $data['sizes']                  = DB::table('sizes')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('sizes.*', 'units.name as unit_name')
                                                ->where('sizes.status', '=', 1)
                                                ->orderBy('sizes.id', 'ASC')
                                                ->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'product.add-edit';
            $data['row']                    = Product::where($this->data['primary_key'], '=', $id)->first();
            $data['brands']                 = Brand::select('id', 'name')->where('status', '=', 1)->get();
            $data['suppliers']              = Supplier::select('id', 'name')->where('status', '=', 1)->get();
            $data['sizes']                  = DB::table('sizes')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('sizes.*', 'units.name as unit_name')
                                                ->where('sizes.status', '=', 1)
                                                ->orderBy('sizes.id', 'ASC')
                                                ->get();
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'sku'                       => 'required',
                    'name'                      => 'required',
                    'receipt_short_name'        => 'required',
                    'shelf_tag_short_name'      => 'required',
                    'barcode'                   => 'required',
                    'brand_id'                  => 'required',
                    'supplier_id'               => 'required',
                    'cost_price_ex_tax'         => 'required',
                    'cost_price_inc_tax'        => 'required',
                    'retail_price_inc_tax'      => 'required',
                    'status'                    => 'required',
                ];
                if($this->validate($request, $rules)){
                    $checkData = Product::where('name', 'LIKE', '%'.$postData['name'].'%')->where('status', '!=', 3)->where('id', '!=', $id)->first();
                    if(!$checkData){
                        /* cover image */
                            $imageFile      = $request->file('cover_image');
                            if($imageFile != ''){
                                $imageName      = $imageFile->getClientOriginalName();
                                $uploadedFile   = $this->upload_single_file('cover_image', $imageName, 'product', 'image');
                                if($uploadedFile['status']){
                                    $cover_image = $uploadedFile['newFilename'];
                                } else {
                                    return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                                }
                            } else {
                                $cover_image = $data['row']->cover_image;
                            }
                        /* cover image */
                        $fields = [
                            'sku'                       => $postData['sku'],
                            'name'                      => $postData['name'],
                            'receipt_short_name'        => $postData['receipt_short_name'],
                            'shelf_tag_short_name'      => $postData['shelf_tag_short_name'],
                            'barcode'                   => $postData['barcode'],
                            'brand_id'                  => $postData['brand_id'],
                            'supplier_id'               => $postData['supplier_id'],
                            'cost_price_ex_tax'         => $postData['cost_price_ex_tax'],
                            'cost_price_inc_tax'        => $postData['cost_price_inc_tax'],
                            'retail_price_inc_tax'      => $postData['retail_price_inc_tax'],
                            'cover_image'               => $cover_image,
                            'stock'                     => $postData['stock'],
                            'status'                    => $postData['status'],
                        ];
                        Product::where($this->data['primary_key'], '=', $id)->update($fields);
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
            Product::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Deleted Successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = Product::find($id);
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
