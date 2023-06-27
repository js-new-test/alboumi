@extends('admin.layouts.master')
<title>{{ $page_name }} | {{ $project_name }} </title>

@section('content')
<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
    @include('admin.include.header')
    <div class="app-main">
        @include('admin.include.sidebar')
        <div class="app-main__outer" style="width:100%;">
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
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Customer Groups List
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <a href="{{url('/admin/custGroups/addGroup')}}"><button
                                        class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i
                                            class="fa fa-plus btn-icon-wrapper"> </i>New Group</button></a>
                            </div>
                           
                        </div>
                    </div>
                </div>
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <table id="custGroupsListing"
                            class="table table-hover table-striped table-bordered desktop-table-layout-fix" style="width: 100%;">
                            <thead>
                                <tr class="text-center">
                                    <th>Sr.No</th>
                                    <th>ID</th>
                                    <th>Group Name</th>
                                 
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

    <!-- Modal for delete group -->
    <div class="modal" id="groupDeleteModel" tabindex="-1" role="dialog" aria-labelledby="groupDeleteModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="groupIdForDelete" id="groupIdForDelete">
                    <p class="mb-0" id="message">Are you Sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="confirmDelete">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/js/custGroups/custGroups.js')}}"></script>
@endpush
