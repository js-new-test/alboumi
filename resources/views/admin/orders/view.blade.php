@extends('admin.layouts.master')
<title>View Order Details | Alboumi</title>

@section('content')
<style>
#toast-container {
    z-index: 9999999999;
}
</style>
<link rel="stylesheet" href="{{asset('public/assets/frontend/css/loader-style.css')}}">
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
    var orderId = <?php echo json_encode($orderDetails->id); ?>;
</script>
<style>
    .badge{
        text-transform: capitalize !important;
    }
    #bgWhiteBtns button, #printBtn, #backBtn, #btnPrintLabelBtn{
        background:#fff;
        color:#3f6ad8;
    }
    #bgWhiteBtns button:hover, #printBtn:hover, #backBtn:hover, #btnPrintLabelBtn:hover{
        background:#3f6ad8;
        color:#fff;
    }
    [class*=" pe-7s-"], [class^=pe-7s-]{
        font-weight: bolder;
        font-size: 13px;
    }
</style>
<div id="ajax-loader" style="z-index: 9999999999;">
  <div class="cv-spinner">
  	<img src="{{ asset('public/assets/frontend/img/Loader.svg') }}" class="spinner">
  </div>
</div>
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
                                    <span class="d-inline-block">View Order Details</span>
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
                                                <a href="javascript:void(0);">View Order Details</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/orders/')}}">Order List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                View Order Details
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
                        <h5 class="card-title">Order Details</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5><i class="fa fa-shopping-cart"></i>
                                            Order #{{ $orderDetails->order_id }} | {{ date('d M Y H:i A',strtotime($OrderDate)) }}</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right" id="bgWhiteBtns">
                                            <a href="{{ url('admin/orders') }}" id="backBtn"
                                                class="btn-icon btn-square btn btn-primary btn-sm">
                                                <i class="fa fa-reply"></i> Back</a>

                                            <a href="{{ url('admin/orders/printOrder/'.$orderDetails->id) }}"
                                                class="btn-icon btn-square btn btn-primary btn-sm" target="_blank" id="printBtn">
                                                <i class="fa fa-print"></i> Print Order</a>

                                            <button type="button" data-toggle="modal" data-target="#generateInvoice"
                                                class="btn-icon btn-square btn btn-primary btn-sm">
                                                <i class="pe-7s-ticket"></i> Generate Invoice</button>

                                            @if($orderDetails->slug == 'order-received')
                                            <button type="button" id="btnMarkAsCancelled" data-toggle="modal" data-target="#orderMarkAsCancelled"
                                                class="btn-icon btn-square btn btn-primary btn-sm"><i class="fa fa-times-circle"></i>
                                                 Mark As Cancelled</button>

                                            <button type="button" id="btnMarkAsShipped"
                                                class="btn-icon btn-square btn btn-primary btn-sm" data-order_id="{{ $orderDetails->id }}">
                                                <i class="fa fa-truck"></i>
                                                Mark As Shipped (Aramex)</button>

                                            <button type="button" id="btnMarkAsShippedWithoutAramex" data-toggle="modal" data-target="#markOrderAsShipped"
                                                class="btn-icon btn-square btn btn-primary btn-sm">
                                                <i class="fa fa-truck"></i>
                                                Mark As Shipped</button>
                                            @endif

                                            @if($orderDetails->slug == 'shipped')
                                            <button type="button" id="btnMarkAsDelivered" data-toggle="modal" data-target="#orderMarkAsDelivered"
                                                class="btn-icon btn-square btn btn-primary btn-sm"><i class="fa fa-check-circle"></i>
                                                Mark As Delivered</button>

                                            <a id="btnPrintLabelBtn" href="{{ $orderDetails->label_url }}" target="_blank"
                                                class="btn-icon btn-square btn btn-primary btn-sm">
                                                <i class="fa fa-check-circle"></i>
                                                Print Label</a>
                                            @endif
                                            @if($orderDetails->slug == 'delivered')
                                            <a id="btnPrintLabelBtn" href="{{ $orderDetails->label_url }}" target="_blank"
                                                class="btn-icon btn-square btn btn-primary btn-sm">
                                                <i class="fa fa-check-circle"></i>
                                                Print Label</a>
                                            @endif
                                            <a href="#">
                              	                	<button class="btn-icon btn-square btn btn-primary btn-sm" id="addNotes" data-toggle="modal" data-target="#addNotesModal">Add Notes +</button>
                              	                </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form class="col-md-12 my-4">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a data-toggle="tab" href="#tabOrderDetails" class="nav-link active">Details</a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" href="#tabOrderNotes" class="nav-link">Order Notes</a>
                                </li>
                                <li class="nav-item">
                                    <a data-toggle="tab" href="#tabOrderActivity" class="nav-link">Order Activities</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                              <div class="tab-pane active" id="tabOrderDetails" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card grey-box">
                                            <div class="card-header text-white">
                                                <div class="row w-100">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <div class="col-md-6 my-auto">
                                                                <i class="fa fa-cogs"></i>&nbsp;Order product Details
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
                                                                <th>Image</th>
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
                                                            @foreach($orderProductDetails as $productDetail)
                                                            <tr>
                                                                <td class="text-center">
                                                                    @if(!empty($productDetail['print_files']))
                                                                        <img src=<?php echo $productDetail['prodImage']; ?> width="100px"></br>
                                                                        <a href="{{ url('admin/orders/downloadOrderProdImages/'.$productDetail['id']) }}" target="_blank">Print Files</a></br>
                                                                        <a href="{{ url('admin/orders/downloadOrderProductFiles/'.$productDetail['id']) }}" target="_blank">Product Files</a></br>
                                                                        <a href="{{ url('admin/orders/downloadPrintFilesPdf/'.$productDetail['id']) }}" target="_blank">Download as PDF</a>
                                                                    @else
                                                                        @if(!empty($productDetail['prodImage']))
                                                                        <img src="{{ $productDetail['prodImage'] }}" width="100px"></br>
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                                <td>{{ $productDetail['product_name'] }} <br>
                                                                    @if(!empty($productDetail['variants']))
                                                                        @foreach($productDetail['variants'] as $prodVariants)
                                                                            @if(!empty($prodVariants['title'])){{ $prodVariants['title'] }} : @endif  @if(!empty($prodVariants['value'])){{ $prodVariants['value'] }}@endif <br>
                                                                        @endforeach
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(!empty($productDetail['sku']))
                                                                        <b>SKU: </b>{{ $productDetail['sku'] }}
                                                                        </br>
                                                                    @endif
                                                                    @if(!empty($productDetail['promo_code']))
                                                                        <b>Promo Code : </b>{{ $productDetail['promo_code'] }}
                                                                        </br>
                                                                    @endif
                                                                    @if($productDetail['lady_operator'] == 0)
                                                                        <b>Lady Operator : </b>{{ 'No' }}
                                                                        </br>
                                                                    @else
                                                                        <b>Lady Operator : </b>{{ 'Yes' }}
                                                                        </br>
                                                                    @endif
                                                                    @if($productDetail['gift_wrap'] == 0)
                                                                        <b>Gift Wrap : </b>{{ 'No' }}
                                                                        </br>
                                                                    @else
                                                                        <b>Gift Wrap : </b>{{ 'Yes' }}
                                                                        </br>
                                                                    @endif
                                                                    @if(!empty($productDetail['gift_message']))
                                                                        <b>Gift Message: </b>{{ $productDetail['gift_message'] }}
                                                                        </br>
                                                                    @endif
                                                                    @if(!empty($productDetail['message']))
                                                                        <b>Message for Staff: </b>{{ $productDetail['message'] }}
                                                                        </br>
                                                                    @endif
                                                                    @if(!empty($productDetail['photobook_caption']))
                                                                        <b>Photo Caption: </b>{{ $productDetail['photobook_caption'] }}
                                                                        </br>
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
                                                                        @if(!empty($orderDetails->loyalty_card))
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <label>Loyalty Number :</label>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <a href="#" data-toggle="modal" data-target="#editLoyaltyNumberModal" id="loyaltyNumberNew">{{ $orderDetails->loyalty_card }}</a>
                                                                            </div>
                                                                        </div>
                                                                        @endif
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
                                                                                <label>Order Status :</label>
                                                                            </div>
                                                                            <div class="col-md-5">
                                                                                @if($orderDetails->slug == "delivered" || $orderDetails->slug == "ready-collect")
                                                                                    <label class="badge badge-success">{{ $orderDetails->status }}</label>
                                                                                @endif
                                                                                @if($orderDetails->slug == "shipped" || $orderDetails->slug == "ready-to-dispatch"  || $orderDetails->slug == "in-transit" || $orderDetails->slug == "order-received" || $orderDetails->slug == "packed")
                                                                                    <label class="badge badge-warning">{{ $orderDetails->status }}</label>
                                                                                @endif
                                                                                @if($orderDetails->slug == "pending" || $orderDetails->slug == "cancelled" || $orderDetails->slug == "payment-failed" || $orderDetails->slug == "awaiting-payment-confirmation")
                                                                                    <label class="badge badge-danger">{{ $orderDetails->status }}</label>
                                                                                @endif
                                                                                @if($orderDetails->slug == "order-under-process" || $orderDetails->slug == "out-for-delivery")
                                                                                    <label class="badge badge-orange">{{ $orderDetails->status }}</label>
                                                                                @endif


                                                                            </div>
                                                                            <div class="col-md-3">
                                                                              <a href="#" data-toggle="modal" data-target="#changeStatusModel" id="changeStatus">Change Status</a>
                                                                            </div>
                                                                          </div>
                                                                        @if(!empty($orderDetails['message']))
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <label>Customer Message :</label>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label class="badge badge-primary">{{ $orderDetails->message }}</label>
                                                                            </div>
                                                                        </div>
                                                                        @endif
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
                                                            <div class="col-md-6 text-right">
                                                                <button type="button" class="btn btn-icon btn-square btn-sm edit_btn_orders" data-toggle="modal" data-target="#editBillingAddressModal">
                                                                    <i class="fa fa-trash"></i>&nbsp;
                                                                    Edit
                                                                </button>
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
                                                            @if($orderDetails->shipping_type == 'delivery')
                                                            <div class="col-md-6 text-right">
                                                                <button type="button" class="btn btn-icon btn-square btn-sm edit_btn_orders" data-toggle="modal" data-target="#editShippingAddressModal">
                                                                    <i class="fa fa-trash"></i>&nbsp;
                                                                    Edit
                                                                </button>
                                                            </div>
                                                            @endif
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
                              <div class="tab-pane " id="tabOrderNotes" role="tabpanel">
                                @include('admin.orders.ordernotes')
                              </div>
                              <div class="tab-pane " id="tabOrderActivity" role="tabpanel">
                                @include('admin.orders.orderactivity')
                              </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @include('admin.include.footer')
        </div>
    </div>
    <!-- edit billing address -->
    <div class="modal" id="editBillingAddressModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Billing Address Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="updateBillingAddressForm" method="post">

                        <input type="hidden" name="orderId" value="{{$orderDetails->id}}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <input type="text" name="b_fullname" id="b_fullname" placeholder="Full Name*" class="form-control" value="{{ $orderDetails->b_fullname }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="b_address_line_1" id="b_address_line_1" placeholder="Address Line 1*" class="form-control" value="{{ $orderDetails->b_address_line_1 }}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <input type="text" name="b_address_line_2" id="b_address_line_2" placeholder="Address Line 2" class="form-control" value="{{ $orderDetails->b_address_line_2 }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="b_country" id="b_country" placeholder="Country*" class="form-control" value="{{ $orderDetails->b_country }}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <input type="text" name="b_state" id="b_state" placeholder="State*" class="form-control" value="{{ $orderDetails->b_state }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="b_city" id="b_city" placeholder="City*" class="form-control" value="{{ $orderDetails->b_city }}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <input type="text" name="b_pincode" id="b_pincode" placeholder="Pincode*" class="form-control" value="{{ $orderDetails->b_pincode }}">
                                    </div>
                                    <div class="col-md-6">
                                        <select name="b_address_type" class="form-control">
                                            <option value="">Select Address Type*</option>
                                            <option value="Home" {{ $orderDetails->b_address_type == "Home" ? "selected" : ""}}>Home</option>
                                            <option value="Office/Commercial" {{ $orderDetails->b_address_type == "Office/Commercial" ? "selected" : ""}}>Office/Commercial</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <input type="text" name="b_phone1" id="b_phone1" placeholder="Phone 1*" class="form-control" value="{{ $orderDetails->b_phone1 }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="b_phone2" id="b_phone2" placeholder="Phone 2" class="form-control" value="{{ $orderDetails->b_phone2 }}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 text-right">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                    <div class="col-md-6 text-left">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- edit shipping address -->
    <div class="modal" id="editShippingAddressModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Shipping Address Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="updateShippingAddressForm" method="post">

                        <input type="hidden" name="orderId" value="{{$orderDetails->id}}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <input type="text" name="s_fullname" id="s_fullname" placeholder="Full Name*" class="form-control" value="{{ $orderDetails->s_fullname }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="s_address_line_1" id="s_address_line_1" placeholder="Address Line 1*" class="form-control" value="{{ $orderDetails->s_address_line_1 }}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <input type="text" name="s_address_line_2" id="s_address_line_2" placeholder="Address Line 2" class="form-control" value="{{ $orderDetails->s_address_line_2 }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="s_country" id="s_country" placeholder="Country*" class="form-control" value="{{ $orderDetails->s_country }}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <input type="text" name="s_state" id="s_state" placeholder="State*" class="form-control" value="{{ $orderDetails->s_state }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="s_city" id="s_city" placeholder="City*" class="form-control" value="{{ $orderDetails->s_city }}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <input type="text" name="s_pincode" id="s_pincode" placeholder="Pincode*" class="form-control" value="{{ $orderDetails->s_pincode }}">
                                    </div>
                                    <div class="col-md-6">
                                        <select name="s_address_type" class="form-control">
                                            <option value="">Select Address Type*</option>
                                            <option value="Home" {{ $orderDetails->s_address_type == "Home" ? "selected" : ""}}>Home</option>
                                            <option value="Office/Commercial" {{ $orderDetails->s_address_type == "Office/Commercial" ? "selected" : ""}}>Office/Commercial</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <input type="text" name="s_phone1" id="s_phone1" placeholder="Phone 1*" class="form-control" value="{{ $orderDetails->s_phone1 }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="s_phone2" id="s_phone2" placeholder="Phone 2" class="form-control" value="{{ $orderDetails->s_phone2 }}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 text-right">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                    <div class="col-md-6 text-left">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- mark as cancel -->
    <div class="modal fade" id="orderMarkAsCancelled" tabindex="-1" role="dialog" aria-labelledby="orderMarkAsCancelledLabel"
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
                    <form method="post" action="{{ url('admin/orders/markOrderAsCancelled') }}">
                    @csrf
                        <input type="hidden" name="orderId" id="cancelOrderId" value="{{$orderDetails->id}}">
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

    <!-- mark as delivered-->
    <div class="modal fade" id="orderMarkAsDelivered" tabindex="-1" role="dialog" aria-labelledby="orderMarkAsDeliveredLabel"
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
                    <form method="post" action="{{ url('admin/orders/markOrderAsDelivered') }}">
                    @csrf
                        <input type="hidden" name="orderId" value="{{$orderDetails->id}}">
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

    <!-- Aramex shipping -->
    <div class="modal fade bd-example-modal-lg" style="z-index: 10000000 !important;" id="aramexShippingModel" tabindex="-1" role="dialog" aria-labelledby="aramexShippingModelLabel" aria-hidden="true">
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
    </div>
    <!-- Modal Over -->

    <!-- Add Notes modal -->
    <div class="modal fade" id="addNotesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Notes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form class="" id="addNotesForm" name="addNotesForm" >
                        <div class="row">
                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <label for="examplePassword" class="">Notes</label><span class="text-danger">*</span>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="position-relative form-group">
                                    <textarea class="form-control" name="notes" id="notes"></textarea>
                                    <div id="desc_error"></div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="orderId" id="orderId" value="{{$orderDetails->id}}">
                        <div class="offset-md-4 col-md-8">
                            <button class="btn btn-info " type="submit" id="saveAddNotes">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>


                    <br>

                </div>
            </div>
        </div>
    </div>
    <!-- Modal-->

    <!-- Edit Loyalty number -->
    <div class="modal fade" id="editLoyaltyNumberModal" tabindex="-1" role="dialog" aria-labelledby="editLoyaltyNumberModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLoyaltyNumberModalLabel">Update Loyalty Number</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateLoyaltyNumberForm" name="updateLoyaltyNumberForm" >
                        <div class="row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="examplePassword" class="">Loyalty Number </label><span class="text-danger"> *</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <input type ="text" class="form-control" name="loyaltyNumber" id="loyaltyNumber" value="{{ $orderDetails->loyalty_card }}">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="orderId" id="orderId" value="{{$orderDetails->id}}">
                        <div class="offset-md-4 col-md-8">
                            <button class="btn btn-info " type="submit" id="changeLoyaltyNumber">Update</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- change status -->
    <div class="modal fade" id="changeStatusModel" tabindex="-1" role="dialog" aria-labelledby="changeStatusModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStatusModelLabel">Change Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changeStatusForm" name="changeStatusForm" >
                        <div class="row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label for="examplePassword" class="">Order Status </label><span class="text-danger"> *</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                              <select name="orderStatus" id="orderStatus" class="form-control">
                                  <option value="">Select Status</option>
                                  @foreach($orderStatus as $status)
                                  @php
                                  $selected='';
                                  if($status->id==$orderDetails->order_status_id)
                                  $selected='selected';
                                  @endphp
                                      <option value="{{ $status->id }}"  {{$selected}}>{{ $status->status }}</option>
                                  @endforeach
                              </select>
                            </div>
                        </div>
                        <input type="hidden" name="orderId" id="orderId" value="{{$orderDetails->id}}">
                        <div class="offset-md-4 col-md-8">
                            <button class="btn btn-info " type="submit" id="changeStatusId">Change</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark as shipped without Aramex-->
    <div class="modal fade" id="markOrderAsShipped" tabindex="-1" role="dialog" aria-labelledby="markOrderAsShippedLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="markOrderAsShippedLabel">Shipment Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ url('admin/orders/markOrderAsShipped') }}">
                    @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label>Carrier Name </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <input type ="text" class="form-control" name="carrierName" id="carrierName">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="position-relative form-group">
                                    <label>Tracking Number </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <input type ="text" class="form-control" name="trackingNumber" id="trackingNumber">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="orderId" id="orderId" value="{{$orderDetails->id}}">
                        <div class="offset-md-4 col-md-8">
                            <button class="btn btn-info " type="submit" id="markOrderAsShippedBtn">Create Shipment</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/js/orders/orders.js')}}"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip-utils/0.1.0/jszip-utils.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip-utils/0.1.0/jszip-utils.min.js" integrity="sha512-3WaCYjK/lQuL0dVIRt1thLXr84Z/4Yppka6u40yEJT1QulYm9pCxguF6r8V84ndP5K03koI9hV1+zo/bUbgMtA==" crossorigin="anonymous"></script> -->
@endpush
