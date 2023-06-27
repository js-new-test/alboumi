@extends('admin.layouts.master')
<title>Show Brand | Alboumi</title>

@section('content')

<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
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
                                        <i class="lnr-users opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Brands</span>
                                </div>
                                <div class="page-title-subheading opacity-10">
                                    <nav class="" aria-label="breadcrumb">
				                        <ol class="breadcrumb">
				                            <li class="breadcrumb-item"><a href="javascript:void(0);">Brand</a></li>
				                            <li class="active breadcrumb-item" aria-current="page"><a href="{{url('/admin/manufacturers')}}">Brands List</a></li>
				                            <li class="active breadcrumb-item" aria-current="page">Edit Existing Brand</li>
				                        </ol>
				                    </nav> 
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="col-md-12">
                	<div class="row">
                		
                    	<div class="col-md-3">
                            <div class="main-card card">
                                <div class="card-body">
                                    <ul class="list-group">
                                        <a href="../{{ $manufacturer->id }}/showBrand">
                                        	<li class="list-group-item">Summary</li>
                                        </a>
                                        <a href="../manufacturers/edit?id={{ $manufacturer->id }}">
                                        	<li class="list-group-item">Update Brand Details</li>
                                        </a>
                                    </ul>
                                </div>
                            </div>
                    	</div>

                        <div class="col-md-9">
	                        <div class="row">
	                            <div class="col-md-3">
	                                <div class="main-card mb-3 card">
	                                    <div class="card-body">
	                                        <h5 class="card-title">Total Active Products</h5>
	                                        <h3><span class="">0</span></h3>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="col-md-3">
	                                <div class="main-card mb-3 card">
	                                    <div class="card-body">
	                                        <h6 class="card-title">Total Inactive Products</h6>
	                                        <h3><span class="">0</span></h3>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="col-md-3">
	                                <div class="main-card mb-3 card">
	                                    <div class="card-body">
	                                        <h6 class="card-title">Total Seller</h6>
	                                        <h3><span class="">0</span></h3>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="col-md-3">
	                                <div class="main-card mb-3 card">
	                                    <div class="card-body">
	                                        <h5 class="card-title">Total Sales</h5>
	                                        <h3><span class="">0</span></h3>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
                	</div>
                </div>

	        </div>
	    </div>
    </div>
</div>
@endsection
