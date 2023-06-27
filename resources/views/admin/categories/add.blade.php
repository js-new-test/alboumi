@extends('admin.layouts.master')
<title>Add Category | Alboumi</title>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
@endpush

@section('content')
<style>
  .breadcrumb-item+.breadcrumb-item::before{
        padding-right:0% !important;
    }
</style>
<script type="text/javascript">
    var language = <?php echo json_encode($language) ?>;
    var baseUrl = <?php echo json_encode($baseUrl);?>;
</script>
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
                                        <i class="pe-7s-cart opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Category</span>
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
                                                <a href="{{url('/admin/categories')}}">Category</a>
                                            </li>
                                            @if(!empty($catTitle))
                                            <li class="active breadcrumb-item" aria-current="page">
                                                @foreach(array_reverse($catTitle) as $title)
                                                    <a href="{{ url('/admin/categories?catId='.$title['category_id'].'&langId='.$defaultLanguageId) }}">{{$title['title'] }}</a>
                                                    @if(!$loop->last)
                                                        <span>/</span>
                                                    @endif
                                                @endforeach
                                            </li>
                                            @endif
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Category
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

                        <form id="addCategoryForm" class="col-md-10 mx-auto" method="post"
                            action="{{url('/admin/categories/addCategory')}}" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="catId" value="{{ $catId }}">
                            <input type="hidden" name="page" value="{{ $page }}">
                            <input type="hidden" name="prevUrl" value="{{ url()->previous() }}">
                            <div class="row">
                                @if(!empty($otherLanguages))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="default_lang">Language :</label>
                                            </div>
                                            @if($page != 'anotherLanguage')
                                            <div class="col-md-5">
                                                <label for="default_lang"> {{ $defaultLanguage }} </label>
                                                <input type="hidden" name="defaultLanguageId"
                                                    value="{{ $defaultLanguageId }}">
                                            </div>
                                            @else
                                            <div class="col-md-5">
                                                <input type="hidden" name="catId" id="catId" value="{{$catId}}">
                                                <select class="form-control multiselect-dropdown" name="defaultLanguage"
                                                    id="defaultLanguage"></select>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @else
                                    <input type="hidden" name="defaultLanguageId" value="{{ $defaultLanguageId }}">
                                @endif
                                @if($page != 'anotherLanguage' && (!empty($catTitle)))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label>Category Path :</label>
                                            </div>
                                            <div class="col-md-8">
                                                @foreach(array_reverse($catTitle) as $title)
                                                    <span>{{$title['title'] }}</span>
                                                    @if(!$loop->last)
                                                        <span>></span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="title" name="title"
                                                onInput="generateSlug()" />
                                        </div>
                                    </div>
                                </div>
                                @if($page != 'anotherLanguage')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="slug">Slug</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="slug"
                                                name="slug" />
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <!-- HTML for banner and mobile banner of CMS :Nivedita(11-01-2021)-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="banner_image">Banner</label>
                                        <div>
                                            <input type="file" class="form-control" id="banner_image" name="banner_image" onchange="_showLoadedBannerDimensions(this)">

                                            <small class="form-text text-muted">Image size should be {{config('app.category_banner_image.width')}} X {{config('app.category_banner_image.height')}} px.</small>
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

                                            <small class="form-text text-muted">Image size should be {{config('app.category_mobile_banner_image.width')}} X {{config('app.category_mobile_banner_image.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="mobilebannerwidth"></small></small>
                                            <small class="form-text text-muted">height = <small  id="mobilebannerheight"></small></small>
                                            <input type="hidden" name="loaded_mobile_banner_height" id="loaded_mobile_banner_height">
                                            <input type="hidden" name="loaded_mobile_banner_width" id="loaded_mobile_banner_width">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- HTML end for banner and mobile banner of CMS :Nivedita(11-01-2021) -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <div>
                                            <textarea class="form-control ckeditor" id="description"
                                                name="description"></textarea>
                                            <div id="desc_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="meta_title">Meta Title</label>
                                        <div>
                                            <input type="text" class="form-control" id="meta_title" name="meta_title" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="meta_keywords">Meta Keywords</label>
                                        <div>
                                            <input type="text" class="form-control" id="meta_keywords"
                                                name="meta_keywords" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if($page != 'anotherLanguage')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="category_image">Image</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="file" class="form-control" id="category_image" name="category_image" onchange="_showLoadedImageDimensions(this)">

                                            <small class="form-text text-muted">Image size should be {{config('app.category_image.width')}} X {{config('app.category_image.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="width"></small></small>
                                            <small class="form-text text-muted">height = <small  id="height"></small></small>
                                            <input type="hidden" name="loaded_image_height" id="loaded_image_height">
                                            <input type="hidden" name="loaded_image_width" id="loaded_image_width">
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="meta_description">Meta Description</label>
                                        <div>
                                            <textarea type="text" class="form-control" id="meta_description"
                                                name="meta_description" rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($page != 'anotherLanguage')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-5 form-group">
                                            <label for="lady_operator">Lady Operator Preference</label>
                                        </div>
                                        <div class="col-md-7 form-group">
                                            <div class="custom-checkbox custom-control">
                                                <input type="checkbox" id="lady_operator"
                                                    class="custom-control-input" name="lady_operator" value="1">
                                                <label class="custom-control-label" for="lady_operator"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="meta_description">Photo Upload Require</label>
                                        <div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input photo_upload" type="radio" id="photo_upload_na" name="photo_upload" value="0" checked>
                                                <label class="custom-control-label" for="photo_upload_na">N/A</label>
                                            </div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input photo_upload" type="radio" id="photo_upload_s" name="photo_upload" value="1">
                                                <label class="custom-control-label" for="photo_upload_s">Yes</label>
                                            </div>
                                            <!-- <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="photo_upload_m" name="photo_upload" value="2">
                                                <label class="custom-control-label" for="photo_upload_m">Multiple</label>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 is_multiple d-none">
                                  <div class=" form-group">
                                      <div class="custom-checkbox custom-control">
                                          <input type="checkbox" id="upload_is_multiple"
                                              class="custom-control-input" name="upload_is_multiple" value="1" checked>
                                          <label class="custom-control-label" for="upload_is_multiple">Is Multiple</label>
                                      </div>
                                  </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qty_matrix">Quantity Price Matrix</label>
                                        <div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="qty_matrix_n" name="qty_matrix" value="0" onclick="hideRange()" checked>
                                                <label class="custom-control-label" for="qty_matrix_n">No</label>
                                            </div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="qty_matrix_y" name="qty_matrix" value="1" onclick="showRange()">
                                                <label class="custom-control-label" for="qty_matrix_y">Yes</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="qty_range_block">
                                    <div class="form-group">
                                        <label for="qty_range">Quantity Range <i class="fa fa-info-circle" title="Please enter comma separated values "></i></label>
                                        <div>
                                            <input type="text" class="form-control" id="qty_range" name="qty_range" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sort_order">Sort Order</label>
                                        <div>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <select class="form-control" name="status" id="status">
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
                                        <label for="display_on">Display On</label>
                                        <div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="display_on_top" name="display_on" value="0" checked>
                                                <label class="custom-control-label" for="display_on_top">Top</label>
                                            </div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="display_on_bottom" name="display_on" value="1">
                                                <label class="custom-control-label" for="display_on_bottom">Bottom</label>
                                            </div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="display_on_none" name="display_on" value="2">
                                                <label class="custom-control-label" for="display_on_none">None</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100"
                                                    id="addCategory">Add
                                                    Category</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url()->previous() }}">
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
<script src="{{asset('public/assets/js/category/category.js')}}"></script>
<script src="{{asset('public/assets/js/category/addCategory.js')}}"></script>
<script type="text/javascript">
    CKEDITOR.replace('description', {
        filebrowserUploadUrl: "{{route('ckeditor.upload_category_image', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });
</script>
@endpush
