@extends('admin.layouts.master')
<title>Add Banner | Alboumi</title>

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
                                        <i class="pe-7s-note2 opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Banner</span>
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
                                                <a href="javascript:void(0);">Banner</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/banner')}}">Banner List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Banner
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
                        <h5 class="card-title">Add Banner</h5>
                        @if(Session::has('msg'))
                            <div class="alert {{ (Session::get('alert-class') == true) ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
                                {{ Session::get('msg') }}
                                <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <form id="addBanner" method="post" action="{{url('/admin/banner/add')}}" enctype="multipart/form-data">
                            @csrf
                            <!-- HTML for Title and Link by Nivedita (13-01-2021)-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title"><strong>Title<span style="color:red">*</span></strong></label>
                                        <div>
                                            <input type="text" class="form-control required" id="title"
                                                name="title" value="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="link"><strong>Link<span style="color:red">*</span></strong></label>
                                        <!-- <div>
                                            <input type="text" class="form-control required" id="link" name="link" value="" />
                                        </div> -->
                                        <select name="link" id="link" class="multiselect-dropdown form-control">
                                            @foreach($categories as $category)
                                                <option value="{{$category->parent_cat_id}}">{{$category->title}}</option>                                            
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- HTML End for Title and Link by Nivedita (13-01-2021)-->
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="banner_text"><strong>Banner Text</strong></label>
                                        <textarea name="banner_text" id="banner_text" class="form-control" cols="30" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="banner_act_deact"><strong>Is Default</strong></label>
                                        <select name="banner_act_deact" id="banner_act_deact" class="multiselect-dropdown form-control">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="banner_image_desktop"><strong>Desktop Banner<span style="color:red">*</span></strong></label>
                                        <div class=""><input required name="banner_image_desktop" id="banner_image_desktop" type="file" class="form-control-file">
                                        <small class="form-text text-muted">Image size should be {{config('app.banner_image_desktop.width')}} X {{config('app.banner_image_desktop.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="desktop_width"></small></small>
                                            <small class="form-text text-muted">height = <small id="desktop_height"></small></small>
                                        </div>
                                    </div>
                                    <input type="hidden" name="loaded_desktop_image_width" id="loaded_desktop_image_width">
                                    <input type="hidden" name="loaded_desktop_image_height" id="loaded_desktop_image_height">
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="banner_image_mobile"><strong>Mobile Banner<span style="color:red">*</span></strong></label>
                                        <div class=""><input required name="banner_image_mobile" id="banner_image_mobile" type="file" class="form-control-file">
                                        <small class="form-text text-muted">Image size should be {{config('app.banner_image_mobile.width')}} X {{config('app.banner_image_mobile.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="mobile_width"></small></small>
                                            <small class="form-text text-muted">height = <small id="mobile_height"></small></small>
                                        </div>
                                    </div>
                                    <input type="hidden" name="loaded_mobile_image_width" id="loaded_mobile_image_width">
                                    <input type="hidden" name="loaded_mobile_image_height" id="loaded_mobile_image_height">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
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
                            <!-- <div class="position-relative form-group">
                                <div>
                                    <div class="custom-checkbox custom-control custom-control-inline"><input type="checkbox" id="banner_act_deact" class="custom-control-input"><label class="custom-control-label" for="banner_act_deact"><strong>Active/Inactive</strong></label></div>
                                </div>
                            </div> -->
                            <input type="hidden" name="act_deact_chk_val" id="act_deact_chk_val">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="add_banner" id="add_banner">Add Banner</button>
                                <a href="{{url('/admin/banner')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
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

    var _URL = window.URL || window.webkitURL;

    var maxwidth = $('#constant_width').val();
    var maxheight = $('#constant_height').val();

    $('#banner_image_desktop').change(function () {
        var file = $(this)[0].files[0];
        img = new Image();
        var imgwidth = 0;
        var imgheight = 0;

        img.src = _URL.createObjectURL(file);
        img.onload = function() {
            imgwidth = this.width;
            imgheight = this.height;

            $("#desktop_width").text(imgwidth);
            $("#desktop_height").text(imgheight);
            $("#loaded_desktop_image_width").val(imgwidth);
            $("#loaded_desktop_image_height").val(imgheight);
        }
    });

    $('#banner_image_mobile').change(function () {
        var file = $(this)[0].files[0];
        img = new Image();
        var imgwidth = 0;
        var imgheight = 0;

        img.src = _URL.createObjectURL(file);
        img.onload = function() {
            imgwidth = this.width;
            imgheight = this.height;

            $("#mobile_width").text(imgwidth);
            $("#mobile_height").text(imgheight);
            $("#loaded_mobile_image_width").val(imgwidth);
            $("#loaded_mobile_image_height").val(imgheight);
        }
    });

    $('#banner_act_deact').on('click', function(){
        if($(this).is(":checked"))
        {
            $('#act_deact_chk_val').val(1);
        }
        else
        {
            $('#act_deact_chk_val').val(0);
        }
    })
});
$("#addBanner").validate({
    rules: {
        // language : {
        //     required: true,
        // },
        title : {
            required: true,
        },
        link:{
            required: true,
        },
        banner_image_desktop: {
            required: true,
            extension: "jpg|jpeg|png|"
        },
         banner_image_mobile:{
             required: true,
             extension: "jpg|jpeg|png|"
         },

        // link:{
        //     required: true,
        // }
    },
    messages: {
        // language : {
        //     required: 'Language is required',
        // },
        title : {
            required: 'Title is required',
        },
        link : {
            required: 'Link is required',
        },
        banner_image_desktop : {
            required: 'Please upload dekstop banner image',
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        banner_image_mobile : {
            required: 'Please upload mobile banner image',
            extension: "Please upload file in these format only (png, jpg, jpeg)."
        },
        // link:{
        //     required: 'Link is required',
        // }
    },
    errorPlacement: function ( error, element ) {
        // Add the `invalid-feedback` class to the error element
        if ( element.prop( "type" ) === "checkbox" ) {
            error.insertAfter( element.next( "label" ) );
        } else {
            error.insertAfter( element );
        }
    },

});
</script>
@endpush
