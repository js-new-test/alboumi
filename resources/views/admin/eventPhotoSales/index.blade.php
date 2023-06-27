@extends('admin.layouts.master')
<title>All Event Photo Sales | Alboumi</title>

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
                                    <span class="d-inline-block">Event Photo Sales</span>
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
                                                <a href="javascript:void(0);">Event Photo Sales</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                            Event Photo Sales List
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
                                    <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group font-weight-bold">
                                        <label for="daterange" class="mr-sm-2">Order Date</label>
                                        <input type="text" class="form-control" name="daterange" id="daterange" value="" />
                                        <input type="hidden" name="startDate" id="startDate" value="">
                                        <input type="hidden" name="endDate" id="endDate" value="">
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
                                        <label class="font-weight-bold">Status</label>
                                        <div>
                                            <select name="status" id="status" class="form-control">
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
                        <table style="width:100%;" id="allEventPhotoSales" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <!-- <th><input name="selectAllOrders" value="1" id="selectAllOrders" type="checkbox" /></th> -->
                                    <th>Order ID</th>
                                    <th>ID</th>
                                    <th>Customer Name</th>
                                    <th>Customer Email</th>
                                    <th>Amount ({{ ($defaultCurrency->currency_code != '') ? $defaultCurrency->currency_code : '' }})</th>
                                    <th>Payment Type</th>
                                    <th>Status</th>
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

<!-- mark as cancel -->
<!-- <div class="modal fade" id="orderMarkAsCancelled" tabindex="-1" role="dialog" aria-labelledby="orderMarkAsCancelledLabel"
        aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderMarkAsCancelledLabel">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <input type="hidden" name="orderId" id="cancelOrderId" value="">
                    <p class="mb-0" id="message">Are you Sure?</p>
                </form>
                <div class="row form-group">
                    <div class="col-md-3 offset-md-9">
                        <button type="submit" class="btn btn-secondary" data-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-primary" id="cancelOrderBtn">Yes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->

<!-- mark as delivered-->
<!-- <div class="modal fade" id="orderMarkAsDelivered" tabindex="-1" role="dialog" aria-labelledby="orderMarkAsDeliveredLabel"
        aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderMarkAsDeliveredLabel">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <input type="hidden" name="orderId" id="deliveredOrderId" value="">
                    <p class="mb-0" id="message">Are you Sure?</p>
                </form>

                <div class="row form-group">
                    <div class="col-md-3 offset-md-9">
                        <button type="submit" class="btn btn-secondary" data-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-primary" id="deliveredOrderBtn">Yes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->


