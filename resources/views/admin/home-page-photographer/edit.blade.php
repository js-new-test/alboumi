@extends('admin.layouts.master')
<title>Edit Home Page Photographer | Alboumi</title>
@section('content')
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl);?>;
    var big_image = <?php echo json_encode($HomePagePhotographer->big_image);?>;
    var small_image = <?php echo json_encode($HomePagePhotographer->small_image);?>;
</script>
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
                                    <span class="d-inline-block">Home Page Photographer</span>
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
                                                <a href="javascript:void(0);">Home Page Photographer</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/home-page-photographer')}}">Home Page Photographer List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Edit Home Page Photographer
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
                        <h5 class="card-title">Update Home Page Photographer</h5>
                        <form id="homePagePhotographerupdateForm" method="post" action="{{url('/admin/home-page-photographer/update')}}" enctype="multipart/form-data">
                            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="hpp_id" id="hpp_id" value="{{$HomePagePhotographer->id}}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="p_name"><strong>Name<span style="color:red">*</span></strong></label>
                                        <select name="p_id" id="p_id" class="multiselect-dropdown form-control">
                                            @foreach($photographer as $p)
                                                <option value="{{$p->id}}" {{($HomePagePhotographer->photographer_id == $p->id) ? 'selected' : ''}}>{{$p->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- HTML for big and small image by Nivedita (13-01-2021)-->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="big_image"><strong>Big Image<span style="color:red">*</span></strong></label>
                                        <div class="">
                                            <input type="file" name="big_image" id="big_image" class="form-control" onchange="_showLoadedBigImgDimensions(this)">
                                            <small class="form-text text-muted">Image size should be {{config('app.home_page_photographer_bigimg.width')}} X {{config('app.home_page_photographer_bigimg.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="widthbigimg"></small></small>
                                            <small class="form-text text-muted">height = <small id="heightbigimg"></small></small>
                                            @if(isset($HomePagePhotographer->big_image))
                                                 <img src="" width="100" height="100" class="mb-3" id="selected_bigimage">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="loaded_bigimage_height" id="loaded_bigimage_height">
                                <input type="hidden" name="loaded_bigimage_width" id="loaded_bigimage_width">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="small_image"><strong>Small Image<span style="color:red">*</span></strong></label>
                                        <div class="">
                                            <input type="file" name="small_image" id="small_image" class="form-control" onchange="_showLoadedSmallImgDimensions(this)">
                                            <small class="form-text text-muted">Image size should be {{config('app.home_page_photographer_smallimg.width')}} X {{config('app.home_page_photographer_smallimg.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="widthsmallimg"></small></small>
                                            <small class="form-text text-muted">height = <small id="heightsmallimg"></small></small>
                                            @if(isset($HomePagePhotographer->small_image))
                                                 <img src="" width="100" height="100" class="mb-3" id="selected_smallimage">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="loaded_smallimage_height" id="loaded_smallimage_height">
                                <input type="hidden" name="loaded_smallimage_width" id="loaded_smallimage_width">
                                <!-- HTML End for big and small image by Nivedita (13-01-2021)-->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="p_sort_order"><strong>Sort Order<span style="color:red">*</span></strong></label>
                                        <input type="number" name="p_sort_order" id="p_sort_order" class="form-control" value="{{$HomePagePhotographer->sort_order}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="p_status"><strong>Status<span style="color:red">*</span></strong></label>
                                        <select name="p_status" id="p_status" class="multiselect-dropdown form-control">
                                            <option value="1" {{($HomePagePhotographer->status == 1) ? 'selected' : ''}}>Active</option>
                                            <option value="0" {{($HomePagePhotographer->status == 0) ? 'selected' : ''}}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                {{--<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="p_image_update"><strong>Image<span style="color:red">*</span></strong></label>
                                        <div class="">
                                            <input type="file" name="p_image_update" id="p_image_update" class="form-control">
                                            <small class="form-text text-muted">Image size should be {{config('app.home_page_photographer.width')}} X {{config('app.home_page_photographer.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="width"></small></small>
                                            <small class="form-text text-muted">height = <small id="height"></small></small>
                                        </div>
                                    </div>
                                    <div style="margin-top: 10px;">
                                        <img height="70" width="80" src="{{url('public/assets/images/home-page-photographer').'/'.$HomePagePhotographer->image}}" alt="">
                                    </div>
                                </div>
                                <input type="hidden" name="loaded_image_height" id="loaded_image_height">
                                <input type="hidden" name="loaded_image_width" id="loaded_image_width">--}}
                            </div>
                            <div class="row">


                            </div>
                            <div class="row">
                                {{--<div class="col-md-6">
                                    <div class="form-group">
                                        <label for="link"><strong>Link<span style="color:red">*</span></strong></label>
                                        <input type="text" name="link" id="link" class="form-control" value="{{$HomePagePhotographer->link}}">
                                    </div>
                                </div>
                                @if(isset($global_languages))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="language"><strong>Select Language<span style="color:red">*</span></strong></label>
                                        <select name="language" id="language" class="multiselect-dropdown form-control">
                                            <optgroup label="Select Language">
                                                @foreach($global_languages as $lang)
                                                    <option value="{{$lang->gl_id}}" {{($HomePagePhotographer->language_id == $lang->gl_id) ? 'selected':''}}>{{$lang->lang_name}}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                @else
                                    <div class="form-group">
                                        <input type="hidden" name="language" id="language" value="{{$global_language->gl_id}}">
                                    </div>
                                @endif--}}
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="edit_photographer" id="edit_photographer">Update Photographer</button>
                                <a href="{{url('/admin/home-page-photographer')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
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
<script src="{{asset('public/assets/js/home-page-photographer/home-page-photographer.js')}}"></script>
@endpush
