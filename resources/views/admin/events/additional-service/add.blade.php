@extends('admin.layouts.master')
<title>Add Additional Service | Alboumi</title>

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
                                                Add Additional Service  
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
                        <h5 class="card-title">Add Additional Service</h5> 
                        @if(Session::has('msg'))                     
                            <div class="alert {{ (Session::get('alert-class') == true) ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
                                {{ Session::get('msg') }}
                                <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif 
                        <form id="addAdditionalService" method="post" action="{{url('/admin/additional-service/add')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="language"><strong>Name<span style="color:red">*</span></strong></label>   
                                        <input type="text" name="addit_service_name" id="addit_service_name" class="form-control">                                                                                     
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service_description"><strong>Description<span style="color:red">*</span></strong></label>
                                        <textarea name="service_description" id="service_description" class="form-control" cols="30" rows="2"></textarea>
                                    </div>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="service_image"><strong>Image<span style="color:red">*</span></strong></label>
                                        <div class=""><input required name="service_image" id="service_image" type="file" class="form-control-file">
                                            <small class="form-text text-muted">Image size should be {{config('app.service_image.height')}} X {{config('app.service_image.width')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="width"></small></small>
                                            <small class="form-text text-muted">height = <small id="height"></small></small>
                                            <input type="hidden" name="loaded_image_height" id="loaded_image_height">
                                            <input type="hidden" name="loaded_image_width" id="loaded_image_width">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">                                    
                                    <div class="form-group">
                                        <label for="samples"><strong>Samples<span style="color:red">*</span></strong></label>
                                        <div>
                                            <input type="file" required name="samples[]" id="samples" class="form-control-file" multiple>                                                
                                            <small class="form-text text-muted">Allowed file extensions .jpeg, .jpg, .png.</small>
                                            <small class="form-text text-muted">Image size should be {{config('app.service_sample_image.height')}} X {{config('app.service_sample_image.width')}} px.</small>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="row">                                                                
                                <div class="col-md-6">                                    
                                    <div class="form-group">
                                        <label for="price" class="font-weight-bold">Price ({{ $default_currency->currency_symbol }})</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="number" class="form-control" id="price" name="price" placeholder="Enter Price" />
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
                                                    <option value="{{$lang->gl_id}}">{{$lang->lang_name}}</option>                                            
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
                            <!-- <div class="form-group">
                                <label for="service_act_deact"><strong>Status</strong></label>
                                <select name="service_act_deact" id="service_act_deact" class="multiselect-dropdown form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>                            -->
                                
                            <label for="Requirements"><strong>Requirements<span style="color:red">*</span></strong></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">                                
                                        <input type="text" name="requirement_labels[]" id="requirement_labels0" placeholder="Requirements" class="form-control dynamic_requirement_field_validation">
                                        <span id="requirements_labels_error0"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">                                
                                        <input type="text" name="requirements[]" id="requirements0" placeholder="Value" class="form-control dynamic_value_field_validation">
                                        <span id="requirements_error0"></span>
                                    </div>
                                </div>                                
                            </div>
                            <div id="dynamic_requirements_textbox"></div>
                            <div style="margin-bottom: 30px;">
                                <button type="button" class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm" id="add_requirements"><i class="fa fa-plus btn-icon-wrapper"> </i>Add</button>
                            </div>

                            <input type="hidden" name="act_deact_service_chk" id="act_deact_service_chk">
                            <input type="hidden" name="loaded_image_height" id="loaded_image_height">
                            <input type="hidden" name="loaded_image_width" id="loaded_image_width">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="add_service" id="add_service">Add Service</button>
                                <a href="{{url('/admin/additional-service')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
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
<script src="{{asset('public/assets/js/additional-service/additional-service.js')}}"></script>
@endpush
