@extends('admin.layouts.master')
<title>Photographer Profile | Alboumi</title>

@section('content')
<div class="app-container body-tabs-shadow fixed-header fixed-sidebar app-theme-gray">
    @include('admin.include.header')    
	<div class="app-main">
        @include('admin.include.sidebar') 
        <div class="app-main__outer">
            <div class="app-main__inner">                
                <div class="col-md-12 col-lg-12 col-xl-12">
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
                        <div class="dropdown-menu-header">
                            <div class="dropdown-menu-header-inner">
                                <!-- <div class="menu-header-image" style="background-image: url('../assets/images/dropdown-header/city2.jpg');"></div> -->
                                <div class="menu-header-content">
                                    <div class="avatar-icon-wrapper avatar-icon-lg">
                                        <div class="avatar-icon btn-hover-shine" style="width: 100px;height: 100px;">
                                        <img src="{{$photo}}" alt="photographer_profile"></div>
                                    </div>
                                    <div>
                                        <h5 class="menu-header-title" style="color: #22447f;">{{$users->firstname.' '.$users->lastname}}</h5>
                                        <h6 class="menu-header-subtitle" style="color: #22447f;">Photographer</h6>
                                    </div>
                                    <!-- <div class="menu-header-btn-pane">
                                        <button class="mr-2 btn btn-info btn-sm">Settings</button>
                                        <button class="btn-icon btn-icon-only btn btn-warning btn-sm"><i class="pe-7s-config btn-icon-wrapper"> </i></button>
                                    </div> -->
                                </div>
                            </div>
                        </div>
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
                    <div class="main-card mb-3 card" style="border-width: 0px">
                        <div class="card-body">
                            <!-- <h5 class="card-title">Profile Information</h5> -->
                            <form class="col-md-6 offset-md-3" method="POST" action="{{url('photographer/update-profile')}}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="photographer_id" value="{{$users->id}}">
                                <div class="form-group">
                                    <label for="photographer_firstname"><strong>First Name</strong></label>
                                    <div class="">
                                        <input type="text" name="photographer_firstname" class="form-control" value="{{$users->firstname}}">
                                        @if($errors->has('photographer_firstname'))
                                            <div class="custom-error">{{ $errors->first('photographer_firstname') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="photographer_lastname"><strong>Last Name</strong></label>
                                    <div class="">
                                        <input type="text" name="photographer_lastname" class="form-control" value="{{$users->lastname}}">
                                        @if($errors->has('photographer_lastname'))
                                            <div class="custom-error">{{ $errors->first('photographer_lastname') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="photographer_email"><strong>Email</strong></label>
                                    <div class="">
                                        <input type="email" name="photographer_email" disabled class="form-control" value="{{$users->email}}">                                        
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="photographer_email"><strong>Mobile</strong></label>
                                    <div class="">
                                        <input type="text" name="photographer_mobile" disabled class="form-control" value="{{$users->phone}}">                                        
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
                                <div class="form-group">
                                    <label for="exampleFile"><strong>Profile Photo</strong></label>
                                    <div class=""><input name="profile_photo" id="exampleFile" type="file" class="form-control-file">
                                        <small class="form-text text-muted">Change profile photo.</small>
                                    </div>
                                </div>
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
                                        <button class="mb-2 mr-2 btn-icon btn btn-secondary btn-sm btn-block">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
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
@include('admin.include.bottom')