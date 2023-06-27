@extends('admin.layouts.master')
<title>{{ $pageTitle }} | {{ $projectName }}</title>

@section('content')
@if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_design_tool') === true)
<?php $permission=1;?>
@else
<?php $permission=0;?>
@endif

@if(config('app.copyProduct') == 1)
    @php $copyProduct = 1; @endphp
@else
    @php $copyProduct = 0; @endphp
@endif

<link rel="stylesheet" href="{{asset('public/assets/frontend/css/loader-style.css')}}">

<style type="text/css">
    #tblDeleteBrand_filter, #tblDeleteBrand_length, #tblDeleteBrand_info, #tblDeleteBrand_paginate{
        display: none;
    }
</style>
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl);?>;
    var permission =<?php echo json_encode($permission);?>;
    var copyProduct = <?php echo json_encode($copyProduct); ?>;
</script>

<div id="ajax-loader">
  <div class="cv-spinner">
  	<img src="{{ asset('public/assets/frontend/img/Loader.svg') }}" class="spinner">
  </div>
</div>


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
                                        <i class="active_icon metismenu-icon pe-7s-cart"></i>
                                    </span>
                                    <span class="d-inline-block">Products</span>
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
                                                <a href="javascript:void(0);">Product</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Product List
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                              @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_add_product') === true)
                                <a href="product/addProduct" class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i class="fa fa-plus btn-icon-wrapper"> </i>Add New</a>
                              @endif
                                <button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm" id="divFilterToggle">
                                        <i aria-hidden="true" class="fa fa-filter"></i> Filter
                                </button>
                                @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_design_tool') === true)
                                <button  class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm loginLumise">Access Product Tool</button>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
                <div class="main-card mb-3 card" id="FilterLangDiv">
                    <div class="card-body">
                        <h5 class="card-title"><i aria-hidden="true" class="fa fa-filter"></i>  Filter</h5>
                        <form class="col-md-12 mx-auto" id="myForm1">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Languages </label>
                                        <div>
                                            <select name="filterProduct" id="filterProduct" class="multiselect-dropdown form-control">
                                               @foreach($languages as $lang)
                                                    <option value="{{$lang->id}}" {{($lang->is_default == 1) ? 'selected' : '' }}>{{$lang->langEN}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active" class="font-weight-bold">Category</label>
                                        <div>
                                            <select name="filterCategory" id="filterCategory" class="multiselect-dropdown form-control">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Brand </label>
                                        <div>
                                            <select name="filterBrand" id="filterBrand" class="multiselect-dropdown form-control">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active" class="font-weight-bold">Status</label>
                                        <div>
                                            <select name="filterStatus" id="filterStatus" class="multiselect-dropdown form-control">
                                                <option value=" ">Select Status</option>
                                                <!-- <option value="Pending">Pending</option>
                                                <option value="Hidden">Hidden</option>
                                                <option value="Approved">Approved</option>
                                                <option value="Rejected">Rejected</option> -->
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                                <!-- <option value="Deleted">Deleted</option> -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Stock </label>
                                        <div>
                                            <select name="filterStock" id="filterStock" class="multiselect-dropdown form-control">
                                                <option value="all">All</option>
                                                <option value="No">In Stock</option>
                                                <option value="Yes">Out of Stock</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Customize </label>
                                        <div>
                                            <select name="filterCustomize" id="filterCustomize" class="multiselect-dropdown form-control">
                                                <option value="all">All</option>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
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
                                                <button type="button" id="btnFilterProduct" class="btn btn-primary">Search</button>
                                                <button type="button" id="resetFilter" class="btn btn-primary">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>


                <div class="main-card mb-3 card element-block-example">
                    <div class="card-body">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a data-toggle="tab" href="#tabAllProducts" class="active nav-link">All</a>
                            </li>
                            <li class="nav-item">
                                <a data-toggle="tab" href="#tabActiveProducts" class="nav-link">Active</a>
                            </li>
                            <li class="nav-item">
                                <a data-toggle="tab" href="#tabInActiveProducts" class="nav-link" id="123">Inactive</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a data-toggle="tab" href="#tabRejectedProducts" class="nav-link">Rejected</a>
                            </li>
                            <li class="nav-item">
                                <a data-toggle="tab" href="#tabOutOfStockProducts" class="nav-link">Out of Stock</a>
                            </li> -->
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tabAllProducts" role="tabpanel">
                                @include('admin.product.allProduct')
                            </div>
                            <div class="tab-pane" id="tabActiveProducts" role="tabpanel">
                                @include('admin.product.activeProduct')
                            </div>
                            <div class="tab-pane" id="tabInActiveProducts" role="tabpanel">
                                @include('admin.product.inactiveProduct')
                            </div>
                            <!-- <div class="tab-pane" id="tabRejectedProducts" role="tabpanel">
                                @include('admin.product.rejectedProduct')
                            </div>
                            <div class="tab-pane" id="tabOutOfStockProducts" role="tabpanel">
                                @include('admin.product.outOfStockProduct')
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Start -->
<div class="modal fade" id="productDeleteModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="productIdForDelete" id="productIdForDelete" readonly="true">
                <p class="mb-0" id="message">Are you Sure?</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmDelete">Yes</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Over -->


 <!-- Product copy -->
 <div class="modal" id="productCopyModel" tabindex="-1" role="dialog" aria-labelledby="productCopyModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="productIdForCopyModel" id="productIdForCopyModel">
                    <p class="mb-0" id="message">Are you Sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="confirmProdCopy">Yes</button>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
    <script src="{{asset('/public/assets/js/product/products.js')}}"></script>
@endpush
@endsection
