@extends('admin.layouts.master')
<title>Edit Home Page Content | Alboumi</title>

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
                                    <span class="d-inline-block">Home Page Content</span>
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
                                                <a href="javascript:void(0);">Home Page Content</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/home-page-content')}}">Home Page Content List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Edit Home Page Content  
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
                        <h5 class="card-title">Update Home Page Content</h5>  
                        <form id="homePageContentForm" method="post" action="{{url('/admin/home-page-content/update')}}" enctype="multipart/form-data">
                            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />   
                            <input type="hidden" name="hpc_id" id="hpc_id" value="{{$HomePageContent->id}}">
                            <div class="row">                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="language"><strong>Language<span style="color:red">*</span></strong></label>                                                                                
                                        <input type="text" name="language" disabled id="language" class="form-control" value="{{$HomePageContent->langName}}">
                                    </div> 
                                </div>                                 
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title"><strong>Title<span style="color:red">*</span></strong></label>   
                                        <input type="text" name="title" id="title" class="form-control" value="{{$HomePageContent->title}}">                                                                                     
                                    </div> 
                                </div>                                
                            </div>    

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description"><strong>Description<span style="color:red">*</span></strong></label>
                                        <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{$HomePageContent->description}}</textarea>
                                    </div> 
                                </div>                                
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="link"><strong>Link 1<span style="color:red">*</span></strong></label>
                                        <!-- <input type="text" name="link" id="link" class="form-control" value="{{$HomePageContent->link}}"> -->
                                        <select name="link" id="link" class="multiselect-dropdown form-control">
                                            @foreach($categories as $category)
                                                <option value="{{$category->parent_cat_id}}" {{($HomePageContent->category_id_1 == $category->parent_cat_id) ? 'selected' : ''}}>{{$category->title}}</option>                                            
                                            @endforeach
                                        </select>
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="link"><strong>Link 2<span style="color:red">*</span></strong></label>
                                        <!-- <input type="text" name="link" id="link" class="form-control" value="{{$HomePageContent->link}}"> -->
                                        <select name="link_2" id="link_2" class="multiselect-dropdown form-control">
                                            @foreach($categories as $category)
                                                <option value="{{$category->parent_cat_id}}" {{($HomePageContent->category_id_2 == $category->parent_cat_id) ? 'selected' : ''}}>{{$category->title}}</option>                                            
                                            @endforeach
                                        </select>
                                    </div> 
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="image_text_1"><strong>Image Text 1<span style="color:red">*</span></strong></label>
                                        <input type="text" name="image_text_1" id="image_text_1" class="form-control" value="{{$HomePageContent->image_text_1}}">
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="image_text_2"><strong>Image Text 2<span style="color:red">*</span></strong></label>
                                        <input type="text" name="image_text_2" id="image_text_2" class="form-control" value="{{$HomePageContent->image_text_2}}">
                                    </div> 
                                </div>                                                              
                            </div>                            

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="image_1_update"><strong>Desktop Image 1<span style="color:red">*</span></strong></label>                                        
                                        <div class="">
                                        <input type="file" name="image_1_update" id="image_1_update" class="form-control">
                                            <small class="form-text text-muted">Image size should be {{config('app.home_page_content.width')}} X {{config('app.home_page_content.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="width_1"></small></small>
                                            <small class="form-text text-muted">height = <small id="height_1"></small></small>
                                        </div>
                                    </div> 
                                    <div style="margin-top: 10px;margin-bottom: 10px;"> 
                                        <img height="70" width="80" src="{{url('public/assets/images/home-page-content/desktop').'/'.$HomePageContent->image_1}}" alt="">    
                                    </div>
                                </div>  
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="image_2_update"><strong>Desktop Image 2<span style="color:red">*</span></strong></label>                                        
                                        <div class="">
                                            <input type="file" name="image_2_update" id="image_2_update" class="form-control"> 
                                            <small class="form-text text-muted">Image size should be {{config('app.home_page_content.width')}} X {{config('app.home_page_content.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="width_2"></small></small>
                                            <small class="form-text text-muted">height = <small id="height_2"></small></small>
                                        </div>                                                                             
                                    </div>
                                    <div style="margin-top: 10px;margin-bottom: 10px;"> 
                                        <img height="70" width="80" src="{{url('public/assets/images/home-page-content/desktop').'/'.$HomePageContent->image_2}}" alt="">
                                    </div> 
                                </div>                                
                            </div>  
                            <input type="hidden" name="loaded_image_height_1" id="loaded_image_height_1">
                            <input type="hidden" name="loaded_image_width_1" id="loaded_image_width_1">
                            <input type="hidden" name="loaded_image_height_2" id="loaded_image_height_2">
                            <input type="hidden" name="loaded_image_width_2" id="loaded_image_width_2"> 

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mobile_image_1_update"><strong>Mobile Image 1<span style="color:red">*</span></strong></label>                                        
                                        <div class="">
                                        <input type="file" name="mobile_image_1_update" id="mobile_image_1_update" class="form-control">
                                            <small class="form-text text-muted">Image size should be {{config('app.home_page_content_mobile.width')}} X {{config('app.home_page_content_mobile.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="mobile_width_1"></small></small>
                                            <small class="form-text text-muted">height = <small id="mobile_height_1"></small></small>
                                        </div>
                                    </div> 
                                    <div style="margin-top: 10px;margin-bottom: 10px;"> 
                                        <img height="70" width="80" src="{{url('public/assets/images/home-page-content/mobile/').'/'.$HomePageContent->mobile_image_1}}" alt="">    
                                    </div>
                                </div>  
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mobile_image_2_update"><strong>Mobile Image 2<span style="color:red">*</span></strong></label>                                        
                                        <div class="">
                                            <input type="file" name="mobile_image_2_update" id="mobile_image_2_update" class="form-control"> 
                                            <small class="form-text text-muted">Image size should be {{config('app.home_page_content_mobile.width')}} X {{config('app.home_page_content_mobile.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="mobile_width_2"></small></small>
                                            <small class="form-text text-muted">height = <small id="mobile_height_2"></small></small>
                                        </div>                                                                             
                                    </div>
                                    <div style="margin-top: 10px;margin-bottom: 10px;"> 
                                        <img height="70" width="80" src="{{url('public/assets/images/home-page-content/mobile').'/'.$HomePageContent->mobile_image_2}}" alt="">
                                    </div> 
                                </div>                                
                            </div>  
                            <input type="hidden" name="loaded_mobile_image_height_1" id="loaded_mobile_image_height_1">
                            <input type="hidden" name="loaded_mobile_image_width_1" id="loaded_mobile_image_width_1">
                            <input type="hidden" name="loaded_mobile_image_height_2" id="loaded_mobile_image_height_2`">
                            <input type="hidden" name="loaded_mobile_image_width_2" id="loaded_mobile_image_width_2">                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
                                                                                                                
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="update_content" id="update_content">Update Content</button>
                                <a href="{{url('/admin/home-page-content')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
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
<script src="{{asset('public/assets/js/home-page-content/home-page-content.js')}}"></script>
@endpush