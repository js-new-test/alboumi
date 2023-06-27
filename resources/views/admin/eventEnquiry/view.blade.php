@extends('admin.layouts.master')
<title>View Enquiry Details | Alboumi</title>

@section('content')

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
                                        <i class="lnr-cog opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">View Enquiry Details</span>
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
                                                <a href="javascript:void(0);">View Enquiry Details</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/eventEnq/')}}">Enquiry List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                View Enquiry Details
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main-card mb-3 card">
                    <div class="card-body"><h5 class="card-title">Enquiry Details</h5>
                        <form class="col-md-10 offset-md-1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="position-relative row form-group"><label class="col-sm-2 col-form-label">Customer Name :</label>
                                        <div class="col-sm-10"><label class="col-sm-10 col-form-label"> {{ $event->full_name }}</label></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative row form-group"><label class="col-sm-2 col-form-label">Customer Email :</label>
                                        <div class="col-sm-10"><label class="col-sm-10 col-form-label"> {{ $event->email }}</label></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative row form-group"><label class="col-sm-2 col-form-label">Event Name :</label>
                                        <div class="col-sm-10"><label class="col-sm-10 col-form-label"> {{ $event->event_name }}</label></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative row form-group"><label class="col-sm-2 col-form-label">Package Name :</label>
                                        <div class="col-sm-10"><label class="col-sm-10 col-form-label"> {{ $event->package_name }}</label></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative row form-group"><label class="col-sm-2 col-form-label">Event Date :</label>
                                        <div class="col-sm-10"><label class="col-sm-10 col-form-label"> {{ date('d M Y',strtotime($event->event_date)) }}</label></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative row form-group"><label class="col-sm-2 col-form-label">Event Time :</label>
                                        <div class="col-sm-10"><label class="col-sm-10 col-form-label"> {{ date('H:i A',strtotime($event->event_time)) }}</label></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative row form-group"><label class="col-sm-2 col-form-label">Additional Packages :</label>
                                        <div class="col-sm-10"><label class="col-sm-10 col-form-label"> 
                                        @if(!empty($event->additional_package_name))
                                            {{ $event->additional_package_name }}
                                        @else
                                            {{ 'N/A' }}
                                        @endif
                                        </label></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative row form-group"><label class="col-sm-2 col-form-label">Enquiry Status :</label>
                                        <div class="col-sm-10">
                                            <label class="col-sm-10 col-form-label">
                                                @if($event->status == 0)
                                                    {{ 'Pending' }}
                                                @endif
                                                @if($event->status == 1)
                                                    {{ 'In Process' }}
                                                @endif
                                                @if($event->status == 2)
                                                    {{ 'Image Uploaded' }}
                                                @endif
                                                @if($event->status == 3)
                                                    {{ 'Completed' }}
                                                @endif
                                                @if($event->status == 4)
                                                    {{ 'Approved' }}
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative row form-group"><label class="col-sm-2 col-form-label">Payment Status :</label>
                                        <div class="col-sm-10">
                                            <label class="col-sm-10 col-form-label"> 
                                                @if($event->payment_status == 0)
                                                    {{ 'Unpaid' }}
                                                @endif
                                                @if($event->payment_status == 1)
                                                    {{ 'Paid' }}
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative row form-group"><label class="col-sm-2 col-form-label">Total Amount (BHD) :</label>
                                        <div class="col-sm-10">
                                            <label class="col-sm-10 col-form-label"> 
                                            @php $totalPrice = 0;
                                            @endphp
                                            @if(!empty($event->additional_package_price))
                                                @foreach($event->additional_package_price as $additionalPrice)
                                                    @php $totalPrice += $additionalPrice @endphp
                                                @endforeach
                                                {{ number_format($totalPrice + $event->pkgPrice * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}
                                            @else
                                                {{ number_format($event->pkgPrice * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}
                                            @endif
                                            </label>
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

