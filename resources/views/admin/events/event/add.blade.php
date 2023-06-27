@extends('admin.layouts.master')
<title>Add Event | Alboumi</title>

@section('content')
<div class="app-container body-tabs-shadow fixed-header fixed-sidebar app-theme-gray closed-sidebar">
    @include('admin.include.header')
    <div class="app-main">
        @include('admin.include.sidebar')
        <div class="app-main__outer">
            <div class="app-main__inner">
                <div class="app-page-title">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div>
                                <div class="page-title-head center-elem">
                                    <span class="d-inline-block pr-2">
                                        <i class="lnr-cog opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Events</span>
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
                                                <a href="javascript:void(0);">Events</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/event/list')}}">Events List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Event
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
                        <h5 class="card-title">Create Event</h5>
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <form id="event_create_form" class="col-md-10 mx-auto" method="post"
                            action="{{url('/admin/event/addEvent')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                @if(isset($global_languages))
                                <div class="col-md-6">                                    
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
                                </div>
                                @else
                                <div class="form-group">                                                                            
                                    <input type="hidden" id="language" name="language" value="{{$global_language->gl_id}}">
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="event_name" class="font-weight-bold">Event Name</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="event_name" name="event_name"
                                                value="{{old('event_name')}}">
                                        </div>
                                    </div>
                                </div>                                
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="event_image" class="font-weight-bold">Event Image</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="file" class="form-control" id="event_image" name="event_image"
                                                value="{{old('event_image')}}">
                                            <small class="form-text text-muted">Image size should be {{config('app.event_image.width')}} X {{config('app.event_image.height')}} px.</small>
                                            <small class="form-text text-muted">Width = <small id="width"></small></small>
                                            <small class="form-text text-muted">Height = <small id="height"></small></small>
                                            <input type="hidden" name="loaded_image_height" id="loaded_image_height">
                                            <input type="hidden" name="loaded_image_width" id="loaded_image_width">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active" class="font-weight-bold">Status
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <select name="is_active" id="is_active" class="form-control">
                                                <option value="1" selected>Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                            
                            <div class="row">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label for="banner_image">Banner</label>
                                      <div>
                                          <input type="file" class="form-control" id="banner_image" name="banner_image" onchange="_showLoadedBannerDimensions(this)">

                                          <small class="form-text text-muted">Image size should be {{config('app.event_banner_image.width')}} X {{config('app.event_banner_image.height')}} px.</small>
                                          <small class="form-text text-muted">width = <small id="bannerwidth"></small></small>
                                          <small class="form-text text-muted">height = <small  id="bannerheight"></small></small>
                                          <input type="hidden" name="loaded_banner_height" id="loaded_banner_height">
                                          <input type="hidden" name="loaded_banner_width" id="loaded_banner_width">
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label for="banner_image">Mobile Banner</label>
                                      <div>
                                          <input type="file" class="form-control" id="mobile_banner_image" name="mobile_banner_image" onchange="_showLoadedMobileBannerDimensions(this)">

                                          <small class="form-text text-muted">Image size should be {{config('app.event_mobile_banner_image.width')}} X {{config('app.event_mobile_banner_image.height')}} px.</small>
                                          <small class="form-text text-muted">width = <small id="mobilebannerwidth"></small></small>
                                          <small class="form-text text-muted">height = <small  id="mobilebannerheight"></small></small>
                                          <input type="hidden" name="loaded_mobile_banner_height" id="loaded_mobile_banner_height">
                                          <input type="hidden" name="loaded_mobile_banner_width" id="loaded_mobile_banner_width">
                                      </div>
                                  </div>
                              </div>
                            </div>
                              
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="event_desc" class="font-weight-bold">Event Description</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <textarea name="event_desc" id="event_desc" type="text"
                                                class="form-control ckeditor"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="ck_error"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="event_desc" class="font-weight-bold">Event Features</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" name="event_feature[]" id="event_feature0" class="form-control required dynamic_event_feature_validation">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">                                        
                                        <label for="sort_order" class="font-weight-bold">Sort Order</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="number" name="sort_order" id="sort_order" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="dynamic_event_feat_textbox"></div>
                            <div style="margin-bottom: 30px;">
                                <button type="button" class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm" id="add_event_feature"><i class="fa fa-plus btn-icon-wrapper"> </i>Add</button>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100" id="addEvent">Add
                                                    Event</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/event/list') }}">
                                                    <button type="button" class="btn btn-light btn-shadow w-100"
                                                        name="cancel" value="Cancel">Cancel</button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
<script src="{{asset('public/assets/js/events/event.js')}}"></script>
<script>
$(document).ready(function(){

    var _URL = window.URL || window.webkitURL;
    $('#event_image').change(function () {
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

    var i = 0;
    $('#add_event_feature').click(function(){
        i++;
        var output = '';        
        output += '<div class="row remove_current_div">';
            output += '<div class="col-md-6">';
                output += '<div class="form-group">';
                    output += '<div>';
                    output += '<input type="text" name="event_feature[]" id="event_feature'+i+'" data-inc-val="'+i+'" class="form-control required dynamic_event_feature_validation">';
                    output += '<span id="event_feature_error'+i+'"></span>';
                    output += '</div>';
                output += '</div>';
            output += '</div>';
            output += '<div class="col-md-6">';
                output += '<div class="form-group">';
                    output += '<div style="margin-top: 0px;">';                        
                        output += '<button class="mb-2 mr-2 btn-icon btn-icon-only btn-square btn btn-primary delete_event_feature"><i class="fa fa-fw" aria-hidden="true" title="Copy to use close"></i></button>'
                    output += '</div>';
                output += '</div>';            
            output += '</div>';            
        output += '</div>';        
        $('#dynamic_event_feat_textbox').append(output);       
    })    

    $(document).on('click', '.delete_event_feature',function(){        
        $(this).closest(".remove_current_div").remove();
    });
})
</script>
<script type="text/javascript">
    CKEDITOR.replace('event_desc', {                
        filebrowserUploadUrl: "{{route('ckeditor.upload_event_image', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });		 
</script>
@endpush
