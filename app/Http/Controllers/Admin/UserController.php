<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Traits\ExportTrait;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Auth;
use Exception;
use DataTables;
use Excel;
use DB;
use App\Traits\ReuseFunctionTrait;
use Mail;

class UserController extends Controller
{
    use ExportTrait;
    use ReuseFunctionTrait;

    /* ###########################################
    // Function: getUserList
    // Description: Display list of users
    // Parameter: request: Request
    // ReturnType: datatable object
    */ ###########################################
    public function getUserList(Request $request)
    {
        if($request->ajax()) {
            try {
                $parent_id = Auth::guard('admin')->user()->id;
                $timezone = \App\Models\UserTimezone::where('user_id', $parent_id)->pluck('zone')->first();
                $user = \App\Models\User::select('users.id', 'users.firstname', 'users.lastname', 'users.email', DB::raw("date_format(users.created_at,'%Y-%m-%d %h:%i:%s') as user_created_at"), 'users.is_active', 'users.is_deleted', 'roles.role_title')
                        ->leftJoin('role_user','role_user.admin_id','=','users.id')
                        ->leftJoin('roles','roles.id','=','role_user.role_id')
                        ->leftJoin('user_timezone','user_timezone.user_id','=','users.id')
                        ->where('users.parent_id', '=', $parent_id)
                        ->where('users.is_deleted', '=', 0)
                        ->get();
                return Datatables::of($user)->editColumn('user_zone', function () use($timezone){
                    return $timezone;
                })->make(true);            
            } catch (\Exception $e) {
                return view('errors.500');
            }
        }
        $roles = \App\Models\Role::get();
        return view('admin.users.user.list-users', compact('roles'));
    }

    /* ###########################################
    // Function: getUserForm
    // Description: Get add new user form
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function getUserForm()
    {
        $roles = \App\Models\Role::where('is_deleted',0)->get();
        return view('admin.users.user.add', compact('roles'));
    }

    /* ###########################################
    // Function: addUser
    // Description: Get add new user form
    // Parameter: first_name: String, last_name: String, phone: Int, email: String, password: String, select_role: Int, id: Int
    // ReturnType: none
    */ ###########################################
    public function addUser(Request $request)
    {
        // try {
            $parent_id = Auth::guard('admin')->user()->id;
            $user = \App\Models\User::where('email', $request->email)->where('is_deleted', 0)->first();
            if($user)
            {
                $notification = array(
                    'message' => config('message.AuthMessages.EmailExists'),
                    'alert-type' => 'error'
                );
                return redirect()->back()->with($notification);
                // return redirect()->back()->with('msg', config('message.AuthMessages.EmailExists'))->with('alert-class', false);
            }

            $user = new \App\Models\User;
            $user->parent_id = $parent_id;
            $user->firstname = $request->first_name;
            $user->lastname = $request->last_name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            // $user->is_active = 1;
            // $user->is_approve = 1;
            $user->created_at = date("Y-m-d H:i:s");
            if($user->save())
            {
                $timezone = new \App\Models\UserTimezone;
                $timezone->user_id = $user->id;
                $timezone->timezone = $request->timezone;
                $timezone->zone = $request->timezone_offset;
                $timezone->created_at = date("Y-m-d H:i:s");
                $timezone->save();

                $role_user = new \App\Models\RoleUser;
                $role_user->role_id = $request->select_role;
                $role_user->admin_id = $user->id;
                $role_user->save();

                // Send email start
                $temp_arr = [];
                $new_user = $this->getEmailTemp();
                foreach($new_user as $code )
                {
                    if($code->code == 'NUSER')
                    {
                        array_push($temp_arr, $code);
                    }
                }

                if(is_array($temp_arr))
                {
                    $value = $temp_arr[0]['value'];
                }

                $replace_data = array(
                    '{{name}}' => $user->firstname,
                    '{{link}}' => url('/admin/login'),
                );
                $html_value = $this->replaceHtmlContent($replace_data,$value);
                $data = [
                    'html' => $html_value,
                ];
                $subject = $temp_arr[0]['subject'];
                $email = $user->email;
                Mail::send('admin.emails.add-new-user-email', $data, function ($message) use ($email,$subject) {
                    $message->from('testmail.magneto@gmail.com', 'Alboumi');
                    $message->to($email)->subject($subject);
                });
                // Send email over
            }
            $notification = array(
                'message' => config('message.Users.AddUserSuccess'),
                'alert-type' => 'success'
            );
            // return redirect()->back()->with('msg', config('message.Users.AddUserSuccess'))->with('alert-class', true);
            return redirect('admin/user/list')->with($notification);
        // } catch (\Exception $th) {
        //     return view('errors.500');
        // }
    }

