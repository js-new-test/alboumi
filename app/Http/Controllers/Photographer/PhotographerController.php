<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Auth;
use DB;
use DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PhotographerController extends Controller
{
    /* ###########################################
    // Function: showLoginForm
    // Description: Display photographer login page  
    // Parameter: No Parameter
    // ReturnType: view
    */ ########################################### 
    public function showLoginForm()
    {        
        return view('admin.photographer.login');
    }

    /* ###########################################
    // Function: login
    // Description: Authentical user for pohotgrapher login  
    // Parameter: email: String, password: Int 
    // ReturnType: view
    */ ###########################################
    public function login(Request $request)
    {                      
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
            
        if($validator->fails()) {
            return redirect('/photographer/login')
                ->withErrors($validator)
                ->withInput();
        }        

        $user = \App\Models\User::where('email', $request['email'])->first();        
        $role = \App\Models\Role::select('roles.role_type')
            ->leftJoin('role_user', 'role_user.role_id','=', 'roles.id')
            ->where('role_user.admin_id', $user->id)
            ->first();

        // If not role type isn't photographer then redirect back 
        if($role->role_type != 'photographer')
        {
            return redirect()->back()->with('msg', config('message.AuthMessages.RoleNotFound'));
        }elseif ($user->is_deleted == 1) {
            return redirect()->back()->with('msg', config('message.AuthMessages.AccountDelete'));
        }elseif ($user->is_approve == 0) {
            return redirect()->back()->with('msg', config('message.AuthMessages.NotVerified'));
        }elseif ($user->is_active == 0) {
            return redirect()->back()->with('msg', config('message.AuthMessages.NotActive'));
        }

        if($request->has('remember'))
        {                                     
            if(Auth::guard('photographer')->attempt(array('email' => $request['email'], 'password' => $request['password']), true)){
                return redirect('/photographer/dashboard')->withCookie("pgpr_email", $request->email)
                    ->withCookie("pgpr_password", $request->password)
                    ->withCookie("pgpr_remember", "checked");      
            }
            else{                         
                return redirect()->back()
                    ->with('msg', config('message.AuthMessages.InvalidCredentials'));
            }
        }
        else
        {                                  
            if(Auth::guard('photographer')->attempt(array('email' => $request['email'], 'password' => $request['password']), false)){
                return redirect('/photographer/dashboard')
                    ->withCookie("pgpr_email", "")
                    ->withCookie("pgpr_password", "")
                    ->withCookie("pgpr_remember", "");            
            }
            else{                               
                return redirect()->back()
                    ->with(['msg' => config('message.AuthMessages.InvalidCredentials')]);
            }
        }                               
    }

    /* ###########################################
    // Function: logout
    // Description: Terminate user current session  
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function logout()
    {
        Auth::guard('photographer')->logout();        
        return redirect('/photographer/login');
    }

    /* ###########################################
    // Function: profile
    // Description: Show photographer profile page  
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function profile()
    {
        $user_id = Auth::guard('photographer')->user()->id;        
        $users = \App\Models\User::where('id', $user_id)->first();
        $user_profile = \App\Models\UserProfilePhoto::where('user_id', $user_id)->first();
        if($user_profile)
        {            
            $photo = ($user_profile->image) ? url('public/assets/images/user_profile').'/'.$user_profile->image : url('public/assets/images/default-user.png');          
        }
        else
        {
            $photo = url('/assets/images/default-user.png');          
        }        
        return view('admin.photographer.profile', compact('users'))->with('photo', $photo);
    }

    /* ###########################################
    // Function: updateProfile
    // Description: Update user profile details  
    // Parameter: id: Int, photographer_firstname: String, photographer_lastname: String, profile_photo: String
    // ReturnType: view
    */ ###########################################
    public function updateProfile(Request $request)
    {                
        $msg = [
            'photographer_firstname.required' => "The firstname field is required.",
            'photographer_lastname.required' => "The lastname field is required.",            
        ];        
        $validator = Validator::make($request->all(), [
            'photographer_firstname' => 'required',
            'photographer_lastname' => 'required',            
        ],$msg);
            
        if($validator->fails()) {
            return redirect('/photographer/profile')
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $user = \App\Models\User::where('id',$request->photographer_id)->first();
        $user->firstname = $request->photographer_firstname;
        $user->lastname = $request->photographer_lastname;
        if($request->hasFile('profile_photo'))
        {
            $photo = $request->file('profile_photo');
            $ext = $request->file('profile_photo')->extension();
            $filename = rand().'_'.time().'.'.$ext;   
            $photo->move(public_path().'/assets/images/user_profile', $filename);         
        }        
                       
        if($user->save()){
            $user_profile = \App\Models\UserProfilePhoto::where('user_id', $request->photographer_id)->first();
            if($user_profile)
            {
                if(!empty($filename))
                {   
                    $path = public_path('/assets/images/user_profile').'/'.$user_profile->image;                
                    if(file_exists($path))
                    {
                        unlink($path);
                    }
                    $user_profile->user_id = $user->id;
                    $user_profile->image = $filename;
                    $user_profile->save();                    
                }                                              
            }                                                                    
            else
            {
                if(!empty($filename))
                {
                    $user_profile = new \App\Models\UserProfilePhoto;
                    $user_profile->user_id = $user->id;
                    $user_profile->image = $filename;
                    $user_profile->save();
                }              
            }
            return redirect()->back()->with('msg', "Profile Updated Successfully!");
        }
        else{
            return redirect()->back();
        }
    }

    /* ###########################################
    // Function: showForgotPassForm
    // Description: Show photographer forgot password form  
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function showForgotPassForm()
    {
        return view('admin.photographer.forgot-password');
    }

    /* ###########################################
    // Function: forgotPassword
    // Description: Send email to user mail id
    // Parameter: forgot_email: String
    // ReturnType: view
    */ ###########################################
    public function forgotPassword(Request $request)
    {        
        $msg = [
            'forgot_email.required' => "The email field is required.",            
            'forgot_email.email' => "The email must be a valid email address..",            
        ];        
        $validator = Validator::make($request->all(), [
            'forgot_email' => 'required|email',
        ],$msg);
            
        if($validator->fails()) {
            return redirect('/photographer/forgot-password')
                        ->withErrors($validator)
                        ->withInput();
        }        
        $user = \App\Models\User::where('email', $request->forgot_email)->first();
        if($user)
        {
            $forgot_password = new \App\Models\ResetPassword; 
            $forgot_password->email = $request->forgot_email;
            $forgot_password->token = Str::random(60);
            $forgot_password->save();
            return redirect()->back()->with('success_msg', "Reset password mail has been successfully sent to your email please check and verify");
        }
        else
        {
            return redirect()->back()->with('msg', "Sorry the email you entered is not available in our record!");
        }
    }

    /* ###########################################
    // Function: showResetPassForm
    // Description: Show photographer forgot password form  
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function showResetPassForm($token)
    {
        $current_time = date('Y-m-d H:i:s');
        $timeOut = \Carbon\Carbon::parse($current_time);    
        $forgot_password = \App\Models\ResetPassword::where('token', $token)->first();        
        $diffInHours = $timeOut->diffInMinutes($forgot_password->created_at);                
        if($diffInHours < 60)
        {            
            return view('admin.photographer.reset-password')->with('email', $forgot_password->email);
        }
        else
        {            
            return view('admin.photographer.link-expire');
        }
    }

    /* ###########################################
    // Function: resetPassword
    // Description: Reset existing password functionality  
    // Parameter: password: String, confirm_password: String
    // ReturnType: view
    */ ###########################################
    public function resetPassword(Request $request)
    {               
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'required|min:6',
        ]);
            
        if($validator->fails()) {
            return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput();
        }

        $reset_password = \App\Models\User::where('email', $request->reset_pass_email)->first();
        $reset_password->password = Hash::make($request->password);
        if($reset_password->save())
        {
            return view('admin.photographer.reset-password-success');
        }
        else
        {
            return redirect()->back()->with('msg', "Something went wrong, Please Try again!");
        }
    }    
}
