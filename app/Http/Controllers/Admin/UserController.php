<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

class UserController extends Controller
{
    /* authentication */
        public function login(Request $request){
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                            'email'     => 'required|email|max:255',
                            'password'  => 'required|max:30',
                        ];
                if($this->validate($request, $rules)){
                    if(Auth::guard('admin')->attempt(['email' => $postData['email'], 'password' => $postData['password'], 'status' => 1])){
                        // Helper::pr(Auth::guard('admin')->user());put
                        $sessionData = Auth::guard('admin')->user();
                        $request->session()->put('user_id', $sessionData->id);
                        $request->session()->put('name', $sessionData->name);
                        $request->session()->put('type', $sessionData->type);
                        $request->session()->put('email', $sessionData->email);
                        $request->session()->put('is_admin_login', 1);

                        /* user activity */
                            $activityData = [
                                'user_email'        => $sessionData->email,
                                'user_name'         => $sessionData->name,
                                'user_type'         => 'ADMIN',
                                'ip_address'        => $request->ip(),
                                'activity_type'     => 1,
                                'activity_details'  => 'Login Success !!!',
                                'platform_type'     => 'WEB',
                            ];
                            UserActivity::insert($activityData);
                        /* user activity */
                        // Helper::pr($request->session());
                        return redirect('admin/dashboard');
                    } else {
                        /* user activity */
                            $activityData = [
                                'user_email'        => $postData['email'],
                                'user_name'         => 'Super Admin',
                                'user_type'         => 'ADMIN',
                                'ip_address'        => $request->ip(),
                                'activity_type'     => 0,
                                'activity_details'  => 'Invalid Email Or Password !!!',
                                'platform_type'     => 'WEB',
                            ];
                            UserActivity::insert($activityData);
                        /* user activity */
                        return redirect()->back()->with('error_message', 'Invalid Email Or Password !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $data                           = [];
            $title                          = 'Sign In';
            $page_name                      = 'signin';
            echo $this->admin_before_login_layout($title,$page_name,$data);
        }
        public function forgotPassword(Request $request){
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                            'email' => 'required|email|max:255',
                        ];
                if($this->validate($request, $rules)){
                    $checkEmail                   = Admin::where('email','=',$postData['email'])->get();
                    if(count($checkEmail) > 0){
                        $row     =  Admin::where('email', '=', $postData['email'])->first();
                        $otp     =  rand(999,10000);
                        $fields  =  [
                                        'remember_token' => $otp
                                    ];
                        Admin::where('id', '=', $row->id)->update($fields);
                        $to = $row->email;
                        $subject = "Reset Password";
                        $message = "Your Reset Password is :" . $otp;
                        // $this->sendMail('avijit@keylines.net',$subject,$message);
                        return redirect('/admin/validateOtp/'.Helper::encoded($row->id))->with('success_message', 'OTP Sent To Your Registered Email !!!');
                    }else{
                        return redirect()->back()->with('error_message', 'We Don\'t Recognized Your Email !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $data                           = [];
            $title                          = 'Forgot Password';
            $page_name                      = 'forgot-password';
            echo $this->admin_before_login_layout($title,$page_name,$data);
        }
        public function validateOtp(Request $request, $id){
            $id                             = Helper::decoded($id);
            $data['id']                     = $id;
            $checkUser                      = Admin::where('id', '=', $id)->first();
            $data['email']                  = (($checkUser)?$checkUser->email:'');

            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                            'otp1'     => 'required|max:1',
                            'otp2'     => 'required|max:1',
                            'otp3'     => 'required|max:1',
                            'otp4'     => 'required|max:1',
                        ];
                if($this->validate($request, $rules)){
                    // $id     = $postData['id'];
                    $otp1   = $postData['otp1'];
                    $otp2   = $postData['otp2'];
                    $otp3   = $postData['otp3'];
                    $otp4   = $postData['otp4'];
                    $newotp    = ($otp1.$otp2.$otp3.$otp4);
                    $checkUser = Admin::where('id', '=', $id)->first();
                    if($checkUser){
                        $otp = $checkUser->remember_token;
                        if($otp == $newotp){
                            $postData = [
                                            'remember_token'        => '',
                                        ];
                            Admin::where('id', '=', $checkUser->id)->update($postData);
                            return redirect('/admin/changePassword/'.Helper::encoded($checkUser->id))->with('success_message', 'OTP Validated. Just Reset Your Password !!!');
                        } else {
                            return redirect()->back()->with('error_message', 'OTP Mismatched !!!');
                        }
                    } else {
                        return redirect()->back()->with('error_message', 'We Don\'t Recognize You !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $title                          = 'Validate OTP';
            $page_name                      = 'validotp';
            echo $this->admin_before_login_layout($title,$page_name,$data);
        }
        public function resendOtp(Request $request, $email){
            $email                          = Helper::decoded($email);
            $checkEmail                     = Admin::where('email','=',$email)->first();
            if(count($checkEmail) > 0){
                $row     =  Admin::where('email', '=', $email)->first();
                $otp     =  rand(999,10000);
                $fields  =  [
                                'remember_token' => $otp
                            ];
                Admin::where('id', '=', $row->id)->update($fields);
                $to = $row->email;
                $subject = "Reset Password";
                $message = "Your Reset Password is :" . $otp;
                // $this->sendMail('avijit@keylines.net',$subject,$message);
                return redirect('/admin/validateOtp/'.Helper::encoded($row->id))->with('success_message', 'OTP Resend To Your Registered Email !!!');
            }else{
                return redirect()->back()->with('error_message', 'We Don\'t Recognized Your Email !!!');
            }
        }
        public function changePassword(Request $request ,$id){
            $ID = Helper::decoded($id);
            if($request->isMethod('post')){
                $postData = $request->all();
                $getAdmin                     = Admin::where('id','=',$ID)->first();
                if($postData['new_password'] != $postData['confirm_password'] ){
                    return redirect()->back()->with('error_message', 'Password Doesn\'t match !!!');
                } else {
                    if(!Hash::check($postData['new_password'], $getAdmin->password)){
                        $postData = [
                                        'password'        => Hash::make($postData['new_password']),
                                    ];
                        Admin::where('id', '=', $ID)->update($postData);
                        return redirect('/admin')->with('success_message', 'Password Reset Successfully. Please Sign In !!!');
                    } else {
                        return redirect()->back()->with('error_message', 'New Password Can\'t Be Same With Existing Password !!!');
                    }
                }
            }
            $data                           = [];
            $title                          = 'Reset Password';
            $page_name                      = 'reset-password';
            echo $this->admin_before_login_layout($title,$page_name,$data);
        }
        public function logout(Request $request){
            $user_email                             = $request->session()->get('email');
            $user_name                              = $request->session()->get('name');
            /* user activity */
                $activityData = [
                    'user_email'        => $user_email,
                    'user_name'         => $user_name,
                    'user_type'         => 'ADMIN',
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
            return redirect()->back()->with('success_message', 'You Are Successfully Logged Out !!!');
        }
    /* authentication */
    /* dashboard */
        public function dashboard(){
            $data                           = [];
            $title                          = 'Dashboard';
            $page_name                      = 'dashboard';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* dashboard */
    /* settings */
        public function settings(Request $request){
            $uId                            = $request->session()->get('user_id');
            $data['setting']                = GeneralSetting::where('id', '=', 1)->first();
            $data['admin']                  = Admin::where('id', '=', $uId)->first();
            $title                          = 'Settings';
            $page_name                      = 'settings';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function profile_settings(Request $request){
            $uId        = $request->session()->get('user_id');
            $row        = Admin::where('id', '=', $uId)->first();
            $postData   = $request->all();
            $rules      = [
                'name'            => 'required',
                'mobile'          => 'required',
                'email'           => 'required',
            ];
            if($this->validate($request, $rules)){
                /* profile image */
                $imageFile      = $request->file('image');
                if($imageFile != ''){
                    $imageName      = $imageFile->getClientOriginalName();
                    $uploadedFile   = $this->upload_single_file('image', $imageName, '', 'image');
                    if($uploadedFile['status']){
                        $image = $uploadedFile['newFilename'];
                    } else {
                        return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                    }
                } else {
                    $image = $row->image;
                }
                /* profile image */
                $fields = [
                    'name'                  => $postData['name'],
                    'mobile'                => $postData['mobile'],
                    'email'                 => $postData['email'],
                    'image'                 => $image
                ];
                // Helper::pr($fields);
                Admin::where('id', '=', $uId)->update($fields);
                return redirect()->back()->with('success_message', 'Profile Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function general_settings(Request $request){
            $row        = GeneralSetting::where('id', '=', 1)->first();
            $postData   = $request->all();
            $rules      = [
                'site_name'            => 'required',
                'site_phone'           => 'required',
                'site_mail'            => 'required',
                'system_email'         => 'required',
                'site_url'             => 'required',
            ];
            if($this->validate($request, $rules)){
                /* site logo */
                    $imageFile      = $request->file('site_logo');
                    if($imageFile != ''){
                        $imageName      = $imageFile->getClientOriginalName();
                        $uploadedFile   = $this->upload_single_file('site_logo', $imageName, '', 'image');
                        if($uploadedFile['status']){
                            $site_logo = $uploadedFile['newFilename'];
                        } else {
                            return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                        }
                    } else {
                        $site_logo = $row->site_logo;
                    }
                /* site logo */
                /* site footer logo */
                    $imageFile      = $request->file('site_footer_logo');
                    if($imageFile != ''){
                        $imageName      = $imageFile->getClientOriginalName();
                        $uploadedFile   = $this->upload_single_file('site_footer_logo', $imageName, '', 'image');
                        if($uploadedFile['status']){
                            $site_footer_logo = $uploadedFile['newFilename'];
                        } else {
                            return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                        }
                    } else {
                        $site_footer_logo = $row->site_footer_logo;
                    }
                /* site footer logo */
                /* site favicon */
                    $imageFile      = $request->file('site_favicon');
                    if($imageFile != ''){
                        $imageName      = $imageFile->getClientOriginalName();
                        $uploadedFile   = $this->upload_single_file('site_favicon', $imageName, '', 'image');
                        if($uploadedFile['status']){
                            $site_favicon = $uploadedFile['newFilename'];
                        } else {
                            return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                        }
                    } else {
                        $site_favicon = $row->site_favicon;
                    }
                /* site favicon */
                                
                $fields = [
                    'site_name'                         => $postData['site_name'],
                    'site_phone'                        => $postData['site_phone'],
                    'site_phone2'                       => $postData['site_phone2'],
                    'site_mail'                         => $postData['site_mail'],
                    'system_email'                      => $postData['system_email'],
                    'site_url'                          => $postData['site_url'],
                    'description'                       => $postData['description'],
                    'copyright_statement'               => $postData['copyright_statement'],
                    'google_map_api_code'               => $postData['google_map_api_code'],
                    'google_analytics_code'             => $postData['google_analytics_code'],
                    'google_pixel_code'                 => $postData['google_pixel_code'],
                    'facebook_tracking_code'            => $postData['facebook_tracking_code'],
                    'theme_color'                       => $postData['theme_color'],
                    'font_color'                        => $postData['font_color'],
                    'twitter_profile'                   => $postData['twitter_profile'],
                    'facebook_profile'                  => $postData['facebook_profile'],
                    'instagram_profile'                 => $postData['instagram_profile'],
                    'linkedin_profile'                  => $postData['linkedin_profile'],
                    'youtube_profile'                   => $postData['youtube_profile'],
                    'site_logo'                         => $site_logo,
                    'site_footer_logo'                  => $site_footer_logo,
                    'site_favicon'                      => $site_favicon,
                ];
                // Helper::pr($fields);
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'General Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function change_password(Request $request){
            $uId        = $request->session()->get('user_id');
            $adminData  = Admin::where('id', '=', $uId)->first();
            $postData   = $request->all();
            $rules      = [
                'old_password'            => 'required',
                'new_password'            => 'required',
                'confirm_password'        => 'required',
            ];
            if($this->validate($request, $rules)){
                $old_password       = $postData['old_password'];
                $new_password       = $postData['new_password'];
                $confirm_password   = $postData['confirm_password'];
                if(Hash::check($old_password, $adminData->password)){
                    if($new_password == $confirm_password){
                        $fields = [
                            'password'            => Hash::make($new_password)
                        ];
                        Admin::where('id', '=', $uId)->update($fields);
                        return redirect()->back()->with('success_message', 'Password Changed Successfully !!!');
                    } else {
                        return redirect()->back()->with('error_message', 'New & Confirm Password Does Not Matched !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'Current Password Is Incorrect !!!');
                }
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function email_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'from_email'            => 'required',
                'from_name'             => 'required',
                'smtp_host'             => 'required',
                'smtp_username'         => 'required',
                'smtp_password'         => 'required',
                'smtp_port'             => 'required',
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'from_email'            => $postData['from_email'],
                    'from_name'             => $postData['from_name'],
                    'smtp_host'             => $postData['smtp_host'],
                    'smtp_username'         => $postData['smtp_username'],
                    'smtp_password'         => $postData['smtp_password'],
                    'smtp_port'             => $postData['smtp_port'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'Email Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function email_template(Request $request){
            $postData = $request->all();
            $rules = [
                'email_template_user_signup'            => 'required',
                'email_template_forgot_password'        => 'required',
                'email_template_change_password'        => 'required',
                'email_template_failed_login'           => 'required',
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'email_template_user_signup_sender_name'            => $postData['email_template_user_signup_sender_name'],
                    'email_template_user_signup_subject'                => $postData['email_template_user_signup_subject'],
                    'email_template_user_signup'                        => $postData['email_template_user_signup'],
                    'email_template_forgot_password'                    => $postData['email_template_forgot_password'],
                    'email_template_change_password'                    => $postData['email_template_change_password'],
                    'email_template_failed_login'                       => $postData['email_template_failed_login'],
                    'email_template_contactus'                          => $postData['email_template_contactus'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'Email Templates Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function sms_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'sms_authentication_key'            => 'required',
                'sms_sender_id'                     => 'required',
                'sms_base_url'                      => 'required',
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'sms_authentication_key'            => $postData['sms_authentication_key'],
                    'sms_sender_id'                     => $postData['sms_sender_id'],
                    'sms_base_url'                      => $postData['sms_base_url'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'SMS Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function footer_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'footer_text'            => 'required',
            ];
            if($this->validate($request, $rules)){
                $footer_link_name_array = $postData['footer_link_name'];
                $footer_link_name       = [];
                if(!empty($footer_link_name_array)){
                    for($f=0;$f<count($footer_link_name_array);$f++){
                        if($footer_link_name_array[$f]){
                            $footer_link_name[]       = $footer_link_name_array[$f];
                        }
                    }
                }
                $footer_link_array = $postData['footer_link'];
                $footer_link       = [];
                if(!empty($footer_link_array)){
                    for($f=0;$f<count($footer_link_array);$f++){
                        if($footer_link_array[$f]){
                            $footer_link[]       = $footer_link_array[$f];
                        }
                    }
                }

                $footer_link_name_array2 = $postData['second_col_link_text'];
                $footer_link_name2       = [];
                if(!empty($footer_link_name_array2)){
                    for($f=0;$f<count($footer_link_name_array2);$f++){
                        if($footer_link_name_array2[$f]){
                            $footer_link_name2[]       = $footer_link_name_array2[$f];
                        }
                    }
                }
                $footer_link_array2 = $postData['second_col_link'];
                $footer_link2       = [];
                if(!empty($footer_link_array2)){
                    for($f=0;$f<count($footer_link_array2);$f++){
                        if($footer_link_array2[$f]){
                            $footer_link2[]       = $footer_link_array2[$f];
                        }
                    }
                }

                $footer_link_name_array3 = $postData['footer_link_name3'];
                $footer_link_name3       = [];
                if(!empty($footer_link_name_array3)){
                    for($f=0;$f<count($footer_link_name_array3);$f++){
                        if($footer_link_name_array3[$f]){
                            $footer_link_name3[]       = $footer_link_name_array3[$f];
                        }
                    }
                }
                $footer_link_array3 = $postData['footer_link3'];
                $footer_link3       = [];
                if(!empty($footer_link_array3)){
                    for($f=0;$f<count($footer_link_array3);$f++){
                        if($footer_link_array3[$f]){
                            $footer_link3[]       = $footer_link_array3[$f];
                        }
                    }
                }

                $fields = [
                    'footer_text'                   => $postData['footer_text'],
                    'footer_description'            => $postData['footer_description'],
                    'footer_link_name'              => json_encode($footer_link_name),
                    'footer_link'                   => json_encode($footer_link),
                    'footer_link_name2'             => json_encode($footer_link_name2),
                    'footer_link2'                  => json_encode($footer_link2),
                    'footer_link_name3'             => json_encode($footer_link_name3),
                    'footer_link3'                  => json_encode($footer_link3),
                ];
                // Helper::pr($fields);
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'Footer Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function seo_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'meta_title'            => 'required',
                'meta_description'      => 'required'
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'meta_title'            => $postData['meta_title'],
                    'meta_description'      => $postData['meta_description']
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'SEO Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function payment_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'stripe_payment_type'   => 'required',
                'stripe_sandbox_sk'     => 'required',
                'stripe_sandbox_pk'     => 'required',
                'stripe_live_sk'        => 'required',
                'stripe_live_pk'        => 'required',
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'stripe_payment_type'   => $postData['stripe_payment_type'],
                    'stripe_sandbox_sk'     => $postData['stripe_sandbox_sk'],
                    'stripe_sandbox_pk'     => $postData['stripe_sandbox_pk'],
                    'stripe_live_sk'        => $postData['stripe_live_sk'],
                    'stripe_live_pk'        => $postData['stripe_live_pk'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'Payment Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function color_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'color_theme'               => 'required',
                'color_button'              => 'required',
                'color_title'               => 'required',
                'color_panel_bg'            => 'required',
                'color_panel_text'          => 'required',
                'color_accept_button'       => 'required',
                'color_reject_button'       => 'required',
                'color_transfer_button'     => 'required',
                'color_complete_button'     => 'required',
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'color_theme'                       => $postData['color_theme'],
                    'color_button'                      => $postData['color_button'],
                    'color_title'                       => $postData['color_title'],
                    'color_panel_bg'                    => $postData['color_panel_bg'],
                    'color_panel_text'                  => $postData['color_panel_text'],
                    'color_accept_button'               => $postData['color_accept_button'],
                    'color_reject_button'               => $postData['color_reject_button'],
                    'color_transfer_button'             => $postData['color_transfer_button'],
                    'color_complete_button'             => $postData['color_complete_button'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'Color Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function signature_settings(Request $request){
            $postData = $request->all();
            /* signature */
                $signature                            = $postData['signature'];
                if(!empty($signature)){
                    $data = $signature;
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $signatureVal   = uniqid() . '.png';
                    file_put_contents('public/uploads/' . $signatureVal, $data);
                }
            /* signature */
            $fields = [
                'owner_signature'                       => $signatureVal
            ];
            // Helper::pr($fields);
            GeneralSetting::where('id', '=', 1)->update($fields);
            return redirect()->back()->with('success_message', 'Owner Signature Settings Updated Successfully !!!');
        }
    /* settings */
    /* email logs */
        public function emailLogs(){
            $data['rows']                   = EmailLog::where('status', '=', 1)->orderBy('id', 'DESC')->get();
            $title                          = 'Email Logs';
            $page_name                      = 'email-logs';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function emailLogsDetails(Request $request,$id ){
            $id = Helper::decoded($id);
            $data['logData']                   = EmailLog::where('id', '=', $id)->orderBy('id', 'DESC')->first();
            $title                          = 'Email Logs Details';
            $page_name                      = 'email-logs-info';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* email logs */
    /* login logs */
        public function loginLogs(){
            $data['rows1']                   = UserActivity::where('activity_type', '=', 0)->orderBy('activity_id', 'DESC')->get();
            $data['rows2']                   = UserActivity::where('activity_type', '=', 1)->orderBy('activity_id', 'DESC')->get();
            $data['rows3']                   = UserActivity::where('activity_type', '=', 2)->orderBy('activity_id', 'DESC')->get();
            $title                          = 'Login Logs';
            $page_name                      = 'login-logs';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* login logs */
}
