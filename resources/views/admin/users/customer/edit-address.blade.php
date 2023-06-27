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
                                                Edit Address
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
                        <h5 class="card-title">Address</h5>
                        <div class="divider"></div>                                                  
                        <form id="customerAddress" class="col-md-6" method="post" action="{{url('admin/customer/address/update')}}">
                            @csrf
                            <input type="hidden" name="address_id" value="{{$customer_address->id}}">  
                            <input type="hidden" name="customer_id" value="{{$customer_address->customer_id}}">                                     
                            <div class="form-group">
                                <label for="full_name">Full Name<span style="color:red">*</span></label>
                                <div>
                                    <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" value="{{$customer_address->fullname}}"/>                                        
                                </div>                                
                            </div>
                            <div class="form-group">
                                <label for="address_1">Address Line 1<span style="color:red">*</span></label>
                                <div>
                                    <input type="text" class="form-control" id="address_1" name="address_1" placeholder="Address 1" value="{{$customer_address->address_1}}"/>                                        
                                </div>                                
                            </div>
                            <div class="form-group">
                                <label for="address_2">Flat No.<span style="color:red"></span></label>
                                <div>
                                    <input type="text" class="form-control" id="address_2" name="address_2" placeholder="Flat No." value="{{$customer_address->address_2}}"/>                                        
                                </div>                                
                            </div>
                            <div class="form-group">
                                <label for="country">Country<span style="color:red">*</span></label>
                                <div>                                                    
                                    <select name="country" id="country" class="multiselect-dropdown form-control">
                                        <!-- <optgroup label="Select Country"> -->
                                            <!-- <option value=""></option>                                 -->
                                            @foreach($countries as $country)                                                                
                                                <option value="{{$country->id}}" {{($country->id == $address_arr['country']) ? 'selected' : ''}}>{{$country->name}}</option>
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
                                    <input type="text" class="form-control" name="states" id="states" placeholder="Block" value="{{$customer_address->state}}"/>
                                </div>                                
                            </div>
                            <div class="form-group">
                                <label for="cities">Road<span style="color:red"></span></label>
                                <div>
                                    <!-- <select name="cities" id="cities" class="multiselect-dropdown form-control">
                                        
                                    </select> -->
                                    <input type="text" class="form-control" name="cities" id="cities" placeholder="Road" value="{{$customer_address->city}}"/>
                                </div>                                
                            </div> 
                            <div class="form-group">
                                <label for="pincode">Building<span style="color:red">*</span></label>
                                <div>
                                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Building" value="{{$customer_address->pincode}}"/>                                        
                                </div>                                
                            </div>                                                                                                       
                            <div class="form-group">
                                <label for="address_type">Address Type<span style="color:red">*</span></label>
                                <div>
                                    <select name="address_type" id="address_type" class="form-control">
                                        <option value="1" {{($customer_address->address_type == 1) ? 'selected' : ''}}>Home</option>
                                        <option value="2" {{($customer_address->address_type == 2) ? 'selected' : ''}}>Office</option>
                                    </select>
                                </div>                                 
                            </div>                                                                                                       
                            <div class="form-group">
                                <label for="phone_1">Phone 1<span style="color:red">*</span></label>
                                <div>
                                    <input type="text" class="form-control" id="phone_1" name="phone_1" placeholder="Phone 1" value="{{$customer_address->phone1}}"/>                                        
                                </div>                                
                            </div>
                            <div class="form-group">
                                <label for="phone_2">Phone 2</label>
                                <div>
                                    <input type="text" class="form-control" id="phone_2" name="phone_2" placeholder="Phone 2" value="{{$customer_address->phone2}}"/>                                        
                                </div>                                
                            </div>
                            <div class="position-relative form-group">
                                <label for="is_default">Is Default</label>
                                <div class="position-relative form-group">
                                    <div>
                                        <div class="custom-radio custom-control custom-control-inline">
                                            <input type="radio" id="is_default" name="is_default" {{($customer_address->is_default == 1) ? 'checked' : ''}} class="custom-control-input" value="1">
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
    $('#country').css('width', '100%');
    $('#states').css('width', '100%');
    $('#cities').css('width', '100%');
    
    var address_arr = <?php echo json_encode($address_arr, true); ?>;    
    if($('#country').val())
    {
        var country = $('#country').val();
        $.ajax({
            // url:'/admin/customer/states/' + country,
            url:"{{url('/admin/customer/states').'/'}}" + country,
            method: "GET",
            success: function(response) {
                if(response)
                {                    
                    var output = '';                    
                    // output += '<optgroup label="Select States">';
                    output += '<option value=""></option>';                                                    
                    $.each(response, function(i, value){  
                        if(value.id == address_arr.state)
                        {
                            output += '<option value="'+value.id+'" selected>'+value.name+'</option>';  
                            localStorage.setItem("state_id", value.id);                              
                        } 
                        else
                        {
                            output += '<option value="'+value.id+'">'+value.name+'</option>';                                
                        }                     
                        
                    })                    
                    // output += '</optgroup>';
                    $('#states').html(output);
                }
                
            }
        })
    }

    var states = localStorage.getItem("state_id");
    if(states)
    {        
        $.ajax({
            url:"{{url('/admin/customer/cities').'/'}}" + states,            
            method: "GET",
            success: function(response) {
                if(response)
                {                    
                    var output = '';                    
                    // output += '<optgroup label="Select Cities">';
                    $.each(response, function(i, value){   
                        if(value.id == address_arr.city)
                        {
                            output += '<option value="'+value.id+'" selected>'+value.name+'</option>';                                                
                        } 
                        else
                        {
                            output += '<option value="'+value.id+'">'+value.name+'</option>';                                
                        }                       
                    })                    
                    // output += '</optgroup>';
                    $('#cities').html(output);
                }                
            }
        })
    }

    $('#country').on('change', function(){
        var country = $(this).val();        
        $.ajax({
            url:"{{url('/admin/customer/states').'/'}}" + country,            
            method: "GET",
            success: function(response) {
                if(response)
                {                    
                    var output = '';                    
                    output += '<optgroup label="Select States">';
                    output += '<option value=""></option>';                                                    
                    $.each(response, function(i, value){                        
                        output += '<option value="'+value.id+'">'+value.name+'</option>';                                
                    })                    
                    output += '</optgroup>';
                    $('#states').html(output);
                }
                
            }
        })
    })

    $('#states').on('change', function(){
        var cities = $(this).val();              
        $.ajax({
            url:"{{url('/admin/customer/cities').'/'}}" + cities,            
            method: "GET",
            success: function(response) {
                if(response)
                {                    
                    var output = '';                    
                    output += '<optgroup label="Select Cities">';
                    $.each(response, function(i, value){                        
                        output += '<option value="'+value.id+'">'+value.name+'</option>';                                
                    })                    
                    output += '</optgroup>';
                    $('#cities').html(output);
                }
                
            }
        })
    })
})
</script>
@endpush