@extends('admin.layouts.master')
<title>Add Attributes | Alboumi</title>


@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.css"
    rel="stylesheet">
@endpush

@section('content')
<style type="text/css">
    .multiselect-container {
        width: 100% !important;
    }
</style>
<script type="text/javascript">
    var language = <?php echo json_encode($language);?>;
    var page_name = '<?php echo $page_name; ?>';  
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
                                    <span class="d-inline-block">Attributes</span>
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
                                                <a href="javascript:void(0);">Attributes</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/attribute')}}">Attributes List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Attribute
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

                        <form id="attribute_create_form" class="col-md-10 mx-auto" method="post"
                            action="{{url('/admin/attribute/addAttribute')}}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="block_count" value="1">
                            @if($page == 'anotherLanguage')
                                <input type="hidden" name="attributeGroupId" value="{{ $attributeGroupId->attribute_group_id }}">
                            @endif
                            <div class="tab-content">
                                <div class="tab-pane active" id="attribute_form" role="tabpanel">
                                    <div class="card-body">
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
                                                            <input type="hidden" name="defaultLanguageId" value="{{ $defaultLanguageId }}">
                                                        </div>
                                                        @else
                                                        <div class="col-md-5">
                                                            <input type="hidden" name="attributeId" id="attributeId" value="{{$attributeId}}">
                                                            <select class="form-control multiselect-dropdown" name="defaultLanguage" id="defaultLanguage"></select>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            @else
                                                <input type="hidden" name="defaultLanguageId" value="{{ $defaultLanguageId }}">
                                            @endif
                                            
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="display_name">Display Name</label>
                                                    <span class="text-danger">*</span>
                                                    <div>
                                                        <input type="text" class="form-control" id="display_name"
                                                            name="display_name" value="{{old('display_name')}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">Name</label>
                                                    <span class="text-danger">*</span>
                                                    <div>
                                                        <input type="text" class="form-control" id="name" name="name"
                                                            value="{{old('name')}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if($page != 'anotherLanguage')
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="attribute_group_id">Attribute Group
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div>
                                                        <select class="js-states browser-default select2 form-control multiselect-dropdown"
                                                            name="attribute_group_id" id="attribute_group_id">
                                                            <option value="" disabled selected>Select</option>
                                                            @foreach($attribute_groups as $attribute_group)
                                                            <option value="{{ $attribute_group->id }}">
                                                                {{ $attribute_group->display_name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sort_order">Sort Order
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div>
                                                        <input type="number" class="form-control" id="sort_order"
                                                            name="sort_order" value="{{old('sort_order')}}" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($page != 'anotherLanguage')
                                        <div class="row">
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
                                            <div class="col-md-6">
                                                <div class="form-group" id="imageBox">                                                
                                                    <label>Image</label><span class="text-danger">*</span>
                                                    <input type="file" class="form-control" id="image" name="image" onchange="_showLoadedImageDimensions(this)">

                                                    <small class="form-text text-muted">Image size should be {{config('app.attribute_image.width')}} X {{config('app.attribute_image.height')}} px.</small> 
                                                    <small class="form-text text-muted">width = <small id="width"></small></small> 
                                                    <small class="form-text text-muted">height = <small id="height"></small></small>
                                                    <input type="hidden" name="loaded_image_height" id="loaded_image_height">
                                                    <input type="hidden" name="loaded_image_width" id="loaded_image_width">
                                                </div>
                                                <div class="form-group" id="colorBox">
                                                    <label>Color</label><span class="text-danger">*</span>
                                                    <input type="text" class="form-control" id="color" name="color">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="row">
                                            <div class="col-md-6 mt-3">
                                                <div class="row">
                                                    <div class="col-md-5 form-group">
                                                        <label for="is_filterable">Is Filterable </label>
                                                    </div>
                                                    <div class="col-md-7 form-group">
                                                        <div class="custom-checkbox custom-control">
                                                            <input type="checkbox" id="is_filterable"
                                                                class="custom-control-input" name="is_filterable" value="1">
                                                            <label class="custom-control-label" for="is_filterable"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        @endif

                                        <div class="row">
                                            <div class="col-md-12 text-center">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-2 offset-md-4">
                                                            <button type="submit" id="send"
                                                                class="btn btn-primary btn-shadow w-100">Add
                                                                Attribute</button>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <a href="{{ url('admin/attribute') }}">
                                                                <button type="button"
                                                                    class="btn btn-light btn-shadow w-100" name="cancel"
                                                                    value="Cancel">Cancel</button>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.js"></script>
<script src="{{asset('public/assets/js/attribute/add_attr.js')}}"></script>
<script src="{{asset('public/assets/js/attribute/attribute.js')}}"></script>
@endpush
