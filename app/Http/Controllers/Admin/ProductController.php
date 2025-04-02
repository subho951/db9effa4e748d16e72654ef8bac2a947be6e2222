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
use App\Models\ProductMultipleBuy;
use App\Models\UploadProduct;
use App\Models\Admin;
use App\Models\ShelfTag;

use Illuminate\Support\Facades\File;
use Picqer\Barcode\BarcodeGeneratorPNG;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
use Dompdf\Dompdf;
use Dompdf\Options;
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
        public function list(Request $request){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'product.list';
            $data['brands']                 = Brand::select('id', 'name')->where('status', '=', 1)->get();
            $data['suppliers']              = Supplier::select('id', 'name')->where('status', '=', 1)->get();

            if ($request->isMethod('get') && $request->has('mode')) {
                $data['status']                 = '';
                $data['brand_id']               = '';
                $data['supplier_id']            = '';
                $data['is_search']              = 1;
                $data['rows']                   = [];
                $status                         = $request->status;
                $brand_id                       = $request->brand_id;
                $supplier_id                    = $request->supplier_id;
                if($status != '' && $brand_id == '' && $supplier_id == ''){
                    $data['rows']                   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '=', $status)
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['supplier_id']            = $supplier_id;
                    $data['is_search']              = 1;
                } elseif($status == '' && $brand_id != '' && $supplier_id == ''){
                    $data['rows']                   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '!=', 3)
                                                ->where('products.brand_id', '=', $brand_id)
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['supplier_id']            = $supplier_id;
                    $data['is_search']              = 1;
                } elseif($status == '' && $brand_id == '' && $supplier_id != ''){
                    $data['rows']                   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '!=', 3)
                                                ->where('products.supplier_id', '=', $supplier_id)
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['supplier_id']            = $supplier_id;
                    $data['is_search']              = 1;
                } elseif($status != '' && $brand_id != '' && $supplier_id == ''){
                    $data['rows']                   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '=', $status)
                                                ->where('products.brand_id', '=', $brand_id)
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['supplier_id']            = $supplier_id;
                    $data['is_search']              = 1;
                } elseif($status != '' && $brand_id == '' && $supplier_id != ''){
                    $data['rows']                   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '=', $status)
                                                ->where('products.supplier_id', '=', $supplier_id)
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['supplier_id']            = $supplier_id;
                    $data['is_search']              = 1;
                } elseif($status == '' && $brand_id != '' && $supplier_id != ''){
                    $data['rows']                   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '!=', 3)
                                                ->where('products.brand_id', '=', $brand_id)
                                                ->where('products.supplier_id', '=', $supplier_id)
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['supplier_id']            = $supplier_id;
                    $data['is_search']              = 1;
                } elseif($status != '' && $brand_id != '' && $supplier_id != ''){
                    $data['rows']                   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '=', $status)
                                                ->where('products.brand_id', '=', $brand_id)
                                                ->where('products.supplier_id', '=', $supplier_id)
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['supplier_id']            = $supplier_id;
                    $data['is_search']              = 1;
                } elseif($status == '' && $brand_id == '' && $supplier_id == ''){
                    $data['is_search']              = 0;
                    return redirect()->back()->with('error_message', 'Please select any of the filter parameter');
                }
                
            } else {
                $data['rows']                   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '!=', 3)
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                $data['status']                 = '';
                $data['brand_id']               = '';
                $data['supplier_id']            = '';
                $data['is_search']              = 0;
            }
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
                        $barcode_image_url = '';
                        $barcode_image_url = $this->generateBarcode($postData['barcode']);
                        $fields = [
                            'sku'                       => $postData['sku'],
                            'name'                      => $postData['name'],
                            'receipt_short_name'        => $postData['receipt_short_name'],
                            'shelf_tag_short_name'      => $postData['shelf_tag_short_name'],
                            'barcode'                   => $postData['barcode'],
                            'barcode_image_url'         => $barcode_image_url,
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
                        /* multiple buys */
                            $first_barcode              = $postData['first_barcode'];
                            $second_barcode             = $postData['second_barcode'];
                            $product2_id                = $postData['product2_id'];
                            $barcode_discount_type      = ((array_key_exists('barcode_discount_type', $postData))?$postData['barcode_discount_type']:[]);
                            $discount_amount            = $postData['discount_amount'];
                            if(count($voucher_code) > 0){
                                for($k=0;$k<count($second_barcode);$k++){
                                    if($second_barcode[$k] != ''){
                                        $getProduct1            = Product::select('retail_price_inc_tax')->where('status', '=', 1)->where('id', '=', $product_id)->first();
                                        $getProduct2            = Product::select('retail_price_inc_tax')->where('status', '=', 1)->where('id', '=', $product2_id[$k])->first();

                                        $retail_price_inc_tax1  = (($getProduct1)?$getProduct1->retail_price_inc_tax:'');
                                        $retail_price_inc_tax2  = (($getProduct2)?$getProduct2->retail_price_inc_tax:'');
                                        $total_price            = ($retail_price_inc_tax1 + $retail_price_inc_tax2);
                                        $discAmt                = 0;
                                        if(array_key_exists('barcode_discount_type', $postData)){
                                            $discAmt        = (($total_price * $discount_amount[$k]) / 100);
                                            $discountType  = 'PERCENTAGE';
                                        } else {
                                            $discAmt        = $discount_amount[$k];
                                            $discountType  = 'FLAT';
                                        }
                                        $discounted_amount = ($total_price - $discAmt);
                                        $fields2                = [
                                            'product_id'                        => $product_id,
                                            'first_barcode'                     => $first_barcode[$k],
                                            'second_barcode'                    => $second_barcode[$k],
                                            'product2_id'                       => $product2_id[$k],
                                            'barcode_discount_type'             => $discountType,
                                            'discount_amount'                   => $discount_amount[$k],
                                            'discounted_amount'                 => $discounted_amount,
                                        ];
                                        // Helper::pr($fields2);
                                        ProductMultipleBuy::insert($fields2);
                                    }
                                }
                            }
                        /* multiple buys */
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
                        // Ensure barcode directory exists
                        $barcode_image_url = '';
                        $barcode_image_url = $this->generateBarcode($postData['barcode']);
                        $fields = [
                            'sku'                       => $postData['sku'],
                            'name'                      => $postData['name'],
                            'receipt_short_name'        => $postData['receipt_short_name'],
                            'shelf_tag_short_name'      => $postData['shelf_tag_short_name'],
                            'barcode'                   => $postData['barcode'],
                            'barcode_image_url'         => $barcode_image_url,
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
                        /* multiple buys */
                            $first_barcode              = $postData['first_barcode'];
                            $second_barcode             = $postData['second_barcode'];
                            $product2_id                = $postData['product2_id'];
                            $barcode_discount_type      = ((array_key_exists('barcode_discount_type', $postData))?$postData['barcode_discount_type']:[]);
                            $discount_amount            = $postData['discount_amount'];
                            if(count($voucher_code) > 0){
                                ProductMultipleBuy::where('status', '=', 1)->where('product_id', '=', $id)->delete();
                                for($k=0;$k<count($second_barcode);$k++){
                                    if($second_barcode[$k] != ''){
                                        $getProduct1            = Product::select('retail_price_inc_tax')->where('status', '=', 1)->where('id', '=', $product_id)->first();
                                        $getProduct2            = Product::select('retail_price_inc_tax')->where('status', '=', 1)->where('id', '=', $product2_id[$k])->first();

                                        $retail_price_inc_tax1  = (($getProduct1)?$getProduct1->retail_price_inc_tax:'');
                                        $retail_price_inc_tax2  = (($getProduct2)?$getProduct2->retail_price_inc_tax:'');
                                        $total_price            = ($retail_price_inc_tax1 + $retail_price_inc_tax2);
                                        $discAmt                = 0;
                                        if(array_key_exists('barcode_discount_type', $postData)){
                                            $discAmt        = (($total_price * $discount_amount[$k]) / 100);
                                            $discountType  = 'PERCENTAGE';
                                        } else {
                                            $discAmt        = $discount_amount[$k];
                                            $discountType  = 'FLAT';
                                        }
                                        $discounted_amount = ($total_price - $discAmt);
                                        $fields2                = [
                                            'product_id'                        => $product_id,
                                            'first_barcode'                     => $first_barcode[$k],
                                            'second_barcode'                    => $second_barcode[$k],
                                            'product2_id'                       => $product2_id[$k],
                                            'barcode_discount_type'             => $discountType,
                                            'discount_amount'                   => $discount_amount[$k],
                                            'discounted_amount'                 => $discounted_amount,
                                        ];
                                        // Helper::pr($fields2);
                                        ProductMultipleBuy::insert($fields2);
                                    }
                                }
                            }
                        /* multiple buys */
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
    public function getBarcodeSuggestions(Request $request){
        $currentDate    = date('Y-m-d');
        $postData       = $request->all();
        $q              = $postData['q'];
        $barcode        = $postData['barcode'];
        $suggestions    = [];
        $getProducts     = Product::select('id', 'barcode')->where('status', '=', 1)->where('barcode', 'LIKE', '%'.$q.'%')->where('barcode', '!=', $barcode)->orderBy('barcode', 'ASC')->get();
        if($getProducts){
            foreach($getProducts as $getProduct){
                $suggestions[]    = $getProduct->barcode;
            }
        }
        // Helper::pr($suggestions);
        $data = $suggestions;
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
    public function selectBarcodeSuggestions(Request $request){
        $currentDate    = date('Y-m-d');
        $postData       = $request->all();
        // Helper::pr($postData);
        $selected       = $postData['selected'];
        $barcode        = $postData['barcode'];
        $response       = [];
        $apiStatus      = 0;
        $getProduct      = Product::where('status', '=', 1)->where('barcode', '=', $selected)->first();
        if($getProduct){
            $discount_type      = $getProduct->discount_type;
            $response = [
                'product_id'         => $getProduct->id,
                'barcode'            => $getProduct->barcode,
            ];
            $apiStatus      = 1;
        }
        // Helper::pr($suggestions);
        $data = ['status' => $apiStatus, 'response' => $response];
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
    /* upload products */
        public function uploadProduct(Request $request){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Upload List';
            $page_name                      = 'product.upload-product';
            $data['rows']                   = UploadProduct::where('status', '=', 1)->orderBy('id', 'DESC')->get();
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'title'                 => 'required',
                    'filename'              => 'required'
                ];
                if($this->validate($request, $rules)){
                    /* banner image */
                        $imageFile      = $request->file('filename');
                        if($imageFile != ''){
                            $imageName      = $imageFile->getClientOriginalName();
                            $uploadedFile   = $this->upload_single_file('filename', $imageName, 'product', 'csv');
                            if($uploadedFile['status']){
                                $filename       = $uploadedFile['newFilename'];
                                $sessionData    = Auth::guard('admin')->user();
                                $fields         = [
                                    'title'                 => $postData['title'],
                                    'filename'              => $filename,
                                    'created_by'            => $sessionData->id,
                                    'updated_by'            => $sessionData->id,
                                ];
                                $upload_id = UploadProduct::insertGetId($fields);
                                /* extract data from file & insert into three category tables */
                                    // Path to the CSV file
                                    $file_path = './public/uploads/product/'.$filename;
                                    // Open the CSV file for reading
                                    if (($handle = fopen($file_path, 'r')) !== FALSE) {
                                        // Loop through each line in the file
                                        $counter = 0;
                                        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                                            // Print each line's data as an array
                                            if($counter > 0){
                                                // Helper::pr($data);die;
                                                $sku                        = $data[0];
                                                $name                       = $data[1];
                                                $receipt_short_name         = $data[2];
                                                $shelf_tag_short_name       = $data[3];
                                                $barcode                    = $data[4];
                                                $brand                      = $data[5];
                                                $supplier                   = $data[6];
                                                $size                       = $data[7];
                                                $style                      = $data[8];
                                                $cost_price_ex_tax          = $data[9];
                                                $cost_price_tax             = $data[10];
                                                $cost_price_inc_tax         = $data[11];
                                                $markup_amount              = $data[12];
                                                $markup_type                = $data[13];
                                                $added_amount               = $data[14];
                                                $retail_price_inc_tax       = $data[15];
                                                
                                                $getBrandId                 = Brand::select('id')->where('name', 'LIKE', '%'.$brand.'%')->first();
                                                $getSupplierId              = Supplier::select('id')->where('name', 'LIKE', '%'.$supplier.'%')->first();
                                                $getSizeId                  = Size::select('id')->where('name', 'LIKE', '%'.$size.'%')->first();

                                                $fields = [
                                                    'sku'                       => $sku,
                                                    'name'                      => $name,
                                                    'receipt_short_name'        => $receipt_short_name,
                                                    'shelf_tag_short_name'      => $shelf_tag_short_name,
                                                    'barcode'                   => $barcode,
                                                    'brand_id'                  => (($getBrandId)?$getBrandId->id:0),
                                                    'supplier_id'               => (($getSupplierId)?$getSupplierId->id:0),
                                                    'size_id'                   => (($getSizeId)?$getSizeId->id:0),
                                                    'style'                     => $style,
                                                    'cost_price_ex_tax'         => $cost_price_ex_tax,
                                                    'cost_price_tax'            => $cost_price_tax,
                                                    'cost_price_inc_tax'        => $cost_price_inc_tax,
                                                    'markup_amount'             => $markup_amount,
                                                    'markup_type'               => $markup_type,
                                                    'added_amount'              => $added_amount,
                                                    'retail_price_inc_tax'      => $retail_price_inc_tax,
                                                    'upload_id'                 => $upload_id,
                                                ];
                                                // Helper::pr($fields);
                                                Product::insert($fields);
                                            }
                                            $counter++;
                                        }
                                        // Close the file
                                        fclose($handle);
                                        return redirect("admin/" . $this->data['controller_route'] . "/upload-product")->with('success_message', $this->data['title'].' Uploaded Successfully !!!');
                                    } else {
                                        return redirect()->back()->with(['error_message' => 'Error opening the file !!!']);
                                    }
                                /* extract data from file & insert into three category tables */
                            } else {
                                return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                            }
                        } else {
                            return redirect()->back()->with(['error_message' => 'CSV file is required to upload !!!']);
                        }
                    /* banner image */
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function deleteUploadProduct(Request $request, $id){
            $id                             = Helper::decoded($id);
            UploadProduct::where($this->data['primary_key'], '=', $id)->delete();
            Product::where('upload_id', '=', $id)->delete();
            return redirect("admin/" . $this->data['controller_route'] . "/upload-product")->with('success_message', 'Uploaded Products Deleted Successfully !!!');
        }
    /* upload products */
    /* generate barcodes */
        public function generateBarcode($code)
        {
            // Ensure the directory exists
            if (!File::exists(public_path('uploads/barcodes'))) {
                File::makeDirectory(public_path('uploads/barcodes'), 0775, true);
            }

            // // Define barcode file path
            // $path = public_path("uploads/barcodes/{$code}.png");

            // Generate barcode
            $generator = new BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);

            // Create an image from the barcode
            $barcodeImage = imagecreatefromstring($barcode);
            $width = imagesx($barcodeImage);
            $height = imagesy($barcodeImage);

            // Create a new image with extra space for text
            $newHeight = $height + 30; // Extra 30px for text
            $finalImage = imagecreatetruecolor($width, $newHeight);

            // Set white background
            $white = imagecolorallocate($finalImage, 255, 255, 255);
            imagefilledrectangle($finalImage, 0, 0, $width, $newHeight, $white);

            // Copy the barcode onto the new image
            imagecopy($finalImage, $barcodeImage, 0, 0, 0, 0, $width, $height);

            // Add text (barcode number)
            $black = imagecolorallocate($finalImage, 0, 0, 0);
            $font = 5; // Built-in GD font
            $textWidth = imagefontwidth($font) * strlen($code);
            $x = ($width - $textWidth) / 2; // Center text
            $y = $height + 5; // Position below barcode
            imagestring($finalImage, $font, $x, $y, $code, $black);

            // Define barcode file path
            $filePath = public_path("uploads/barcodes/{$code}.png");

            // Save the final image
            imagepng($finalImage, $filePath);

            // Free up memory
            imagedestroy($barcodeImage);
            imagedestroy($finalImage);

            // Save barcode image
            // file_put_contents($path, $barcode);
            $barcode_url =  url("public/uploads/barcodes/{$code}.png");
            return $barcode_url;
        }
    /* generate barcodes */
    /* print barcodes */
        public function printBarcode($id){
            $id                             = Helper::decoded($id);
            $data['row']                    = Product::select('sku', 'name', 'barcode', 'barcode_image_url')->where($this->data['primary_key'], '=', $id)->first();
            return view('admin.maincontents.product.print-barcode', $data);
        }
    /* print barcodes */
    /* search products for barcode */
        public function generateProductBarcode(Request $request){
            $data['module']                 = $this->data;
            $title                          = 'Shelf Tags and Discounts';
            $page_name                      = 'product.generate-product-barcode';
            $data['brands']                 = Brand::select('id', 'name')->where('status', '=', 1)->get();
            $data['status']                 = '';
            $data['brand_id']               = '';
            $data['discount_type']          = '';
            $data['is_search']              = 1;
            $data['rows']                   = [];

            if ($request->isMethod('get') && $request->has('mode')) {
                $status                         = $request->status;
                $brand_id                       = $request->brand_id;
                $discount_type                  = $request->discount_type;
                if($status != '' && $brand_id == '' && $discount_type == ''){
                    $data['rows']   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '=', $status)
                                                ->groupBy('products.id')
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['discount_type']          = $discount_type;
                    $data['is_search']              = 1;
                } elseif($status == '' && $brand_id != '' && $discount_type == ''){
                    $data['rows']   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '!=', 3)
                                                ->where('products.brand_id', '=', $brand_id)
                                                ->groupBy('products.id')
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['discount_type']          = $discount_type;
                    $data['is_search']              = 1;
                } elseif($status == '' && $brand_id == '' && $discount_type != ''){
                    $data['rows']   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '!=', 3)
                                                ->where('product_discount_vouchers.discount_type', '=', $discount_type)
                                                ->groupBy('products.id')
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['discount_type']          = $discount_type;
                    $data['is_search']              = 1;
                } elseif($status != '' && $brand_id != '' && $discount_type == ''){
                    $data['rows']   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '=', $status)
                                                ->where('products.brand_id', '=', $brand_id)
                                                ->groupBy('products.id')
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['discount_type']          = $discount_type;
                    $data['is_search']              = 1;
                } elseif($status != '' && $brand_id == '' && $discount_type != ''){
                    $data['rows']   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '=', $status)
                                                ->where('product_discount_vouchers.discount_type', '=', $discount_type)
                                                ->groupBy('products.id')
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['discount_type']          = $discount_type;
                    $data['is_search']              = 1;
                } elseif($status == '' && $brand_id != '' && $discount_type != ''){
                    $data['rows']   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '!=', 3)
                                                ->where('products.brand_id', '=', $brand_id)
                                                ->where('product_discount_vouchers.discount_type', '=', $discount_type)
                                                ->groupBy('products.id')
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['discount_type']          = $discount_type;
                    $data['is_search']              = 1;
                } elseif($status != '' && $brand_id != '' && $discount_type != ''){
                    $data['rows']   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '=', $status)
                                                ->where('products.brand_id', '=', $brand_id)
                                                ->where('product_discount_vouchers.discount_type', '=', $discount_type)
                                                ->groupBy('products.id')
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
                    $data['status']                 = $status;
                    $data['brand_id']               = $brand_id;
                    $data['discount_type']          = $discount_type;
                    $data['is_search']              = 1;
                } elseif($status == '' && $brand_id == '' && $discount_type == ''){
                    $data['is_search']              = 0;
                    return redirect()->back()->with('error_message', 'Please select any of the filter parameter');
                }
            }
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function printProducts(Request $request){
            $postData = $request->all();
            $product_id = $postData['product_id'];
            $products = [];
            if(!empty($product_id)){
                $getLastPriceTag = ShelfTag::orderBy('id', 'DESC')->first();
                if($getLastPriceTag){
                    $sl_no              = $getLastPriceTag->sl_no;
                    $next_sl_no         = $sl_no + 1;
                    $next_sl_no_string  = str_pad($next_sl_no, 4, 0, STR_PAD_LEFT);
                    $sequence_no        = $next_sl_no_string;
                } else {
                    $next_sl_no         = 111;
                    $next_sl_no_string  = str_pad($next_sl_no, 4, 0, STR_PAD_LEFT);
                    $sequence_no        = $next_sl_no_string; 
                }
                $fields = [
                    'sl_no'        => $next_sl_no,
                    'sequence_no'  => $sequence_no
                ];
                $shilf_tag_id = ShelfTag::insertGetId($fields);

                /* shelf tag pdf generate */
                    $sequence_no                    = $sequence_no;
                    $generalSetting                 = GeneralSetting::find('1');
                    $subject                        = 'PriceTag' . $sequence_no;

                    for($p=0;$p<count($product_id);$p++){
                        $getProduct = Product::select('name', 'retail_price_inc_tax')->where('id', '=', $product_id[$p])->first();
                        $products[] = [
                            'name'  => (($getProduct)?$getProduct->name:''),
                            'price' => (($getProduct)?$getProduct->retail_price_inc_tax:0),
                        ];
                        $discountVouchers = ProductDiscountVoucher::select('voucher_code', 'retail_discounted_price')->where('product_id', $product_id[$p])->where('status', 1)->get();
                        if($discountVouchers){ foreach($discountVouchers as $discountVoucher){
                            $products[] = [
                                'name'  => (($getProduct)?$getProduct->name.' [<small style="font-size: 8px;">'.$discountVoucher->voucher_code.'</small>]':''),
                                'price' => $discountVoucher->retail_discounted_price,
                            ];
                        } }
                    }
                    $data['products']   = $products;
                    $message            = view('admin.maincontents.product.print-products', $data);                       
                    // echo $message;die;
                    $options            = new Options();
                    $options->set('defaultFont', 'Courier');
                    $dompdf             = new Dompdf($options);
                    $html               = $message;
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
                    $output             = $dompdf->output();
                    // $dompdf->stream("document.pdf", array("Attachment" => false));die;
                    $filename           = $sequence_no.'.pdf';
                    $pdfFilePath        = 'public/uploads/shelf_tags/' . $filename;
                    file_put_contents($pdfFilePath, $output);
                    ShelfTag::where('id', '=', $shilf_tag_id)->update(['filename' => $filename]);
                /* shelf tag pdf generate */
                return redirect("admin/" . $this->data['controller_route'] . "/shelf-tag-list")->with('success_message', 'Shelf tag generated successfully');
            }
        }
        public function shelfTagList(Request $request){
            $data['module']                 = $this->data;
            $title                          = 'Shelf Tags List';
            $page_name                      = 'product.shelf-tag-list';
            $data['rows']                   = ShelfTag::where('status', 1)->orderBy('id', 'DESC')->get();
            if($request->isMethod('post')){
                $generalSetting     = GeneralSetting::find(1);
                $postData           = $request->all();
                $shilf_tag_id       = $postData['shilf_tag_id'];
                $filename           = $postData['filename'];
                $emails2            = explode(",",$postData['emails']);
                $getShelfTag        = ShelfTag::where('id', '=', $shilf_tag_id)->first();
                $emails1            = [];
                if($getShelfTag){
                    $emails1 = json_decode($getShelfTag->emails);
                }
                if(!empty($emails1)){
                    $updated_emails = array_merge($emails1, $emails2);
                } else {
                    $updated_emails = $emails2;
                }
                // Helper::pr($updated_emails);
                ShelfTag::where('id', '=', $shilf_tag_id)->update(['emails' => json_encode($updated_emails)]);
                /* email sent */
                    if(!empty($updated_emails)){
                        for($k=0;$k<count($updated_emails);$k++){
                            $to                         = $updated_emails[$k];
                            $subject                    = $generalSetting->site_name . " Price Tag " . (($getShelfTag)?$getShelfTag->sequence_no:'');
                            $message                    = $subject;
                            $attchment                  = 'public/uploads/shelf_tags/' . $getShelfTag->filename;
                            $this->sendMail($to, $subject, $message, $attchment);
                        }
                    }
                /* email sent */
                return redirect()->back()->with('success_message', 'Shelf price tag file sent successfully');
            }
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* search products for barcode */
    /* validate admin pin products */
        public function validateAdminPinProduct(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            // Helper::pr($requestData);
            if($requestData['key'] == env('PROJECT_KEY')){
                $pin1           = $requestData['pin1'];
                $pin2           = $requestData['pin2'];
                $pin3           = $requestData['pin3'];
                $pin4           = $requestData['pin4'];
                $product_id     = $requestData['product_id'];
                $completePin    = $pin1.$pin2.$pin3.$pin4;
                $getAdmin       = Admin::where('id','=',1)->first();
                if(Hash::check($completePin, $getAdmin->password)){
                    $redirectUrl = url('admin/products/edit/'.Helper::encoded($product_id));
                    // $apiResponse['redirectUrl'] = $redirectUrl;
                    // http_response_code(200);
                    // $apiStatus          = TRUE;
                    // $apiMessage         = 'Admin PIN matched !!!';
                    // $apiExtraField      = 'response_code';
                    // $apiExtraData       = http_response_code();
                    return redirect($redirectUrl)->with('success_message', 'Admin PIN matched !!!');
                } else {
                    // http_response_code(200);
                    // $apiStatus          = FALSE;
                    // $apiMessage         = 'Admin PIN Doesn\'t match !!!';
                    // $apiExtraField      = 'response_code';
                    // $apiExtraData       = http_response_code();
                    return redirect()->back()->with('error_message', 'Admin PIN Doesn\'t match !!!');
                }
            } else {
                // http_response_code(400);
                // $apiStatus          = FALSE;
                // $apiMessage         = $this->getResponseCode(http_response_code());
                // $apiExtraField      = 'response_code';
                // $apiExtraData       = http_response_code();
                return redirect()->back()->with('error_message', 'Invalid request');
            }
            // $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function validateAdminPinExport(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            // Helper::pr($requestData);
            if($requestData['key'] == env('PROJECT_KEY')){
                $pin1           = $requestData['pin1'];
                $pin2           = $requestData['pin2'];
                $pin3           = $requestData['pin3'];
                $pin4           = $requestData['pin4'];
                $completePin    = $pin1.$pin2.$pin3.$pin4;
                $getAdmin       = Admin::where('id','=',1)->first();
                if(Hash::check($completePin, $getAdmin->password)){
                    $request->session()->put('is_export', 1);
                    // $apiResponse['redirectUrl'] = $redirectUrl;
                    // http_response_code(200);
                    // $apiStatus          = TRUE;
                    // $apiMessage         = 'Admin PIN matched !!!';
                    // $apiExtraField      = 'response_code';
                    // $apiExtraData       = http_response_code();
                    return redirect(url('admin/products/list'))->with('success_message', 'Admin PIN matched !!!');
                } else {
                    // http_response_code(200);
                    // $apiStatus          = FALSE;
                    // $apiMessage         = 'Admin PIN Doesn\'t match !!!';
                    // $apiExtraField      = 'response_code';
                    // $apiExtraData       = http_response_code();
                    return redirect()->back()->with('error_message', 'Admin PIN Doesn\'t match !!!');
                }
            } else {
                // http_response_code(400);
                // $apiStatus          = FALSE;
                // $apiMessage         = $this->getResponseCode(http_response_code());
                // $apiExtraField      = 'response_code';
                // $apiExtraData       = http_response_code();
                return redirect()->back()->with('error_message', 'Invalid request');
            }
            // $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
    /* validate admin pin products */
}
