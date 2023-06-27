@extends('admin.layouts.master')
<title>Add Home Page Text | Alboumi</title>

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
                                    <span class="d-inline-block">Home Page Text</span>
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
                                                <a href="javascript:void(0);">Home Page Text</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/home-page-text')}}">Home Page Text List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Home Page Text  
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
                        <h5 class="card-title">Add Home Page Text</h5>  
                        <form id="homePageTextForm" method="post" action="{{url('/admin/home-page-text/add')}}">
                            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />                                                    
                            <div class="row">
                                @if($defualt_languages_count->count() > 1)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="language"><strong>Select Language<span style="color:red">*</span></strong></label>   
                                        <select name="language" id="language" class="multiselect-dropdown form-control">
                                            <optgroup label="Select Language">
                                                @foreach($languages as $lang)                                            
                                                    <option value="{{$lang->id}}">{{$lang->langEN}}</option>                                            
                                                @endforeach                                            
                                            </optgroup>
                                        </select>                                                                                       
                                    </div>
                                </div>
                                @else
                                    <div class="form-group">    
                                        @foreach($defualt_languages_count as $lang)                                    
                                            <input type="hidden" name="language" id="language" value="{{$lang->id}}">                                                                                     
                                        @endforeach
                                    </div> 
                                @endif
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="content_1"><strong>Content 2<span style="color:red">*</span></strong></label>   
                                        <input type="text" name="content_1" id="content_1" class="form-control">                                                                                     
                                    </div> 
                                </div>                                
                            </div>    
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="content_2"><strong>Content 2<span style="color:red">*</span></strong></label>
                                        <textarea name="content_2" id="content_2" cols="30" rows="5" class="form-control"></textarea>
                                    </div> 
                                </div>
                            </div>                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
                                                                                                                
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="add_footer" id="add_footer">Add Text</button>
                                <a href="{{url('/admin/home-page-text')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
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
<script src="{{asset('public/assets/js/home-page-text/home-page-text.js')}}"></script>
@endpush