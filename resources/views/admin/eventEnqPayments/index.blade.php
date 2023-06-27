@extends('admin.layouts.master')
<title>All Event Enquiry Payments | Alboumi</title>

@section('content')
<link rel="stylesheet" href="{{asset('public/assets/frontend/css/loader-style.css')}}">
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
</script>
<style>
    .badge{
        text-transform: capitalize !important;
    }
</style>

<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
    @include('admin.include.header')
    <div class="app-main">
        @include('admin.include.sidebar')
        <div class="app-main__outer w-100">
            <div class="app-main__inner">
                <div class="app-page-title app-page-title-simple">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div>
                                <div class="page-title-head center-elem">
                                    <span class="d-inline-block pr-2">
                                        <i class="pe-7s-cart opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Event Enquiry Payments</span>
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
                                                <a href="javascript:void(0);">Event Enquiry Payments</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                            Event Enquiry Payments List
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
                <div class="main-card mb-3 card" id="FilterDiv">
                    <div class="card-body">
                        <h5 class="card-title"><i aria-hidden="true" class="fa fa-filter"></i>  Filter</h5>
                        <form class="col-md-12 mx-auto" id="filterEventPhotoSalesForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Order ID </label>
                                        <div>
                                            <input type="text" name="orderId" id="orderId" placeholder="Order ID" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Payment ID </label>
                                        <div>
                                            <input type="text" name="paymentId" id="paymentId" placeholder="Payment ID" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Customer Name </label>
                                        <div>
                                            <input type="text" name="custName" id="custName" placeholder="Customer Name" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Customer Email </label>
                                        <div>
                                            <input type="email" name="custEmail" id="custEmail" placeholder="Customer Email" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Payment Status</label>
                                        <div>
                                            <select name="paymentStatus" id="paymentStatus" class="form-control">
                                                <option value="">Select Status</option>
                                                <option value="-1" selected>All</option>
                                                <option value="0">Pending</option>
                                                <option value="1">Success</option>
                                                <option value="2">Failed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Payment Type</label>
                                        <div>
                                            <select name="paymentType" id="paymentType" class="form-control">
                                                <option value="">Select Payment Type</option>
                                                <option value="-1" selected>All</option>
                                                <option value="1">Credit Card</option>
                                                <option value="2">Debit Card</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group font-weight-bold">
                                        <label for="daterange" class="mr-sm-2">Payment Date</label>
                                        <input type="text" class="form-control" name="daterange" id="daterange" value="" />
                                        <input type="hidden" name="startDate" id="startDate" value="">
                                        <input type="hidden" name="endDate" id="endDate" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-1 offset-md-5">
                                                <button type="button" id="btnFilterEventPhotoSales" class="btn btn-primary">Search</button>
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
                        <table style="width:100%;" id="allEventEnqPayments" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>Order ID</th>
                                    <th>ID</th>
                                    <th>Customer Name</th>
                                    <th>Customer Email</th>
                                    <th>Payment Type</th>
                                    <th>Payment Status</th>
                                    <th>Amount ({{ ($defaultCurrency->currency_code != '') ? $defaultCurrency->currency_code : '' }})</th>
                                    <th>Payment ID</th>
                                    <th>Created Date</th>
                                </tr>
                            </thead>
                        </table>
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
<script src="{{asset('public/assets/js/eventEnqPayments/eventEnqPayments.js')}}"></script>
<script src="{{asset('/public/assets/js/vendors/form-components/daterangepicker.js')}}"></script>
@endpush
