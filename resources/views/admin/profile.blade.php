@extends('admin.layouts.master')
<title>Profile | Alboumi</title>

@section('content')
<div class="app-container body-tabs-shadow fixed-header fixed-sidebar app-theme-white closed-sidebar">
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
                                    <span class="d-inline-block">Profile</span>
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
                                                Update Profile  
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>                            
                        </div>                                                 
                    </div>
                </div>            
                <!-- <div class="col-md-12 col-lg-12 col-xl-12"> -->
                    <!-- <div class="card-shadow-primary card-border mb-3 card"> -->
                        <!-- <div class="dropdown-menu-header">
                            <div class="dropdown-menu-header-inner bg-primary">
                                <div class="menu-header-image" style="background-image: url('../assets/images/dropdown-header/city2.jpg');"></div>
                                <div class="menu-header-content btn-pane-right">
                                    <div class="avatar-icon-wrapper avatar-icon-lg">
                                        <div class="avatar-icon rounded btn-hover-shine" style="width: 90px;height: 90px;">
                                            <img src="../assets/images/avatars/12.jpg" alt="Avatar 5">
                                        </div>
                                    </div>
                                    <div><h5 class="menu-header-title">Jessica Walberg</h5><h6 class="menu-header-subtitle">Photographer</h6></div>                                   
                                </div>
                            </div>
                        </div>                         -->
                        <!-- <div class="dropdown-menu-header">
                            <div class="dropdown-menu-header-inner"> -->
                                <!-- <div class="menu-header-image" style="background-image: url('../assets/images/dropdown-header/city2.jpg');"></div> -->
                                <!-- <div class="menu-header-content">
                                    <div class="avatar-icon-wrapper avatar-icon-lg">
                                        <div class="avatar-icon btn-hover-shine" style="width: 100px;height: 100px;">
                                        <img src="{{$photo}}" alt="profile"></div>
                                    </div>
                                    <div>
                                        <h5 class="menu-header-title" style="color: #22447f;">{{$users->firstname.' '.$users->lastname}}</h5>
                                        <h6 class="menu-header-subtitle" style="color: #22447f;">Photographer</h6>
                                    </div> -->
                                    <!-- <div class="menu-header-btn-pane">
                                        <button class="mr-2 btn btn-info btn-sm">Settings</button>
                                        <button class="btn-icon btn-icon-only btn btn-warning btn-sm"><i class="pe-7s-config btn-icon-wrapper"> </i></button>
                                    </div> -->
                                <!-- </div>
                            </div>
                        </div> -->
                    <!-- </div> -->
                    <div class="col-md-6 offset-md-3">
                        @if(Session::has('msg'))                     
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ Session::get('msg') }}
                                <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    </div>                    
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">Profile</h5>
                            <div class="divider"></div> 
                            <form id="adminProfile" class="col-md-6" method="POST" action="{{url('admin/update-profile')}}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{$users->id}}">
                                <div class="form-group">
                                    <label for="firstname"><strong>First Name<span style="color:red">*</span></strong></label>
                                    <div class="">
                                        <input type="text" name="firstname" class="form-control" value="{{$users->firstname}}">
                                        @if($errors->has('firstname'))
                                            <div class="custom-error">{{ $errors->first('firstname') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="lastname"><strong>Last Name<span style="color:red">*</span></strong></label>
                                    <div class="">
                                        <input type="text" name="lastname" class="form-control" value="{{$users->lastname}}">
                                        @if($errors->has('lastname'))
                                            <div class="custom-error">{{ $errors->first('lastname') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email"><strong>Email</strong></label>
                                    <div class="">
                                        <input type="email" disabled name="email" class="form-control" value="{{$users->email}}">                                        
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email"><strong>Mobile<span style="color:red">*</span></strong></label>
                                    <div class="">
                                        <input type="text" name="mobile" class="form-control" value="{{$users->phone}}">                                        
                                    </div>
                                </div>
                                <!-- <div class="position-relative row form-group">
                                    <label for="examplePassword" class="col-sm-2 col-form-label">Password</label>
                                    <div class="col-sm-6">
                                        <input name="password" id="examplePassword" placeholder="password placeholder" type="password" class="form-control">
                                    </div>
                                </div> -->
                                <!-- <div class="position-relative row form-group">
                                    <label for="exampleSelect" class="col-sm-2 col-form-label">Select</label>
                                    <div class="col-sm-6">
                                        <select name="select" id="exampleSelect" class="form-control"></select>
                                    </div>
                                </div> -->
                                <!-- <div class="position-relative row form-group">
                                    <label for="exampleSelectMulti" class="col-sm-2 col-form-label">Select Multiple</label>
                                    <div class="col-sm-6">
                                        <select multiple="" name="selectMulti" id="exampleSelectMulti" class="form-control"></select>
                                    </div>
                                </div> -->
                                <!-- <div class="position-relative row form-group">
                                    <label for="exampleText" class="col-sm-2 col-form-label">Text Area</label>
                                    <div class="col-sm-6">
                                        <textarea name="text" id="exampleText" class="form-control"></textarea>
                                    </div>
                                </div> -->
                                <!-- <div class="form-group">
                                    <label for="exampleFile"><strong>Profile Photo<span style="color:red">*</span></strong></label>
                                    <div class=""><input name="profile_photo" id="profile_photo" type="file" class="form-control-file">
                                        <small class="form-text text-muted">Change profile photo.</small>
                                    </div>
                                </div> -->
                                <div class="form-group">
                                <label for="timezone_offset"><strong>Timezone</strong></label> 
                                <select name="timezone_offset" id="timezone-offset" class="multiselect-dropdown form-control">
                                    <optgroup label="Select Timezone">
                                        <option value="-12:00" {{($users->zone == "-12:00") ? 'selected' : ''}}>(GMT -12:00) Eniwetok, Kwajalein</option>
                                        <option value="-11:00" {{($users->zone == "-11:00") ? 'selected' : ''}}>(GMT -11:00) Midway Island, Samoa</option>
                                        <option value="-10:00" {{($users->zone == "-10:00") ? 'selected' : ''}}>(GMT -10:00) Hawaii</option>
                                        <option value="-09:30" {{($users->zone == "-09:30") ? 'selected' : ''}}>(GMT -9:30) Taiohae</option>
                                        <option value="-09:00" {{($users->zone == "-09:00") ? 'selected' : ''}}>(GMT -9:00) Alaska</option>
                                        <option value="-08:00" {{($users->zone == "-08:00") ? 'selected' : ''}}>(GMT -8:00) Pacific Time (US &amp; Canada)</option>
                                        <option value="-07:00" {{($users->zone == "-07:00") ? 'selected' : ''}}>(GMT -7:00) Mountain Time (US &amp; Canada)</option>
                                        <option value="-06:00" {{($users->zone == "-06:00") ? 'selected' : ''}}>(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
                                        <option value="-05:00" {{($users->zone == "-05:00") ? 'selected' : ''}}>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
                                        <option value="-04:30" {{($users->zone == "-04:30") ? 'selected' : ''}}>(GMT -4:30) Caracas</option>
                                        <option value="-04:00" {{($users->zone == "-04:00") ? 'selected' : ''}}>(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
                                        <option value="-03:30" {{($users->zone == "-03:30") ? 'selected' : ''}}>(GMT -3:30) Newfoundland</option>
                                        <option value="-03:00" {{($users->zone == "-03:00") ? 'selected' : ''}}>(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
                                        <option value="-02:00" {{($users->zone == "-02:00") ? 'selected' : ''}}>(GMT -2:00) Mid-Atlantic</option>
                                        <option value="-01:00" {{($users->zone == "-01:00") ? 'selected' : ''}}>(GMT -1:00) Azores, Cape Verde Islands</option>
                                        <option value="+00:00" {{($users->zone == "+00:00") ? 'selected' : ''}}>(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
                                        <option value="+01:00" {{($users->zone == "+01:00") ? 'selected' : ''}}>(GMT +1:00) Brussels, Copenhagen, Madrid, Paris</option>
                                        <option value="+02:00" {{($users->zone == "+02:00") ? 'selected' : ''}}>(GMT +2:00) Kaliningrad, South Africa</option>
                                        <option value="+03:00" {{($users->zone == "+03:00") ? 'selected' : ''}}>(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
                                        <option value="+03:30" {{($users->zone == "+03:30") ? 'selected' : ''}}>(GMT +3:30) Tehran</option>
                                        <option value="+04:00" {{($users->zone == "+04:00") ? 'selected' : ''}}>(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
                                        <option value="+04:30" {{($users->zone == "+04:30") ? 'selected' : ''}}>(GMT +4:30) Kabul</option>
                                        <option value="+05:00" {{($users->zone == "+05:00") ? 'selected' : ''}}>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                                        <option value="+05:30" {{($users->zone == "+05:30") ? 'selected' : ''}}>(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
                                        <option value="+05:45" {{($users->zone == "+05:45") ? 'selected' : ''}}>(GMT +5:45) Kathmandu, Pokhara</option>
                                        <option value="+06:00" {{($users->zone == "+06:00") ? 'selected' : ''}}>(GMT +6:00) Almaty, Dhaka, Colombo</option>
                                        <option value="+06:30" {{($users->zone == "+06:30") ? 'selected' : ''}}>(GMT +6:30) Yangon, Mandalay</option>
                                        <option value="+07:00" {{($users->zone == "+07:00") ? 'selected' : ''}}>(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
                                        <option value="+08:00" {{($users->zone == "+08:00") ? 'selected' : ''}}>(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
                                        <option value="+08:45" {{($users->zone == "+08:45") ? 'selected' : ''}}>(GMT +8:45) Eucla</option>
                                        <option value="+09:00" {{($users->zone == "+09:00") ? 'selected' : ''}}>(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                                        <option value="+09:30" {{($users->zone == "+09:30") ? 'selected' : ''}}>(GMT +9:30) Adelaide, Darwin</option>
                                        <option value="+10:00" {{($users->zone == "+10:00") ? 'selected' : ''}}>(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
                                        <option value="+10:30" {{($users->zone == "+10:30") ? 'selected' : ''}}>(GMT +10:30) Lord Howe Island</option>
                                        <option value="+11:00" {{($users->zone == "+11:00") ? 'selected' : ''}}>(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
                                        <option value="+11:30" {{($users->zone == "+11:30") ? 'selected' : ''}}>(GMT +11:30) Norfolk Island</option>
                                        <option value="+12:00" {{($users->zone == "+12:00") ? 'selected' : ''}}>(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
                                        <option value="+12:45" {{($users->zone == "+12:45") ? 'selected' : ''}}>(GMT +12:45) Chatham Islands</option>
                                        <option value="+13:00" {{($users->zone == "+13:00") ? 'selected' : ''}}>(GMT +13:00) Apia, Nukualofa</option>
                                        <option value="+14:00" {{($users->zone == "+14:00") ? 'selected' : ''}}>(GMT +14:00) Line Islands, Tokelau</option>
                                    </optgroup>
                                </select>   
                            </div>
                            <input type="hidden" name="timezone" id="timezone">
                                <!-- <fieldset class="position-relative row form-group">
                                    <legend class="col-form-label col-sm-2">Radio Buttons</legend>
                                    <div class="col-sm-6">
                                        <div class="position-relative form-check"><label class="form-check-label"><input name="radio2" type="radio" class="form-check-input"> Option one is this and that—be sure to include why it's great</label></div>
                                        <div class="position-relative form-check"><label class="form-check-label"><input name="radio2" type="radio" class="form-check-input"> Option two can be something else and selecting it will deselect option
                                            one</label></div>
                                        <div class="position-relative form-check disabled"><label class="form-check-label"><input name="radio2" disabled="" type="radio" class="form-check-input"> Option three is disabled</label></div>
                                    </div>
                                </fieldset> -->
                                <!-- <div class="position-relative row form-group"><label for="checkbox2" class="col-sm-2 col-form-label">Checkbox</label>
                                    <div class="col-sm-6">
                                        <div class="position-relative form-check"><label class="form-check-label"><input id="checkbox2" type="checkbox" class="form-check-input"> Check me out</label></div>
                                    </div>
                                </div> -->
                                <div class="">
                                    <div class="" >
                                        <button class="mb-2 mr-2 btn-icon btn btn-primary btn-sm btn-block">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <!-- </div>                                           -->
            </div>
            @include('admin.include.footer')
        </div>
    </div>    
</div>
@endsection
<div class="app-drawer-overlay d-none animated fadeIn"></div>
@push('scripts')
<script>
$(document).ready(function(){               
    $('#timezone').val(moment.tz.guess());        
})
</script>
@endpush