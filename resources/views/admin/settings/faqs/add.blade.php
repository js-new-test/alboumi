@extends('admin.layouts.master')
<title>Add FAQ | Alboumi</title>

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
                                        <i class="lnr-cog opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">FAQs</span>
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
                                                <a href="javascript:void(0);">FAQs</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/faq')}}">FAQs List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add FAQs
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
                        <h5 class="card-title">Add FAQ</h5>
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

                        <form id="addFaqForm" class="col-md-10 mx-auto" method="post"
                            action="{{ url('admin/faq/addFaq') }}">
                            @csrf
                            <div class="row">
                                @if(isset($languages))
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="language_id" class="font-weight-bold">Language</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <select
                                                class="js-states browser-default form-control w-100 multiselect-dropdown"
                                                name="language_id" id="language_id">
                                                <option value="" disabled selected>Select Language</option>
                                                @foreach($languages as $language)
                                                <option value="{{ $language->id }}">
                                                    {{ $language->langEN}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="form-group">                                    
                                    <input type="hidden" id="language_id" name="language_id" value="{{$language->id}}">
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sort_order" class="font-weight-bold">Sort Order</label>
                                        <div>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order"
                                                placeholder="Enter Sort Order" />
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
                                        <label for="is_active" class="font-weight-bold">Status
                                        </label>
                                        <div>
                                            <select name="is_active" id="is_active" class="form-control">
                                                <option value="1" selected>Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="question" class="font-weight-bold">Question
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <textarea name="question" id="question" type="text"
                                                class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="answer" class="font-weight-bold">Answer
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <textarea name="answer" id="answer" type="text"
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
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100" id="addFaq">Add
                                                    FAQ</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/faq') }}">
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
<script src="{{asset('public/assets/js/settings/faqs.js')}}"></script>
@endpush
