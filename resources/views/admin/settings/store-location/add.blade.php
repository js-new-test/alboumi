@extends('admin.layouts.master')
<title>Add Store Location | Alboumi</title>

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
                                    <span class="d-inline-block">Store Location</span>
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
                                                <a href="javascript:void(0);">Store Location</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/store-location')}}">Store Location List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Store Location  
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
                        <h5 class="card-title">Add Store Location</h5>                          
                        <form id="storeLocationForm" method="post" action="{{url('/admin/store-location/add')}}">
                            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />                            
                            <div class="row">                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title"><strong>Title<span style="color:red">*</span></strong></label>
                                        <input type="text" name="title" id="title" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">                                    
                                    <div class="form-group">
                                        <label for="phone"><strong>Phone<span style="color:red">*</span></strong></label>                                        
                                        <input type="number" name="phone" id="phone" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">    
                                <div class="col-md-6">                                    
                                    <div class="form-group">
                                        <label for="address_1"><strong>Address 1<span style="color:red">*</span></strong></label>                                        
                                        <textarea name="address_1" id="address_1" cols="30" rows="2" class="form-control"></textarea>
                                    </div>
                                </div>                            
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address_2"><strong>Address 2</strong></label>                                        
                                        <textarea name="address_2" id="address_2" cols="30" rows="2" class="form-control"></textarea>
                                    </div>
                                </div>                                
                            </div>
                            <div class="row">                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="map_url"><strong>Map URL</strong></label>                                        
                                        <input type="text" name="map_url" id="map_url" class="form-control">
                                    </div>
                                </div>
                                @if(isset($languages))
                                <div class="col-md-6">                                    
                                    <div class="form-group">
                                        <label for="language"><strong>Select Langauge<span style="color:red">*</span></strong></label>
                                        <select name="language" id="language" class="multiselect-dropdown form-control">
                                            @foreach($languages as $language)
                                                <option value="{{$language->gl_id}}">{{$language->lang_name}}</option>    
                                            @endforeach                                    
                                        </select>
                                    </div>
                                </div>
                                @else
                                <div class="form-group">                                                                             
                                    <input type="hidden" name="language" id="language" value="{{$language->gl_id}}">
                                </div> 
                                @endif
                            </div>
                            <div class="row">                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="latitude"><strong>Latitude</strong></label>
                                        <input type="text" name="latitude" id="latitude" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="longitude"><strong>Longitude</strong></label>
                                        <input type="text" name="longitude" id="longitude" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="add_store_loc" id="add_store_loc">Add Location</button>
                                <a href="{{url('/admin/store-location')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
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
<script src="{{asset('public/assets/js/settings/store-location.js')}}"></script>
@endpush
