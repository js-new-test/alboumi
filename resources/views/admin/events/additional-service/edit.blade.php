@extends('admin.layouts.master')
<title>Edit Additional Service | Alboumi</title>

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
                                    <span class="d-inline-block">Additional Service</span>
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
                                                <a href="javascript:void(0);">Additional Service</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/additional-service')}}">Additional Service List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Edit Additional Service  
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
                        <h5 class="card-title">Update Additional Service</h5> 
                        @if(Session::has('msg'))                     
                            <div class="alert {{ (Session::get('alert-class') == true) ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
                                {{ Session::get('msg') }}
                                <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif 
                        <form id="addAdditionalService" method="post" action="{{url('/admin/additional-service/update')}}" enctype="multipart/form-data">
                            @csrf  
                            <input type="hidden" name="service_id" id="service_id" value="{{$services->id}}">                             
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="language"><strong>Name<span style="color:red">*</span></strong></label>   
                                        <input type="text" name="addit_service_name" id="addit_service_name" value="{{$services->name}}" class="form-control">                                                                                     
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service_description"><strong>Description<span style="color:red">*</span></strong></label>
                                        <textarea name="service_description" id="service_description" class="form-control" cols="30" rows="2">{{$services->text}}</textarea>
                                    </div>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service_image_update"><strong>Image<span style="color:red">*</span></strong></label>
                                        <div class=""><input name="service_image_update" id="service_image_update" type="file" class="form-control-file">
                                        <small class="form-text text-muted">Image size should be {{config('app.service_image.height')}} X {{config('app.service_image.width')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="width"></small></small>
                                            <small class="form-text text-muted">height = <small id="height"></small></small>
                                        </div>
                                    </div>
                                    <div style="position: relative;width: 100px;height: 100px;">
                                        <img style="width: 130px;height: 85px;position: absolute;" src="{{asset('public/assets/images/additional-service').'/'.$services->image}}" alt="current_image">
                                    </div>
                                </div>
                                <div class="col-md-6">                                    
                                    <div class="form-group">
                                        <label for="samples_update"><strong>Samples<span style="color:red">*</span></strong></label>
                                        <div>
                                            <input type="file" name="samples_update[]" id="samples_update" class="form-control-file" multiple>                                                
                                            <small class="form-text text-muted">Allowed file extensions .jpeg, .jpg, .png.</small>
                                            <small class="form-text text-muted">Image size should be {{config('app.service_sample_image.height')}} X {{config('app.service_sample_image.width')}} px.</small>

                                        </div>
                                    </div>                                     
                                    <div style="width:100%;overflow-x: auto;">                                        
                                        @foreach($services_samples as $image)
                                            <!-- <img style="width: 80px;height: 80px;" src="{{asset('public/assets/images/additional-service/samples').'/'.$image->image}}" alt="current_image">                                             -->
                                            <div class="image_container remove_samples_section">                                                
                                                <img src="{{asset('public/assets/images/additional-service/samples').'/'.$image->image}}" alt="Avatar" class="service_sample_image" style="width: 80px;height: 80px;">
                                                <div class="middle">
                                                    <div class="text">
                                                        <button type="button" class="btn btn-primary btn-sm" id="delete_sample_image" data-sample-id="{{$image->id}}">
                                                            <span aria-hidden="true">X</span>
                                                        </button>
                                                    </div>
                                                </div>                                                                             
                                            </div>
                                        @endforeach                                   
                                    </div>                                    
                                </div>
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-6">                                    
                                    <div class="form-group">
                                        <label for="price" class="font-weight-bold">Price ({{ $default_currency->currency_symbol }})</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="number" class="form-control" id="price" name="price" placeholder="Enter Price"  value="{{$services->price}}"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">                                    
                                    @if(isset($global_languages))
                                    <div class="form-group">
                                        <label for="language"><strong>Select Language<span style="color:red">*</span></strong></label>   
                                        <select name="language" id="language" class="multiselect-dropdown form-control">
                                            <optgroup label="Select Language">
                                                @foreach($global_languages as $lang)                                            
                                                    <option value="{{$lang->gl_id}}" {{($services->language_id == $lang->gl_id) ? 'selected' : ''}}>{{$lang->lang_name}}</option>                                            
                                                @endforeach                                            
                                            </optgroup>
                                        </select>                                                                                       
                                    </div>
                                    @else
                                        <div class="form-group">                                
                                            <input type="hidden" name="language" id="language" value="{{$global_language->gl_id}}">                                                                                     
                                        </div>
                                    @endif
                                </div>
                            </div>  
                            <label for="Requirements"><strong>Requirements<span style="color:red">*</span></strong></label>
                            <?php $counter = 0; ?>
                            @foreach($services_requirement as $s_requirement)
                            <div class="row remove_current_div">
                                <div class="col-md-4">
                                    <div class="form-group">                                
                                        <input type="text" name="update_requirement_labels[{{$s_requirement->id}}]" id="requirement_labels<?php echo $counter; ?>" data-inc-val="<?php echo $counter; ?>" placeholder="Requirements" class="form-control dynamic_requirement_field_validation" value="{{$s_requirement->requirements}}">
                                        <span id="requirements_labels_error<?php echo $counter; ?>"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">                                
                                        <input type="text" name="update_requirements[{{$s_requirement->id}}]" id="requirements<?php echo $counter; ?>" data-inc-val="<?php echo $counter; ?>" placeholder="Value" value="{{$s_requirement->value}}" class="form-control dynamic_value_field_validation">
                                        <span id="requirements_error<?php echo $counter; ?>"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">                                                                 
                                        <button type="button" data-id="{{$s_requirement->id}}" class="mb-2 mr-2 btn-icon btn-icon-only btn-square btn btn-primary delete_requirement"><i class="fa fa-fw" aria-hidden="true" title="Copy to use close"></i></button>                               
                                    </div>
                                </div>
                            </div>
                            <?php $counter++; ?>      
                            @endforeach 
                            <div id="dynamic_requirements_textbox"></div>
                            <div style="margin-bottom: 30px;">
                                <button type="button" class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm" id="edit_requirements"><i class="fa fa-plus btn-icon-wrapper"> </i>Add</button>
                            </div>                                              
                            <!-- <div class="form-group">
                                <label for="service_act_deact"><strong>Status</strong></label>
                                <select name="service_act_deact" id="service_act_deact" class="multiselect-dropdown form-control">
                                    <option value="1" {{($services->status == 1) ? 'selected' : ''}}>Active</option>
                                    <option value="0" {{($services->status == 0) ? 'selected' : ''}}>Inactive</option>
                                </select>
                            </div> -->
                            <!-- <div class="position-relative form-group">
                                <div>
                                    <div class="custom-checkbox custom-control custom-control-inline"><input type="checkbox" id="service_act_deact" {{($services->status == 1) ? 'checked' : ''}} class="custom-control-input"><label class="custom-control-label" for="service_act_deact"><strong>Active/Inactive</strong></label></div>                                    
                                </div>
                            </div> -->
                            <input type="hidden" name="act_deact_service_chk" id="act_deact_service_chk">
                            <input type="hidden" name="loaded_image_height" id="loaded_image_height">
                            <input type="hidden" name="loaded_image_width" id="loaded_image_width">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="update_service" id="update_service">Update Service</button>
                                <a href="{{url('/admin/additional-service')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
                            </div>
                        </form>                
                    </div>
                </div>                                              
            </div>
            @include('admin.include.footer')
        </div>
    </div> 
    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-sm" id="serviceRequirementDeleteModel" tabindex="-1" role="dialog" aria-labelledby="serviceRequirementDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceRequirementDeleteLabel">Confirmation</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="event_feature_id" id="event_feature_id">
                    <input type="hidden" name="event_id" id="event_id">
                    <p class="mb-0" id="event_f_message">Are you sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeServiceRModel" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="deleteService_R">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->
    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-sm" id="serviceSamplesDeleteModel" tabindex="-1" role="dialog" aria-labelledby="serviceSamplesDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceSamplesDeleteLabel">Confirmation</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="service_sample_id" id="service_sample_id">                    
                    <p class="mb-0" id="event_f_message">Are you sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeEventFModel" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="deleteServiceSample">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->   
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/js/additional-service/additional-service.js')}}"></script>
<script>
$(document).ready(function(){

    var _URL = window.URL || window.webkitURL;
   
    var maxwidth = $('#constant_width').val();
    var maxheight = $('#constant_height').val();   

    $('#service_image_update').change(function () {
        var file = $(this)[0].files[0];
        img = new Image();
        var imgwidth = 0;
        var imgheight = 0;
        
        img.src = _URL.createObjectURL(file);
        img.onload = function() {
            imgwidth = this.width;
            imgheight = this.height;
            
            $("#width").text(imgwidth);
            $("#height").text(imgheight);   
            $("#loaded_image_width").val(imgwidth);
            $("#loaded_image_height").val(imgheight);                                    
        }    
    }); 
    
    if($('#service_act_deact').is(":checked"))
    {
        $('#act_deact_service_chk').val(1);
    }
    
    $('#service_act_deact').on('click', function(){
        if($(this).is(":checked"))
        {
            $('#act_deact_service_chk').val(1);
        }
        else
        {
            $('#act_deact_service_chk').val(0);
        }
    })
    
});
</script>
@endpush
