<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Product;
use App\Models\Unit;
use App\Models\WarehouseStock;
use App\Models\ShopStock;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
class StockController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Stock',
            'controller'        => 'StockController',
            'controller_route'  => 'stock',
            'primary_key'       => 'id',
        );
    }
    /* warehouse stock */
        public function warehouseStock(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'stock.warehouse-stock-list';
            $data['rows']                   = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*', 'brands.name as brand_name', 'suppliers.name as supplier_name', 'sizes.name as size_name', 'units.name as unit_name')
                                                ->where('products.status', '!=', 3)
                                                ->orderBy('products.id', 'DESC')
                                                ->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function manageWarehouseStock(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            if($requestData['key'] == env('PROJECT_KEY')){
                $postData       = $request->all();
                $product_id     = $postData['product_id'];
                $txn_type       = $postData['txn_type'];
                $stock_date     = $postData['stock_date'];
                $txn_qty        = (int)$postData['txn_qty'];
                $note           = trim((string)$postData['note']);
                $getProduct     = Product::where('id', $product_id)->first();
                if($getProduct){
                    if($txn_type == 'IN'){
                        $opening_qty                = (($getProduct)?$getProduct->warehouse_stock:0);
                        $txn_qty                    = $txn_qty;
                        $closing_qty                = ($opening_qty + $txn_qty);
                        $fields11                   = [
                            'txn_type'          => 'IN',
                            'stock_date'        => date_format(date_create($stock_date), "Y-m-d"),
                            'product_id'        => $product_id,
                            'opening_qty'       => $opening_qty,
                            'txn_qty'           => $txn_qty,
                            'closing_qty'       => $closing_qty,
                            'note'              => $note,
                        ];
                        WarehouseStock::insert($fields11);
                        Product::where('id', $product_id)->update(['warehouse_stock' => $closing_qty]);

                        $apiStatus                          = TRUE;
                        http_response_code(200);
                        $apiResponse                        = [
                            'closing_qty'            => $closing_qty,
                            'warehouse_closing_qty'  => $closing_qty,
                            'shop_closing_qty'       => $getProduct->shop_stock,
                        ];
                        $apiMessage                         = $getProduct->name . ' Stock IN successfully';
                        $apiExtraField                      = 'response_code';
                        $apiExtraData                       = http_response_code();
                    } elseif($txn_type == 'SHOP_TO_WAREHOUSE'){
                        if($txn_qty <= 0){
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'Please enter valid return stock quantity';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } elseif($note == ''){
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'Please enter return note';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } elseif($getProduct->shop_stock < $txn_qty){
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'You have only '.$getProduct->shop_stock.' shop stock. Can\'t return more than '.$getProduct->shop_stock.'';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            try {
                                DB::transaction(function() use ($product_id, $stock_date, $txn_qty, $note, &$apiResponse) {
                                    $product = Product::where('id', $product_id)->lockForUpdate()->first();
                                    if(!$product){
                                        throw new \Exception('Product not found');
                                    }
                                    if($product->shop_stock < $txn_qty){
                                        throw new \Exception('You have only '.$product->shop_stock.' shop stock. Can\'t return more than '.$product->shop_stock.'');
                                    }

                                    $stockDate = date_format(date_create($stock_date), "Y-m-d");

                                    $shopOpening = (int)$product->shop_stock;
                                    $shopClosing = ($shopOpening - $txn_qty);
                                    $shopStockId = ShopStock::insertGetId([
                                        'txn_type'      => 'OUT',
                                        'stock_date'    => $stockDate,
                                        'product_id'    => $product_id,
                                        'opening_qty'   => $shopOpening,
                                        'txn_qty'       => $txn_qty,
                                        'closing_qty'   => $shopClosing,
                                        'note'          => $note,
                                    ]);

                                    $warehouseOpening = (int)$product->warehouse_stock;
                                    $warehouseClosing = ($warehouseOpening + $txn_qty);
                                    WarehouseStock::insert([
                                        'txn_type'      => 'IN',
                                        'stock_date'    => $stockDate,
                                        'product_id'    => $product_id,
                                        'opening_qty'   => $warehouseOpening,
                                        'txn_qty'       => $txn_qty,
                                        'closing_qty'   => $warehouseClosing,
                                        'note'          => $note,
                                    ]);

                                    Product::where('id', $product_id)->update([
                                        'warehouse_stock' => $warehouseClosing,
                                        'shop_stock'      => $shopClosing,
                                    ]);

                                    $apiResponse = [
                                        'closing_qty'            => $warehouseClosing,
                                        'warehouse_closing_qty'  => $warehouseClosing,
                                        'shop_closing_qty'       => $shopClosing,
                                        'shop_stock_id'          => $shopStockId,
                                    ];
                                });

                                $apiStatus                          = TRUE;
                                http_response_code(200);
                                $apiMessage                         = $getProduct->name . ' returned to warehouse successfully';
                                $apiExtraField                      = 'response_code';
                                $apiExtraData                       = http_response_code();
                            } catch(\Exception $e) {
                                $apiStatus          = FALSE;
                                http_response_code(200);
                                $apiMessage         = $e->getMessage();
                                $apiExtraField      = 'response_code';
                                $apiExtraData       = http_response_code();
                            }
                        }
                    } else {
                        if($getProduct->warehouse_stock < $txn_qty){
                            $apiStatus          = FALSE;
                            http_response_code(200);
                            $apiMessage         = 'You have only '.$getProduct->warehouse_stock.' stock. Can\'t stock OUT more than '.$getProduct->warehouse_stock.'';
                            $apiExtraField      = 'response_code';
                            $apiExtraData       = http_response_code();
                        } else {
                            $opening_qty                = (($getProduct)?$getProduct->warehouse_stock:0);
                            $txn_qty                    = $txn_qty;
                            $closing_qty                = ($opening_qty - $txn_qty);
                            $fields11                   = [
                                'txn_type'          => 'OUT',
                                'stock_date'        => date_format(date_create($stock_date), "Y-m-d"),
                                'product_id'        => $product_id,
                                'opening_qty'       => $opening_qty,
                                'txn_qty'           => $txn_qty,
                                'closing_qty'       => $closing_qty,
                                'note'              => $note,
                            ];
                            $warehouse_stock_id = WarehouseStock::insertGetId($fields11);
                            Product::where('id', $product_id)->update(['warehouse_stock' => $closing_qty]);

                            /* shop stock opening entry */
                                $opening_qty2                = (($getProduct)?$getProduct->shop_stock:0);
                                $txn_qty2                    = $txn_qty;
                                $closing_qty2                = ($opening_qty2 + $txn_qty2);
                                $fields12                   = [
                                    'warehouse_stock_id'    => $warehouse_stock_id,
                                    'txn_type'              => 'IN',
                                    'stock_date'            => date('Y-m-d'),
                                    'product_id'            => $product_id,
                                    'opening_qty'           => $opening_qty2,
                                    'txn_qty'               => $txn_qty2,
                                    'closing_qty'           => $closing_qty2,
                                    'note'                  => (($note)?$note:'Transfer stock from warehouse'),
                                ];
                                ShopStock::insert($fields12);
                                Product::where('id', $product_id)->update(['shop_stock' => $closing_qty2]);
                            /* shop stock opening entry */

                            $apiStatus                          = TRUE;
                            http_response_code(200);
                            $apiResponse                        = [
                                'closing_qty'            => $closing_qty,
                                'warehouse_closing_qty'  => $closing_qty,
                                'shop_closing_qty'       => $closing_qty2,
                            ];
                            $apiMessage                         = $getProduct->name . ' Stock OUT successfully';
                            $apiExtraField                      = 'response_code';
                            $apiExtraData                       = http_response_code();
                        }
                    }
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(200);
                    $apiMessage         = 'Product not found';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function warehouseStockHistory($id){
            $id                             = Helper::decoded($id);
            $data['module']                 = $this->data;
            $page_name                      = 'stock.warehouse-stock-history';
            $data['product']                = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*')
                                                ->where('products.id', '=', $id)
                                                ->first();

            $data['stocks']                 = WarehouseStock::select('txn_type', 'opening_qty', 'txn_qty', 'closing_qty', 'note', 'stock_date', 'created_at')->where('status', 1)->where('product_id', $id)->orderBy('id', 'DESC')->get();
            $title                          = 'Warehouse ' . $this->data['title'].' IN/OUT History : ' . (($data['product'])?$data['product']->name . ' (' . $data['product']->sku . ')':'');
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* warehouse stock */
    /* shop stock */
        public function shopStock(){
            return redirect('admin/stock/warehouse-stock');
        }
        public function shopStockHistory($id){
            $id                             = Helper::decoded($id);
            $data['module']                 = $this->data;
            $page_name                      = 'stock.shop-stock-history';
            $data['product']                = DB::table('products')
                                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                                ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
                                                ->join('sizes', 'products.size_id', '=', 'sizes.id')
                                                ->join('units', 'sizes.unit_id', '=', 'units.id')
                                                ->select('products.*')
                                                ->where('products.id', '=', $id)
                                                ->first();

            $data['stocks']                 = ShopStock::select('txn_type', 'opening_qty', 'txn_qty', 'closing_qty', 'note', 'stock_date', 'created_at')->where('status', 1)->where('product_id', $id)->orderBy('id', 'DESC')->get();
            $title                          = 'Shop ' . $this->data['title'].' IN/OUT History : ' . (($data['product'])?$data['product']->name . ' (' . $data['product']->sku . ')':'');
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* shop stock */
}