    /* ###########################################
    // Function: editUser
    // Description: Get user information from user id
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function editUser($id)
    {
        $user = \App\Models\User::select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.phone', 'roles.role_title', 'roles.id as role_id', 'user_timezone.zone as tz_offset')
                ->leftJoin('role_user','role_user.admin_id','=','users.id')
                ->leftJoin('roles','roles.id','=','role_user.role_id')
                ->leftJoin('user_timezone','user_timezone.user_id','=','users.id')
                ->where('role_user.admin_id',$id)
                ->first();
        $roles = \App\Models\Role::where('is_deleted',0)->get();
        return view('admin.users.user.edit', compact('roles', 'user'));
    }

    /* ###########################################
    // Function: exportUsers
    // Description: Exporting Users to xlsx file
    // Parameter: No parameter
    // ReturnType: mime type
    */ ###########################################
    public function exportUsers()
    {
        try{
            return Excel::download(new UsersExport, 'Alboumi_Users.xlsx');
        } catch(\Exception $ex) {
            return view('errors.500');
        }
    }

    /* ###########################################
    // Function: exportUsers
    // Description: Display import form for user
    // Parameter: No parameter
    // ReturnType: view
    */ ###########################################
    public function getimportUsersForm()
    {
        return view('admin.users.user.import');
    }

    /* ###########################################
    // Function: importUser
    // Description: Display import form for user
    // Parameter: import_user_file: File
    // ReturnType: view
    */ ###########################################
    public function importUser(Request $request)
    {
        try{
            if($request->hasFile('import_user_file'))
            {
                $import = new UsersImport;
                Excel::import($import, $request->file('import_user_file'));
                $collection = $import->getCommon();

                $counter = 0;
                $errors = [];
                $suc_uploaded = [];
                $fail_uploaded = [];
                foreach($collection as $row)
                {
                    $email_arr = \App\Models\User::select('*')->pluck('email')->toArray();
                    $counter++;
                    $flag = 'true';
                    if($row[0] == "" || $row[1] == "" || $row[2] == "" || $row[3] == "" || $row[4] == "" || $row[5] == "")
                    {
                        $errors[] = "Record is incomplete for Row - ".$counter.". Please try again.";
                        $flag = 'false';
                    }

                    if(in_array($row[3], $email_arr))
                    {
                        $errors[] = $row[3]. " is already exist. Please use different email.";
                        $flag = 'false';
                    }

                    if(!in_array($row[3], $email_arr))
                    {
                        if (!filter_var($row[3], FILTER_VALIDATE_EMAIL)) {
                            $errors[] = $row[3]. " is Invalid.";
                            $flag = 'false';
                        }
                    }

                    if (strlen($row[5]) < 6) {
                        $errors[] = 'Password for '. $row[3] . ' should be 6 digits.';
                        $flag = 'false';
                    }

                    if($flag == 'true')
                    {
                        $user = new \App\Models\User;
                        if ($row[4] != '') {
                            $role = \App\Models\Role::where('role_title', $row[4])->first();
                        }
                        $user->firstname =  $row[0];
                        $user->lastname = $row[1];
                        $user->email = $row[3];
                        $user->phone = $row[2];
                        $user->parent_id = Auth::guard('admin')->user()->id;
                        $user->password = Hash::make($row[5]);
                        if($user->save())
                        {
                            $role_user = new \App\Models\RoleUser;
                            $role_user->role_id = $role->id;
                            $role_user->admin_id = $user->id;
                            $role_user->save();
                            $suc_uploaded[] = $counter;
                        }
                    }
                    else
                    {
                        $fail_uploaded[] = $counter;
                    }
                }
                return redirect()->back()->with('msg', $errors)->with('success', $suc_uploaded)->with('faile', $fail_uploaded);
            }
        } catch(\Maatwebsite\Excel\Validators\ValidationException $ex) {
            return view('errors.500');
        }

    }

