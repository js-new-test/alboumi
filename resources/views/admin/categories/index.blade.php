@extends('admin.layouts.master')
<title>Category | Alboumi</title>

@section('content')
<style type="text/css">
    #tblDeleteCategory_filter, #tblDeleteCategory_length, #tblDeleteCategory_info, #tblDeleteCategory_paginate{
        display: none;
    }
    #tblDeleteCategory
    {
        width:100% !important;
    }
    .breadcrumb-item+.breadcrumb-item::before{
        padding-right:0% !important;
    }
</style>
<script type="text/javascript">
    var otherLanguages = <?php echo json_encode($otherLanguages);?>;
    var baseUrl = <?php echo json_encode($baseUrl);?>;
</script>
<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
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
                                        <i class="pe-7s-cart opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Category</span>
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
                                                @if(!empty($catTitle))
                                                    <a href="{{url('/admin/categories')}}">Category</a>
                                                @else
                                                    <span>Category</span>
                                                @endif
                                            </li>
                                            @if(!empty($catTitle))
                                            <li class="active breadcrumb-item" aria-current="page">
                                                @foreach(array_reverse($catTitle) as $title)
                                                    @if(!$loop->last)
                                                        <a href="{{ url('/admin/categories?catId='.$title->category_id.'&langId='.$defaultLanguageId) }}">{{ $title->title }}</a>
                                                        <span>/</span>
                                                    @else
                                                        <span>{{ $title->title }}</span>
                                                    @endif
                                                @endforeach
                                            </li>
                                            @endif
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <input type="hidden" name="catId" value="{{ $catId }}">
                                @if(!empty($catId))
                                <a href="{{url('/admin/categories/addCategory?catId='.$catId)}}"><button
                                        class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i
                                            class="fa fa-plus btn-icon-wrapper"> </i>New Category</button></a>
                                @else
                                <a href="{{url('/admin/categories/addCategory')}}"><button
                                        class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i
                                            class="fa fa-plus btn-icon-wrapper"> </i>New Category</button></a>
                                @endif
                                @if(!empty($otherLanguages))
                                <button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm" id="divFilterToggle">
                                    <i aria-hidden="true" class="fa fa-filter"></i> Filter
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @if(!empty($otherLanguages))
                <div class="main-card mb-3 card" id="FilterLangDiv">
                    <div class="card-body">
                        <h5 class="card-title"><i aria-hidden="true" class="fa fa-filter"></i> Filter</h5>
                        <form method="post">
                            @csrf
                            <input type="hidden" name="langId" id="langId" value="{{ $defaultLanguageId }}">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label class="mr-sm-2">Languages</label>
                                    <select name="filter_category" id="filter_category" class="multiselect-dropdown form-control">
                                        @foreach($languages as $lang)
                                            <option value="{{$lang->id}}" {{($lang->defaultSelected == 1) ? 'selected' : '' }}>{{$lang->text}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <button type="button" id="filter_cat" class="btn btn-primary desktop-filter-search-btn mobile-filter-search-btn">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <table style="width:100%;" id="category_listing" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>Sr.No</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Slug</th>
                                    <th>Sort Order</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
            @include('admin.include.footer')
        </div>
    </div>
    <!-- Modal for delete CMS Category -->
    <div class="modal fade" id="categoryDeleteModel" tabindex="-1" role="dialog"
        aria-labelledby="categoryDeleteModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Category </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="tblDeleteCategory" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Language</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="categoryLangDeleteModel" tabindex="-1" role="dialog"
        aria-labelledby="categoryLangDeleteModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Category </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="categoryDetailId" id="categoryDetailId">
                    <p class="mb-0" id="message">Are you Sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="confirmDelete">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Category active inactive -->
    <div class="modal" id="categoryActiveInactiveModel" tabindex="-1" role="dialog" aria-labelledby="categoryActiveInactiveModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="categoryIdForActiveInactive" id="categoryIdForActiveInactive">
                    <input type="hidden" name="is_active" id="is_active">
                    <p class="mb-0" id="message">Are you Sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="confirmActiveInactive">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/js/category/category.js')}}"></script>
@endpush
