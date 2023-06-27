@extends('admin.layouts.master')
<title>Add How It Works Banner | Alboumi</title>

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
                                    <span class="d-inline-block">How It Works Banner</span>
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
                                                <a href="{{url('/admin/how-it-works-banner')}}">How It Works Banner</a>
                                            </li>                                            
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add How It Works Banner  
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
                        <h5 class="card-title">Add How It Works Banner</h5>  
                        <form id="hotItWorksBnnr" method="post" action="{{url('admin/how-it-works-banner/add')}}" enctype="multipart/form-data">
                            @csrf             
                            <div class="row">
                                <div class="col-sm-6">
                                    @if(isset($all_languages))
                                        <div class="form-group">
                                            <label for="language"><strong>Select Language<span style="color:red">*</span></strong></label>
                                            <select name="language" id="language" class="multiselect-dropdown form-control">
                                                <optgroup label="Select Language">
                                                    @foreach($all_languages as $lang)
                                                        <option value="{{$lang->id}}">{{$lang->text}}</option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                        </div>
                                    @else
                                    <div class="form-group">
                                        <input type="hidden" name="language" id="language" value="{{$default_language}}">
                                    </div>
                                    @endif
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="banner_image"><strong>Banner<span style="color:red">*</span></strong></label>
                                        <div class=""><input name="banner_image" id="banner_image" type="file" class="form-control-file">
                                            <small class="form-text text-muted">Allowed extentions .jpg, .jpeg, .png</small>
                                            <small class="form-text text-muted">Image size should be {{config('app.how_it_works_banner.width')}} X {{config('app.how_it_works_banner.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="width"></small></small>
                                            <small class="form-text text-muted">height = <small id="height"></small></small>
                                        </div>
                                    </div>
                                    <input type="hidden" name="loaded_banner_image_width" id="loaded_banner_image_width">
                                    <input type="hidden" name="loaded_banner_image_height" id="loaded_banner_image_height">
                                </div>
                            </div>                                                                                                      
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="add_howitworksbnr" value="add_howitworksbnr">Add Banner</button>
                                <a href="{{url('/admin/how-it-works-banner')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
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
<script src="{{asset('public/assets/js/how-it-works-bnr/how-it-works-bnr.js')}}"></script>
@endpush
