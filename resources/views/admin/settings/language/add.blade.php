@extends('admin.layouts.master')
<title>Add Global Language | Alboumi</title>

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
                                    <span class="d-inline-block">Language</span>
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
                                                <a href="javascript:void(0);">Settings</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/language/list')}}">Language</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Language
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
                        <h5 class="card-title">Add Language</h5>
                        <form method="post" action="{{url('/admin/language/add')}}"  enctype="multipart/form-data">
                            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="country_selector">Select Langauge<span style="color:red">*</span></label>
                                    <div class="form-group">
                                        <select name="language_selector" id="language_selector" class="multiselect-dropdown form-control">
                                            @foreach($languages as $language)
                                                <option value="{{$language->sortcode}}">{{$language->lang_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for = "visibility"> Visibility</label>
                                        <div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="visibility_ltr" name="visibility" value="0" checked>
                                                <label class="custom-control-label" for="visibility_ltr">LTR</label>
                                            </div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="visibility_rtl" name="visibility" value="1">
                                                <label class="custom-control-label" for="visibility_rtl">RTL</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for = "lang_flag"> Image</label>
                                        <span class="text-danger">*</span>

                                        <div>
                                            <input type="file" class="form-control" id="lang_flag" name="lang_flag" onchange="_showLoadedImageDimensions(this)">

                                            <small class="form-text text-muted">Image size should be {{config('app.language_image.width')}} X {{config('app.language_image.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="width"></small></small>
                                            <small class="form-text text-muted">height = <small  id="height"></small></small>
                                            <input type="hidden" name="loaded_image_height" id="loaded_image_height">
                                            <input type="hidden" name="loaded_image_width" id="loaded_image_width">
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="currency_selector">Select Currency<span style="color:red">*</span></label>
                                    <div class="form-group">
                                        <select name="currency" id="currency" class="multiselect-dropdown form-control">
                                            @foreach($currencies as $currency)
                                                <option value="{{$currency->id}}">{{$currency->currency_name}} ({{$currency->currency_code}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">Decimal Number</label>
                                    <span class="text-danger">*</span>
                                    <div>
                                        <input type="text" class="form-control" id="decimal_number" name="decimal_number"
                                            value="">
                                    </div>
                                </div>
                            </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="font-weight-bold">Decimal Separator</label>
                                <span class="text-danger">*</span>
                                <div>
                                    <input type="text" class="form-control" id="decimal_separator" name="decimal_separator"
                                        value="">
                                </div>
                            </div>
                        </div>
                          </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">Thousand Separator</label>
                                    <span class="text-danger">*</span>
                                    <div>
                                        <input type="text" class="form-control" id="thousand_separator" name="thousand_separator"
                                            value="">
                                    </div>
                                </div>
                            </div>
                      </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="add_language" id="add_language" value="add_language">Add Language</button>
                                <a href="{{url('/admin/language/list')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
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
// Show image dimensions for service image
var _URL = window.URL || window.webkitURL;

function _showLoadedImageDimensions(image)
{
    var file = image.files[0];
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
};
</script>
@endpush
