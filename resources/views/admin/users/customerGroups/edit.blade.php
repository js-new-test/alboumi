@extends('admin.layouts.master')
<title>{{ $page_name }} | {{ $project_name }} </title>

@section('content')
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
                                        <i class="lnr-users"></i>
                                    </span>
                                    <span class="d-inline-block">Customer Groups</span>
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
                                                <a href="javascript:void(0);">Customer Groups</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/custGroups')}}">Customer Groups List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Update Group
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
                        <h5 class="card-title">Update Customer Group</h5>
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

                        <form id="updateGroupForm" class="col-md-10 mx-auto" method="post"
                            action="{{ url('/admin/custGroups/updateGroup') }}" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="group_id" value="{{ $custGroup['id'] }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group_name" class="font-weight-bold">Group Name</label><span class="text-danger">*</span>
                                        <div>
                                            <input type="text" class="form-control" id="group_name" name="group_name"
                                                placeholder="Enter group name" value="{{ $custGroup->group_name }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2 offset-md-1">
                                                <button type="submit" class="btn btn-primary btn-shadow w-100">Update
                                                    Group</button>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('/admin/custGroups') }}">
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
<script src="{{asset('public/assets/js/custGroups/custGroups.js')}}"></script>
@endpush
