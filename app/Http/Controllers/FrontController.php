<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Services\OpenAiAuth;
use Illuminate\Http\Request;
use PHPExperts\RESTSpeaker\RESTSpeaker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Models\GeneralSetting;
use App\Models\Admin;
use App\Models\EmailLog;
use App\Models\Enquiry;
use App\Models\UserActivity;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Models\UserSubscription;

use Auth;
use Mail;
use App\Mail\ForgotPwdMail;
use Dompdf\Dompdf;
use PDF;
use Session;
use Helper;
use Hash;

class FrontController extends Controller
{
    /* authentication */
        public function login(Request $request){
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                            'username'  => 'required|max:15',
                            'otp1'      => 'required|max:1',
                            'otp2'      => 'required|max:1',
                            'otp3'      => 'required|max:1',
                            'otp4'      => 'required|max:1',
                        ];
                if($this->validate($request, $rules)){
                    $otp1           = $postData['otp1'];
                    $otp2           = $postData['otp2'];
                    $otp3           = $postData['otp3'];
                    $otp4           = $postData['otp4'];
                    $password       = ($otp1.$otp2.$otp3.$otp4);
                    if(Auth::guard('admin')->attempt(['username' => $postData['username'], 'password' => $password, 'status' => 1, 'type' => 'SO'])){
                        // Helper::pr(Auth::guard('admin')->user());
                        $sessionData = Auth::guard('admin')->user();
                        $request->session()->put('user_id', $sessionData->id);
                        $request->session()->put('name', $sessionData->name);
                        $request->session()->put('type', $sessionData->type);
                        $request->session()->put('email', $sessionData->email);
                        $request->session()->put('username', $sessionData->username);
                        $request->session()->put('is_user_login', 1);

                        /* user activity */
                            $activityData = [
                                'user_email'        => $sessionData->email,
                                'user_name'         => $sessionData->name,
                                'user_type'         => 'USER',
                                'ip_address'        => $request->ip(),
                                'activity_type'     => 1,
                                'activity_details'  => 'SignIn Success !!!',
                                'platform_type'     => 'WEB',
                            ];
                            UserActivity::insert($activityData);
                        /* user activity */
                        // Helper::pr($request->session());
                        return redirect('user/take-order');
                    } else {
                        /* user activity */
                            $activityData = [
                                'user_email'        => 'Sale Operator',
                                'user_name'         => 'Master Admin',
                                'user_type'         => 'USER',
                                'ip_address'        => $request->ip(),
                                'activity_type'     => 0,
                                'activity_details'  => 'Invalid Username Or PIN !!!',
                                'platform_type'     => 'WEB',
                            ];
                            UserActivity::insert($activityData);
                        /* user activity */
                        return redirect()->back()->with('error_message', 'Invalid Username Or PIN !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $data                           = [];
            $title                          = 'Sign In';
            $page_name                      = 'signin';
            echo $this->user_before_login_layout($title,$page_name,$data);
        }
        public function logout(Request $request){
            $user_email                             = $request->session()->get('email');
            $user_name                              = $request->session()->get('name');
            /* user activity */
                $activityData = [
                    'user_email'        => $user_email,
                    'user_name'         => $user_name,
                    'user_type'         => 'USER',
                    'ip_address'        => $request->ip(),
                    'activity_type'     => 2,
                    'activity_details'  => 'You Are Successfully Logged Out !!!',
                    'platform_type'     => 'WEB',
                ];
                UserActivity::insert($activityData);
            /* user activity */
            $request->session()->forget(['user_id', 'name', 'email']);
            // Helper::pr(session()->all());die;
            Auth::guard('admin')->logout();
            return redirect(url('/'))->with('success_message', 'You Are Successfully Logged Out !!!');
        }
    /* authentication */
    /* dashboard */
        public function dashboard(){
            $data                           = [];
            $title                          = 'Dashboard';
            $page_name                      = 'dashboard';
            echo $this->user_after_login_layout($title,$page_name,$data);
        }
    /* dashboard */
    /* take orders */
        public function takeOrder(){
            $data                           = [];
            $title                          = 'Take Order';
            $page_name                      = 'take-order';
            echo $this->user_after_login_layout($title,$page_name,$data);
        }
    /* take orders */
}
