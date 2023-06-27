@extends('admin.layouts.master')
<title>Add User | Alboumi</title>

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
                                    <span class="d-inline-block">Users</span>
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
                                                <a href="javascript:void(0)">Users</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/user/list')}}">User List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add User
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
                        <h5 class="card-title">Add User</h5>
                        <!-- @if(Session::has('msg'))                     
                            <div class="alert {{(Session::get('alert-class') == true) ? 'alert-success' : 'alert-danger'}} alert-dismissible fade show" role="alert">
                                {{ Session::get('msg') }}
                                <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif                                                    -->
                        <form id="addNewUser" method="post" action="{{url('admin/user/add')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <div>
                                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter first name"/>                                        
                                        </div>
                                    </div>    
                                    <div class="form-group">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <div>
                                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter last name"/>                                        
                                        </div>                                
                                    </div>   
                                    <div class="form-group">
                                        <label for="phone">Phone <span class="text-danger">*</span></label>
                                        <div>
                                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number"/>                                        
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password <span class="text-danger">*</span></label>
                                        <div>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password"/>                                        
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <div>
                                            <input type="text" class="form-control" id="email" name="email" placeholder="Enter email"/>                                        
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="select_role">Role <span class="text-danger">*</span></label>                                
                                        <select name="select_role" id="select_role" class="multiselect-dropdown form-control">
                                            <optgroup label="Select Role">
                                                @foreach($roles as $role)
                                                    @if($role->role_type != "super_admin")
                                                        <option value="{{$role->id}}">{{$role->role_title}}</option>
                                                    @endif
                                                @endforeach                                            
                                            </optgroup>
                                        </select>                                
                                    </div>
                                    <div class="form-group">
                                        <label for="timezone_offset">Timezone</label> 
                                        <select name="timezone_offset" id="timezone-offset" class="multiselect-dropdown form-control">
                                            <optgroup label="Select Timezone">
                                                <option value="-12:00">(GMT -12:00) Eniwetok, Kwajalein</option>
                                                <option value="-11:00">(GMT -11:00) Midway Island, Samoa</option>
                                                <option value="-10:00">(GMT -10:00) Hawaii</option>
                                                <option value="-09:50">(GMT -9:30) Taiohae</option>
                                                <option value="-09:00">(GMT -9:00) Alaska</option>
                                                <option value="-08:00">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
                                                <option value="-07:00">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
                                                <option value="-06:00">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
                                                <option value="-05:00">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
                                                <option value="-04:50">(GMT -4:30) Caracas</option>
                                                <option value="-04:00">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
                                                <option value="-03:50">(GMT -3:30) Newfoundland</option>
                                                <option value="-03:00">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
                                                <option value="-02:00">(GMT -2:00) Mid-Atlantic</option>
                                                <option value="-01:00">(GMT -1:00) Azores, Cape Verde Islands</option>
                                                <option value="+00:00">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
                                                <option value="+01:00">(GMT +1:00) Brussels, Copenhagen, Madrid, Paris</option>
                                                <option value="+02:00">(GMT +2:00) Kaliningrad, South Africa</option>
                                                <option value="+03:00">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
                                                <option value="+03:50">(GMT +3:30) Tehran</option>
                                                <option value="+04:00">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
                                                <option value="+04:50">(GMT +4:30) Kabul</option>
                                                <option value="+05:00">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                                                <option value="+05:50">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
                                                <option value="+05:75">(GMT +5:45) Kathmandu, Pokhara</option>
                                                <option value="+06:00">(GMT +6:00) Almaty, Dhaka, Colombo</option>
                                                <option value="+06:50">(GMT +6:30) Yangon, Mandalay</option>
                                                <option value="+07:00">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
                                                <option value="+08:00">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
                                                <option value="+08:75">(GMT +8:45) Eucla</option>
                                                <option value="+09:00">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                                                <option value="+09:50">(GMT +9:30) Adelaide, Darwin</option>
                                                <option value="+10:00">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
                                                <option value="+10:50">(GMT +10:30) Lord Howe Island</option>
                                                <option value="+11:00">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
                                                <option value="+11:50">(GMT +11:30) Norfolk Island</option>
                                                <option value="+12:00">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
                                                <option value="+12:75">(GMT +12:45) Chatham Islands</option>
                                                <option value="+13:00">(GMT +13:00) Apia, Nukualofa</option>
                                                <option value="+14:00">(GMT +14:00) Line Islands, Tokelau</option>
                                            </optgroup>
                                        </select>   
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm Password <span style="color:red">*</span></label>
                                        <div>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm password"/>                                        
                                        </div>
                                    </div>
                                </div>
                            </div>                                                                                     
                            <input type="hidden" name="timezone" id="timezone">                                                    
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="add_role" value="add_role">Add User</button>
                                <a href="{{url('/admin/user/list')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
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
@push('scripts')
<script>
$(document).ready(function(){
    $('#timezone').val(moment.tz.guess());        
})
</script>
@endpush