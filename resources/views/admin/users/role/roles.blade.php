@extends('admin.layouts.master')
<title>Role | Alboumi</title>

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
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Role List  
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>                            
                        </div>  
                        <div class="page-title-actions">                            
                            <div class="d-inline-block dropdown">
                                <!-- <a href="{{url('/admin/user/role/add')}}"><button type="button" class="btn-shadow btn btn-info">
                                    <span class="btn-icon-wrapper pr-2 opacity-7">
                                        <i class="fa fa-plus fa-w-20"></i>
                                    </span>
                                    New Role
                                </button></a> -->
                                <a href="{{url('/admin/user/role/add')}}"><button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i class="fa fa-plus btn-icon-wrapper"> </i>Add Role</button></a>
                            </div>
                        </div>                         
                    </div>
                </div>                                                        
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <!-- <h5 class="card-title">List Of Role</h5>                     -->
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="300px">
                                        Role
                                    </th>
                                    <th>
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($roles->count())
                                    @foreach ($roles as $index => $role)
                                    <tr class="odd gradeX">
                                        <td>
                                            {{$role->role_title}}
                                        </td>
                                        <td style="max-width:100px; min-width:100px;">                                        
                                        @if($current_user_role_id != $role->id)
                                            @if($role->role_slug != 'admin')
                                                @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_role_permission_edit') === true)
                                                    <!-- <a class="btn default btn-xs purple pull-left" href="{{ url('admin/role/permissions').'/'.$role->id}}">
                                                        <i class="fa fa-cogs"></i> Edit Permissions
                                                    </a> -->
                                                    <a href="{{ url('admin/role/permissions').'/'.$role->id}}"><i aria-hidden="true" class="fa fa-lock" style="margin-bottom: 10px;font-size: 16px;"></i></a>
                                                @endif
                                                @if($role->role_slug != 'photographer')                                                
                                                    @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_role_edit') === true )
                                                        <!-- <a class="btn default btn-xs purple pull-left" href="{{ url('admin/user/role/edit').'/'.$role->id}}">
                                                            <i class="fa fa-edit"></i> Edit Role
                                                        </a> -->
                                                        <a href="{{ url('admin/user/role/edit').'/'.$role->id}}"><i aria-hidden="true" class="fa fa-edit" style="margin-left: 10px;margin-bottom: 10px;font-size: 16px;"></i></a>
                                                    @endif
                                                @endif
                                                @if($role->role_slug != 'photographer')                                                
                                                    @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_role_delete') === true )
                                                        <!-- <a class="btn default btn-xs purple pull-left" href="{{ url('admin/user/role/edit').'/'.$role->id}}">
                                                            <i class="fa fa-trash"></i> Delete Role
                                                        </a> -->
                                                        <a href="javascript:void(0);" data-role_id="{{$role->id}}" class="delete_role"><i class="fa fa-trash btn-icon-wrapper" style="margin-left: 10px;margin-bottom: 10px;font-size: 16px;"> </i></a>
                                                    @endif
                                                @endif                                               
                                            @endif
                                        @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="2">No Roles Found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>                
            </div>
            @include('admin.include.footer')
        </div>
    </div>
    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-sm" id="deleteRoleModel" tabindex="-1" role="dialog" aria-labelledby="deleteRoleModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRoleModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="role_id" id="role_id">                    
                    <p class="mb-0" id="delete_message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="deleteRole">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function(){
    $('.delete_role').click(function(){
        var role_id = $(this).attr('data-role_id');
        var message = "Are you sure?";                
        $('#deleteRoleModel').on('show.bs.modal', function(e){
            $('#role_id').val(role_id);            
            $('#delete_message').text(message);
        });
        $('#deleteRoleModel').modal('show');        
    })

    $('#deleteRole').on('click', function(){
        var role_id = $('#role_id').val();
        $.ajax({
            url: "{{url('/admin/user/role/delete')}}",
            method: 'POST',
            data:{
                "_token": "{{ csrf_token() }}",
                "role_id":role_id
            },
            success: function(response)
            {                    
                if (response.status == 'true') {
                    // $('#deleteRoleModel').modal('hide');
                    // toastr.clear();
                    // toastr.options.closeButton = true;
                    // toastr.options.timeOut = 0;
                    // toastr.success('Role has been deleted successfully!');
                    // setTimeout(function(){ 
                        location.reload(); 
                    // }, 3000);
                }
                else
                {
                    // $('#deleteRoleModel').modal('hide');
                    // toastr.clear();
                    // toastr.options.closeButton = true;
                    // toastr.options.timeOut = 0;
                    // toastr.success('Role has not been deleted successfully!');
                    // setTimeout(function(){ 
                        //  = '/admin/user/role/list'; 
                    // }, 3000);
                }
            }
        })
    })
})
</script>
@endpush