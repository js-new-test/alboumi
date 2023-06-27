@extends('admin.layouts.master')
<title>View Invoice Details | Alboumi</title>

@section('content')
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
</script>
<style>
    .badge{
        text-transform: capitalize !important;
    }
    #bgWhiteBtns button, #printBtn, #backBtn{
        background:#fff;
        color:#3f6ad8;
    }
    #bgWhiteBtns button:hover, #printBtn:hover, #backBtn:hover{
        background:#3f6ad8;
        color:#fff;
    }
    [class*=" pe-7s-"], [class^=pe-7s-]{
        font-weight: bolder;
        font-size: 13px;
    }
</style>
<div class="app-container body-tabs-shadow fixed-header fixed-sidebar app-theme-gray closed-sidebar">
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
                                        <i class="pe-7s-cart opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">View Invoice Details</span>
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
                                                <a href="javascript:void(0);">View Invoice Details</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/invoices/')}}">Invoice List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                View Invoice Details
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
                        <h5 class="card-title">Invoice Details</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <h5><i class="fa fa-shopping-cart"></i>
                                            Invoice #{{ $orderDetails->invoice_id }} | {{ date('d M Y H:i A',strtotime($InvoiceDate)) }}</h5>
                                        </div>
                                        <div class="col-md-6 offset-md-1 text-right" id="bgWhiteBtns">
                                            <a href="{{ url('admin/invoices') }}" id="backBtn"
                                                class="btn-icon btn-square btn btn-primary btn-sm">
                                                <i class="fa fa-reply"></i> Back</a>

                                            <a href="{{ url('admin/invoices/printInvoice/'.$orderDetails->id) }}"
                                                class="btn-icon btn-square btn btn-primary btn-sm" target="_blank" id="printBtn">
                                                <i class="fa fa-print"></i> Print Invoice</a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form class="col-md-12 my-4">
                            <div class="tab-content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card grey-box">
                                            <div class="card-header text-white">
                                                <div class="row w-100">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6 my-auto">
                                                                <i class="fa fa-cogs"></i>&nbsp;Order Invoices
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="tab-pane fade show active" id="home">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Sr. No</th>
                                                                <th>Name</th>
                                                                <th>Other Info</th>
                                                                <th>Price</th>
                                                                <th>Quantity</th>
                                                                <th>Order Status</th>
                                                                <th>Sub Total</th>
                                                                <th>Shipment</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(!empty($orderProductDetails))
                                                            @foreach($orderProductDetails as $productDetail)
                                                            <tr>
                                                                <td class="text-center">{{ $productDetail['srNo'] }}</td>
                                                                <td>{{ $productDetail['product_name'] }} <br>
                                                                    @if(!empty($productDetail['variants']))
                                                                    @foreach($productDetail['variants'] as $prodVariants)
                                                                        @if(!empty($prodVariants))
                                                                        {{ $prodVariants['title'] }} : {{ $prodVariants['value'] }} <br>
                                                                        @endif
                                                                    @endforeach
                                                                    @endif
                                                                </td>
                                                                <td>@if(!empty($productDetail['promo_code']))
                                                                        <b>Promo Code : </b>{{ $productDetail['promo_code'] }}
                                                                    @endif
                                                                    </br>
                                                                    @if($productDetail['lady_operator'] == 0)
                                                                        <b>Lady Operator : </b>{{ 'No' }}
                                                                    @endif
                                                                    </br>
                                                                    @if($productDetail['gift_wrap'] == 0)
                                                                        <b>Gift Wrap : </b>{{ 'No' }}
                                                                    @endif
                                                                    </br>
                                                                    @if(!empty($productDetail['gift_message']))
                                                                        <b>Gift Message: </b>{{ $productDetail['gift_message'] }}
                                                                    @endif
                                                                    </br>
                                                                    @if(!empty($productDetail['message']))
                                                                        <b>Message: </b>{{ $productDetail['message'] }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $defaultCurrency->currency_code }}{{ $productDetail['price'] }}</td>
                                                                <td class="text-center">{{ $productDetail['quantity'] }}</td>
                                                                <td>{{ $productDetail['status'] }}</td>
                                                                <td>{{ $defaultCurrency->currency_code }}{{ number_format(($productDetail['quantity'] * $productDetail['price']),$decimalNumber, $decimalSeparator, $thousandSeparator) }}</td>
                                                                <td><b>Carrier :</b> {{ $productDetail['carrier'] }} <br>
                                                                    <b>Tracking Number :</b> {{ $productDetail['tracking_number'] }}
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                    <div class="col-md-12 my-4">
                                                        <div class="row">
                                                            <div class="col-md-6 pl-0">
                                                                <div class="card grey-box">
                                                                    <div class="card-header"><i class="fa fa-cogs"></i>&nbsp; Order Details</div>
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <label>Order # :</label>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label>{{ $orderDetails->order_id }}</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <label>Order Date & Time :</label>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label>{{ date('d M Y H:i A',strtotime($orderDetails->orderDate)) }}</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <label>Shipping Method :</label>
                                                                            </div>
                                                                            <div class="col-md-8">
                                                                                <label>{{ $orderDetails->shipping_method }}</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <label>Payment Mode :</label>
                                                                            </div>
                                                                            <div class="col-md-8">
                                                                                <label>{{ $orderDetails->payment_mode }}</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <label>Grand Total :</label>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label>{{ $defaultCurrency->currency_code }} {{ number_format($orderDetails->total,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <label>Payment Information :</label>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label>{{ $orderDetails->payment_method }}</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <label>Payment ID :</label>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label>{{ $orderDetails->payment_id }}</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <label>Invoice Status :</label>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                @if($orderDetails->invoice_status == 1)
                                                                                    <label class="badge badge-success">{{ 'Paid' }}</label>
                                                                                @endif
                                                                                @if($orderDetails->invoice_status == 2)
                                                                                    <label class="badge badge-danger">{{ 'Unpaid' }}</label>
                                                                                @endif
                                                                                @if($orderDetails->invoice_status == 3)
                                                                                    <label class="badge badge-warning">{{ 'Cancelled' }}</label>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 pl-0">
                                                                <div class="card price_box">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-md-5 offset-md-1">
                                                                                <label>Sub Total :</label>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label>{{ $defaultCurrency->currency_code }} {{ number_format($orderDetails->subtotal,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</label>
                                                                            </div>
                                                                        </div>
                                                                        @if($orderDetails->discount_amount > 0)
                                                                        <div class="row">
                                                                            <div class="col-md-5 offset-md-1">
                                                                                <label>Order Discount ({{ $orderDetails->promotions }}) :</label>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label>{{ $defaultCurrency->currency_code }} {{ number_format($orderDetails->discount_amount,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-5 offset-md-1">
                                                                                <label>Price After Discount :</label>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label>{{ $defaultCurrency->currency_code }} {{ number_format(($orderDetails->subtotal - $orderDetails->discount_amount),$decimalNumber, $decimalSeparator, $thousandSeparator) }}</label>
                                                                            </div>
                                                                        </div>
                                                                        @endif
                                                                        <div class="row">
                                                                            <div class="col-md-5 offset-md-1">
                                                                                <label>Shipping Cost :</label>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label>{{ $defaultCurrency->currency_code }} {{ number_format($orderDetails->total_shipping_cost,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-5 offset-md-1">
                                                                                <label>Tax :</label>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label>{{ $defaultCurrency->currency_code }} {{ number_format($orderDetails->tax_amount,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-5 offset-md-1">
                                                                                <label>Grand Total :</label>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label>{{ $defaultCurrency->currency_code }} {{ number_format($orderDetails->total,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</label>
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
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card grey-box">
                                            <div class="card-header">
                                                <div class="row w-100">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6 my-auto">
                                                                <i class="fa fa-cogs"></i>&nbsp; Billing Address
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <p>{{ $orderDetails->b_fullname }}</p>
                                                <p>{{ $orderDetails->b_address_line_1 }},{{ $orderDetails->b_address_line_2 }},<br>
                                                {{ $orderDetails->b_city }},{{ $orderDetails->b_state }},<br>
                                                {{ $orderDetails->b_country }} - {{ $orderDetails->b_pincode }}
                                                </p>
                                                <p><i class="fa fa-phone-square"></i> {{ $orderDetails->b_phone1 }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card grey-box">
                                            <div class="card-header">
                                                <div class="row w-100">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6 my-auto">
                                                                <i class="fa fa-cogs"></i>&nbsp;
                                                                @if($orderDetails->shipping_type == 'store_pickup')
                                                                    {{ 'Store Pickup'}}
                                                                @endif
                                                                @if($orderDetails->shipping_type == 'delivery')
                                                                    {{ 'Shipping Address'}}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @if($orderDetails->shipping_type == 'delivery')
                                                    <p>{{ $orderDetails->s_fullname }}</p>
                                                    <p>{{ $orderDetails->s_address_line_1 }},{{ $orderDetails->s_address_line_2 }},<br>
                                                    {{ $orderDetails->s_city }},{{ $orderDetails->s_state }},<br>
                                                    {{ $orderDetails->s_country }} - {{ $orderDetails->s_pincode }}
                                                    </p>
                                                    <p><i class="fa fa-phone-square"></i> {{ $orderDetails->s_phone1 }}</p>
                                                @endif
                                                @if($orderDetails->shipping_type == 'store_pickup')
                                                @if(!empty($storePickupAddress))
                                                    <p>{{ $storePickupAddress['title'] }}</p>
                                                    <p>{{ $storePickupAddress->address_1 }},{{ $storePickupAddress->address_2 }}<br>
                                                    </p>
                                                    <p><i class="fa fa-phone-square"></i> {{ $storePickupAddress->phone }}</p>
                                                @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card grey-box">
                                            <div class="card-header"><i class="fa fa-cogs"></i>&nbsp; Customer Information</div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label>Customer Name :</label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label>{{ $orderDetails->first_name }} {{ $orderDetails->last_name }}</label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label>Email :</label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label>{{ $orderDetails->email }}</label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label>Customer Group :</label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        @if($orderDetails->group_name == null)
                                                            <label>{{ 'Not Assigned' }}</label>
                                                        @else
                                                            <label>{{ $orderDetails->group_name }}</label>
                                                        @endif
                                                    </div>
                                                </div>
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

    <!-- generate invoice-->
    <div class="modal fade" id="generateInvoice" tabindex="-1" role="dialog" aria-labelledby="generateInvoiceLabel"
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
                    <form method="post" action="{{ url('admin/orders/generateOrderInvoice') }}">
                    @csrf
                        <input type="hidden" name="orderId" value="{{$orderDetails->id}}">
                        <input type="hidden" name="orderStatus" value="{{$orderDetails->status}}">
                        <p class="mb-0" id="message">Are you Sure?</p>

                        <div class="row form-group">
                            <div class="col-md-3 offset-md-9">
                                <button type="submit" class="btn btn-secondary" data-dismiss="modal">No</button>
                                <button type="submit" class="btn btn-primary">Yes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-lg" style="z-index: 10000000 !important;" id="aramexShippingModel" tabindex="-1" role="dialog" aria-labelledby="aramexShippingModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAddressLabel">Aramex Shipment Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="aramexShippingDetailsForm" method="post">
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
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save_aramex_details_btn">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/js/orders/orders.js')}}"></script>
@endpush
