@extends('admin.layouts.master')
<title>CMS Pages | Alboumi</title>

@section('content')
<style type="text/css">
    #tblDeletePage_filter, #tblDeletePage_length, #tblDeletePage_info, #tblDeletePage_paginate{
        display: none;
    }
    #tblDeletePage
    {
        width:100% !important;
    }
</style>
<script type="text/javascript">
	var otherLanguages = <?php echo json_encode($otherLanguages);?>;
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
                                        <i class="pe-7s-note2 opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">CMS Pages</span>
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
                                                <a href="javascript:void(0);">CMS Pages</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                CMS Pages List
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <a href="{{url('/admin/cmsPages/addPage')}}"><button
                                        class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i
                                            class="fa fa-plus btn-icon-wrapper"> </i>New Page</button></a>
                                @if($languages->count() >= 2)
                                <button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm" id="divFilterToggle">
                                    <i aria-hidden="true" class="fa fa-filter"></i> Filter
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @if($languages->count() >= 2)
                <div class="main-card mb-3 card" id="FilterLangDiv">
                    <div class="card-body">
                        <h5 class="card-title"><i aria-hidden="true" class="fa fa-filter"></i>  Filter</h5>
                        <form method="post">
                            @csrf
                            <!-- <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group"> -->
                            <div class="row">
                                <div class="col-sm-4">
                                <label class="mr-sm-2">Languages</label>
                                <select name="filter_cms_page" id="filter_cms_page" class="multiselect-dropdown form-control">
                                    @foreach($languages as $lang)
                                        <option value="{{$lang->id}}" {{($lang->defaultSelected == 1) ? 'selected' : '' }}>{{$lang->text}}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div> 
                            <!-- </div> -->
                            <div class="row">
                                <div class="col-sm-4">
                                    <button type="button" id="filter_cms_page" class="btn btn-primary desktop-filter-search-btn mobile-filter-search-btn">Search</button>
                                </div>
                            </div> 
                        </form>
                    </div>
                </div>
                @endif
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <table style="width:100%;" id="cms_listing" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>Sr.No</th>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Created At</th>
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
    <!-- Modal for delete CMS Page -->
    <div class="modal fade" id="pageDeleteModel" tabindex="-1" role="dialog"
        aria-labelledby="pageDeleteModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Page </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="tblDeletePage" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Page Name</th>
                                <th>Language</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="pageLangDeleteModel" tabindex="-1" role="dialog"
        aria-labelledby="pageLangDeleteModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Page </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="pageDetailId" id="pageDetailId">
                    <p class="mb-0" id="message">Are you Sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="confirmDelete">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Page active inactive -->
    <div class="modal" id="pageActiveInactiveModel" tabindex="-1" role="dialog" aria-labelledby="pageActiveInactiveModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="pageIdForActiveInactive" id="pageIdForActiveInactive">
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
<script src="{{asset('public/assets/js/cms/cmsPages.js')}}"></script>
@endpush
