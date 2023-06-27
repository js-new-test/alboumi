@extends('admin.layouts.master')
<title>Add Locale | Alboumi</title>

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
                                    <span class="d-inline-block">Localization</span>
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
                                                <a href="{{url('/admin/locale')}}">Localization</a>
                                            </li>
                                            <!-- <li class="breadcrumb-item">
                                                <a href="{{url('/admin/language/list')}}">Language</a>
                                            </li> -->
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Locale
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
                        <h5 class="card-title">Add Locale</h5>
                        @if(Session::has('msg'))
                        <div class="alert {{((Session::get('alert-class') == true) ? 'alert-success' : 'alert-danger')}} alert-dismissible fade show"
                            role="alert">
                            {{ Session::get('msg') }}
                            <button type="button" class="close session_error" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <form id="localeForm" class="col-md-12" method="POST" action="{{url('/admin/locale/add')}}">
                            @csrf
                            <div class="form-group row">
                                <label for="code" class="col-sm-2 col-form-label">Code<span
                                        style="color:red">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="code" id="code" placeholder="Code">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="title" class="col-sm-2 col-form-label">Title<span
                                        style="color:red">*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="title" id="title" placeholder="Title">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="title" class="col-sm-6 col-form-label">Locales<span
                                        style="color:red">*</span>
                                    <div style="color:red;">Note: All tabs are mandatory. Please check before submit.
                                    </div>
                                </label>
                                <div class="col-md-12">
                                    <div class="mb-3 card">
                                       
                                        <div class="card-header card-header-tab-animation">
                                            <ul class="nav nav-justified" id="LocaleTab">
                                                @foreach($languages as $lang)
                                                <li class="nav-item">
                                                    <a data-toggle="tab" href="#tab_{{$lang->id}}"
                                                        class="nav-link">{{ $lang->langEN }} ({{ $lang->alpha2 }})
                                                        <span class="text-danger">*</span>
                                                    </a>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                       
                                        <div class="card-body">
                                            <div class="tab-content">
                                                @foreach($languages as $lang)
                                                <div class="tab-pane tab_content" id="tab_{{$lang->id}}"
                                                    role="tabpanel">
                                                    <textarea type="text" 
                                                    name="test[{{$lang->alpha2}}]" cols="100" rows="5"
                                                    class="form-control locale_textarea"></textarea>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <!-- <input type="submit" class="btn btn-primary" name="add_locale" value="Submit"> -->
                                        <button type="submit" class="btn btn-primary" name="add_locale"
                                            id="add_locale">Add Locale</button>
                                        <a href="{{url('/admin/locale')}}"><button type="button" class="btn btn-light"
                                                name="cancel" value="Cancel">Cancel</button></a>
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