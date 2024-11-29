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
use App\Models\Coupon;
use App\Models\ProductDiscountVoucher;

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
            $generalSetting           = GeneralSetting::find('1');
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'sku'                       => 'required',
                    'barcode'                   => 'required',
                    'name'                      => 'required',
                    'receipt_short_name'        => 'required',
                    'shelf_tag_short_name'      => 'required',
                    'barcode'                   => 'required',
                    'brand_id'                  => 'required',
                    'supplier_id'               => 'required',
                    'cost_price_ex_tax'         => 'required',
                    'cost_price_inc_tax'        => 'required',
                    'retail_price_inc_tax'      => 'required',
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
                            'size_id'                   => $postData['size_id'],
                            'style'                     => $postData['style'],
                            'cost_price_ex_tax'         => $postData['cost_price_ex_tax'],
                            'cost_price_tax'            => $generalSetting->tax_percent,
                            'cost_price_inc_tax'        => $postData['cost_price_inc_tax'],
                            'markup_amount'             => $postData['markup_amount'],
                            'markup_type'               => ((array_key_exists("markup_type",$postData))?'PERCENTAGE':'FLAT'),
                            'added_amount'              => $postData['added_amount'],
                            'retail_price_inc_tax'      => $postData['retail_price_inc_tax'],
                            'cover_image'               => $cover_image,
                            'shop_stock'                => $postData['shop_stock'],
                            'warehouse_stock'           => $postData['warehouse_stock'],
                            'status'                    => ((array_key_exists("status",$postData))?1:0),
                        ];
                        // Helper::pr($fields);
                        $product_id = Product::insertGetId($fields);
                        /* discount vouchers */
                            $voucher_code               = $postData['voucher_code'];
                            $coupon_id                  = $postData['coupon_id'];
                            $discount_value             = $postData['discount_value'];
                            $discount_type              = $postData['discount_type'];
                            $retail_discount            = $postData['retail_discount'];
                            $retail_discounted_price    = $postData['retail_discounted_price'];
                            if(count($voucher_code) > 0){
                                for($k=0;$k<count($voucher_code);$k++){
                                    $fields2 = [
                                        'voucher_code'                      => $voucher_code[$k],
                                        'product_id'                        => $product_id,
                                        'coupon_id'                         => $coupon_id[$k],
                                        'discount_value'                    => $discount_value[$k],
                                        'discount_type'                     => $discount_type[$k],
                                        'retail_discount'                   => $retail_discount[$k],
                                        'retail_discounted_price'           => $retail_discounted_price[$k],
                                    ];
                                    // Helper::pr($fields2);
                                    ProductDiscountVoucher::insert($fields2);
                                }
                            }
                        /* discount vouchers */
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
            $generalSetting                 = GeneralSetting::find('1');
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
                    'barcode'                   => 'required',
                    'name'                      => 'required',
                    'receipt_short_name'        => 'required',
                    'shelf_tag_short_name'      => 'required',
                    'barcode'                   => 'required',
                    'brand_id'                  => 'required',
                    'supplier_id'               => 'required',
                    'cost_price_ex_tax'         => 'required',
                    'cost_price_inc_tax'        => 'required',
                    'retail_price_inc_tax'      => 'required',
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
                            'size_id'                   => $postData['size_id'],
                            'style'                     => $postData['style'],
                            'cost_price_ex_tax'         => $postData['cost_price_ex_tax'],
                            'cost_price_tax'            => $generalSetting->tax_percent,
                            'cost_price_inc_tax'        => $postData['cost_price_inc_tax'],
                            'markup_amount'             => $postData['markup_amount'],
                            'markup_type'               => ((array_key_exists("markup_type",$postData))?'PERCENTAGE':'FLAT'),
                            'added_amount'              => $postData['added_amount'],
                            'retail_price_inc_tax'      => $postData['retail_price_inc_tax'],
                            'cover_image'               => $cover_image,
                            'shop_stock'                => $postData['shop_stock'],
                            'warehouse_stock'           => $postData['warehouse_stock'],
                            'status'                    => ((array_key_exists("status",$postData))?1:0),
                        ];
                        // Helper::pr($fields);
                        Product::where($this->data['primary_key'], '=', $id)->update($fields);
                        $product_id = $id;
                        /* discount vouchers */
                            $voucher_code               = $postData['voucher_code'];
                            $coupon_id                  = $postData['coupon_id'];
                            $discount_value             = $postData['discount_value'];
                            $discount_type              = $postData['discount_type'];
                            $retail_discount            = $postData['retail_discount'];
                            $retail_discounted_price    = $postData['retail_discounted_price'];
                            if(count($voucher_code) > 0){
                                ProductDiscountVoucher::where('status', '=', 1)->where('product_id', '=', $id)->delete();
                                for($k=0;$k<count($voucher_code);$k++){
                                    if($voucher_code[$k] != ''){
                                        $fields2 = [
                                            'voucher_code'                      => $voucher_code[$k],
                                            'product_id'                        => $product_id,
                                            'coupon_id'                         => $coupon_id[$k],
                                            'discount_value'                    => $discount_value[$k],
                                            'discount_type'                     => $discount_type[$k],
                                            'retail_discount'                   => $retail_discount[$k],
                                            'retail_discounted_price'           => $retail_discounted_price[$k],
                                        ];
                                        // Helper::pr($fields2);
                                        ProductDiscountVoucher::insert($fields2);
                                    }
                                }
                            }
                        /* discount vouchers */
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
    public function getSuggestions(Request $request){
        $currentDate    = date('Y-m-d');
        $postData       = $request->all();
        $q              = $postData['q'];
        $retail_price   = $postData['retail_price'];
        $suggestions    = [];
        $getCoupons     = Coupon::select('name')->where('status', '=', 1)->where('discount_nature', '=', 'Voucher')->where('name', 'LIKE', '%'.$q.'%')->where('discount_amount', '<=', $retail_price)->orderBy('name', 'ASC')->get();
        // ->where('from_date', '>=', $currentDate)->where('to_date', '<=', $currentDate)
        if($getCoupons){
            foreach($getCoupons as $getCoupon){
                $suggestions[]    = $getCoupon->name;
            }
        }
        // Helper::pr($suggestions);
        $data = $suggestions;
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
    public function selectSuggestions(Request $request){
        $currentDate    = date('Y-m-d');
        $postData       = $request->all();
        // Helper::pr($postData);
        $selected       = $postData['selected'];
        $retail_price   = $postData['retail_price'];
        $response       = [];
        $apiStatus      = 0;
        $getCoupon      = Coupon::where('status', '=', 1)->where('name', '=', $selected)->first();
        if($getCoupon){
            $discount_type      = $getCoupon->discount_type;
            $discount_amount    = $getCoupon->discount_amount;
            if($discount_type == 'Percentage'){
                $discAmt = (($retail_price * $discount_amount) / 100);
            } else {
                $discAmt = $discount_amount;
            }
            $retail_discounted_price = ($retail_price - $discAmt);
            $response = [
                'coupon_id'                 => $getCoupon->id,
                'discount_value'            => $getCoupon->discount_amount,
                'discount_type'             => $getCoupon->discount_type,
                'retail_discount'           => $discAmt,
                'retail_discounted_price'   => $retail_discounted_price,
            ];
            $apiStatus      = 1;
        }
        // Helper::pr($suggestions);
        $data = ['status' => $apiStatus, 'response' => $response];
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}
