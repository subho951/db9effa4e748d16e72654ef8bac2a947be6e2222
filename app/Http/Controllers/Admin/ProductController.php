<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\ProductCategory;
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
use App\Models\WarehouseStock;
use App\Models\ShopStock;

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
                // Helper::pr($postData);
                $rules = [
                    'sku'                       => 'required|alpha_num|min:4|max:10',
                    'barcode'                   => [
                                                        'required',
                                                        'alpha_num',
                                                        'min:8',
                                                        'max:25',
                                                        Rule::unique('products', 'barcode')->where(function($query) {
                                                            return $query->where('status', '!=', 3);
                                                        }),
                                                    ],
                    'name'                      => 'required',
                    'receipt_short_name'        => 'required',
                    'shelf_tag_short_name'      => 'required',
                    'brand_id'                  => 'required',
                    'category_id'               => [
                                                        'required',
                                                        Rule::exists('product_categories', 'id')->where(function($query) {
                                                            return $query->where('status', '=', 1);
                                                        }),
                                                    ],
                    'supplier_id'               => 'required',
                    'cost_price_ex_tax'         => 'required|numeric|min:0',
                    'retail_price_inc_tax'      => 'required|numeric|min:0',
                ];
                $messages = [
                    'barcode.alpha_num' => 'Barcode must contain only letters and numbers.',
                    'barcode.unique'    => 'Barcode already exists. Please enter a unique barcode.',
                ];
                if($this->validate($request, $rules, $messages)){
                    $offerErrors = $this->validateProductDiscountOfferRows($postData);
                    if(!empty($offerErrors)){
                        return redirect()->back()->withInput()->with('error_message', implode('<br>', $offerErrors));
                    }

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

                        /* new brand name */
                            $brand_id = $postData['brand_id'];
                            if($brand_id == 'New'){
                                $brand_name = $postData['brand_name'];
                                $brandField = [
                                    'name' => $brand_name
                                ];
                                $brand_id = Brand::insertGetId($brandField);
                            }
                        /* new brand name */

                        $pricing = $this->calculateProductPricing(
                            $postData['cost_price_ex_tax'],
                            $postData['retail_price_inc_tax'],
                            $generalSetting->tax_percent
                        );

                        $fields = [
                            'sku'                       => $postData['sku'],
                            'name'                      => $postData['name'],
                            'receipt_short_name'        => $postData['receipt_short_name'],
                            'shelf_tag_short_name'      => $postData['shelf_tag_short_name'],
                            'barcode'                   => $postData['barcode'],
                            'barcode_image_url'         => $barcode_image_url,
                            'brand_id'                  => $brand_id,
                            'category_id'               => $postData['category_id'],
                            'supplier_sku'              => $postData['supplier_sku'],
                            'supplier_product_name'     => $postData['supplier_product_name'],
                            'supplier_id'               => $postData['supplier_id'],
                            'size_id'                   => $postData['size_id'],
                            'style'                     => $postData['style'],
                            'cost_price_ex_tax'         => $pricing['cost_price_ex_tax'],
                            'cost_price_tax'            => $pricing['cost_price_tax'],
                            'cost_price_inc_tax'        => $pricing['cost_price_inc_tax'],
                            'markup_amount'             => $pricing['margin_percent'],
                            'markup_type'               => 'PERCENTAGE',
                            'added_amount'              => $pricing['margin_amount'],
                            'retail_price_inc_tax'      => $pricing['retail_price_inc_tax'],
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
                        $this->syncProductDiscountOffers($product_id, $postData);
                        /* warehouse stock opening entry */
                            $opening_qty                = 0;
                            $txn_qty                    = $postData['warehouse_stock'];
                            $closing_qty                = ($opening_qty + $txn_qty);
                            $fields11                   = [
                                'txn_type'          => 'IN',
                                'stock_date'        => date('Y-m-d'),
                                'product_id'        => $product_id,
                                'opening_qty'       => $opening_qty,
                                'txn_qty'           => $txn_qty,
                                'closing_qty'       => $closing_qty,
                                'note'              => 'Opening stock',
                            ];
                            WarehouseStock::insert($fields11);
                        /* warehouse stock opening entry */
                        /* shop stock opening entry */
                            $opening_qty                = 0;
                            $txn_qty                    = $postData['shop_stock'];
                            $closing_qty                = ($opening_qty + $txn_qty);
                            $fields11                   = [
                                'txn_type'          => 'IN',
                                'stock_date'        => date('Y-m-d'),
                                'product_id'        => $product_id,
                                'opening_qty'       => $opening_qty,
                                'txn_qty'           => $txn_qty,
                                'closing_qty'       => $closing_qty,
                                'note'              => 'Opening stock',
                            ];
                            ShopStock::insert($fields11);
                        /* shop stock opening entry */
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
            $data['categories']             = ProductCategory::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
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
            $data['categories']             = ProductCategory::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['suppliers']              = Supplier::select('id', 'name')->where('status', '=', 1)->get();
            $data['sizes']                  = DB::table('sizes')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('sizes.*', 'units.name as unit_name')
                                                ->where('sizes.status', '=', 1)
                                                ->orderBy('sizes.id', 'ASC')
                                                ->get();
            if($request->isMethod('post')){
                $postData = $request->all();
                // Helper::pr($postData);
                $rules = [
                    'sku'                       => 'required|alpha_num|min:4|max:10',
                    'barcode'                   => [
                                                        'required',
                                                        'alpha_num',
                                                        'min:8',
                                                        'max:25',
                                                        Rule::unique('products', 'barcode')->ignore($id)->where(function($query) {
                                                            return $query->where('status', '!=', 3);
                                                        }),
                                                    ],
                    'name'                      => 'required',
                    'receipt_short_name'        => 'required',
                    'shelf_tag_short_name'      => 'required',
                    'brand_id'                  => 'required',
                    'category_id'               => [
                                                        'required',
                                                        Rule::exists('product_categories', 'id')->where(function($query) {
                                                            return $query->where('status', '=', 1);
                                                        }),
                                                    ],
                    'supplier_id'               => 'required',
                    'cost_price_ex_tax'         => 'required|numeric|min:0',
                    'retail_price_inc_tax'      => 'required|numeric|min:0',
                ];
                $messages = [
                    'barcode.alpha_num' => 'Barcode must contain only letters and numbers.',
                    'barcode.unique'    => 'Barcode already exists. Please enter a unique barcode.',
                ];
                if($this->validate($request, $rules, $messages)){
                    $offerErrors = $this->validateProductDiscountOfferRows($postData);
                    if(!empty($offerErrors)){
                        return redirect()->back()->withInput()->with('error_message', implode('<br>', $offerErrors));
                    }

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

                        /* new brand name */
                            $brand_id = $postData['brand_id'];
                            if($brand_id == 'New'){
                                $brand_name = $postData['brand_name'];
                                $brandField = [
                                    'name' => $brand_name
                                ];
                                $brand_id = Brand::insertGetId($brandField);
                            }
                        /* new brand name */

                        $pricing = $this->calculateProductPricing(
                            $postData['cost_price_ex_tax'],
                            $postData['retail_price_inc_tax'],
                            $generalSetting->tax_percent
                        );

                        $fields = [
                            'sku'                       => $postData['sku'],
                            'name'                      => $postData['name'],
                            'receipt_short_name'        => $postData['receipt_short_name'],
                            'shelf_tag_short_name'      => $postData['shelf_tag_short_name'],
                            'barcode'                   => $postData['barcode'],
                            'barcode_image_url'         => $barcode_image_url,
                            'brand_id'                  => $brand_id,
                            'category_id'               => $postData['category_id'],
                            'supplier_sku'              => $postData['supplier_sku'],
                            'supplier_product_name'     => $postData['supplier_product_name'],
                            'supplier_id'               => $postData['supplier_id'],
                            'size_id'                   => $postData['size_id'],
                            'style'                     => $postData['style'],
                            'cost_price_ex_tax'         => $pricing['cost_price_ex_tax'],
                            'cost_price_tax'            => $pricing['cost_price_tax'],
                            'cost_price_inc_tax'        => $pricing['cost_price_inc_tax'],
                            'markup_amount'             => $pricing['margin_percent'],
                            'markup_type'               => 'PERCENTAGE',
                            'added_amount'              => $pricing['margin_amount'],
                            'retail_price_inc_tax'      => $pricing['retail_price_inc_tax'],
                            'cover_image'               => $cover_image,
                            'shop_stock'                => $postData['shop_stock'],
                            'warehouse_stock'           => $postData['warehouse_stock'],
                            'status'                    => ((array_key_exists("status",$postData))?1:0),
                        ];

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
                        $this->syncProductDiscountOffers($product_id, $postData);
                        /* warehouse stock opening entry */
                            // $opening_qty                = 0;
                            // $txn_qty                    = $postData['warehouse_stock'];
                            // $closing_qty                = ($opening_qty + $txn_qty);
                            // $fields11                   = [
                            //     'txn_type'          => 'IN',
                            //     'product_id'        => $product_id,
                            //     'opening_qty'       => $opening_qty,
                            //     'txn_qty'           => $txn_qty,
                            //     'closing_qty'       => $closing_qty,
                            //     'note'              => 'Opening stock',
                            // ];
                            // WarehouseStock::insert($fields11);
                        /* warehouse stock opening entry */
                        /* shop stock opening entry */
                            // $opening_qty                = 0;
                            // $txn_qty                    = $postData['shop_stock'];
                            // $closing_qty                = ($opening_qty + $txn_qty);
                            // $fields11                   = [
                            //     'txn_type'          => 'IN',
                            //     'product_id'        => $product_id,
                            //     'opening_qty'       => $opening_qty,
                            //     'txn_qty'           => $txn_qty,
                            //     'closing_qty'       => $closing_qty,
                            //     'note'              => 'Opening stock',
                            // ];
                            // ShopStock::insert($fields11);
                        /* shop stock opening entry */
                    return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Updated Successfully !!!');
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
            if(!$request->isMethod('post')){
                return redirect("admin/" . $this->data['controller_route'] . "/list")->with('error_message', 'Admin password is required to delete '.$this->data['title'].' !!!');
            }

            $rules = [
                'pin1' => ['required', 'regex:/^[0-9]$/'],
                'pin2' => ['required', 'regex:/^[0-9]$/'],
                'pin3' => ['required', 'regex:/^[0-9]$/'],
                'pin4' => ['required', 'regex:/^[0-9]$/'],
            ];
            $messages = [
                'pin1.regex' => 'Admin password must contain numbers only.',
                'pin2.regex' => 'Admin password must contain numbers only.',
                'pin3.regex' => 'Admin password must contain numbers only.',
                'pin4.regex' => 'Admin password must contain numbers only.',
            ];
            if($this->validate($request, $rules, $messages)){
                $adminPassword = $request->pin1.$request->pin2.$request->pin3.$request->pin4;
                $getAdmin = Admin::where('id','=',1)->first();
                if(!$getAdmin || !Hash::check($adminPassword, $getAdmin->password)){
                    return redirect("admin/" . $this->data['controller_route'] . "/list")->with('error_message', 'Admin password does not match !!!');
                }
            }

            $fields = [
                'status'             => 3
            ];
            Product::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Deleted Successfully !!!');
        }
        public function bulkDelete(Request $request){
            $productIds = $this->parseSelectedProductIds($request->product_ids ?? []);
            if(empty($productIds)){
                return redirect("admin/" . $this->data['controller_route'] . "/list")->with('error_message', 'Please select at least one product !!!');
            }

            $rules = [
                'pin1' => ['required', 'regex:/^[0-9]$/'],
                'pin2' => ['required', 'regex:/^[0-9]$/'],
                'pin3' => ['required', 'regex:/^[0-9]$/'],
                'pin4' => ['required', 'regex:/^[0-9]$/'],
            ];
            $messages = [
                'pin1.regex' => 'Admin password must contain numbers only.',
                'pin2.regex' => 'Admin password must contain numbers only.',
                'pin3.regex' => 'Admin password must contain numbers only.',
                'pin4.regex' => 'Admin password must contain numbers only.',
            ];
            if($this->validate($request, $rules, $messages)){
                $adminPassword = $request->pin1.$request->pin2.$request->pin3.$request->pin4;
                $getAdmin = Admin::where('id','=',1)->first();
                if(!$getAdmin || !Hash::check($adminPassword, $getAdmin->password)){
                    return redirect("admin/" . $this->data['controller_route'] . "/list")->with('error_message', 'Admin password does not match !!!');
                }
            }

            Product::whereIn($this->data['primary_key'], $productIds)->update(['status' => 3]);
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', count($productIds).' product(s) deleted successfully !!!');
        }
    /* delete */
    /* transfer selected */
        public function transferSelected(Request $request){
            $data['module']                 = $this->data;
            $title                          = 'Product Stock Transfer';
            $page_name                      = 'product.transfer-selected';
            $productIds                     = $this->parseSelectedProductIds($request->product_ids ?? '');

            if(empty($productIds)){
                return redirect("admin/" . $this->data['controller_route'] . "/list")->with('error_message', 'Please select at least one product to transfer !!!');
            }

            if($request->isMethod('post')){
                $direction      = $request->transfer_direction;
                $stockDate      = (($request->stock_date)?date_format(date_create($request->stock_date), "Y-m-d"):date('Y-m-d'));
                $note           = trim((string)$request->note);
                $qtyByProduct   = $request->transfer_qty ?? [];

                if(!in_array($direction, ['WAREHOUSE_TO_SHOP', 'SHOP_TO_WAREHOUSE'], true)){
                    return redirect()->back()->withInput()->with('error_message', 'Please select a valid transfer direction !!!');
                }

                $products = Product::whereIn('id', $productIds)->where('status', '!=', 3)->get()->keyBy('id');
                $transferRows = [];
                $errors = [];

                foreach($productIds as $productId){
                    if(!isset($products[$productId])){
                        continue;
                    }
                    $qty = (int)($qtyByProduct[$productId] ?? 0);
                    if($qty <= 0){
                        continue;
                    }

                    $product = $products[$productId];
                    if($direction == 'WAREHOUSE_TO_SHOP' && $product->warehouse_stock < $qty){
                        $errors[] = $product->name.' has only '.$product->warehouse_stock.' warehouse stock';
                    }
                    if($direction == 'SHOP_TO_WAREHOUSE' && $product->shop_stock < $qty){
                        $errors[] = $product->name.' has only '.$product->shop_stock.' shop stock';
                    }
                    $transferRows[] = [
                        'product_id' => $productId,
                        'qty'        => $qty,
                    ];
                }

                if(!empty($errors)){
                    return redirect()->back()->withInput()->with('error_message', implode('<br>', $errors));
                }
                if(empty($transferRows)){
                    return redirect()->back()->withInput()->with('error_message', 'Please enter a transfer quantity for at least one selected product !!!');
                }

                DB::transaction(function() use ($transferRows, $direction, $stockDate, $note) {
                    foreach($transferRows as $transferRow){
                        $product = Product::where('id', '=', $transferRow['product_id'])->lockForUpdate()->first();
                        if(!$product){
                            continue;
                        }

                        $qty = $transferRow['qty'];
                        if($direction == 'WAREHOUSE_TO_SHOP'){
                            $warehouseOpening = $product->warehouse_stock;
                            $warehouseClosing = $warehouseOpening - $qty;
                            $warehouseStockId = WarehouseStock::insertGetId([
                                'txn_type'      => 'OUT',
                                'stock_date'    => $stockDate,
                                'product_id'    => $product->id,
                                'opening_qty'   => $warehouseOpening,
                                'txn_qty'       => $qty,
                                'closing_qty'   => $warehouseClosing,
                                'note'          => (($note != '')?$note:'Transfer stock from warehouse to shop'),
                            ]);

                            $shopOpening = $product->shop_stock;
                            $shopClosing = $shopOpening + $qty;
                            ShopStock::insert([
                                'warehouse_stock_id'    => $warehouseStockId,
                                'txn_type'              => 'IN',
                                'stock_date'            => $stockDate,
                                'product_id'            => $product->id,
                                'opening_qty'           => $shopOpening,
                                'txn_qty'               => $qty,
                                'closing_qty'           => $shopClosing,
                                'note'                  => (($note != '')?$note:'Transfer stock from warehouse to shop'),
                            ]);

                            Product::where('id', '=', $product->id)->update([
                                'warehouse_stock' => $warehouseClosing,
                                'shop_stock'      => $shopClosing,
                            ]);
                        } else {
                            $shopOpening = $product->shop_stock;
                            $shopClosing = $shopOpening - $qty;
                            ShopStock::insert([
                                'txn_type'      => 'OUT',
                                'stock_date'    => $stockDate,
                                'product_id'    => $product->id,
                                'opening_qty'   => $shopOpening,
                                'txn_qty'       => $qty,
                                'closing_qty'   => $shopClosing,
                                'note'          => (($note != '')?$note:'Transfer stock from shop to warehouse'),
                            ]);

                            $warehouseOpening = $product->warehouse_stock;
                            $warehouseClosing = $warehouseOpening + $qty;
                            WarehouseStock::insert([
                                'txn_type'      => 'IN',
                                'stock_date'    => $stockDate,
                                'product_id'    => $product->id,
                                'opening_qty'   => $warehouseOpening,
                                'txn_qty'       => $qty,
                                'closing_qty'   => $warehouseClosing,
                                'note'          => (($note != '')?$note:'Transfer stock from shop to warehouse'),
                            ]);

                            Product::where('id', '=', $product->id)->update([
                                'warehouse_stock' => $warehouseClosing,
                                'shop_stock'      => $shopClosing,
                            ]);
                        }
                    }
                });

                return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', 'Stock transfer completed successfully !!!');
            }

            $data['rows']                   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '!=', 3)
                                                ->whereIn('products.id', $productIds)
                                                ->orderBy('products.name', 'ASC')
                                                ->get();

            if(count($data['rows']) <= 0){
                return redirect("admin/" . $this->data['controller_route'] . "/list")->with('error_message', 'Selected products were not found !!!');
            }

            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* transfer selected */
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
        // $getProducts     = Product::select('id', 'barcode')->where('status', '=', 1)->where('barcode', 'LIKE', '%'.$q.'%')->where('barcode', '!=', $barcode)->orderBy('barcode', 'ASC')->get();
        $getProducts     = Product::select('id', 'barcode')->where('status', '=', 1)->where('barcode', 'LIKE', '%'.$q.'%')->orderBy('barcode', 'ASC')->get();
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
    private function validateProductDiscountOfferRows($postData){
        $errors = [];
        if(!array_key_exists('discount_offers', $postData)){
            return $errors;
        }

        $rows = $this->productDiscountOfferRows($postData);
        foreach($rows as $index => $row){
            $label = 'Discount offer '.($index + 1);
            if($row['offer_name'] === ''){
                $errors[] = $label.' offer name is required';
            }
            if($row['discount_amount'] <= 0){
                $errors[] = $label.' discount amount must be greater than zero';
            }
            if($row['product1_min_qty'] <= 0){
                $errors[] = $label.' minimum quantity must be greater than zero';
            }
            if($row['discount_scope'] === ''){
                $errors[] = $label.' discount type is required';
            }
            if($row['barcode_discount_type'] === ''){
                $errors[] = $label.' discount value type is required';
            }
            if($row['offer_start_date'] !== '' && !empty($row['offer_end_date']) && strtotime($row['offer_end_date']) < strtotime($row['offer_start_date'])){
                $errors[] = $label.' end date can not be before start date';
            }
        }

        return $errors;
    }
    private function productDiscountOfferRows($postData){
        $offerNames          = $postData['offer_name'] ?? [];
        $offerDisplayNames  = $postData['offer_display_name'] ?? [];
        $discountScopes     = $postData['discount_scope'] ?? [];
        $startDates         = $postData['offer_start_date'] ?? [];
        $endDates           = $postData['offer_end_date'] ?? [];
        $noExpiry           = $postData['offer_no_expiry'] ?? [];
        $startTimes         = $postData['offer_start_time'] ?? [];
        $endTimes           = $postData['offer_end_time'] ?? [];
        $daysByKey          = $postData['offer_available_days'] ?? [];
        $minQtys            = $postData['offer_min_qty'] ?? [];
        $discountTypes      = $postData['offer_discount_type'] ?? [];
        $discountAmounts    = $postData['offer_discount_amount'] ?? [];
        $statuses           = $postData['offer_status'] ?? [];
        $rows               = [];
        $allowedDays        = ['ALL', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];

        foreach($offerNames as $key => $offerName){
            $offerName          = trim((string)$offerName);
            $displayName        = trim((string)($offerDisplayNames[$key] ?? ''));
            $discountAmount     = (float)($discountAmounts[$key] ?? 0);
            $hasAnyValue        = $offerName !== ''
                                || $displayName !== ''
                                || $discountAmount > 0
                                || trim((string)($startDates[$key] ?? '')) !== ''
                                || trim((string)($endDates[$key] ?? '')) !== '';

            if(!$hasAnyValue){
                continue;
            }

            $scope = strtoupper((string)($discountScopes[$key] ?? 'PRODUCT'));
            if(!in_array($scope, ['PRODUCT', 'BRAND'], true)){
                $scope = '';
            }

            $type = strtoupper((string)($discountTypes[$key] ?? 'FLAT'));
            if(!in_array($type, ['FLAT', 'PERCENTAGE'], true)){
                $type = '';
            }

            $days = $daysByKey[$key] ?? ['ALL'];
            if(!is_array($days)){
                $days = [$days];
            }
            $days = array_values(array_intersect(array_map('strtoupper', $days), $allowedDays));
            if(empty($days) || in_array('ALL', $days, true)){
                $days = ['ALL'];
            }

            $startDate = $this->normalizeOfferDate($startDates[$key] ?? '');
            $endDate   = $this->normalizeOfferDate($endDates[$key] ?? '');
            $noExpiryValue = (array_key_exists($key, $noExpiry) || $endDate === '') ? 1 : 0;

            $rows[] = [
                'offer_name'                => $offerName,
                'offer_display_name'        => (($displayName !== '')?$displayName:$offerName),
                'discount_scope'            => $scope,
                'offer_start_date'          => (($startDate !== '')?$startDate:date('Y-m-d')),
                'offer_end_date'            => (($noExpiryValue)?null:$endDate),
                'offer_no_expiry'           => $noExpiryValue,
                'offer_start_time'          => $this->normalizeOfferTime($startTimes[$key] ?? ''),
                'offer_end_time'            => $this->normalizeOfferTime($endTimes[$key] ?? ''),
                'offer_available_days'      => $days,
                'product1_min_qty'          => max(1, (int)($minQtys[$key] ?? 1)),
                'barcode_discount_type'     => $type,
                'discount_amount'           => round($discountAmount, 2),
                'status'                    => (array_key_exists($key, $statuses)?1:0),
            ];
        }

        return $rows;
    }
    private function normalizeOfferDate($value){
        $value = trim((string)$value);
        if($value === ''){
            return '';
        }

        $timestamp = strtotime($value);
        return (($timestamp === false)?'':date('Y-m-d', $timestamp));
    }
    private function normalizeOfferTime($value){
        $value = trim((string)$value);
        if($value === ''){
            return null;
        }

        $timestamp = strtotime($value);
        return (($timestamp === false)?null:date('H:i:s', $timestamp));
    }
    private function syncProductDiscountOffers($productId, $postData){
        ProductMultipleBuy::where('product_id', '=', $productId)->where('status', '!=', 3)->delete();
        if(!array_key_exists('discount_offers', $postData)){
            return;
        }

        $product = Product::select('barcode', 'retail_price_inc_tax')->where('id', '=', $productId)->first();
        if(!$product){
            return;
        }

        $rows = $this->productDiscountOfferRows($postData);
        foreach($rows as $row){
            $retailPrice = (float)$product->retail_price_inc_tax;
            $discountValue = ($row['barcode_discount_type'] === 'PERCENTAGE')
                                ? (($retailPrice * $row['discount_amount']) / 100)
                                : $row['discount_amount'];
            $discountedAmount = max(0, ($retailPrice - $discountValue));

            ProductMultipleBuy::insert([
                'product_id'                => $productId,
                'offer_name'                => $row['offer_name'],
                'offer_display_name'        => $row['offer_display_name'],
                'discount_scope'            => $row['discount_scope'],
                'first_barcode'             => $product->barcode,
                'product1_min_qty'          => $row['product1_min_qty'],
                'second_barcode'            => null,
                'product2_id'               => 0,
                'product2_min_qty'          => 0,
                'barcode_discount_type'     => $row['barcode_discount_type'],
                'discount_amount'           => $row['discount_amount'],
                'discounted_amount'         => $discountedAmount,
                'offer_start_date'          => $row['offer_start_date'],
                'offer_end_date'            => $row['offer_end_date'],
                'offer_no_expiry'           => $row['offer_no_expiry'],
                'offer_start_time'          => $row['offer_start_time'],
                'offer_end_time'            => $row['offer_end_time'],
                'offer_available_days'      => json_encode($row['offer_available_days']),
                'offer_availability'        => 'ALL',
                'status'                    => $row['status'],
            ]);
        }
    }
    private function isEmptyCsvRow($row){
        foreach($row as $cell){
            if(trim((string)$cell) !== ''){
                return false;
            }
        }
        return true;
    }
    private function generateUniqueCsvSku($reservedSkus = []){
        do {
            $sku = (string) random_int(100000, 999999);
        } while(in_array($sku, $reservedSkus, true) || Product::where('sku', '=', $sku)->exists());

        return $sku;
    }
    private function parseSelectedProductIds($productIds){
        if(is_array($productIds)){
            $productIds = implode(',', $productIds);
        }

        return collect(explode(',', (string)$productIds))
                ->map(function($id){
                    return trim($id);
                })
                ->filter(function($id){
                    return ctype_digit($id);
                })
                ->map(function($id){
                    return (int)$id;
                })
                ->filter(function($id){
                    return $id > 0;
                })
                ->unique()
                ->values()
                ->all();
    }
    private function normalizeCsvBarcode($barcode){
        $barcode = trim((string)$barcode);
        if($barcode === ''){
            return '';
        }

        if(preg_match('/^(\d+)\.0+$/', $barcode, $matches)){
            return $matches[1];
        }

        if(preg_match('/^(\d+)(?:\.(\d+))?[eE]\+?(\d+)$/', $barcode, $matches)){
            $integer        = $matches[1];
            $fraction       = $matches[2] ?? '';
            $exponent       = (int) $matches[3];
            $digits         = $integer.$fraction;
            $decimalPlaces  = strlen($fraction);

            if($exponent >= $decimalPlaces){
                return $digits.str_repeat('0', $exponent - $decimalPlaces);
            }
        }

        return $barcode;
    }
    private function normalizeCsvVolId($volId){
        $volId = trim((string)$volId);
        if(preg_match('/^(\d+)\.0+$/', $volId, $matches)){
            return $matches[1];
        }

        return $volId;
    }
    private function findProductCategoryId($categoryName){
        $categoryName = trim((string)$categoryName);
        if($categoryName === ''){
            return 0;
        }

        $category = ProductCategory::select('id')->where('name', 'LIKE', '%'.$categoryName.'%')->where('status', '=', 1)->first();
        return (($category)?$category->id:0);
    }
    private function calculateProductPricing($costPriceExTax, $retailPriceIncTax, $taxPercent){
        $costPriceExTax     = round((float)$costPriceExTax, 2);
        $retailPriceIncTax  = round((float)$retailPriceIncTax, 2);
        $taxPercent         = round((float)$taxPercent, 2);
        $costPriceIncTax    = round($costPriceExTax + (($costPriceExTax * $taxPercent) / 100), 2);
        $marginAmount       = round($retailPriceIncTax - $costPriceIncTax, 2);
        $marginPercent      = ($retailPriceIncTax > 0)
                                ? round(($marginAmount / $retailPriceIncTax) * 100, 2)
                                : 0.00;

        return [
            'cost_price_ex_tax'     => $costPriceExTax,
            'cost_price_tax'        => $taxPercent,
            'cost_price_inc_tax'    => $costPriceIncTax,
            'retail_price_inc_tax'  => $retailPriceIncTax,
            'margin_amount'         => $marginAmount,
            'margin_percent'        => $marginPercent,
        ];
    }
    private function buildCsvHeaderMap($headers){
        $headerMap = [];
        foreach($headers as $index => $header){
            $header = preg_replace('/^\xEF\xBB\xBF/', '', (string)$header);
            $header = strtolower(trim($header));
            $header = preg_replace('/[^a-z0-9]+/', '_', $header);
            $header = trim($header, '_');
            if($header !== ''){
                $headerMap[$header] = $index;
            }
        }

        return $headerMap;
    }
    private function getCsvValue($row, $headerMap, $headerNames, $fallbackIndex = null){
        foreach($headerNames as $headerName){
            if(array_key_exists($headerName, $headerMap)){
                return trim((string)($row[$headerMap[$headerName]] ?? ''));
            }
        }

        if($fallbackIndex !== null){
            return trim((string)($row[$fallbackIndex] ?? ''));
        }

        return '';
    }
    /* upload products */
        public function uploadProduct(Request $request){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Upload List';
            $page_name                      = 'product.upload-product';
            $data['rows']                   = UploadProduct::where('status', '=', 1)->orderBy('id', 'DESC')->get();
            $generalSetting                 = GeneralSetting::find('1');
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'title'                 => 'required',
                    'filename'              => 'required'
                ];
                if($this->validate($request, $rules)){
                    /* upload product csv file */
                        $imageFile      = $request->file('filename');
                        if($imageFile != ''){
                            $imageName      = $imageFile->getClientOriginalName();
                            $uploadedFile   = $this->upload_single_file('filename', $imageName, 'product', 'csv');
                            if($uploadedFile['status']){
                                $filename       = $uploadedFile['newFilename'];
                                $file_path      = './public/uploads/product/'.$filename;
                                $csvWarnings    = [];
                                $enteredCsvSkus = [];
                                $validVolIds    = Size::where('status', '=', 1)->pluck('id')->map(function($id){
                                                    return (string)$id;
                                                })->all();

                                if (($handle = fopen($file_path, 'r')) !== FALSE) {
                                    $counter = 0;
                                    $csvHeaderMap = [];
                                    while (($csvRow = fgetcsv($handle, 1000, ',')) !== FALSE) {
                                        $counter++;
                                        if($counter == 1){
                                            $csvHeaderMap = $this->buildCsvHeaderMap($csvRow);
                                            continue;
                                        }
                                        if($this->isEmptyCsvRow($csvRow)){
                                            continue;
                                        }

                                        $enteredSku = $this->getCsvValue($csvRow, $csvHeaderMap, ['sku'], 0);
                                        if($enteredSku !== ''){
                                            $enteredCsvSkus[] = $enteredSku;
                                        }

                                        $productName = $this->getCsvValue($csvRow, $csvHeaderMap, ['name', 'product_name'], 1);
                                        $volId = $this->normalizeCsvVolId($this->getCsvValue($csvRow, $csvHeaderMap, ['vol_id', 'volume_id'], 7));
                                        $costPrice = $this->getCsvValue($csvRow, $csvHeaderMap, ['cost_price', 'cost_price_ex_gst', 'cost_price_ex_tax'], 9);
                                        $retailPrice = $this->getCsvValue($csvRow, $csvHeaderMap, ['retail_inc_gst', 'retail_price_inc_gst', 'retail_price_inc_tax'], 15);
                                        $missingFields = [];
                                        if($productName === ''){
                                            $missingFields[] = 'Product name';
                                        }
                                        if($volId === ''){
                                            $missingFields[] = 'Vol_id';
                                        }

                                        if(!empty($missingFields)){
                                            $csvWarnings[] = 'Row '.$counter.': '.implode(', ', $missingFields).' is empty';
                                        } elseif(!ctype_digit($volId) || !in_array($volId, $validVolIds, true)){
                                            $csvWarnings[] = 'Row '.$counter.': Vol_id "'.htmlspecialchars($volId, ENT_QUOTES, 'UTF-8').'" is not in the volume list';
                                        }
                                        if($costPrice !== '' && (!is_numeric($costPrice) || (float)$costPrice < 0)){
                                            $csvWarnings[] = 'Row '.$counter.': Cost price must be a positive number or zero';
                                        }
                                        if($retailPrice !== '' && (!is_numeric($retailPrice) || (float)$retailPrice < 0)){
                                            $csvWarnings[] = 'Row '.$counter.': Retail price incl. GST must be a positive number or zero';
                                        }
                                    }
                                    fclose($handle);
                                } else {
                                    if(File::exists($file_path)){
                                        File::delete($file_path);
                                    }
                                    return redirect()->back()->with(['error_message' => 'Error opening the file !!!']);
                                }

                                if(!empty($csvWarnings)){
                                    if(File::exists($file_path)){
                                        File::delete($file_path);
                                    }
                                    return redirect()->back()->with([
                                        'error_message' => '<strong>CSV import warning:</strong> Please fix these cells before importing.<br>'.implode('<br>', $csvWarnings),
                                    ]);
                                }

                                $sessionData    = Auth::guard('admin')->user();
                                $fields0         = [
                                    'title'                 => $postData['title'],
                                    'filename'              => $filename,
                                    'created_by'            => $sessionData->id,
                                    'updated_by'            => $sessionData->id,
                                ];
                                $upload_id = UploadProduct::insertGetId($fields0);
                                /* extract data from file & insert into three category tables */
                                    $generatedSkus = $enteredCsvSkus;
                                    // Open the CSV file for reading
                                    if (($handle = fopen($file_path, 'r')) !== FALSE) {
                                        // Loop through each line in the file
                                        $counter = 0;
                                        $csvHeaderMap = [];
                                        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                                            if($counter === 0){
                                                $csvHeaderMap = $this->buildCsvHeaderMap($data);
                                                $counter++;
                                                continue;
                                            }
                                            if(!$this->isEmptyCsvRow($data)){
                                                $sku                        = $this->getCsvValue($data, $csvHeaderMap, ['sku'], 0);
                                                $name                       = $this->getCsvValue($data, $csvHeaderMap, ['name', 'product_name'], 1);
                                                $receipt_short_name         = $this->getCsvValue($data, $csvHeaderMap, ['receipt_short_name'], 2);
                                                $shelf_tag_short_name       = $this->getCsvValue($data, $csvHeaderMap, ['shelf_tag_short_name'], 3);
                                                $barcode                    = $this->normalizeCsvBarcode($this->getCsvValue($data, $csvHeaderMap, ['barcode'], 4));
                                                $brand                      = $this->getCsvValue($data, $csvHeaderMap, ['brand', 'brand_id'], 5);
                                                $supplier                   = $this->getCsvValue($data, $csvHeaderMap, ['supplier', 'supplier_id'], 6);
                                                $category                   = $this->getCsvValue($data, $csvHeaderMap, ['category', 'product_category']);
                                                $volId                      = $this->normalizeCsvVolId($this->getCsvValue($data, $csvHeaderMap, ['vol_id', 'volume_id'], 7));
                                                $style                      = $this->getCsvValue($data, $csvHeaderMap, ['style'], 8);
                                                $cost_price_ex_tax          = $this->getCsvValue($data, $csvHeaderMap, ['cost_price', 'cost_price_ex_gst', 'cost_price_ex_tax'], 9);
                                                $retail_price_inc_tax       = $this->getCsvValue($data, $csvHeaderMap, ['retail_inc_gst', 'retail_price_inc_gst', 'retail_price_inc_tax'], 15);
                                                $shop_stock                 = $this->getCsvValue($data, $csvHeaderMap, ['shop_stock'], 16);
                                                $warehouse_stock            = $this->getCsvValue($data, $csvHeaderMap, ['warehouse_stock'], 17);

                                                if($sku == ''){
                                                    $sku = $this->generateUniqueCsvSku($generatedSkus);
                                                    $generatedSkus[] = $sku;
                                                }

                                                $checkProduct               = Product::where('sku', '=', $sku)->first();
                                                if(empty($checkProduct)){
                                                    $getBrandId                 = Brand::select('id')->where('name', 'LIKE', '%'.$brand.'%')->first();
                                                    $getSupplierId              = Supplier::select('id')->where('name', 'LIKE', '%'.$supplier.'%')->first();
                                                    $categoryId                 = $this->findProductCategoryId($category);
                                                    $pricing                    = $this->calculateProductPricing(
                                                        ($cost_price_ex_tax !== '') ? $cost_price_ex_tax : 0,
                                                        ($retail_price_inc_tax !== '') ? $retail_price_inc_tax : 0,
                                                        $generalSetting->tax_percent
                                                    );

                                                    $fields = [
                                                        'sku'                       => $sku,
                                                        'name'                      => $name,
                                                        'receipt_short_name'        => $receipt_short_name,
                                                        'shelf_tag_short_name'      => $shelf_tag_short_name,
                                                        'barcode'                   => $barcode,
                                                        'brand_id'                  => (($getBrandId)?$getBrandId->id:0),
                                                        'category_id'               => $categoryId,
                                                        'supplier_id'               => (($getSupplierId)?$getSupplierId->id:0),
                                                        'size_id'                   => (int)$volId,
                                                        'style'                     => $style,
                                                        'cost_price_ex_tax'         => $pricing['cost_price_ex_tax'],
                                                        'cost_price_tax'            => $pricing['cost_price_tax'],
                                                        'cost_price_inc_tax'        => $pricing['cost_price_inc_tax'],
                                                        'markup_amount'             => $pricing['margin_percent'],
                                                        'markup_type'               => 'PERCENTAGE',
                                                        'added_amount'              => $pricing['margin_amount'],
                                                        'retail_price_inc_tax'      => $pricing['retail_price_inc_tax'],
                                                        'upload_id'                 => $upload_id,
                                                        'shop_stock'                => $shop_stock,
                                                        'warehouse_stock'           => $warehouse_stock,
                                                    ];
                                                    // Helper::pr($fields,0);
                                                    $product_id = Product::insertGetId($fields);

                                                    // warehouse stock
                                                        $opening_qty                = 0;
                                                        $txn_qty                    = $warehouse_stock;
                                                        $closing_qty                = ($opening_qty + $txn_qty);
                                                        $fields11                   = [
                                                            'txn_type'          => 'IN',
                                                            'stock_date'        => date("Y-m-d"),
                                                            'product_id'        => $product_id,
                                                            'opening_qty'       => $opening_qty,
                                                            'txn_qty'           => $txn_qty,
                                                            'closing_qty'       => $closing_qty,
                                                            'note'              => 'Opening stock',
                                                        ];
                                                        WarehouseStock::insert($fields11);
                                                    // warehouse stock

                                                    // shop stock
                                                        $opening_qty                = 0;
                                                        $txn_qty                    = $shop_stock;
                                                        $closing_qty                = ($opening_qty + $txn_qty);
                                                        $fields12                   = [
                                                            'txn_type'          => 'IN',
                                                            'stock_date'        => date("Y-m-d"),
                                                            'product_id'        => $product_id,
                                                            'opening_qty'       => $opening_qty,
                                                            'txn_qty'           => $txn_qty,
                                                            'closing_qty'       => $closing_qty,
                                                            'note'              => 'Opening stock',
                                                        ];
                                                        ShopStock::insert($fields12);
                                                    // shop stock
                                                } else  {
                                                    $fields                 = [];
                                                    $fields['sku']          = $sku;
                                                    $fields['status']       = 1;
                                                    $fields['upload_id']    = $upload_id;

                                                    if($name != ''){
                                                        $fields['name']   = $name;
                                                    }

                                                    if($receipt_short_name != ''){
                                                        $fields['receipt_short_name']   = $receipt_short_name;
                                                    }

                                                    if($shelf_tag_short_name != ''){
                                                        $fields['shelf_tag_short_name']   = $shelf_tag_short_name;
                                                    }

                                                    if($barcode != ''){
                                                        $fields['barcode']   = $barcode;
                                                    }

                                                    if($brand != ''){
                                                        $getBrandId                 = Brand::select('id')->where('name', 'LIKE', '%'.$brand.'%')->first();
                                                        $fields['brand_id']         = (($getBrandId)?$getBrandId->id:0);
                                                    }

                                                    if($supplier != ''){
                                                        $getSupplierId              = Supplier::select('id')->where('name', 'LIKE', '%'.$supplier.'%')->first();
                                                        $fields['supplier_id']      = (($getSupplierId)?$getSupplierId->id:0);
                                                    }

                                                    if($category != ''){
                                                        $fields['category_id']      = $this->findProductCategoryId($category);
                                                    }

                                                    if($volId != ''){
                                                        $fields['size_id']          = (int)$volId;
                                                    }

                                                    if($style != ''){
                                                        $fields['style']   = $style;
                                                    }

                                                    if($cost_price_ex_tax !== '' || $retail_price_inc_tax !== ''){
                                                        $pricing = $this->calculateProductPricing(
                                                            ($cost_price_ex_tax !== '') ? $cost_price_ex_tax : $checkProduct->cost_price_ex_tax,
                                                            ($retail_price_inc_tax !== '') ? $retail_price_inc_tax : $checkProduct->retail_price_inc_tax,
                                                            $generalSetting->tax_percent
                                                        );
                                                        $fields['cost_price_ex_tax']     = $pricing['cost_price_ex_tax'];
                                                        $fields['cost_price_tax']        = $pricing['cost_price_tax'];
                                                        $fields['cost_price_inc_tax']    = $pricing['cost_price_inc_tax'];
                                                        $fields['markup_amount']         = $pricing['margin_percent'];
                                                        $fields['markup_type']           = 'PERCENTAGE';
                                                        $fields['added_amount']          = $pricing['margin_amount'];
                                                        $fields['retail_price_inc_tax']  = $pricing['retail_price_inc_tax'];
                                                    }

                                                    $product_id = $checkProduct->id;
                                                    if($shop_stock != ''){
                                                        // shop stock
                                                            $opening_qty                = $checkProduct->shop_stock;
                                                            $txn_qty                    = $shop_stock;
                                                            $closing_qty                = ($opening_qty + $txn_qty);
                                                            $fields12                   = [
                                                                'txn_type'          => 'IN',
                                                                'stock_date'        => date("Y-m-d"),
                                                                'product_id'        => $product_id,
                                                                'opening_qty'       => $opening_qty,
                                                                'txn_qty'           => $txn_qty,
                                                                'closing_qty'       => $closing_qty,
                                                                'note'              => 'Opening stock',
                                                            ];
                                                            ShopStock::insert($fields12);
                                                        // shop stock

                                                        $fields['shop_stock']   = $closing_qty;
                                                    }
                                                    
                                                    if($warehouse_stock != ''){
                                                        // warehouse stock
                                                            $opening_qty                = $checkProduct->warehouse_stock;
                                                            $txn_qty                    = $warehouse_stock;
                                                            $closing_qty                = ($opening_qty + $txn_qty);
                                                            $fields11                   = [
                                                                'txn_type'          => 'IN',
                                                                'stock_date'        => date("Y-m-d"),
                                                                'product_id'        => $product_id,
                                                                'opening_qty'       => $opening_qty,
                                                                'txn_qty'           => $txn_qty,
                                                                'closing_qty'       => $closing_qty,
                                                                'note'              => 'Opening stock',
                                                            ];
                                                            WarehouseStock::insert($fields11);
                                                        // warehouse stock

                                                        $fields['warehouse_stock']   = $closing_qty;
                                                    }

                                                    // Helper::pr($fields,0);
                                                    Product::where('id', '=', $checkProduct->id)->update($fields);
                                                }
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
                    /* upload product csv file */
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
                                                // ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'sizes.name as size_name', 'units.name as unit_name')
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
                                                // ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'sizes.name as size_name', 'units.name as unit_name')
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
                                                // ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'sizes.name as size_name', 'units.name as unit_name')
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
                                                // ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'sizes.name as size_name', 'units.name as unit_name')
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
                                                // ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'sizes.name as size_name', 'units.name as unit_name')
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
                                                // ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'sizes.name as size_name', 'units.name as unit_name')
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
                                                // ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->leftjoin('product_discount_vouchers', 'products.id', '=', 'product_discount_vouchers.product_id')
                                                ->select('products.*', 'brands.name as brand_name', 'sizes.name as size_name', 'units.name as unit_name')
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
                        $getProduct = Product::select('name', 'retail_price_inc_tax', 'shelf_tag_short_name')->where('id', '=', $product_id[$p])->first();
                        $products[] = [
                            'name'  => (($getProduct)?$getProduct->shelf_tag_short_name:''),
                            'price' => (($getProduct)?$getProduct->retail_price_inc_tax:0),
                        ];
                        $discountVouchers = ProductDiscountVoucher::select('voucher_code', 'retail_discounted_price')->where('product_id', $product_id[$p])->where('status', 1)->get();
                        if($discountVouchers){ foreach($discountVouchers as $discountVoucher){
                            $products[] = [
                                // 'name'  => (($getProduct)?$getProduct->name.' [<small style="font-size: 8px;">'.$discountVoucher->voucher_code.'</small>]':''),
                                'name'  => (($getProduct)?$getProduct->shelf_tag_short_name:''),
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
                $completePin    = $pin1.$pin2.$pin3.$pin4;
                $getAdmin       = Admin::where('id','=',1)->first();
                if(Hash::check($completePin, $getAdmin->password)){
                    $redirectUrl = url('admin/products/list/');
                    // $redirectUrl = url('admin/products/edit/'.Helper::encoded($product_id));
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
    public function updateDiscountVoucherStatus(Request $request)
    {
        $id = $request->id;
        $status = $request->status;

        // Example: Update in DB
        $updated = ProductDiscountVoucher::where('id', $id)
                    ->update(['status' => $status]);

        return response()->json([
            'success' => $updated,
            'message' => 'Discount voucher status updated successfully'
        ]);
    }
    public function updateMultiBuyStatus(Request $request)
    {
        $id = $request->id;
        $status = $request->status;

        // Example: Update in DB
        $updated = ProductMultipleBuy::where('id', $id)
                    ->update(['status' => $status]);

        return response()->json([
            'success' => $updated,
            'message' => 'Discount offer status updated successfully'
        ]);
    }
}
