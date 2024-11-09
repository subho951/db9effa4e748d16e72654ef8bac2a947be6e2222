<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Consignment;
use App\Models\ConsignmentDetail;
use App\Models\Customer;
use App\Models\Pol;
use App\Models\Pod;
use App\Models\ProcessFlow;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
class ConsignmentController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Consignment',
            'controller'        => 'ConsignmentController',
            'controller_route'  => 'consignment',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'consignment.list';
            $data['rows']                   = DB::table('consignments')
                                                ->join('customers', 'consignments.customer_id', '=', 'customers.id')
                                                ->leftjoin('pols', 'consignments.pol', '=', 'pols.id')
                                                ->leftjoin('pods', 'consignments.pod', '=', 'pods.id')
                                                ->select('consignments.*', 'customers.name as customer_name', 'pols.name as pol_name', 'pods.name as pod_name')
                                                ->where('consignments.status', '!=', 3)
                                                ->orderBy('consignments.id', 'DESC')
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
                    'shipment_type'             => 'required',
                    'customer_id'               => 'required',
                    'pol'                       => 'required',
                    'pod'                       => 'required',
                    'booking_date'              => 'required',
                ];
                if($this->validate($request, $rules)){
                    /* generate consignment no */
                        if($postData['shipment_type'] == 'Import'){
                            $shipmentTypeShort = 'IMP';
                        } else {
                            $shipmentTypeShort = 'EXP';
                        }
                        $getLastEnquiry = Consignment::orderBy('id', 'DESC')->first();
                        if($getLastEnquiry){
                            $sl_no              = $getLastEnquiry->sl_no;
                            $next_sl_no         = $sl_no + 1;
                            $next_sl_no_string  = str_pad($next_sl_no, 7, 0, STR_PAD_LEFT);
                            $consignment_no     = 'MACOLINE-'.$shipmentTypeShort.'-'.$next_sl_no_string;
                        } else {
                            $next_sl_no         = 1;
                            $next_sl_no_string  = str_pad($next_sl_no, 7, 0, STR_PAD_LEFT);
                            $consignment_no     = 'MACOLINE-'.$shipmentTypeShort.'-'.$next_sl_no_string;
                        }
                    /* generate consignment no */
                    $fields = [
                        'sl_no'                             => $next_sl_no,
                        'consignment_no'                    => $consignment_no,
                        'shipment_type'                     => $postData['shipment_type'],
                        'type'                              => ((array_key_exists("type",$postData))?$postData['type']:''),
                        'customer_id'                       => $postData['customer_id'],
                        'pol'                               => $postData['pol'],
                        'pod'                               => $postData['pod'],
                        'booking_date'                      => date_format(date_create($postData['booking_date']), "Y-m-d"),
                    ];
                    Consignment::insert($fields);
                    /* consignment process flow */
                        $process_flow_id    = $postData['process_flow_id'];
                        $date_nums          = $postData['date_nums'];
                        if(!empty($process_flow_id)){
                            for($k=0;$k<count($process_flow_id);$k++){
                                $booking_date       = date_format(date_create($postData['booking_date']), "Y-m-d");
                                $notification_date  = date('Y-m-d',strtotime('+'.$date_nums[$k].' day', strtotime($booking_date)));
                                $fields2            = [
                                    'consignment_id'                        => $id,
                                    'process_flow_id'                       => $process_flow_id[$k],
                                    'date_nums'                             => $date_nums[$k],
                                    'booking_date'                          => date_format(date_create($postData['booking_date']), "Y-m-d"),
                                    'notification_date'                     => $notification_date,
                                ];
                                ConsignmentDetail::insert($fields2);
                            }
                        }
                    /* consignment process flow */
                    return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Inserted Successfully !!!');
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'consignment.add-edit';
            $data['row']                    = [];
            $data['customers']              = Customer::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['pols']                   = Pol::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['pods']                   = Pod::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'consignment.add-edit';
            $data['row']                    = Consignment::where($this->data['primary_key'], '=', $id)->first();
            $data['customers']              = Customer::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['pols']                   = Pol::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['pods']                   = Pod::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            if($request->isMethod('post')){
                $postData = $request->all();
                // Helper::pr($postData);
                $rules = [
                    'shipment_type'             => 'required',
                    'customer_id'               => 'required',
                    'pol'                       => 'required',
                    'pod'                       => 'required',
                    'booking_date'              => 'required',
                ];
                if($this->validate($request, $rules)){
                    $fields = [
                        'shipment_type'                     => $postData['shipment_type'],
                        'type'                              => ((array_key_exists("type",$postData))?$postData['type']:''),
                        'customer_id'                       => $postData['customer_id'],
                        'pol'                               => $postData['pol'],
                        'pod'                               => $postData['pod'],
                        'booking_date'                      => date_format(date_create($postData['booking_date']), "Y-m-d"),
                    ];
                    // Helper::pr($fields);
                    Consignment::where($this->data['primary_key'], '=', $id)->update($fields);
                    
                    /* consignment process flow */
                        $process_flow_id    = $postData['process_flow_id'];
                        $date_nums          = $postData['date_nums'];
                        if(!empty($process_flow_id)){
                            for($k=0;$k<count($process_flow_id);$k++){
                                $booking_date       = date_format(date_create($postData['booking_date']), "Y-m-d");
                                $notification_date  = date('Y-m-d',strtotime('+'.$date_nums[$k].' day', strtotime($booking_date)));

                                $getConsignmentDetail = ConsignmentDetail::where('consignment_id', '=', $id)->where('process_flow_id', '=', $process_flow_id[$k])->first();
                                if($getConsignmentDetail){
                                    $fields2            = [
                                        'date_nums'                             => $date_nums[$k],
                                        'booking_date'                          => date_format(date_create($postData['booking_date']), "Y-m-d"),
                                        'notification_date'                     => $notification_date,
                                    ];
                                    ConsignmentDetail::where('consignment_id', '=', $id)->where('process_flow_id', '=', $process_flow_id[$k])->update($fields2);
                                } else {
                                    $fields2            = [
                                        'consignment_id'                        => $id,
                                        'process_flow_id'                       => $process_flow_id[$k],
                                        'date_nums'                             => $date_nums[$k],
                                        'booking_date'                          => date_format(date_create($postData['booking_date']), "Y-m-d"),
                                        'notification_date'                     => $notification_date,
                                    ];
                                    ConsignmentDetail::insert($fields2);
                                }
                            }
                        }
                    /* consignment process flow */
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
            $fields = [
                'status'             => 3
            ];
            Consignment::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect("admin/" . $this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Deleted Successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = Consignment::find($id);
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
    public function getProcessFlow(Request $request){
        $apiStatus          = TRUE;
        $apiMessage         = '';
        $apiResponse        = [];
        $apiExtraField      = '';
        $apiExtraData       = '';
        $postData           = $request->all();
        $shipment_type      = $postData['shipment_type'];
        
        if($shipment_type == 'Import'){
            $process_flows      = ProcessFlow::select('id', 'name', 'options', 'notification_after_booking_date')->where('status', '=', 1)->where('shipment_type', '=', $shipment_type)->orderBy('id', 'ASC')->get();
        } else {
            $type               = $postData['selectedValue2'];
            $process_flows      = ProcessFlow::select('id', 'name', 'options', 'notification_after_booking_date')->where('status', '=', 1)->where('shipment_type', '=', $shipment_type)->where('type', '=', $type)->orderBy('id', 'ASC')->get();
        }
        // Helper::pr($process_flows);
        $html               = '';
        if(count($process_flows) > 0){
            foreach($process_flows as $process_flow){
                // $option_html = '';
                // if($process_flow->options != ''){
                //     $deal_keywords = explode(",", $process_flow->options);
                //     if(!empty($deal_keywords)){
                //         for($k=0;$k<count($deal_keywords);$k++){
                //             $option_html = '<span class="badge">'.$deal_keywords[$k].' <span class="remove" data-tag="'.$deal_keywords[$k].'">&times;</span></span>';
                //     }   }
                // }
                      
                $html .= '<tr>
                              <td><input type="hidden" name="process_flow_id[]" value="' . $process_flow->id . '">' . $process_flow->name . '</td>
                              <td>' . $process_flow->options . '</td>
                              <td><input type="hidden" name="date_nums[]" value="' . $process_flow->notification_after_booking_date . '">' . $process_flow->notification_after_booking_date . ' days</td>
                            </tr>';
            }
            $apiResponse['processDateHTML']        = $html;
            $apiStatus          = TRUE;
        } else {
            $apiResponse['processDateHTML']        = '';
            $apiStatus          = FALSE;
        }

        http_response_code(200);
        $apiMessage         = 'Data Available !!!';
        $apiExtraField      = 'response_code';
        $apiExtraData       = http_response_code();
        $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
    }
    public function process_flow_details(Request $request, $id){
        $data['module']                 = $this->data;
        $id                             = Helper::decoded($id);
        $title                          = $this->data['title'].' Details';
        $page_name                      = 'consignment.process-flow-details';
        $data['row']                    = DB::table('consignments')
                                                ->join('customers', 'consignments.customer_id', '=', 'customers.id')
                                                ->leftjoin('pols', 'consignments.pol', '=', 'pols.id')
                                                ->leftjoin('pods', 'consignments.pod', '=', 'pods.id')
                                                ->select('consignments.*', 'customers.name as customer_name', 'pols.name as pol_name', 'pods.name as pod_name')
                                                ->where('consignments.id', '=', $id)
                                                ->first();
        $data['consignmentDetails']     = ConsignmentDetail::where('consignment_id', '=', $id)->orderBy('process_flow_id', 'ASC')->get();
        if($request->isMethod('post')){
            $postData = $request->all();
            // Helper::pr($postData,0);
            $consignment_id     = $postData['consignment_id'];
            $process_flow_id    = $postData['process_flow_id'];
            $input_value        = $postData['input_value'];

            if(!empty($process_flow_id)){
                for($k=0;$k<count($process_flow_id);$k++){
                    if(isset($input_value[$process_flow_id[$k]])){
                        $fields             = [
                            // 'process_flow_id'   => $process_flow_id[$k],
                            'input_value'   => $input_value[$process_flow_id[$k]],
                            'status'        => 1,
                        ];
                        // Helper::pr($fields,0);
                        ConsignmentDetail::where('consignment_id', '=', $id)->where('process_flow_id', '=', $process_flow_id[$k])->update($fields);
                    }
                }
            }
            return redirect()->back()->with('success_message', 'Process Data Updated !!!');
        }
        echo $this->admin_after_login_layout($title,$page_name,$data);
    }
}
