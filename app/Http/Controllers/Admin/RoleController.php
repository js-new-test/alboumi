<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CreateRoleRequest;
use Illuminate\Support\Str;
use App\Models\Role;
use Auth;
use DB;
use Session;
use DataTables;
use Exception;

class RoleController extends Controller
{
    protected $role;

    public function __construct(Role $role, Request $request)
    {
        $this->role = $role;
        $this->request = $request;
    }

    /* ###########################################
    // Function: getListOfRoles
    // Description: Display list of available roles  
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function getListOfRoles()
    {                       
        try {
            $admins_slug = array('admin', 'super_admin');                        
            $role_slug = $this->role->getCurrentRole('role_slug');                        
                
            if(in_array($role_slug, $admins_slug)) {
                $roles = $this->role->getRoles();
            } else {
                $segment = $this->request->segment(1);
                $auth_id = Auth::guard('admin')->user()->id;
                $roles = Role::where('parent_id', $auth_id)->orderBy('id')->get();
            }                        
            $current_user_role_id = $this->role->getCurrentRole('id');               
            return view('admin.users.role.roles', compact('roles', 'current_user_role_id'));
        } catch (Exception $e) {
            return view('errors.500');
        }              
    }

    /* ###########################################
    // Function: getRoleForm
    // Description: Display add new role form  
    // Parameter: No Parameter
    // ReturnType: view
    */ ###########################################
    public function getRoleForm()
    {
        return view('admin.users.role.add');
    }

