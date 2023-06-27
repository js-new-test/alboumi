@extends('admin.layouts.master')
<title>Add Megamenu | Alboumi</title>

@section('content')
<script>
    var cms_pages = <?php echo json_encode($cms_pages); ?>;
    var categories = <?php echo json_encode($categories); ?>;
</script>
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
                                    <span class="d-inline-block">Megamenu</span>
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
                                                <a href="javascript:void(0);">Megamenu</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/mega-menu')}}">Megamenu List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Megamenu
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
                        <h5 class="card-title">Add Megamenu</h5>
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

                        <form id="addMegamenuForm" class="col-md-10 mx-auto" method="post"
                            action="{{ url('admin/mega-menu/addMenu') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type" class="font-weight-bold">Type <span class="text-danger">*</span></label>
                                        <div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="type_cms" name="type" value="0" onclick="showRelatedBlock()" checked>
                                                <label class="custom-control-label" for="type_cms">CMS</label>
                                            </div>
                                            <div class="custom-radio custom-control custom-control-inline">
                                                <input class="custom-control-input" type="radio" id="type_category" name="type" value="1" onclick="showRelatedBlock()">
                                                <label class="custom-control-label" for="type_category">Category</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="radioRelatedBlock">
                                    <div class="form-group">
                                        <label class="font-weight-bold"></label> <span class="text-danger">*</span>
                                        <div>
                                            <select class="form-control multiselect-dropdown" id="selectDropdownOnRadio" name="name"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="small_image" class="font-weight-bold">Small Image</label>    
                                        <div>
                                            <input type="file" class="form-control" id="small_image" name="small_image" onchange="_showSmallImageDimensions(this)">
                                            <small class="form-text text-muted">Image size should be {{config('app.megamenu_small_image.width')}} X {{config('app.megamenu_small_image.height')}} px.</small> 
                                            <small class="form-text text-muted">width = <small id="small_width"></small></small> 
                                            <small class="form-text text-muted">height = <small id="small_height"></small></small>
                                            <input type="hidden" name="image_height" id="small_image_height">
                                            <input type="hidden" name="image_width" id="small_image_width">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="big_image" class="font-weight-bold">Big Image</label>    
                                        <div>
                                            <input type="file" class="form-control" id="big_image" name="big_image" onchange="_showBigImageDimensions(this)">
                                            <small class="form-text text-muted">Image size should be {{config('app.megamenu_big_image.width')}} X {{config('app.megamenu_big_image.height')}} px.</small> 
                                            <small class="form-text text-muted">width = <small id="big_width"></small></small> 
                                            <small class="form-text text-muted">height = <small id="big_height"></small></small>
                                            <input type="hidden" name="image_height" id="big_image_height">
                                            <input type="hidden" name="image_width" id="big_image_width">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description" class="font-weight-bold">Description</label>
                                        <div>
                                            <textarea type="text" class="form-control" id="description" name="description"
                                                placeholder="Write description here" rows=4></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sort_order" class="font-weight-bold">Sort Order</label>
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order"
                                                placeholder="Enter sort order" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="icon_image" class="font-weight-bold">Icon Image</label>    
                                        <div>
                                            <input type="file" class="form-control" id="icon_image" name="icon_image" onchange="_showIconImageDimensions(this)">
                                            <small class="form-text text-muted">Image size should be {{config('app.megamenu_icon_image.width')}} X {{config('app.megamenu_icon_image.height')}} px.</small> 
                                            <small class="form-text text-muted">width = <small id="icon_width"></small></small> 
                                            <small class="form-text text-muted">height = <small id="icon_height"></small></small>
                                            <input type="hidden" name="image_height" id="icon_image_height">
                                            <input type="hidden" name="image_width" id="icon_image_width">
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
                                                    Menu</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/mega-menu') }}">
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
<script src="{{asset('public/assets/js/megamenu/addMegamenu.js')}}"></script>
<script src="{{asset('public/assets/js/megamenu/megamenu.js')}}"></script>
@endpush
