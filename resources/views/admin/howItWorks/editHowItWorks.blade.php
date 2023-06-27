@extends('admin.layouts.master')
<title>{{$pageTitle }} | {{ $projectName }} </title>

@section('content')
<div class="app-container body-tabs-shadow fixed-header fixed-sidebar app-theme-gray closed-sidebar">
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
                                    <span class="d-inline-block">How it Works</span>
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
                                                <a href="javascript:void(0);">How it Works</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/howitWorks')}}">How it Works List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Edit How it Works
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
                        <h5 class="card-title">Edit How it Works</h5>
                        <div class="row">
                            <div class="col-md-6 offset-md-1">
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if(Session::has('msg'))
                            <div class="alert {{ (Session::get('alert-class') == true) ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show" role="alert">
                                {{ Session::get('msg') }}
                                <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <form id="editHowitWorks" name="editHowitWorks" class="col-md-10 mx-auto" method="post" action="{{ url('admin/howitWorks/editHowitWorks') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="howitWorksId" id="howitWorksId" value="{{ $howItWorks->id }}" readonly="true">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="language" class="font-weight-bold">Language</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <select class="js-states browser-default form-control w-100 multiselect-dropdown" name="language" id="language">
                                                <option value="" disabled selected>Select Language</option>
                                                @foreach($languages as $language)
                                                <option value="{{ $language->id }}" {{ $howItWorks->language_id == $language->id ? 'selected' : '' }}>
                                                    {{ $language->langEN}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title" class="font-weight-bold">Title</label><span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title" value="{{ $howItWorks->title }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="lang_error"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active" class="font-weight-bold">Image
                                        </label>
                                        <div>
                                            <input type="file" name="image" class="form-control" id="image">
                                            <small class="form-text text-muted">Image size should be {{config('app.how_it_works_image.width')}} X {{config('app.how_it_works_image.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="width"></small></small>
                                            <small class="form-text text-muted">height = <small id="height"></small></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="link" class="font-weight-bold">Description
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <textarea class="form-control" name="description">{{ $howItWorks->description }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="link" class="font-weight-bold">Sort Order
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <input name="sortOrder" id="sortOrder" type="number" class="form-control" placeholder="Enter Sort Order" value="{{ $howItWorks->sort_order }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="font-weight-bold">Status
                                        </label>
                                        <div>
                                            <select name="status" id="status" class="form-control">
                                                <option {{ ( $howItWorks->status == 'Active' ) ? 'selected' : '' }}>
                                                    Active</option>
                                                <option {{ ( $howItWorks->status == 'Inactive' ) ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div id="ck_error"></div>
                                </div>
                            </div>
                            <input type="hidden" name="loaded_image_height" id="loaded_image_height">
                            <input type="hidden" name="loaded_image_width" id="loaded_image_width">

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100" id="addFaq">Update How it Works</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/howitWorks') }}">
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
<script src="{{asset('public/assets/js/howItWorks/editHowItWorks.js')}}"></script>
@endpush