<!-- Modal Start -->
    <!-- <div class="modal fade bd-example-modal-lg" style="z-index: 10000000 !important;" id="aramexShippingModel" tabindex="-1" role="dialog" aria-labelledby="aramexShippingModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAddressLabel">Aramex Shipment Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="aramexShippingDetailsForm" method="post">
                @csrf
                <input type="hidden" name="order_id" id="order_id">
                <input type="hidden" name="order_primary_id" id="order_primary_id">
                <input type="hidden" name="order_first_name" id="order_first_name">
                <input type="hidden" name="order_last_name" id="order_last_name">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-lg-6">
                            <div class="card-hover-shadow-2x mb-3 card">
                                <div class="card-header">Shipper Details</div>
                                <div style="padding:10px;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="contact_name" class="font-weight-bold">Contact Name<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="contact_name" placeholder="Contact Name" name="contact_name" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="company_name" class="font-weight-bold">Company Name<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" placeholder="Company Name" id="company_name" name="company_name" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="line_1" class="font-weight-bold">Line 1<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" placeholder="Line 1" id="line_1" name="line_1" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="line_2" class="font-weight-bold">Line 2<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" placeholder="Line 2" id="line_2" name="line_2" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="city" class="font-weight-bold">City<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" placeholder="City" id="city" name="city" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="country_code" class="font-weight-bold">Country Code<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" readonly placeholder="Country Code" id="country_code" name="country_code" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="phone_ext" class="font-weight-bold">Phone Extension<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="number" class="form-control" placeholder="Phone Extension" id="phone_ext" name="phone_ext" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="phone_number" class="font-weight-bold">Phone Number<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="number" class="form-control" placeholder="Phone Number" id="phone_number" name="phone_number" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="email" class="font-weight-bold">Email Address<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" placeholder="Email Address" id="email" name="email" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-6">
                            <div class="card-hover-shadow-2x mb-3 card">
                                <div class="card-header">Consignee Details</div>
                                <div style="padding:10px;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="contact_name" class="font-weight-bold">Address 1<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" id="consignee_address_1" placeholder="Address 1" name="consignee_address_1" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="company_name" class="font-weight-bold">Address 2</label>
                                                <div>
                                                    <input type="text" class="form-control" id="consignee_address_2" placeholder="Address 2" name="consignee_address_2" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="city" class="font-weight-bold">City<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" placeholder="City" id="consignee_city" name="consignee_city" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="country_code" class="font-weight-bold">Country Code<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" value="BH" placeholder="Country" readonly id="consignee_country" name="consignee_country" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="phone_ext" class="font-weight-bold">Phone Extension<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="number" class="form-control" value="973" placeholder="Phone Extension" id="consignee_phone_ext" name="consignee_phone_ext" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="phone_number" class="font-weight-bold">Phone Number<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="number" class="form-control" placeholder="Phone Number" id="consignee_phone_number" name="consignee_phone_number" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="email" class="font-weight-bold">Email Address<span class="text-danger">*</span></label>
                                                <div>
                                                    <input type="text" class="form-control" placeholder="Email Address" id="consignee_email" name="consignee_email" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 card">
                        <div class="card-header">Package Details</div>
                        <div style="padding:10px">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_name" class="font-weight-bold">Comments</label>
                                        <div>
                                            <input type="text" class="form-control" id="comments" placeholder="Comments" name="comments" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_name" class="font-weight-bold">Pickup Location</label>
                                        <div>
                                            <input type="text" class="form-control" id="pickup_location" value="Reception" placeholder="Pickup Location" name="pickup_location" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_name" class="font-weight-bold">Actual Weight (Kg)<span class="text-danger">*</span></label>
                                        <div>
                                            <input type="text" class="form-control" value="1.000" id="actual_weight" placeholder="Actual Weight (Kg)" name="actual_weight" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_name" class="font-weight-bold">Product Group</label>
                                        <select name="product_group" id="product_group" class="form-control">
                                            <option value="">----- Select Group -----</option>
                                            <option value="EXP">International Express</option>
                                            <option value="DOM">Domestic</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="product_type_section" style="margin-bottom: 20px;margin-top: -15px;"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label for="contact_name" class="font-weight-bold">Payment Type</label>
                                        <select name="payment_type" id="payment_type" class="form-control">
                                            <option value="P">Prepaid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_name" class="font-weight-bold">Number Of Pieces<span class="text-danger">*</span></label>
                                        <div>
                                            <input type="text" class="form-control" value="1" id="no_of_pieces" placeholder="Number Of Pieces" name="no_of_pieces" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_name" class="font-weight-bold">Description Of Goods<span class="text-danger">*</span></label>
                                        <div>
                                            <input type="text" class="form-control" id="goods_desc" placeholder="Description Of Goods" name="goods_desc" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_name" class="font-weight-bold">Goods Origin Country<span class="text-danger">*</span></label>
                                        <div>
                                            <input type="text" class="form-control" value="BH" readonly id="goods_origin_country" placeholder="Goods Origin Country" name="goods_origin_country" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="display: block;">
                    <div class="text-center">
                        <button type="button" class="btn btn-primary" id="save_aramex_details_btn">Create Shipment</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div> -->
<!-- Modal Over -->
<!-- generate invoice-->
<!-- <div class="modal fade" id="generateInvoice" tabindex="-1" role="dialog" aria-labelledby="generateInvoiceLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateInvoiceLabel">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <p class="mb-0" id="message">Are you Sure?</p>

                    <div class="row form-group">
                        <div class="col-md-3 offset-md-9">
                            <button type="submit" class="btn btn-secondary" data-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary" id="generateBtn">Yes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> -->
<!-- cancel order-->
<!-- <div class="modal fade" id="btnMarkAsCancelled" tabindex="-1" role="dialog" aria-labelledby="cancelOrder"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelOrder">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <p class="mb-0" id="message">Are you Sure?</p>

                    <div class="row form-group">
                        <div class="col-md-3 offset-md-9">
                            <button type="submit" class="btn btn-secondary" data-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary" id="cancelBtn">Yes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> -->
@endsection
@push('scripts')
<script src="{{asset('public/assets/js/eventPhotoSales/eventPhotoSales.js')}}"></script>
<script src="{{asset('/public/assets/js/vendors/form-components/daterangepicker.js')}}"></script>
@endpush
