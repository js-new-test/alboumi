@extends('admin.layouts.master')
<title>Edit Role | Alboumi</title>

@section('content')
<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
    @include('admin.include.header')    
	<div class="app-main">
        @include('admin.include.sidebar') 
        <div class="app-main__outer">
            <div class="app-main__inner">
                <div class="app-page-title app-page-title-simple">
                    <div class="page-title-wrapper">
                    <div class="page-title-heading">                            
                        <div>
                            <div class="page-title-head center-elem">
                                <span class="d-inline-block pr-2">
                                    <i class="lnr-users opacity-6"></i>
                                </span>
                                <span class="d-inline-block">Role</span>
                            </div>
                            <div class="page-title-subheading opacity-10">
                                <nav class="" aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a>
                                                <i aria-hidden="true" class="fa fa-home"></i>
                                            </a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="javascript:void(0);">Role</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <a href="{{url('/admin/user/role/list')}}">Role List</a>
                                        </li>
                                        <li class="breadcrumb-item" aria-current="page">
                                            Edit Role
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>                            
                    </div>                          
                    </div>
                </div>                                                        
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Update Role</h5>  
                        <!-- @if(Session::has('msg'))                     
                            <div class="alert {{(Session::get('alert-class') == true) ? 'alert-success' : 'alert-danger'}} alert-dismissible fade show" role="alert">
                                {{ Session::get('msg') }}
                                <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif                                              -->
                        <form id="roleForm" class="col-md-6" method="post" action="{{url('/admin/user/role/update')}}">
                            @csrf
                            <input type="hidden" name="role_id" value="{{$role->id}}">
                            <div class="form-group">
                                <label for="role_title">Role Title<span style="color:red">*</span></label>
                                <div>
                                    <input type="text" class="form-control" id="role_title" name="role_title" placeholder="Role Title" value="{{$role->role_title}}"/>                                        
                                </div>
                            </div>
    
                            <div class="form-group" style="display:none;">
                                <label for="role_type">Role Type<span style="color:red">*</span></label>
                                <div>                                    
                                    <select name="role_type" id="role_type" class="multiselect-dropdown form-control">                                    
                                        <optgroup label="Select Role">                                    
                                            <option value="admin" {{($role->role_type == "admin") ? 'selected' : ''}}>Admin</option>                                        
                                        </optgroup>                                    
                                        <!-- <option value="photographer" {{($role->role_type == "photographer") ? 'selected' : ''}}>Photographer</option>                                         -->
                                    </select>
                                </div>
                            </div>                                
    
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="add_role" value="add_role">Update Role</button>
                                <a href="{{url('/admin/user/role/list')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
                            </div>
                        </form>
                    </div>
                </div>                
            </div>
            @include('admin.include.footer')
        </div>
    </div>
</div>
@endsection
