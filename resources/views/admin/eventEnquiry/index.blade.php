@extends('admin.layouts.master')
<title>Event Enquiry | Alboumi</title>

@section('content')
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
</script>
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
                                        <i class="pe-7s-note2 opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Event Enquiry</span>
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
                                                <a href="javascript:void(0);">Event Enquiry</a>
                                            </li>

                                            <li class="active breadcrumb-item" aria-current="page">
                                            Event Enquiry List
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="page-title-actions">
                            <div class="d-inline-block dropdown">
                                <button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm" id="divFilterToggle">
                                    <i aria-hidden="true" class="fa fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-card mb-3 card" id="filterEnqDiv" style="display:none">
                    <div class="card-body">
                        <h5 class="card-title"><i aria-hidden="true" class="fa fa-filter"></i>  Filter</h5>
                        <form class="col-md-10 mx-auto" id="filterEventEnqForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group font-weight-bold">
                                        <label for="filter_date" class="mr-sm-2">Select Date Range</label>
                                        <input type="text" class="form-control" name="daterange" id="daterange" value="" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Languages </label>
                                        <div>
                                            <select name="filterLanguage" id="filterLanguage" class="form-control">
                                               @foreach($languages as $lang)
                                                    <option value="{{$lang->id}}" {{($lang->is_default == 1) ? 'selected' : '' }}>{{$lang->text}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Event </label>
                                        <div>
                                            <select name="filterEvent" id="filterEvent" class="form-control">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Package </label>
                                        <div>
                                            <select name="filterPackage" id="filterPackage" class="form-control">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active" class="font-weight-bold">Status</label>
                                        <div>
                                            <select name="filterStatus" id="filterStatus" class="form-control">
                                                <option value="-1" selected>Select Status</option>
                                                <option value="0">Pending</option>
                                                <option value="1">In Process</option>
                                                <option value="2">Image Uploaded</option>
                                                <option value="3">Completed</option>
                                                <option value="4">Approved</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Payment Status </label>
                                        <div>
                                            <select name="filterPaymentStatus" id="filterPaymentStatus" class="form-control">
                                                <option value="-1" selected>Select Payment Status</option>
                                                <option value="all">All</option>
                                                <option value="0">Unpaid</option>
                                                <option value="1">Paid</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @if($user_role->role_title != 'Photographer')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Photographer </label>
                                        <div>
                                            <select name="photographerFilter" id="photographerFilter" class="form-control">
                                                <option value="-1" selected>Select Photographer</option>
                                                <option value="all">All</option>
                                                <option value="assigned">Assigned</option>
                                                <option value="not_assigned">Not Assigned</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-1 offset-md-5">
                                                <button type="button" id="btnFilterEventEnq" class="btn btn-primary">Search</button>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" id="resetFilter" class="btn btn-primary">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <table style="width:100%;" id="eventEnqListing"
                            class="table table-hover table-striped table-bordered nowrap dataTable no-footer">
                            <thead>
                                <tr class="text-center">
                                    <th>Sr.No</th>
                                    <th>ID</th>
                                    <th>Price Per Photo</th>
                                    <th>Event Date</th>
                                    <th>Customer</th>
                                    <th>Event</th>
                                    <th>Package</th>
                                    <th>Additional Packages</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th>Date of Enquiry</th>
                                    <th>Photographer</th>
                                    <th>Action</th>
                                    <th>Total Amount</th>
                                    <th>Advance Payment</th>
                                    <th>Free Photo Download</th>
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
    <div class="modal fade bd-example-modal-sm" id="allocatePhotographerModel" tabindex="-1" role="dialog" aria-labelledby="allocatePhotographerModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allocatePhotographerModelLabel">Photographer Allocation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        @csrf
                        <input type="hidden" name="event_enq_id" id="event_enq_id">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Select Photographer </label>
                                    <div>
                                        <select name="photographer" id="photographer" class="multiselect-dropdown form-control">
                                            @foreach($users as $user)
                                                <option value="{{$user->id}}">{{$user->firstname}} {{$user->lastname}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </form>                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="add_alloc_photographer">Save</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->
    <!-- Modal for status change -->
    <div class="modal fade bd-example-modal-sm" id="changeEnqStatusModal" tabindex="-1" role="dialog" aria-labelledby="changeEnqStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeEnqStatusModalLabel">Change Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        @csrf
                        <input type="hidden" name="event_enq_id" id="event_enq_id">
                        <input type="hidden" name="currentStatus" id="currentStatus">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Select Status </label>
                                    <div>
                                        <select name="enqStatus" id="enqStatus" class="form-control">
                                            <option value="0">Pending</option>
                                            <option value="1">In Process</option>
                                            <option value="2">Image Uploaded</option>
                                            <option value="3">Completed</option>
                                            <option value="4">Approved</option>
                                        </select>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </form>                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="changeEnqStatusBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal for adding photo price -->
     <div class="modal fade bd-example-modal-sm" id="updatePhotoPriceModal" tabindex="-1" role="dialog" aria-labelledby="updatePhotoPriceLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePhotoPriceLabel">Update Enquiry Price</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        @csrf
                        <input type="hidden" name="event_enq_id" id="event_enq_id">
                        <input type="hidden" name="currentPhotoPrice" id="currentPhotoPrice">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Total Amount</label>
                                    <div>
                                        <input type="number" name="total_amt" id="total_amt" class="form-control">
                                    </div>
                                </div>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Advance Payment</label>
                                    <div>
                                        <input type="number" name="advance_payment" id="advance_payment" class="form-control">
                                    </div>
                                </div>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Price Per Photo<span style="color:red">*</span></label>
                                    <div>
                                        <input type="number" name="price_per_photo" id="price_per_photo" class="form-control">
                                    </div>
                                </div>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div>
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" id="free_photo_download" class="custom-control-input" name="free_photo_download">
                                            <label class="custom-control-label font-weight-bold" for="free_photo_download">Free Photo Download</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updatePhotoPriceBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('public/assets/js/eventEnq/eventEnq.js')}}"></script>
<script src="{{asset('/public/assets/js/vendors/form-components/daterangepicker.js')}}"></script>
@endpush
