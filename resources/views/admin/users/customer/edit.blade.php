@extends('admin.layouts.master')
<title>Edit User</title>

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
                                    <span class="d-inline-block">Customer</span>
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
                                                <a href="javascript:void(0);">Customer</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                <a href="{{url('/admin/customer/list')}}">Customer List</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                Edit Customer
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>                            
                        </div>                          
                    </div>
                </div>                                                                           
                <!-- <div id="common_msg"> -->
                    @if(Session::has('msg'))                        
                        <div class="alert {{(Session::get('alert_type') == true) ? 'alert-success' : 'alert-danger'}} alert-dismissible fade show" role="alert">
                            {{ Session::get('msg') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                <!-- </div> -->
                <div class="mb-3 card">                    
                    <div class="card-body">
                        <ul class="tabs-animated-shadow nav-justified tabs-animated nav" id="tabMenu">
                            <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-c1-0" data-toggle="tab" href="#tab-account">
                                    <span class="nav-text">Account</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-c1-1" data-toggle="tab" href="#tab-address">
                                    <span class="nav-text">Addresses</span>
                                </a>
                            </li>
                            <!-- <li class="nav-item">
                                <a role="tab" class="nav-link " id="tab-c1-2" data-toggle="tab" href="#tab-animated1-2">
                                    <span class="nav-text">Bank Details</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-c1-3" data-toggle="tab" href="#tab-animated1-3">
                                    <span class="nav-text">Bank Details Log</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-c1-4" data-toggle="tab" href="#tab-animated1-4">
                                    <span class="nav-text">Order Summary</span>
                                </a>
                            </li> -->
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab-account" role="tabpanel">
                                <div style="margin-top:20px;">
                                    <h5 class="card-title">Update User</h5>
                                    <div class="divider"></div>  
                                </div>
                                <form id="updateCustomer" method="post" action="{{url('admin/customer/update')}}">
                                    @csrf
                                    <input type="hidden" name="customer_id" value="{{$customers->id}}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="first_name">First Name</label>
                                                <div>
                                                    <input type="text" class="form-control" value="{{$customers->first_name}}" readonly />                                        
                                                </div>
                                            </div>    
                                            <div class="form-group">
                                                <label for="last_name">Last Name</label>
                                                <div>
                                                    <input type="text" class="form-control" value="{{$customers->last_name}}" readonly />                                        
                                                </div>                                
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Mobile</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-fw" aria-hidden="true" title="Copy to use mobile"></i></span></div>
                                                    <input type="text" id="phone" name="phone" class="form-control" value="{{$customers->mobile}}"  readonly >
                                                </div>
                                            </div>
                                            <div class="form-group" style="display:none">
                                                <label for="os_name">OS Name</label>
                                                <div>
                                                    <input type="text" class="form-control" value="{{$customers->os_name}}" readonly />                                        
                                                </div>                                
                                            </div>
                                            <div class="form-group">
                                                <label for="os_name">Date Of Birth</label>
                                                <div>
                                                    <input type="text" class="form-control" value="{{$customers->date_of_birth}}" readonly />                                        
                                                </div>                                
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <div>
                                                    <input type="text" class="form-control" placeholder="Enter email" value="{{$customers->email}}" readonly />                                        
                                                </div>
                                            </div> 
                                            <div class="form-group mt-4">
                                                <label for="email">Gender</label>
                                                <div class="form-group">
                                                    <div>
                                                        <div class="custom-radio custom-control custom-control-inline">
                                                            <input type="radio" id="exampleCustomRadio" name="gender" class="custom-control-input" disabled value="Male" {{($customers->gender == "Male") ? 'checked' : ''}}>
                                                            <label class="custom-control-label" for="exampleCustomRadio">Male</label>
                                                        </div>
                                                        <div class="custom-radio custom-control custom-control-inline">
                                                            <input type="radio" id="exampleCustomRadio2" name="gender" class="custom-control-input" disabled value="Female" {{($customers->gender == "Female") ? 'checked' : ''}}>
                                                            <label class="custom-control-label" for="exampleCustomRadio2">Female</label>
                                                        </div>
                                                    </div>
                                                </div>                                        
                                            </div>
                                            <div class="form-group mt-4">
                                                <label for="ip_address">IP Address</label>
                                                <div>
                                                    <input type="text" class="form-control"value="{{$customers->ip_address}}" readonly />                                        
                                                </div>
                                            </div> 
                                            <div class="form-group mt-4" style="display:none">
                                                <label>Browser Name & Version</label>
                                                <div>
                                                    <input type="text" class="form-control" value="{{$customers->browser_name.' - '.$customers->browser_version }}" readonly />                                        
                                                </div>
                                            </div> 
                                        </div>
                                    </div>                                                                                                                                                                                                               
                                    <div class="form-group" style="display:none">
                                        <button type="submit" class="btn btn-primary" name="update_role" value="update_role">Update</button>                                        
                                        <a href="{{url('/admin/customer/list')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="tab-address" role="tabpanel">                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div style="margin-top:20px;">
                                            <h5 class="card-title">Address</h5>
                                            <div class="divider"></div>  
                                        </div>
                                        
                                        <form id="customerAddress" method="post" action="{{url('admin/customer/address')}}">
                                            @csrf
                                            <input type="hidden" name="customer_id" value="{{$customers->id}}">                                       
                                            <div class="form-group">
                                                <label for="full_name">Full Name<span style="color:red">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name"/>                                        
                                                </div>                                
                                            </div>
                                            <div class="form-group">
                                                <label for="address_1">Address Line 1<span style="color:red">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="address_1" name="address_1" placeholder="Address 1"/>                                        
                                                </div>                                
                                            </div>
                                            <div class="form-group">
                                                <label for="address_2">Flat No.<span style="color:red"></span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="address_2" name="address_2" placeholder="Flat No."/>                                        
                                                </div>                                
                                            </div>
                                            <div class="form-group">
                                                <label for="country">Country<span style="color:red">*</span></label>
                                                <div>                                                    
                                                    <select name="country" id="country" class="multiselect-dropdown form-control">
                                                        <!-- <optgroup label="Select Country"> -->
                                                            <!-- <option value=""></option>                                 -->
                                                            @foreach($countries as $country)                                                                
                                                                <option value="{{$country->id}}">{{$country->name}}</option>
                                                            @endforeach                                       
                                                        <!-- </optgroup> -->
                                                    </select>
                                                </div>                                
                                            </div>
                                            <div class="form-group">
                                                <label for="state">Block<span style="color:red"></span></label>
                                                <div>
                                                    <!-- <select name="states" id="states" class="multiselect-dropdown form-control">
                                                        
                                                    </select> -->
                                                    <input type="text" class="form-control" name="states" id="states" placeholder="Block" />
                                                </div>                                
                                            </div>
                                            <div class="form-group">
                                                <label for="cities">Road<span style="color:red"></span></label>
                                                <div>
                                                    <!-- <select name="cities" id="cities" class="multiselect-dropdown form-control">
                                                    
                                                    </select> -->
                                                    <input type="text" class="form-control" name="cities" id="cities" placeholder="Road" />
                                                </div>                                
                                            </div> 
                                            <div class="form-group">
                                                <label for="pincode">Building<span style="color:red">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Building"/>                                        
                                                </div>                                
                                            </div>                                                                                                       
                                            <div class="form-group">
                                                <label for="address_type">Address Type<span style="color:red">*</span></label>
                                                <div>
                                                    <select name="address_type" id="address_type" class="form-control">
                                                        <option value="1">Home</option>
                                                        <option value="2">Office</option>
                                                    </select>
                                                </div>                                 
                                            </div>                                                                                                       
                                            <div class="form-group">
                                                <label for="phone_1">Phone 1<span style="color:red">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="phone_1" name="phone_1" placeholder="Phone 1"/>                                        
                                                </div>                                
                                            </div>
                                            <div class="form-group">
                                                <label for="phone_2">Phone 2</label>
                                                <div>
                                                    <input type="text" class="form-control" id="phone_2" name="phone_2" placeholder="Phone 2"/>                                        
                                                </div>                                
                                            </div>
                                            <div class="position-relative form-group">
                                                <label for="is_default">Is Default</label>
                                                <div class="position-relative form-group">
                                                    <div>
                                                        <div class="custom-radio custom-control custom-control-inline">
                                                            <input type="radio" id="is_default" name="is_default" class="custom-control-input" value="1">
                                                            <label class="custom-control-label" for="is_default">Yes</label>
                                                        </div>
                                                        <div class="custom-radio custom-control custom-control-inline">
                                                            <input type="radio" id="is_default2" name="is_default" class="custom-control-input" value="0">
                                                            <label class="custom-control-label" for="is_default2">No</label>
                                                        </div>
                                                    </div>
                                                </div>                                        
                                            </div> 

                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary" name="update_role" value="update_role">Save</button>                                        
                                                <a href="{{url('/admin/customer/list')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <div style="margin-top:20px;">
                                            <h5 class="card-title">Available Addresses</h5>
                                            <div class="divider"></div>  
                                        </div>
                                        <div id="address_list_section">
                                            @if(count($address_arr) > 0)
                                                @foreach($address_arr as $address)
                                                <div class="card-shadow-secondary border mb-3 card card-body border-secondary">
                                                    <h5 class="card-title">{{$address['customer_name']}}</h5>
                                                    <p>{{$address['address_1']}}, {{$address['address_2']}} </br> {{$address['city']}} - {{$address['pincode']}}, {{$address['state']}}, {{$address['country']}}.</p>                                                
                                                    @if($address['is_default'] == 1)                                                                                                    
                                                        <div class="custom-checkbox custom-control custom-control-inline"><input type="checkbox" checked  disabled="" id="exampleCustomInline2" class="custom-control-input"><label class="custom-control-label" for="exampleCustomInline2">Is Default</label></div>
                                                    @endif
                                                    <div class="divider"></div>  
                                                    <div>
                                                        <a href="{{url('/admin/customer/address/edit').'/'.$address['address_id'] }}"><button class="mb-2 mr-2 btn btn-shadow btn-outline-primary">Edit</button></a>
                                                        <button type="button" data-address-id="{{$address['address_id']}}" class="mb-2 mr-2 btn btn-shadow btn-outline-danger deleteAddress">Delete</button>
                                                    </div>                                                
                                                </div>
                                                @endforeach
                                            @else
                                                <div class="card-shadow-secondary border mb-3 card card-body border-secondary">                                            
                                                    No addresses found!
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                            <div class="tab-pane" id="tab-animated1-2" role="tabpanel">
                                
                            </div>
                            <div class="tab-pane" id="tab-animated1-3" role="tabpanel">
                                
                            </div>
                            <div class="tab-pane" id="tab-animated1-4" role="tabpanel">
                                
                            </div>
                        </div>
                    </div>
                </div>                            
            </div>
            @include('admin.include.footer')
        </div>
    </div>
    <!-- Modal Start -->
    <div class="modal fade" id="customerDeleteAddressModel" tabindex="-1" role="dialog" aria-labelledby="customerDeleteAddressModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerDeleteAddressModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="address_id" id="address_id">
                    <p class="mb-0" id="message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="customerDeleteAddress">Yes</button>
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

    //redirect to specific tab
    var tab = "{{$tab}}";
    var hash = window.location.hash;    
    if(hash)
    {
        $('#tabMenu a[href="'+hash+'"]').tab('show')        
    }
    else
    {
        $('#tabMenu a[href="'+tab+'"]').tab('show')    
    }
    
    
    $('#country').on('change', function(){
        var country = $(this).val();
        $.ajax({
            url:'/admin/customer/states/' + country,
            method: "GET",
            success: function(response) {
                if(response)
                {                    
                    var output = '';                    
                    // output += '<optgroup label="Select States">';
                    output += '<option value=""></option>';                                                    
                    $.each(response, function(i, value){                        
                        output += '<option value="'+value.id+'">'+value.name+'</option>';                                
                    })                    
                    // output += '</optgroup>';
                    $('#states').html(output);
                }
                
            }
        })
    })

    $('#states').on('change', function(){
        var cities = $(this).val();        
        $.ajax({
            url:'/admin/customer/cities/' + cities,
            method: "GET",
            success: function(response) {
                if(response)
                {                    
                    var output = '';                    
                    // output += '<optgroup label="Select Cities">';
                    $.each(response, function(i, value){                        
                        output += '<option value="'+value.id+'">'+value.name+'</option>';                                
                    })                    
                    // output += '</optgroup>';
                    $('#cities').html(output);
                }
                
            }
        })
    })

    $(document).on('click', '.deleteAddress', function(){
        var address_id = $(this).attr('data-address-id');
        localStorage.setItem('activeTab', '#tab-address');
        var message = "Are you sure you want to delete ?";
        $('#customerDeleteAddressModel').on('show.bs.modal', function(e){
            $('#address_id').val(address_id);
            $('#message').text(message);
        });
        $('#customerDeleteAddressModel').modal('show');
    })

    $('#customerDeleteAddress').click(function(){
        var address_id = $('#address_id').val();
        $.ajax({
            url:'/admin/customer/address/delete/' + address_id,
            method: 'GET',
            success: function(response)
            {                                
                if(response.status == 'true')
                {                                        
                    $('#customerDeleteAddressModel').modal('hide');
                    $('#address_list_section').html('');                    
                    $('#tabMenu a[href="'+response.tab+'"]').tab('show')
                    var output = '';
                    if(response.address_arr.length > 0)
                    {
                        $.each(response.address_arr, function(i, v){                            
                            output += '<div class="card-shadow-secondary border mb-3 card card-body border-secondary">'
                            output += '<h5 class="card-title">'+v.customer_name+'</h5>'
                            output += '<p>'+v.address_1+', '+v.address_2+' </br> '+v.city+' - '+v.pincode+', '+v.state+', '+v.country+'.</p>'
                            if(v.is_default == 1)
                            {
                                output += '<div class="custom-checkbox custom-control custom-control-inline"><input type="checkbox" checked  disabled="" id="exampleCustomInline2" class="custom-control-input"><label class="custom-control-label" for="exampleCustomInline2">Is Default</label></div>'
                            }                            
                            output += '<div class="divider"></div>'
                            output += '<div>'
                            output += '<a href="'+window.location.origin+'/admin/customer/address/edit/'+v.address_id+'"><button class="mb-2 mr-2 btn btn-shadow btn-outline-primary">Edit</button></a>'
                            output += '<button type="button" data-address-id="'+v.address_id+'" class="mb-2 mr-2 btn btn-shadow btn-outline-danger deleteAddress">Delete</button>'
                            output += '</div>'
                            output += '</div>'
                        })                                           
                    }
                    else
                    {
                        output += '<div class="card-shadow-secondary border mb-3 card card-body border-secondary">';
                        output += 'No addresses found!';
                        output += '</div>';
                    }
                    $('#address_list_section').html(output);
                    // $('#common_msg').html('');
                    // $('#common_msg').html('<div class="alert alert-success alert-dismissible fade show" role="alert">'
                    // +'Address deleted successfully!'
                    // +'<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
                    // +'<span aria-hidden="true">&times;</span>'
                    // +'</button>'
                    // +'</div>');
                }
                else
                {
                    toastr.clear();
                    toastr.options.closeButton = true;
                    toastr.options.timeOut = 0;
                    toastr.success('Falied to delete address!'); 
                }
            }
        })
    })
    
})
</script>
@endpush