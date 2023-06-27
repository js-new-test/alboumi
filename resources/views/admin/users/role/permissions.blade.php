@extends('admin.layouts.master')
<title>Permission | Alboumi</title>

@section('content')
<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
    @include('admin.include.header')    
	<div class="app-main">
        @include('admin.include.sidebar') 
        <div class="app-main__outer" style="width: 100%;">
            <div class="app-main__inner">
                <div class="app-page-title app-page-title-simple">
                    <div class="page-title-heading">                            
                        <div>
                            <div class="page-title-head center-elem">
                                <span class="d-inline-block pr-2">
                                    <i class="lnr-users opacity-6"></i>
                                </span>
                                <span class="d-inline-block">Permission</span>
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
                                            Permission
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>                            
                    </div>
                </div>                                                            
                <div class="main-card mb-3 card">
                    <div class="card-body">  
                        <!-- <h5 class="card-title">List Of Permissions</h5> -->                        
                        <div style="width:100%;height:387px;overflow-x:auto;overflow-y:auto;">
                            <table style="white-space:nowrap;margin-bottom: 0;" class="table table-striped table-bordered table-hover">
                                @foreach($arrPermissions as $group => $permissionGroup)                            
                                <tr>                                
                                    <th>{{ Str::studly($group) }}</th> 
                                    @foreach($permissionGroup as $permission)
                                        <td style="font-size:14px;">
                                            @if($role->permissions()->find($permission->id))
                                            <div class="row">
                                                <div class="col-sm-12" style="display: flex;justify-content: space-between;align-items:center">
                                                    <label style="margin-bottom: 0;" for="permission_title">{{ $permission->permission_title}}</label>
                                                    <button type="button" style="margin-right: 0" data-permId="{{ $permission->id }}" class="btn btn-sm btn-toggle active toggle-is-active-switch permission_click" data-toggle="button" aria-pressed="true" autocomplete="off">
                                                    <div class="handle"></div>
                                                    </button>
                                                </div>
                                            </div>
                                            @else
                                            <div class="row">
                                                <div class="col-sm-12" style="display: flex;justify-content: space-between;align-items:center">
                                                    <label style="margin-bottom: 0;" for="permission_title">{{ $permission->permission_title}}</label>
                                                    <button type="button" style="margin-right: 0" data-permId="{{ $permission->id }}" class="btn btn-sm btn-toggle toggle-is-active-switch permission_click" data-toggle="button" aria-pressed="false" autocomplete="off">
                                                    <div class="handle"></div>
                                                    </button>
                                                </div>
                                            </div>
                                            @endif
                                        </td>                                                               
                                    @endforeach                                                                      
                                </tr>
                                @endforeach                            
                            </table>
                        </div>                    
                    </div>
                </div>                
            </div>
            @include('admin.include.footer')
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function() {
    var role_id = "{{$role->id}}";             
    $('.permission_click').on('click', function(){
        var permId = $(this).attr('data-permId');        
        $('#loaderimage').css("display", "block");
        $('#loadingorverlay').css("display", "block");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
                url:"{{url('/admin/role/permissions/')}}" + "/" + role_id,
                type: "post",
                async: true,
                data: 'permission=' + permId + '&_token={{ csrf_token() }}',
                success: function (response) {                
                    $('#loaderimage').css("display", "none");
                    $('#loadingorverlay').css("display", "none");
                    if (response == 'success') {
                        toastr.clear();
                        toastr.options.closeButton = true;
                        toastr.options.timeOut = 0;
                        toastr.success('Permission has been updated successfully!'); 
                    setTimeout(function(){ 
                        toastr.clear();
                    }, 5000);                   
                } else {
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success('Permission has not been updated!')
                }

                }
        });
    })
});
</script>
@endpush