    /* ###########################################
    // Function: deleteUser
    // Description: Delete exiting user
    // Parameter: id: Int, is_deleted: Int
    // ReturnType: array
    */ ###########################################
    public function deleteUser($user_id, Request $request)
    {
        $is_deleted = $request->is_deleted;
        $user = \App\Models\User::where('id', $user_id)->first();
        if($user)
        {
            if($is_deleted == 0)
            {
                $user->is_deleted = 1;
            }
            $user->save();
            $result['status'] = 'true';
            return $result;
        }
        else
        {
            $result['status'] = 'false';
            return $result;
        }
    }

    /* ###########################################
    // Function: userActDeaAct
    // Description: Delete exiting user
    // Parameter: user_id: Int, is_active: Int
    // ReturnType: array
    */ ###########################################
    public function userActDeaAct(Request $request)
    {
        try {
            $user = \App\Models\User::where('id',$request->user_id)->first();
            if($request->is_active == 1)
            {
                $user->is_active = $request->is_active;
                $msg = "User Activated Successfully!";
            }
            else
            {
                $user->is_active = $request->is_active;
                $msg = "User Deactivated Successfully!";
            }
            $user->save();
            $result['status'] = 'true';
            $result['msg'] = $msg;
            return $result;
        } catch(\Exception $ex) {
            return view('errors.500');
        }
    }

    /* ###########################################
    // Function: updateUser
    // Description: Update existing user information
    // Parameter: first_name: String, last_name: String, phone: Int, email: String, password: String, select_role: Int, id: Int
    // ReturnType: none
    */ ###########################################
    public function updateUser(Request $request)
    {
        try {
            $parent_id = Auth::guard('admin')->user()->id;
            $user = \App\Models\User::where('id', $request->user_id)->where('parent_id', $parent_id)->first();
            $user->firstname = $request->first_name;
            $user->lastname = $request->last_name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            if($user->save())
            {
                $timezone = \App\Models\UserTimezone::where('user_id', $user->id)->first();
                if($timezone)
                {
                    $timezone->timezone = $request->timezone;
                    $timezone->zone = $request->timezone_offset;
                    $timezone->created_at = date("Y-m-d H:i:s");
                    $timezone->save();
                }
                else
                {
                    $timezone = new \App\Models\UserTimezone;
                    $timezone->user_id = $user->id;
                    $timezone->timezone = $request->timezone;
                    $timezone->zone = $request->timezone_offset;
                    $timezone->created_at = date("Y-m-d H:i:s");
                    $timezone->save();
                    return redirect()->back()->with('msg', config('message.Users.AddUserSuccess'))->with('alert-class', true);
                }
                $role_user = \App\Models\RoleUser::where('admin_id', $user->id)->first();
                $role_user->role_id = $request->select_role;
                $role_user->save();
            }
            $notification = array(
                'message' => config('message.Users.UpdateUserSuccess'),
                'alert-type' => 'success'
            );
            // return redirect()->back()->with('msg', config('message.Users.UpdateUserSuccess'))->with('alert-class', true);
            return redirect('admin/user/list')->with($notification);
        } catch (\Exception $th) {
            return view('errors.500');
        }
    }
}
