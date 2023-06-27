<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Auth;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'role_title', 'role_slug', 'role_type', 'is_deleted'];

    /**
     * users() one-to-many relationship method
     *
     * @return QueryBuilder
     */
    public function users()
    {
        return $this->belongsToMany('App\DB\Admin\Admin', 'role_user', 'role_id');
    }

    public function allusers()
    {
        return $this->belongsToMany('App\Models\User','role_user','admin_id');
    }

    /**
     * permissions() many-to-many relationship method
     *
     * @return QueryBuilder
     */
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission');
    }

    /**
     * It will fetch the current roles info
     * @param $field
     * @return mixed
     */
    public function getCurrentRole($field)
    {
        $auth = $this->getAuthenticData();    
        $get_role_user = \App\Models\RoleUser::where('admin_id', $auth)->first();            
        $columns = Schema::getColumnListing('roles');
        if(in_array($field, $columns))
        {
            $role = Role::select($field)->where('id', $get_role_user->role_id)->first();         
        }
        return $role->$field;         
    }

    /**
     * This function is use to get current authentic details
     * @return mixed
     */
    public function getAuthenticData()
    {
        if(Auth::guard('admin')->check()) {
            $auth = Auth::guard('admin')->user()->id;
        }elseif (Auth::guard('photographer')->check()) {
            $auth = Auth::guard('photographer')->user()->id;
        } 
        // else if(Request::segment(1) == 'seller' && Auth::seller()->check()) {
        //     $auth = Auth::seller();
        // } else if(Request::segment(1) == 'affiliate' && Auth::affiliate()->check()) {
        //     $auth = Auth::affiliate();
        // }
        return $auth;
        // return "Admin";
    }

    /**
     * List of roles
     * @param null $id
     * @return mixed
     */
    public function getRoles($id = null)
    {
        if (isset($id) && $id != null) {
             return Role::findOrFail($id);
        } else {
            //To get the role slug of a current (logged in) user
            $role_slug = $this->getCurrentRole('role_slug');
            $admins_slug = config('app.admin_slug');

            if(in_array($role_slug, $admins_slug)) {
                return Role::where('is_deleted', 0)->get();
            } else {
                $auth = $this->getAuthenticData();
                return Role::where('parent_id', '=', $auth->get()->id)->where('is_deleted', 0)->get();
            }
        }
    }

    public function permissionsID($id)
    {
        $res = \App\Models\Permission::find($id);
        if($res)
        {
            return true;
        }
        
    }
}
