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
use App\Models\ShopStock;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
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
                    if(!$this->itemsBelongToSupplier($postData['item_id'] ?? [], $postData['supplier_id'])){
                        return redirect()->back()->withInput()->with('error_message', 'Only products from the selected supplier can be added to this purchase order !!!');
                    }

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
                        'total_lines'                   => ($postData['total_lines'] ?? 0),
                        'total_quantity'                => ($postData['total_quantity'] ?? 0),
                        'subtotal'                      => ($postData['subtotal'] ?? 0),
                        'tax_total'                     => ($postData['tax_total'] ?? 0),
                        'total_inc_tax'                 => ($postData['total_inc_tax_val'] ?? 0),
                        'note'                          => ($postData['note'] ?? ''),
                        'status'                        => $postData['status'],
                    ];
                    // Helper::pr($fields);
                    $purchase_order_id = PurchaseOrder::insertGetId($fields);
                    if(isset($postData['item_id']) && is_array($postData['item_id']) && count($postData['item_id']) > 0){
                        $this->insertPurchaseOrderItems($purchase_order_id, $postData);
                        $this->generatePurchaseOrderPdf($purchase_order_id);
                        return redirect("admin/" . $this->data['controller_route'] . "/list/")->with('success_message', 'Puchase order created successfully !!!');
                    }
                    return redirect("admin/" . $this->data['controller_route'] . "/edit/" . Helper::encoded($purchase_order_id))->with('success_message', '');
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $selectedSupplierId            = $this->parseSupplierId($request->query('supplier_id', ''));
            $prefillProductIds             = $this->parseSelectedProductIds($request->query('product_ids', ''));
            $prefillItems                  = collect();
            $prefillSupplierId             = '';
            if(!empty($prefillProductIds)){
                $prefillItems = Product::whereIn('id', $prefillProductIds)->where('status', '=', 1)->get();
                $supplierIds = $prefillItems->pluck('supplier_id')->filter()->unique()->values();
                if(count($prefillItems) <= 0){
                    return redirect("admin/products/list")->with('error_message', 'Selected products were not found !!!');
                }
                if(count($supplierIds) != 1){
                    return redirect("admin/products/list")->with('error_message', 'Please select products from one supplier only !!!');
                }
                $prefillSupplierId = $supplierIds[0];
                $selectedSupplierId = $prefillSupplierId;
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'purchase-order.add-edit';
            $data['row']                    = [];
            $data['suppliers']              = Supplier::select('id', 'name', 'supplier_code', 'phone')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['deliveryLocations']      = DeliveryLocation::select('id', 'name', 'address', 'phone')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['items']                  = collect();
            if($selectedSupplierId != ''){
                $data['items']              = $this->supplierItems($selectedSupplierId);
            }
            $data['supplierProducts']       = collect();
            if($selectedSupplierId != '' && count($prefillItems) <= 0){
                $data['supplierProducts']   = Product::select(
                                                    'id',
                                                    'name',
                                                    'sku',
                                                    'barcode',
                                                    'supplier_sku',
                                                    'supplier_product_name',
                                                    'cost_price_ex_tax',
                                                    'cost_price_tax',
                                                    'shop_stock',
                                                    'warehouse_stock'
                                                )
                                                ->where('status', '=', 1)
                                                ->where('supplier_id', '=', $selectedSupplierId)
                                                ->orderBy('name', 'ASC')
                                                ->get();
            }
            $data['couns']                  = Country::select('country', 'currency_name', 'currency_code')->where('status', '=', 1)->orderBy('country', 'ASC')->get();
            $data['prefillItems']           = $prefillItems;
            $data['prefillSupplierId']      = $prefillSupplierId;
            $data['selectedSupplierId']     = $selectedSupplierId;
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
            $data['items']                  = $this->supplierItems($supplier_id);
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
                    if(!$this->itemsBelongToSupplier($postData['item_id'] ?? [], $postData['supplier_id'])){
                        return redirect()->back()->withInput()->with('error_message', 'Only products from the selected supplier can be added to this purchase order !!!');
                    }

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

                    PurchaseOrderItem::where('purchase_order_id', '=', $id)->delete();
                    if(isset($postData['item_id']) && is_array($postData['item_id']) && count($postData['item_id']) > 0){
                        $this->insertPurchaseOrderItems($purchase_order_id, $postData);
                    }
                    
                    $this->generatePurchaseOrderPdf($purchase_order_id);

                    return redirect("admin/" . $this->data['controller_route'] . "/list/")->with('success_message', 'Puchase order saved successfully !!!');
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* edit */
    public function getSupplierItems(Request $request)
    {
        $supplierId = $this->parseSupplierId($request->query('supplier_id', ''));

        if($supplierId === ''){
            return response()->json([]);
        }

        return response()->json($this->supplierItems($supplierId));
    }
    public function getItemInfo(Request $request)
    {
        $supplierId = $this->parseSupplierId($request->query('supplier_id', ''));
        if($supplierId === ''){
            return response()->json([], 422);
        }

        $item = Product::where('id', '=', $request->item_id)
                        ->where('supplier_id', '=', $supplierId)
                        ->where('status', '=', 1)
                        ->first();

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
    public function receive(Request $request, $id){
        $data['module']                 = $this->data;
        $id                             = Helper::decoded($id);
        $data['row']                    = PurchaseOrder::where($this->data['primary_key'], '=', $id)->first();
        if(!$data['row']){
            return redirect("admin/" . $this->data['controller_route'] . "/list/")->with('error_message', 'Purchase order not found !!!');
        }

        $stockAlreadyReceived = $this->purchaseOrderHasReceivedStock($data['row']);
        if($request->isMethod('post')){
            if($stockAlreadyReceived){
                return redirect("admin/" . $this->data['controller_route'] . "/list/")->with('error_message', 'Stock has already been received for this purchase order !!!');
            }

            $postData       = $request->all();
            $itemIds        = $postData['item_id'] ?? [];
            $qtys           = $postData['qty'] ?? [];
            $costPrices     = $postData['cost_price'] ?? [];
            $shopQtys       = $postData['shop_qty'] ?? [];
            $warehouseQtys  = $postData['warehouse_qty'] ?? [];
            $deliveryCost   = (float)($postData['delivery_cost'] ?? 0);
            $receiveDate    = (($request->receive_date)?date_format(date_create($request->receive_date), "Y-m-d"):date('Y-m-d'));
            $errors         = [];
            $receiveRows    = [];
            $totalQty       = 0;

            if($deliveryCost < 0){
                $errors[] = 'Delivery cost can not be negative';
            }

            $poItems = PurchaseOrderItem::where('purchase_order_id', '=', $id)->get()->keyBy('id');
            foreach($poItems as $poItem){
                $poItemId       = $poItem->id;
                $productId      = (int)($itemIds[$poItemId] ?? $poItem->item_id);
                $qty            = (int)($qtys[$poItemId] ?? 0);
                $costPrice      = (float)($costPrices[$poItemId] ?? 0);
                $shopQty        = (int)($shopQtys[$poItemId] ?? 0);
                $warehouseQty   = (int)($warehouseQtys[$poItemId] ?? 0);

                if(!Product::where('id', '=', $productId)->where('status', '!=', 3)->exists()){
                    $errors[] = $poItem->item_name.' product was not found';
                }
                if($qty <= 0){
                    $errors[] = $poItem->item_name.' quantity must be greater than zero';
                }
                if($costPrice < 0){
                    $errors[] = $poItem->item_name.' cost price can not be negative';
                }
                if($shopQty < 0 || $warehouseQty < 0){
                    $errors[] = $poItem->item_name.' shop and warehouse quantities can not be negative';
                }
                if(($shopQty + $warehouseQty) != $qty){
                    $errors[] = $poItem->item_name.' shop + warehouse quantity must equal PO quantity';
                }

                $receiveRows[] = [
                    'po_item'       => $poItem,
                    'product_id'    => $productId,
                    'qty'           => $qty,
                    'cost_price'    => $costPrice,
                    'shop_qty'      => $shopQty,
                    'warehouse_qty' => $warehouseQty,
                ];
                $totalQty += $qty;
            }

            if($totalQty <= 0){
                $errors[] = 'Total received quantity must be greater than zero';
            }
            if(!empty($errors)){
                return redirect()->back()->withInput()->with('error_message', implode('<br>', $errors));
            }

            $deliveryCostPerItem = ($deliveryCost / $totalQty);
            DB::transaction(function() use ($id, $data, $receiveRows, $deliveryCost, $deliveryCostPerItem, $receiveDate) {
                $subtotal = 0;
                $taxTotal = 0;
                $totalQty = 0;
                $totalLines = 0;

                foreach($receiveRows as $receiveRow){
                    $product = Product::where('id', '=', $receiveRow['product_id'])->lockForUpdate()->first();
                    if(!$product){
                        continue;
                    }

                    $poItem         = $receiveRow['po_item'];
                    $qty            = $receiveRow['qty'];
                    $costPrice      = $receiveRow['cost_price'];
                    $shopQty        = $receiveRow['shop_qty'];
                    $warehouseQty   = $receiveRow['warehouse_qty'];
                    $taxPercent     = (float)$poItem->tax_percent;
                    $rowSubtotal    = ($qty * $costPrice);
                    $rowTax         = (($rowSubtotal * $taxPercent) / 100);
                    $rowTotal       = ($rowSubtotal + $rowTax);
                    $landedCost     = ($costPrice + $deliveryCostPerItem);
                    $landedTax      = (($landedCost * $taxPercent) / 100);
                    $landedIncTax   = ($landedCost + $landedTax);
                    $note           = 'Received from PO '.$data['row']->po_no;

                    PurchaseOrderItem::where('id', '=', $poItem->id)->update([
                        'item_id'       => $product->id,
                        'supplier_sku'  => (($product->supplier_sku != '')?$product->supplier_sku:$product->sku),
                        'merchant_sku'  => $product->sku,
                        'item_name'     => (($product->supplier_product_name != '')?$product->supplier_product_name:$product->name),
                        'qty'           => $qty,
                        'cost_price'    => $costPrice,
                        'tax_percent'   => $taxPercent,
                        'tax_amount'    => $rowTax,
                        'total_inc_tax' => $rowTotal,
                    ]);

                    $warehouseStockId = 0;
                    if($warehouseQty > 0){
                        $warehouseOpening = $product->warehouse_stock;
                        $warehouseClosing = ($warehouseOpening + $warehouseQty);
                        $warehouseStockId = WarehouseStock::insertGetId([
                            'txn_type'      => 'IN',
                            'stock_date'    => $receiveDate,
                            'product_id'    => $product->id,
                            'opening_qty'   => $warehouseOpening,
                            'txn_qty'       => $warehouseQty,
                            'closing_qty'   => $warehouseClosing,
                            'note'          => $note,
                            'po_id'         => $id,
                        ]);
                        $product->warehouse_stock = $warehouseClosing;
                    }

                    if($shopQty > 0){
                        $shopOpening = $product->shop_stock;
                        $shopClosing = ($shopOpening + $shopQty);
                        ShopStock::insert([
                            'warehouse_stock_id'    => $warehouseStockId,
                            'txn_type'              => 'IN',
                            'stock_date'            => $receiveDate,
                            'product_id'            => $product->id,
                            'opening_qty'           => $shopOpening,
                            'txn_qty'               => $shopQty,
                            'closing_qty'           => $shopClosing,
                            'note'                  => $note,
                        ]);
                        $product->shop_stock = $shopClosing;
                    }

                    $product->cost_price_ex_tax     = $landedCost;
                    $product->cost_price_tax        = $taxPercent;
                    $product->cost_price_inc_tax    = $landedIncTax;
                    $product->save();

                    $totalLines++;
                    $totalQty += $qty;
                    $subtotal += $rowSubtotal;
                    $taxTotal += $rowTax;
                }

                $note = trim((string)$data['row']->note);
                $deliveryNote = 'Delivery cost: $'.number_format($deliveryCost, 2, '.', '');
                $note = (($note != '')?$note."\n":'').$deliveryNote;
                PurchaseOrder::where('id', '=', $id)->update([
                    'total_lines'       => $totalLines,
                    'total_quantity'    => $totalQty,
                    'subtotal'          => $subtotal,
                    'tax_total'         => $taxTotal,
                    'total_inc_tax'     => ($subtotal + $taxTotal),
                    'note'              => $note,
                ]);
            });

            $this->generatePurchaseOrderPdf($id);
            return redirect("admin/" . $this->data['controller_route'] . "/list/")->with('success_message', 'Purchase order stock received successfully !!!');
        }

        $title                          = 'Receive Goods : ' . $data['row']->po_no;
        $page_name                      = 'purchase-order.receive';
        $data['poItems']                = PurchaseOrderItem::where('purchase_order_id', '=', $id)->orderBy('id', 'ASC')->get();
        $data['products']               = Product::whereIn('id', $data['poItems']->pluck('item_id')->all())->get()->keyBy('id');
        $data['stockAlreadyReceived']   = $stockAlreadyReceived;
        echo $this->admin_after_login_layout($title,$page_name,$data);
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
    private function parseSupplierId($supplierId){
        $supplierId = trim((string)$supplierId);
        if($supplierId === '' || !ctype_digit($supplierId)){
            return '';
        }

        $supplierId = (int)$supplierId;
        if($supplierId <= 0){
            return '';
        }

        return Supplier::where('id', '=', $supplierId)->where('status', '=', 1)->exists() ? $supplierId : '';
    }
    private function supplierItems($supplierId){
        return Product::select('id', 'name')
                        ->where('status', '=', 1)
                        ->where('supplier_id', '=', $supplierId)
                        ->orderBy('name', 'ASC')
                        ->get();
    }
    private function itemsBelongToSupplier($itemIds, $supplierId){
        if(!is_array($itemIds) || count($itemIds) === 0){
            return true;
        }

        $validItemIds = collect($itemIds)->map(function($itemId){
            $itemId = trim((string)$itemId);
            return ctype_digit($itemId) && (int)$itemId > 0 ? (int)$itemId : null;
        });

        if($validItemIds->contains(null)){
            return false;
        }

        $validItemIds = $validItemIds->unique()->values();
        return Product::whereIn('id', $validItemIds)
                        ->where('supplier_id', '=', $supplierId)
                        ->where('status', '=', 1)
                        ->count() === $validItemIds->count();
    }
    private function insertPurchaseOrderItems($purchase_order_id, $postData){
        $item_id                = $postData['item_id'];
        $supplier_sku           = $postData['supplier_sku'];
        $merchant_sku           = $postData['merchant_sku'];
        $item_name              = $postData['item_name'];
        $qty                    = $postData['qty'];
        $cost_price             = $postData['cost_price'];
        $tax_percent            = $postData['tax_percent'];
        $tax_amount             = $postData['tax_amount'];
        $total_inc_tax          = $postData['total_inc_tax'];

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
            PurchaseOrderItem::insert($fields2);
        }
    }
    private function purchaseOrderHasReceivedStock($purchaseOrder){
        return WarehouseStock::where('po_id', '=', $purchaseOrder->id)->exists()
            || ShopStock::where('note', 'LIKE', '%Received from PO '.$purchaseOrder->po_no.'%')->exists();
    }
    private function generatePurchaseOrderPdf($id){
        $data['poData']                 = PurchaseOrder::where('id', '=', $id)->first();
        $data['id']                     = $id;
        $po_no                          = (($data['poData'])?$data['poData']->po_no:'');
        $message                        = view('admin.maincontents.purchase-order.pdf-po', $data);
        $options                        = new Options();
        $options->set('defaultFont', 'Courier');
        $dompdf                         = new Dompdf($options);
        $dompdf->loadHtml($message);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output                         = $dompdf->output();
        $filename                       = $po_no.'.pdf';
        $pdfFilePath                    = 'public/uploads/purchase-order/' . $filename;
        file_put_contents($pdfFilePath, $output);
        PurchaseOrder::where('id', '=', $id)->update(['invoice_file' => $filename]);
    }
}
