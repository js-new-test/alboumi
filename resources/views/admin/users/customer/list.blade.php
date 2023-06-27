@extends('admin.layouts.master')
<title>List Customer | Alboumi</title>

@section('content')
<script>
    var custGroups = <?php echo json_encode($custGroups); ?>;
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
</script>
<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
    @include('admin.include.header')
	<div class="app-main">
        @include('admin.include.sidebar')
        <div class="app-main__outer" style="width: 100%;">
            <div class="app-main__inner">
                <div class="app-page-title app-page-title-simple">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div>
                                <div class="page-title-head center-elem">
                                    <span class="d-inline-block pr-2">
                                        <i class="lnr-users opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Customer</span>
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
                                                <a href="javascript:void(0);">Customer</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Customer List
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm custGroupRemoveBtn">Remove Group</button>
                                <button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm custGroupAssignBtn"><i class="fa fa-plus btn-icon-wrapper"> </i>Assign Group</button>
                                <a href="{{url('/admin/customer/export')}}"><button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i class="fa fa-download btn-icon-wrapper"></i>Export</button></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <table style="width: 100%;" id="customer_list" class="table table-hover table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th><input name="selectAllCust" value="1" id="selectAllCust" type="checkbox" /></th>
                                        <th>Unique ID</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Customer Group</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>IP Address</th>
                                        <th>Created At</th>
                                        <th>Is Active</th>
                                        <th>Is Verified</th>
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
    <!-- Modal Start -->
    <div class="modal fade" id="customerIsActiveModel" tabindex="-1" role="dialog" aria-labelledby="customerIsActiveModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerIsActiveModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="customer_id" id="customer_id">
                    <input type="hidden" name="is_active" id="is_active">
                    <p class="mb-0" id="message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="customerIsActive">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->
     <!-- Modal Start -->
     <div class="modal fade bd-example-modal-sm" id="deleteCustomerModel" tabindex="-1" role="dialog" aria-labelledby="deleteCustomerModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCustomerModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="customer_id" id="customer_id">
                    <input type="hidden" name="is_deleted" id="is_deleted">
                    <p class="mb-0" id="delete_message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="customerDelete">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->

    <!-- assign group modal -->
    <div class="modal" id="custGroupModel" tabindex="-1" role="dialog" aria-labelledby="custGroupAssignModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Groups</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="col-md-10 mx-auto">
                        <input type="hidden" name="custGroupCounterValue" id="custGroupCounterValue" readonly="true">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="custGroup_ids">Customer Groups</label>
                                    <select class="form-control multiselect-dropdown" name="custGroupIds" id="custGroupIds">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="confirmAssign">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- remove cust group modal -->
    <div class="modal fade" id="removeCustGroupModel" tabindex="-1" role="dialog" aria-labelledby="removeCustGroupLabel"
    aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeCustGroupLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <p class="mb-0" id="message">Are you Sure?</p>

                        <div class="row form-group">
                            <div class="col-md-3 offset-md-9">
                                <button type="submit" class="btn btn-secondary" data-dismiss="modal">No</button>
                                <button type="button" class="btn btn-primary" id="confirmRemove">Yes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/custom/datatables/user/customer-list-datatable.js')}}"></script>
@endpush