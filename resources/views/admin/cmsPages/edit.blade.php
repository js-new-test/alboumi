@extends('admin.layouts.master')
<title>Update CMS Page | Alboumi</title>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
@endpush

@section('content')
<style>
    #seo_description{
        height:145px;
    }
</style>
<script type="text/javascript">
	var nonDefaultLanguage = <?php echo json_encode($nonDefaultLanguage);?>;
    var defaultLanguageId = <?php echo json_encode($defaultLanguageId);?>;
    var baseUrl = <?php echo json_encode($baseUrl);?>;
    var banner_image = <?php echo json_encode($cmsDetails['banner_image']);?>;
    var mobile_banner_image = <?php echo json_encode($cmsDetails['mobile_banner_image']);?>;
</script>
<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
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
                                        <i class="pe-7s-note2 opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">CMS Pages</span>
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
                                                <a href="javascript:void(0);">CMS Page</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/cmsPages')}}">CMS Pages List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Update Page
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
                        <h5 class="card-title">{{ $formTitle }}</h5>

                        <form id="updatecmsDetailsForm" class="col-md-10 mx-auto" method="post"
                            action="{{url('/admin/cmsPages/updatePage')}}" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="page_id" value="@if(!empty($cmsDetails)){{ $cmsDetails['id'] }} @endif">

                            <div class="row">
                                @if(!empty($otherLanguages))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="default_lang">Language :</label>
                                            </div>
                                            <div class="col-md-5">
                                                <select class="form-control multiselect-dropdown" name="language_id" id="defaultLanguage"></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                    <input type="hidden" name="language_id" value="{{ $defaultLanguageId }}">
                                @endif
                                <div class="col-md-6 commonElement">
                                    <div class="form-group">
                                        <label for="display_on">Display On</label>
                                        <div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="display_on_top" name="display_on" value="0" {{ ($cmsDetails['display_on'] =="0")? "checked" : "" }}>
                                                <label class="custom-control-label" for="display_on_top">Top</label>
                                            </div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="display_on_bottom" name="display_on" value="1" {{ ($cmsDetails['display_on'] =="1")? "checked" : "" }}>
                                                <label class="custom-control-label" for="display_on_bottom">Bottom</label>
                                            </div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="display_on_none" name="display_on" value="2" {{ ($cmsDetails['display_on'] =="2")? "checked" : "" }}>
                                                <label class="custom-control-label" for="display_on_none">None</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="title"
                                                name="title" value="{{ $cmsDetails['title'] }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 commonElement">
                                    <div class="form-group">
                                        <label for="slug">Slug</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="slug" name="slug" value="{{ $cmsDetails['slug'] }}" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- HTML for banner and mobile banner of CMS :Nivedita(11-01-2021)-->

                            <div class="row">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label for="banner_image">Banner</label>
                                      <div>
                                          <input type="file" class="form-control" id="banner_image" name="banner_image" onchange="_showLoadedBannerDimensions(this)">

                                          <small class="form-text text-muted">Image size should be {{config('app.cms_banner_image.width')}} X {{config('app.cms_banner_image.height')}} px.</small>
                                          <small class="form-text text-muted">width = <small id="width"></small></small>
                                          <small class="form-text text-muted">height = <small  id="height"></small></small>
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

                                          <small class="form-text text-muted">Image size should be {{config('app.cms_mobile_banner_image.width')}} X {{config('app.cms_mobile_banner_image.height')}} px.</small>
                                          <small class="form-text text-muted">width = <small id="mobilebannerwidth"></small></small>
                                          <small class="form-text text-muted">height = <small  id="mobilebannerheight"></small></small>
                                          <input type="hidden" name="loaded_mobile_banner_height" id="loaded_mobile_banner_height">
                                          <input type="hidden" name="loaded_mobile_banner_width" id="loaded_mobile_banner_width">
                                      </div>
                                  </div>
                              </div>
                              </div>
                              <div class="row">
                                  <div class="col-md-6">
                                    @if(isset($cmsDetails['banner_image']))
                                         <img src="" width="100" height="100" class="mb-3" id="selected_banner">
                                    @endif
                                  </div>
                                  <div class="col-md-6">
                                      @if(isset($cmsDetails['mobile_banner_image']))
                                         <img src="" width="100" height="100" class="mb-3" id="selected_mobile_banner">
                                      @endif
                                  </div>
                              </div>

                            <!-- HTML end for banner and mobile banner of CMS :Nivedita(11-01-2021) -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <div>
                                            <textarea class="form-control ckeditor" id="description"
                                                name="description" >{{$cmsDetails['description']}}</textarea>
                                            <div id="desc_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 commonElement">
                                    <div class="form-group">
                                        <label for="status" class="font-weight-bold">Status
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1" @if(!empty($cmsDetails)) {{ ( $cmsDetails['status'] == 1 ) ? 'selected' : '' }} @endif>Active
                                                </option>
                                                <option value="0" @if(!empty($cmsDetails)) {{ ( $cmsDetails['status'] == 0 ) ? 'selected' : '' }} @endif>
                                                    Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="seo_title">SEO Title</label>
                                        <div>
                                            <input type="text" class="form-control" id="seo_title" name="seo_title" value="{{ $cmsDetails['seo_title'] }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="seo_description">SEO Description</label>
                                        <div>
                                            <textarea type="text" class="form-control" id="seo_description"
                                                name="seo_description">{{$cmsDetails['seo_description']}}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="seo_keyword">SEO Keyword</label>
                                        <div>
                                            <input type="text" class="form-control" id="seo_keyword" name="seo_keyword" value="{{ $cmsDetails['seo_keyword'] }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100" id="updateFaq">Update
                                                    Page</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/cmsPages') }}">
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
<script src="{{asset('public/assets/js/cms/cmsPages.js')}}"></script>
<script src="{{asset('public/assets/js/cms/editPage.js')}}"></script>

<script type="text/javascript">
    CKEDITOR.replace('description', {                
        filebrowserUploadUrl: "{{route('ckeditor.upload_cms_page_image', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });
</script>
<script>
    CKEDITOR.replace('description', {
      fullPage: true,
      allowedContent: true,
      height: 320
    });
</script>
@endpush
