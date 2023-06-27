@extends('admin.layouts.master')
<title>Add Collection | Alboumi</title>

@section('content')
<script type="text/javascript">
    var page_name = <?php echo json_encode($page_name); ?>
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
                                    <span class="d-inline-block">Our Collection</span>
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
                                                <a href="javascript:void(0);">Our Collection</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/collection')}}">Collection List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Add Collection
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
                        <h5 class="card-title">Add Collection</h5>
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

                        <form id="addCollectionForm" class="col-md-10 mx-auto" method="post"
                            action="{{ url('admin/collection/addCollection') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
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
                                                    {{ $language->text}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="collection_title" class="font-weight-bold">Title</label><span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="collection_title" name="collection_title"
                                                placeholder="Enter collection title" />
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
                                        <label for="collection_image" class="font-weight-bold">Image</label>    
                                        <span class="text-danger">*</span>
                                        <div>
                                            <input type="file" class="form-control" id="collection_image" name="collection_image" onchange="_showLoadedImageDimensions(this)">
                                            <small class="form-text text-muted">Image size should be {{config('app.collection_image.width')}} X {{config('app.collection_image.height')}} px.</small> 
                                            <small class="form-text text-muted">width = <small id="width"></small></small> 
                                            <small class="form-text text-muted">height = <small id="height"></small></small>
                                            <input type="hidden" name="image_height" id="image_height">
                                            <input type="hidden" name="image_width" id="image_width">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="collection_link" class="font-weight-bold">Link
                                            <span class="text-danger">*</span>
                                        </label>
                                        <!-- <div>
                                            <input type="text" class="form-control" id="collection_link" name="collection_link"
                                                placeholder="Enter link" />
                                        </div> -->
                                        <select name="collection_link" id="collection_link" class="multiselect-dropdown form-control">
                                            @foreach($categories as $category)
                                                <option value="{{$category->parent_cat_id}}">{{$category->title}}</option>                                            
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
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
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-4">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100">Add
                                                    Collection</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/collection') }}">
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
<script src="{{asset('public/assets/js/collection/collection.js')}}"></script>
@endpush
