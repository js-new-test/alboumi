@extends('admin.layouts.master')
<title>All Invoices | Alboumi</title>

@section('content')

<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
    var defaultLangSym = <?php echo json_encode($defaultCurrency->currency_code); ?>;
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
                                    <span class="d-inline-block">Invoices</span>
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
                                                <a href="javascript:void(0);">Invoices</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                            Invoices List
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
                        <form class="col-md-12 mx-auto" id="filterOrderInvoicesForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Invoice ID</label>
                                        <div>
                                            <input type="text" name="invoiceId" id="invoiceId" placeholder="Invoice ID" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Order ID </label>
                                        <div>
                                            <input type="text" name="orderId" id="orderId" placeholder="Order ID" class="form-control">
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
                                    <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group font-weight-bold">
                                        <label for="invoiceDaterange" class="mr-sm-2">Invoice Date</label>
                                        <input type="text" class="form-control" name="invoiceDaterange" id="invoiceDaterange" value="" />
                                        <input type="hidden" name="invoiceStartDate" id="invoiceStartDate" value="">
                                        <input type="hidden" name="invoiceEndDate" id="invoiceEndDate" value="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group font-weight-bold">
                                        <label for="orderDaterange" class="mr-sm-2">Order Date</label>
                                        <input type="text" class="form-control" name="orderDaterange" id="orderDaterange" value="" />
                                        <input type="hidden" name="orderStartDate" id="orderStartDate" value="">
                                        <input type="hidden" name="orderEndDate" id="orderEndDate" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Invoice Status</label>
                                        <div>
                                            <select name="invoiceStatus" id="invoiceStatus" class="form-control">
                                                <option value="">Select Status</option>
                                                <option value="1">Paid</option>
                                                <option value="2">Unpaid</option>
                                                <option value="3">Cancelled</option>
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
                                                <button type="button" id="btnFilterInvoices" class="btn btn-primary">Search</button>
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
                      <form  action="{{url('/admin/invoices/printbulkInvoice')}}" method="post"/>
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm btn-right printBtn" type="submit" name="submit"><i class="fa fa-print btn-icon-wrapper"> </i>Print</button>

                        <table style="width:100%;" id="allInvoicesList" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th><input name="selectAllInv" value="1" id="selectAllInv" type="checkbox" /></th>
                                    <th>Invoice ID</th>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Customer Email</th>
                                    <th>Grand Total</th>
                                    <th>Status</th>
                                    <th>Invoice Date</th>
                                    <th>Action</th>
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
<script src="{{asset('public/assets/js/orderInvoices/invoice.js')}}"></script>
<script src="{{asset('/public/assets/js/vendors/form-components/daterangepicker.js')}}"></script>
@endpush
