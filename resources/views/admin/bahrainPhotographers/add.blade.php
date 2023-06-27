@extends('admin.layouts.master')
<title>Add Photographer | Alboumi</title>

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
    var language = <?php echo json_encode($language);?>;
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
                                        <i class="pe-7s-photo opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Photographers</span>
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
                                                <a href="javascript:void(0);">Photographers</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/photgraphers')}}">Photographers List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Photographer
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

                        <form id="addPhotographerForm" class="col-md-10 mx-auto" method="post"
                            action="{{url('/admin/photgraphers/addPhotographer')}}" enctype="multipart/form-data">
                            @csrf

                            @if(!empty($otherLanguages))
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="default_lang">Language :</label>
                                            </div>
                                            @if($page != 'anotherLanguage')
                                            <div class="col-md-5">
                                                <label for="default_lang"> {{ $defaultLanguage }} </label>
                                                <input type="hidden" name="defaultLanguageId" value="{{ $defaultLanguageId }}">
                                            </div>
                                            @else
                                            <div class="col-md-5">
                                                <input type="hidden" name="photoId" id="photoId" value="{{$photoId}}">
                                                <select class="form-control multiselect-dropdown" name="defaultLanguage" id="defaultLanguage"></select>
                                            </div>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @else
                                <input type="hidden" name="defaultLanguageId" value="{{ $defaultLanguageId }}">
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="name"
                                                name="name" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location">Location</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="location" name="location" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="about">About</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <textarea class="form-control" id="about" name="about"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($page != 'anotherLanguage')
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Profile Picture</label><span class="text-danger">*</span>
                                    <input type="file" class="form-control" id="profile_pic" name="profile_pic" onchange="_showProfilePicDimensions(this)">
                                    <small class="form-text text-muted">Image size should be {{config('app.photographer_profile_pic.width')}} X {{config('app.photographer_profile_pic.height')}} px.</small>
                                    <small class="form-text text-muted">width = <small id="profile_width"></small></small>
                                    <small class="form-text text-muted">height = <small id="profile_height"></small></small>
                                    <input type="hidden" name="profile_image_height" id="profile_image_height">
                                    <input type="hidden" name="profile_image_width" id="profile_image_width">
                                </div>
                                <div class="col-md-6">
                                    <label>Cover Photo</label><span class="text-danger">*</span>
                                    <input type="file" class="form-control" id="cover_photo" name="cover_photo" onchange="_showCoverPicDimensions(this)">
                                    <small class="form-text text-muted">Image size should be {{config('app.photographer_cover_pic.width')}} X {{config('app.photographer_cover_pic.height')}} px.</small>
                                    <small class="form-text text-muted">width = <small id="cover_width"></small></small>
                                    <small class="form-text text-muted">height = <small id="cover_height"></small></small>
                                    <input type="hidden" name="cover_image_height" id="cover_image_height">
                                    <input type="hidden" name="cover_image_width" id="cover_image_width">
                                </div>
                            </div>
                            @endif

                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="experience">Experience</label><span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="experience" name="experience">
                                        </div>
                                    </div>
                                </div>
                                @if($page != 'anotherLanguage')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="web">Web</label>
                                        <div>
                                            <input type="text" class="form-control" id="web" name="web" />
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="row">
                                @if($page != 'anotherLanguage')
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
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="seo_title">SEO Title</label>
                                        <div>
                                            <input type="text" class="form-control" id="seo_title" name="seo_title"  value="{{ old('seo_title') }}"/>
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
                                            name="seo_description">{{ old('seo_description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="seo_keyword">SEO Keyword</label>
                                    <div>
                                        <input type="text" class="form-control" id="seo_keyword" name="seo_keyword" value="{{ old('seo_keyword') }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100">Add
                                                    Photographer</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/photgraphers') }}">
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
<script src="{{asset('public/assets/js/photographers/photographers.js')}}"></script>
<script src="{{asset('public/assets/js/photographers/addPhotographer.js')}}"></script>
@endpush
