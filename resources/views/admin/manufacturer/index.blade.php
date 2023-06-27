@extends('admin.layouts.master')
<title>Alboumi | Brands</title>

@section('content')
<style type="text/css">
    #tblDeleteBrand_filter, #tblDeleteBrand_length, #tblDeleteBrand_info, #tblDeleteBrand_paginate{
        display: none;
    }
</style>
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl);?>;
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
                                        <i class="lnr-users opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Brands</span>
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
                                                <a href="javascript:void(0);">Brand</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Brands List
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <a href="{{url('/admin/manufacturers/add')}}" class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i class="fa fa-plus btn-icon-wrapper"> </i>Add New</a>
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
                        <h5 class="card-title"><i aria-hidden="true" class="fa fa-filter"></i>  Filter</h5>
                        <form method="post">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4">
                                <label class="mr-sm-2">Languages</label>
                                <select name="filter_brands" id="filter_brands" class="multiselect-dropdown form-control">
                                    @foreach($languages as $lang)
                                        <option value="{{$lang->id}}" {{($lang->is_default == 1) ? 'selected' : '' }}>{{$lang->langEN}}</option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <button type="button" id="filter_brands" class="btn btn-primary desktop-filter-search-btn mobile-filter-search-btn">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <div class="main-card mb-3 card element-block-example">
                    <div class="card-body">
                    	<table id="tableManufacturers" class="table table-hover table-striped table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th width="20%">Name</th>
                                    <th>Slug</th>
                                    <th>Image</th>
                                    <th>Is Active</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
	        </div>
	    </div>
    </div>
</div>

<!-- Modal Start -->
<div class="modal fade" id="brandDeleteModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            	<!-- <input type="hidden" name="brandId" id="brandId">
                <p class="mb-0" id="message">Are you Sure?</p> -->
                <table id="tblDeleteBrand"  class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Brand</th>
                            <th>Language</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button> -->
                <!-- <button type="button" class="btn btn-primary" id="confirmDelete">Yes</button> -->
            </div>
        </div>
    </div>
</div>
<!-- Modal Over -->

<!-- Modal Start -->
<div class="modal fade" id="brandLanguageDeleteModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="brandDetailId" id="brandDetailId">
                <p class="mb-0" id="message">Are you Sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmDelete">Yes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Over -->

<!-- Modal Start -->
<div class="modal fade" id="confirmationModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="brandId" id="brandId">
                <input type="hidden" name="brandStatus" id="brandStatus">

                <p class="mb-0" id="message">Are you Sure?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmStatus">Yes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Over -->
@push('scripts')
	<script src="{{asset('public/assets/js/manufacturer/manufacturer.js')}}"></script>
@endpush
@endsection