    /* ###########################################
    // Function: addRole
    // Description: Display add new role form  
    // Parameter: role_title: String, role_type: String, 
    // ReturnType: none
    */ ###########################################
    public function addRole(Request $request)
    {            
        try {               
            $parent_id = Auth::guard('admin')->user()->id;                          
            $roleTitle = $request->role_title;
                        
            $roleSlug = Str::slug($roleTitle, '_');            

            if ($request->role_type == 'admin') {
                $role_type = 'admin';
            }             
            else if ($request->role_type == 'photographer') {
                $role_type = 'photographer';
            }

            $role = new \App\Models\Role; 
            $role->parent_id = $parent_id;
            $role->role_title = $roleTitle;
            $role->role_type = $role_type;
            $role->role_slug = $roleSlug;             
            if($role->save())
            {
                $role_user = new \App\Models\RoleUser;
                $role_user->role_id = $role->id;
                $role_user->admin_id = $parent_id;
                $role_user->save();
                // Session::flash('success', 'Role has been saved successfully.');
                // return redirect('/admin/user/role/list');
                $notification = array(
                    'message' => config('message.Role.RoleAddSuccess'), 
                    'alert-type' => 'success'
                );
                return redirect('/admin/user/role/list')->with($notification);                                          
            }                                                            
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());            
            return redirect('/admin/user/role');
        }
    }
    
    /* ###########################################
    // Function: getPermissions
    // Description: View and Update Role permissions
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function getPermissions($id, Request $request)
    {       
        $role = $this->role->getRoles($id);        
        if ($request->method() == 'GET') {
            $current_user_logged_in_id = $this->role->getCurrentRole('id');              
            $current_user_role = $this->role->getRoles($current_user_logged_in_id);        
            
            if($id == 1) {
                //Temporary Restricted For Admin Change Permissions on Modules as Admin Have all Authorities
                return View('errors.401');
            } else {                
                $permissions = \App\Models\Permission::get();                
                $edit_role = $this->role->getRoles($id);                
                
                $arrPermissions = array();
                if($edit_role['role_type'] == 'admin') {
                    //$arrPermissions[] = $permissions->groupBy('permission_group');
                    foreach ($permissions as $key => $permission) {
                        $arrPermissions[$permission->permission_group][$key] = $permission;
                    }
                } else {
                    $arr_modules = config('app.' . $edit_role['role_type']);                    
                    foreach ($permissions as $key => $permission) {
                        // if (in_array($permission->permission_group, $arr_modules)) {
                            $arrPermissions[$permission->permission_group][$key] = $permission;
                        // }
                    }
                }                
                return view('admin.users.role.permissions', compact('role', 'arrPermissions'));
            }            
        }
        
        // $role->permissions()->sync(json_decode($request->permission), true);
        $permission_role = \App\Models\PermissionRole::where('permission_id',$request->permission)->where('role_id',$id)->first();        
        if($permission_role)
        {
            $permission_role->delete();
            return "success";
        }
        else
        {
            $permission_role = new \App\Models\PermissionRole;
            $permission_role->permission_id = $request->permission;
            $permission_role->role_id = $id;
            $permission_role->save();
            return "success";
        }        
    }

    /* ###########################################
    // Function: editRole
    // Description: Get role data from role id
    // Parameter: id: Int
    // ReturnType: view
    */ ###########################################
    public function editRole($id)
    {
        $role = \App\Models\Role::where('id', $id)->first();
        return view('admin.users.role.edit', compact('role'));
    }

    /* ###########################################
    // Function: updateRole
    // Description: Get role data from role id
    // Parameter: role_title: String, role_type: String,
    // ReturnType: none
    */ ###########################################
    public function updateRole(Request $request)
    {
        $parent_id = Auth::guard('admin')->user()->id;                          
        $roleTitle = $request->role_title;
        
        //Remove space and replace with '_' and set 'admin' as prefix
        $roleSlug = Str::slug($roleTitle, '_');            

        if ($request->role_type == 'admin') {
            $role_type = 'admin';
        }             
        else if ($request->role_type == 'photographer') {
            $role_type = 'photographer';
        }

        $role = \App\Models\Role::where('id', $request->role_id)->first(); 
        $role->parent_id = $parent_id;
        $role->role_title = $roleTitle;
        $role->role_type = $role_type;
        $role->role_slug = $roleSlug;                     
        if($role->save())
        {
            $notification = array(
                'message' => config('message.Role.RoleUpdateSuccess'), 
                'alert-type' => 'success'
            );
            // return redirect()->back()->with('msg', config('message.Role.RoleUpdateSuccess'))->with('alert-class', true);                                          
            return redirect('/admin/user/role/list')->with($notification);        
        }        
    }

    /* ###########################################
    // Function: deleteRole
    // Description: Delete existing role from role id
    // Parameter: id: Int
    // ReturnType: array
    */ ###########################################
    public function deleteRole(Request $request)
    {
        $role_id = $request->role_id;
        $role = \App\Models\Role::where('id', $role_id)->first();
        if($role)
        {
            $role->is_deleted = 1;
            $role->save();
            DB::table('role_user')->where('role_id', $role->id)->delete();
            DB::table('permission_role')->where('role_id', $role->id)->delete();
            // Session::flash('success', 'Role has been deleted successfully.');            
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
    // Function: searchRole
    // Description: Search roles from user list
    // Parameter: id: Int
    // ReturnType: array
    */ ###########################################
    public function searchRole(Request $request)
    {        
        if($request->ajax()) {            
            try {
                $parent_id = Auth::guard('admin')->user()->id;    
                $timezone = \App\Models\UserTimezone::where('user_id', $parent_id)->pluck('zone')->first();                                    
                $user = \App\Models\User::select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.created_at', 'users.is_active', 'users.is_deleted', 'roles.role_title',DB::raw("date_format(users.created_at,'%Y-%m-%d %h:%i:%s') as r_created_at"),'user_timezone.zone')
                        ->leftJoin('role_user','role_user.admin_id','=','users.id')
                        ->leftJoin('roles','roles.id','=','role_user.role_id')
                        ->leftJoin('user_timezone','user_timezone.user_id','=','users.id')
                        ->where('users.parent_id', '=', $parent_id)
                        ->where('users.is_deleted', '=', 0);
                        if ($request->filter_role != 'All') {
                            $user = $user->where('role_user.role_id', '=', $request->filter_role);
                        } 
                $user = $user->orderBy('users.id','desc');                       
                $user = $user->get();                
                // return $user;
                return Datatables::of($user)->editColumn('user_zone', function () use($timezone){
                    return $timezone;
                })->make(true);            
            } catch (Exception $e) {
                return view('errors.500');
            }            
        }     
    }
}
