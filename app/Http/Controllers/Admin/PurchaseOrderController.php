<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Brand;
use App\Models\Country;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\DeliveryLocation;
use App\Models\WarehouseStock;

use Auth;
use Session;
use Helper;
use Hash;
use Dompdf\Dompdf;
use Dompdf\Options;
class PurchaseOrderController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Purchase Order',
            'controller'        => 'PurchaseOrderController',
            'controller_route'  => 'purchase-orders',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'purchase-order.list';
            $data['rows']                   = PurchaseOrder::where('status', '!=', 3)->orderBy('id', 'DESC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $data['module']           = $this->data;
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'order_date'                        => 'required',
                    'delivery_id'                       => 'required',
                    'supplier_id'                       => 'required',
                    's_street_address1'                 => 'required',
                    's_street_address2'                 => 'required',
                    's_city'                            => 'required',
                    's_state'                           => 'required',
                    's_postcode'                        => 'required',
                    's_country'                         => 'required',
                ];
                if($this->validate($request, $rules)){
                    /* purchase order no generate */
                        $getLastOrder = PurchaseOrder::orderBy('id', 'DESC')->first();                       
                        if($getLastOrder){
                            $sl_no              = $getLastOrder->sl_no;
                            $next_sl_no         = $sl_no + 1;
                            $next_sl_no_string  = str_pad($next_sl_no, 5, 0, STR_PAD_LEFT);
                            $po_no              = 'PO' . $next_sl_no_string;
                        } else {
                            $next_sl_no         = 1;
                            $next_sl_no_string  = str_pad($next_sl_no, 5, 0, STR_PAD_LEFT);
                            $po_no              = 'PO' . $next_sl_no_string;
                        }
                    /* purchase order no generate */

                    $getSupplier              = Supplier::select('id', 'name', 'phone', 'b_street_address1', 'b_street_address2', 'b_city', 'b_state', 'b_postcode', 'b_country')->where('id', '=', $postData['supplier_id'])->first();
                    $getDeliveryLocation      = DeliveryLocation::select('id', 'name', 'address', 'phone')->where('id', '=', $postData['delivery_id'])->first();

                    $fields = [
                        'sl_no'                         => $next_sl_no,
                        'po_no'                         => $po_no,
                        'supplier_id'                   => $postData['supplier_id'],
                        'supplier_name'                 => (($getSupplier)?$getSupplier->name:''),
                        'supplier_phone'                => (($getSupplier)?$getSupplier->phone:''),
                        'supplier_address'              => (($getSupplier)?$getSupplier->b_street_address1 . ' ' . $getSupplier->b_street_address2 . ' ' . $getSupplier->b_city . ' ' . $getSupplier->b_state . ' ' . $getSupplier->b_postcode . ' ' . $getSupplier->b_country:''),
                        's_street_address1'             => $postData['s_street_address1'],
                        's_street_address2'             => $postData['s_street_address2'],
                        's_city'                        => $postData['s_city'],
                        's_state'                       => $postData['s_state'],
                        's_postcode'                    => $postData['s_postcode'],
                        's_country'                     => $postData['s_country'],
                        'delivery_id'                   => $postData['delivery_id'],
                        'delivery_name'                 => (($getDeliveryLocation)?$getDeliveryLocation->name:''),
                        'delivery_phone'                => (($getDeliveryLocation)?$getDeliveryLocation->phone:''),
                        'delivery_address'              => (($getDeliveryLocation)?$getDeliveryLocation->address:''),
                        'order_date'                    => $postData['order_date'],
                        'order_time'                    => date('Y-m-d'),
                        'status'                        => $postData['status'],
                    ];
                    // Helper::pr($fields);
                    $purchase_order_id = PurchaseOrder::insertGetId($fields);
                    return redirect("admin/" . $this->data['controller_route'] . "/edit/" . Helper::encoded($purchase_order_id))->with('success_message', '');
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'purchase-order.add-edit';
            $data['row']                    = [];
            $data['suppliers']              = Supplier::select('id', 'name', 'supplier_code', 'phone')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['deliveryLocations']      = DeliveryLocation::select('id', 'name', 'address', 'phone')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['items']                  = Product::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['couns']                  = Country::select('country', 'currency_name', 'currency_code')->where('status', '=', 1)->orderBy('country', 'ASC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'purchase-order.add-edit';
            $data['id']                     = $id;
            $data['row']                    = PurchaseOrder::where($this->data['primary_key'], '=', $id)->first();
            $data['suppliers']              = Supplier::select('id', 'name', 'supplier_code', 'phone')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['deliveryLocations']      = DeliveryLocation::select('id', 'name', 'address', 'phone')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $supplier_id                    = $data['row']->supplier_id;
            $data['items']                  = Product::select('id', 'name')->where('status', '=', 1)->where('supplier_id', '=', $supplier_id)->orderBy('name', 'ASC')->get();
            $data['couns']                  = Country::select('country', 'currency_name', 'currency_code')->where('status', '=', 1)->orderBy('country', 'ASC')->get();

            if($request->isMethod('post')){
                $postData = $request->all();
                // Helper::pr($postData);
                $rules = [
                    'order_date'                        => 'required',
                    'delivery_id'                       => 'required',
                    'supplier_id'                       => 'required',
                    'total_lines'                       => 'required',
                    'total_quantity'                    => 'required',
                    'subtotal'                          => 'required',
                    'tax_total'                         => 'required',
                    'total_inc_tax'                     => 'required',
                ];
                if($this->validate($request, $rules)){
                    $getSupplier              = Supplier::select('id', 'name', 'phone', 'b_street_address1', 'b_street_address2', 'b_city', 'b_state', 'b_postcode', 'b_country')->where('id', '=', $postData['supplier_id'])->first();
                    $getDeliveryLocation      = DeliveryLocation::select('id', 'name', 'address', 'phone')->where('id', '=', $postData['delivery_id'])->first();

                    $fields = [
                        'supplier_id'                   => $postData['supplier_id'],
                        'supplier_name'                 => (($getSupplier)?$getSupplier->name:''),
                        'supplier_phone'                => (($getSupplier)?$getSupplier->phone:''),
                        'supplier_address'              => (($getSupplier)?$getSupplier->b_street_address1 . ' ' . $getSupplier->b_street_address2 . ' ' . $getSupplier->b_city . ' ' . $getSupplier->b_state . ' ' . $getSupplier->b_postcode . ' ' . $getSupplier->b_country:''),
                        's_street_address1'             => $postData['s_street_address1'],
                        's_street_address2'             => $postData['s_street_address2'],
                        's_city'                        => $postData['s_city'],
                        's_state'                       => $postData['s_state'],
                        's_postcode'                    => $postData['s_postcode'],
                        's_country'                     => $postData['s_country'],
                        'delivery_id'                   => $postData['delivery_id'],
                        'delivery_name'                 => (($getDeliveryLocation)?$getDeliveryLocation->name:''),
                        'delivery_phone'                => (($getDeliveryLocation)?$getDeliveryLocation->phone:''),
                        'delivery_address'              => (($getDeliveryLocation)?$getDeliveryLocation->address:''),
                        'order_date'                    => $postData['order_date'],
                        'order_time'                    => date('Y-m-d'),
                        'status'                        => $postData['status'],
                        'total_lines'                   => $postData['total_lines'],
                        'total_quantity'                => $postData['total_quantity'],
                        'subtotal'                      => $postData['subtotal'],
                        'tax_total'                     => $postData['tax_total'],
                        'total_inc_tax'                 => $postData['total_inc_tax_val'],
                        'note'                          => $postData['note'],
                    ];
                    // Helper::pr($fields);
                    PurchaseOrder::where('id', '=', $id)->update($fields);
                    $purchase_order_id = $id;

                    $item_id                = $postData['item_id'];
                    $supplier_sku           = $postData['supplier_sku'];
                    $merchant_sku           = $postData['merchant_sku'];
                    $item_name              = $postData['item_name'];
                    $qty                    = $postData['qty'];
                    $cost_price             = $postData['cost_price'];
                    $tax_percent            = $postData['tax_percent'];
                    $tax_amount             = $postData['tax_amount'];
                    $total_inc_tax          = $postData['total_inc_tax'];

                    PurchaseOrderItem::where('purchase_order_id', '=', $id)->delete();
                    WarehouseStock::where('po_id', '=', $id)->delete();
                    if(count($item_id) > 0){
                        for($k=0;$k<count($item_id);$k++){
                            $fields2 = [
                                'purchase_order_id'         => $purchase_order_id,
                                'item_id'                   => $item_id[$k],
                                'supplier_sku'              => $supplier_sku[$k],
                                'merchant_sku'              => $merchant_sku[$k],
                                'item_name'                 => $item_name[$k],
                                'qty'                       => $qty[$k],
                                'cost_price'                => $cost_price[$k],
                                'tax_percent'               => $tax_percent[$k],
                                'tax_amount'                => $tax_amount[$k],
                                'total_inc_tax'             => $total_inc_tax[$k],
                            ];
                            // Helper::pr($fields2,0);
                            PurchaseOrderItem::insert($fields2);

                            /* insert into warehouse stock */
                                $checkProduct               = Product::where('id', '=', $item_id[$k])->first();
                                $opening_qty                = $checkProduct->warehouse_stock;
                                $txn_qty                    = $qty[$k];
                                $closing_qty                = ($opening_qty + $txn_qty);
                                $fields11                   = [
                                    'txn_type'          => 'IN',
                                    'stock_date'        => date("Y-m-d"),
                                    'product_id'        => $item_id[$k],
                                    'opening_qty'       => $opening_qty,
                                    'txn_qty'           => $txn_qty,
                                    'closing_qty'       => $closing_qty,
                                    'note'              => 'Opening stock',
                                    'po_id'             => $purchase_order_id,
                                ];
                                WarehouseStock::insert($fields11);
                                Product::where('id', '=', $checkProduct->id)->update(['warehouse_stock' => $closing_qty]);
                            /* insert into warehouse stock */
                        }
                    }
                    
                    /* invoice pdf generate */
                        $data['poData']                 = PurchaseOrder::where('id', '=', $id)->first();
                        $data['id']                     = $id;
                        $po_no                          = (($data['poData'])?$data['poData']->po_no:'');
                        $generalSetting                 = GeneralSetting::find('1');
                        $subject                        = $po_no;
                        $message                        = view('admin.maincontents.purchase-order.pdf-po', $data);                        
                        // echo $message;die;
                        $options        = new Options();
                        $options->set('defaultFont', 'Courier');
                        $dompdf         = new Dompdf($options);
                        $html           = $message;
                        $dompdf->loadHtml($html);
                        $dompdf->setPaper('A4', 'portrait');
                        $dompdf->render();
                        $output         = $dompdf->output();
                        // $dompdf->stream("document.pdf", array("Attachment" => false));die;
                        $filename       = $po_no.'.pdf';
                        $pdfFilePath    = 'public/uploads/purchase-order/' . $filename;
                        file_put_contents($pdfFilePath, $output);
                        PurchaseOrder::where('id', '=', $purchase_order_id)->update(['invoice_file' => $filename]);
                    /* invoice pdf generate */                    

                    return redirect("admin/" . $this->data['controller_route'] . "/list/")->with('success_message', 'Puchase order created successfully !!!');
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* edit */
    public function getItemInfo(Request $request)
    {
        $item = Product::find($request->item_id);

        if (!$item) {
            return response()->json([], 404);
        }

        $taxAmount = ($item->cost_price_ex_tax * $item->cost_price_tax) / 100;
        $totalIncTax = $item->cost_price_ex_tax + $taxAmount;

        return response()->json([
            'supplier_sku'   => (($item->supplier_sku != '')?$item->supplier_sku:$item->sku),
            'merchant_sku'   => $item->sku,
            'name'           => (($item->supplier_product_name != '')?$item->supplier_product_name:$item->name),
            'cost_price'     => $item->cost_price_ex_tax,
            'tax_percent'    => $item->cost_price_tax,
            'tax_amount'     => $taxAmount,
            'total_inc_tax'  => $totalIncTax,
        ]);
    }
}
