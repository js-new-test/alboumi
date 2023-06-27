@extends('admin.layouts.master')
<title>{{$pageTitle }} | {{ $projectName }} </title>

@section('content')
<script type="text/javascript">
    var profile_pic = <?php echo json_encode($books->image);?>;
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
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
                                    <span class="d-inline-block">Photo Books</span>
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
                                                <a href="javascript:void(0);">Photo Books</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/books')}}">Photo Book List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Edit Photo Books
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
                        <h5 class="card-title">Edit Photo Books</h5>
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

                        <form id="editBook" name="editBook" class="col-md-10 mx-auto" method="post" action="{{ url('admin/books/editBook') }}" enctype="multipart/form-data">
                            <input type="hidden" name="bookId" id="bookId" value="{{ $books->id }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title" class="font-weight-bold">Title</label><span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title" value="{{ $books->title }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="font-weight-bold">Status
                                        </label>
                                        <div>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1" {{ ( $books->status == '1' ) ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="0" {{ ( $books->status == '0' ) ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
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
                                            <input type="file" name="bookImage" class="form-control" id="bookImage">
                                            <small class="form-text text-muted">Image size should be {{config('app.photobook_image.width')}} X {{config('app.photobook_image.height')}} px.</small>
                                            <small class="form-text text-muted">width = <small id="width"></small></small>
                                            <small class="form-text text-muted">height = <small id="height"></small></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="link" class="font-weight-bold">Link
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="link" id="link" class="multiselect-dropdown form-control">
                                            @foreach($products as $product)
                                                <option value="{{$product->id}}" {{($books->link == $product->id) ? 'selected' : ''}}>{{$product->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                       <img src="" width="100" height="100" class="mb-3" id="selected_image">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="link" class="font-weight-bold">Description
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <textarea name="description" id="description" type="text" class="form-control" placeholder="Enter Description">{{$books->description}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active" class="font-weight-bold">Price ({{ $defaultCurrency->currency_code }})
                                        </label><span class="text-danger">*</span>
                                        <div>
                                            <input type="text" name="price" class="form-control" id="price" placeholder="Enter Price" value="{{ $books->price }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="link" class="font-weight-bold">Sort Order
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <input name="sortOrder" id="sortOrder" type="number" class="form-control" placeholder="Enter Sort Order" value="{{ $books->sort_order }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                @if(isset($languages))
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="language" class="font-weight-bold">Language</label>
                                            <span class="text-danger">*</span>
                                            <div>
                                                <select
                                                    class="js-states browser-default form-control w-100 multiselect-dropdown"
                                                    name="language" id="language">
                                                    <option value="" disabled selected>Select Language</option>
                                                    @foreach($languages as $language)
                                                    <option value="{{ $language->id }}" {{ $books->language_id == $language->id ? 'selected' : '' }}>
                                                        {{ $language->langEN}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @if(isset($language))
                                        <div class="form-group">
                                            <input type="hidden" name="language" id="language" value="{{ $language->gl_id }}">
                                        </div>
                                    @endif
                                @endif
                                <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="question" class="font-weight-bold">Question
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div>
                                            <textarea name="question" id="question" type="text"
                                                class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                            <!-- <div class="row">
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
                            </div> -->
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
                                                <button type="submit" class="btn btn-primary btn-shadow w-100" id="addFaq">Update
                                                    Photo Book</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('admin/books') }}">
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
<script src="{{asset('public/assets/js/books/editBook.js')}}"></script>
@endpush
