@extends('admin.layouts.master')
<title>Admin | Change Password</title>

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
                                        <i class="lnr-cog opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Change Password</span>
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
                                                <a href="{{url('/admin/dashboard')}}">Dashboard</a>
                                            </li>                                            
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Change Password  
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>                            
                        </div>                                                 
                    </div>
                </div>
                <!-- <div class="app-page-title">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div class="page-title-icon">
                                <i class="lnr-user icon-gradient bg-ripe-malin">
                                </i>
                            </div>
                            <div>Change Password                                
                                <div class="page-title-subheading">
                                    Add New User.
                                </div>
                            </div>
                        </div>                          
                    </div>
                </div>   -->
                <!-- <div>                   
                    <nav class="" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">User</a></li>
                            <li class="active breadcrumb-item" aria-current="page"><a href="{{url('/admin/user/list')}}">User List</a></li>
                            <li class="active breadcrumb-item" aria-current="page">Add New User</li>
                        </ol>
                    </nav>                    
                </div>                                           -->
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Change Password</h5>
                        <div class="divider"></div> 
                        @if(Session::has('msg'))                     
                            <div class="alert {{(Session::get('alert_class') == true) ? 'alert-success' : 'alert-danger'}} alert-dismissible fade show" role="alert">
                                {{ Session::get('msg') }}
                                <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif                                                
                        <form id="changePassword" class="col-md-6" method="post" action="{{url('admin/change/password')}}">
                            @csrf                                                        
                            <div class="form-group">
                                <label for="password">Password<span style="color:red">*</span></label>
                                <div>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password"/>                                        
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password<span style="color:red">*</span></label>
                                <div>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password"/>                                        
                                </div>
                            </div>                         

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="change_password" value="change_password">Change Password</button>
                                <!-- <button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button> -->
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
<div class="app-drawer-overlay d-none animated fadeIn"></div>
</html>
