@extends('admin.layouts.master')
<title>Admin Dashboard</title>

@section('content')   
<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar closed-sidebar">
    @include('admin.include.header')    
	<div class="app-main">
        @include('admin.include.sidebar') 
        <div class="app-main__outer" style="width:100%">
            <div class="app-main__inner">
                <div class="app-page-title app-page-title-simple">
                    <div class="page-title-wrapper">                        
                        <div class="page-title-heading">                            
                            <div>
                                <div class="page-title-head center-elem">
                                    <span class="d-inline-block pr-2">
                                        <i class="lnr-apartment opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Dashboard</span>
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
                                                <a href="javascript:void(0);">Dashboard</a>
                                            </li>                                            
                                        </ol>
                                    </nav>
                                </div>
                            </div>                            
                        </div>                       
                    </div>
                </div> 
                <!-- Section 1  -->
                <input type="hidden" name="baseUrl" id="baseUrl" value="{{$baseUrl}}">
                <div class="row">
                    {{--<div class="col-md-6 col-lg-3">
                        <div class="widget-chart widget-chart2 text-left mb-3 card-btm-border card-shadow-primary border-primary card">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content">
                                    <div class="widget-title opacity-5 text-uppercase">NEW CUSTOMERS</div>
                                    <div class="widget-numbers mt-2 fsize-4 mb-0 w-100">
                                        <div class="widget-chart-flex align-items-center">
                                            <div>                                                                                                
                                                {{$total_customers_today}}
                                            </div>                                                                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>--}}
                    <div class="col-md-6 col-lg-3">
                        <div class="widget-chart widget-chart2 text-left mb-3 card-btm-border card-shadow-primary border-primary card">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content">
                                    <div class="widget-title opacity-5 text-uppercase">CUSTOMERS</div>
                                    <div class="widget-numbers mt-2 fsize-4 mb-0 w-100">
                                        <div class="widget-chart-flex align-items-center">
                                            <div>                                                
                                                {{$total_customers}}                                             
                                            </div>     
                                            <div class="widget-title ml-auto font-size-lg font-weight-normal text-muted">
                                                <div class="circle-progress circle-progress-gradient-alt-sm d-inline-block">
                                                    <!-- <small></small> -->
                                                    <span><i style="font-size: 25px;" class="fa fa-fw" aria-hidden="true"></i></span>
                                                </div>
                                            </div>                                       
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="widget-chart widget-chart2 text-left mb-3 card-btm-border card-shadow-danger border-danger card">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content">
                                    <div class="widget-title opacity-5 text-uppercase">TODAY'S ORDERS</div>
                                    <div class="widget-numbers mt-2 fsize-4 mb-0 w-100">
                                        <div class="widget-chart-flex align-items-center">
                                            <div>
                                                {{$total_today_orders}}
                                            </div>                                             
                                            <div class="widget-title ml-auto font-size-lg font-weight-normal text-muted">
                                                <div class="circle-progress circle-progress-gradient-alt-sm d-inline-block">
                                                    <!-- <small></small> -->
                                                    <span><i style="font-size: 25px;" class="fa fa-shopping-basket" aria-hidden="true"></i></span>
                                                </div>
                                            </div>                                     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="widget-chart widget-chart2 text-left mb-3 card-btm-border card-shadow-warning border-warning card">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content">
                                    <div class="widget-title opacity-5 text-uppercase">TODAY'S SALES ({{$currency->currency_code}})</div>
                                    <div class="widget-numbers mt-2 fsize-4 mb-0 w-100">
                                        <div class="widget-chart-flex align-items-center">
                                            <div>
                                                {{number_format($total_today_sales, $decimalNumber, $decimalSeparator, $thousandSeparator)}}
                                            </div>  
                                            <div class="widget-title ml-auto font-size-lg font-weight-normal text-muted">
                                                <div class="circle-progress circle-progress-gradient-alt-sm d-inline-block">
                                                    <!-- <small></small> -->
                                                    <span><i style="font-size: 25px;" class="fa fa-fw" aria-hidden="true"></i></span>
                                                </div>
                                            </div>                                      
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="widget-chart widget-chart2 text-left mb-3 card-btm-border card-shadow-success border-success card">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content">
                                    <div class="widget-title opacity-5 text-uppercase">PENDING ENQUIRIES</div>
                                    <div class="widget-numbers mt-2 fsize-4 mb-0 w-100">
                                        <div class="widget-chart-flex align-items-center">
                                            <div>
                                                {{$pending_enquiry}}
                                            </div>
                                            <div class="widget-title ml-auto font-size-lg font-weight-normal text-muted">
                                                <div class="circle-progress circle-progress-gradient-alt-sm d-inline-block">
                                                    <!-- <small></small> -->
                                                    <span><i style="font-size: 25px;" class="fa fa-question-circle" aria-hidden="true"></i></span>
                                                </div>
                                            </div>                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-card mb-3 card expand_filter">
                    <div class="card-body">
                        <h5 class="card-title"><i aria-hidden="true" class="fa fa-filter"></i>  Filter</h5>
                        <form id="filterDashboardForm" method="post" class="form-inline">
                            @csrf
                            <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group">
                                <label for="from_date" class="mr-sm-2">From Date</label>
                                <input type="text" name="from_date" id="from_date" class="form-control"/>
                                <div id="from_date_error" style="color: red;"></div>
                            </div>
                            <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group">
                                <label for="to_date" class="mr-sm-2">To Date</label>
                                <input type="text" name="to_date" id="to_date" class="form-control"/>
                            </div>
                            <button type="button" id="filter_dashboard_count" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                </div>
                <!-- Section 2  -->
                <div class="row">
                    <div class="col-md-6 col-lg-3">
                        <div class="widget-chart widget-chart2 text-left mb-3 card-btm-border card-shadow-primary border-primary card">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content">
                                    <div class="widget-title opacity-5 text-uppercase">PENDING ORDERS</div>
                                    <div class="widget-numbers mt-2 fsize-4 mb-0 w-100">
                                        <div class="widget-chart-flex align-items-center">
                                            <div id="pending_orders">                                                
                                                {{$pending_orders}}
                                            </div>       
                                            <div class="widget-title ml-auto font-size-lg font-weight-normal text-muted">
                                                <div class="circle-progress circle-progress-gradient-alt-sm d-inline-block">
                                                    <!-- <small></small> -->
                                                    <span><i style="font-size: 25px;" class="fa fa-shopping-basket" aria-hidden="true"></i></span>
                                                </div>
                                            </div>                                     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="widget-chart widget-chart2 text-left mb-3 card-btm-border card-shadow-danger border-danger card">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content">
                                    <div class="widget-title opacity-5 text-uppercase">TOTAL ORDERS</div>
                                    <div class="widget-numbers mt-2 fsize-4 mb-0 w-100">
                                        <div class="widget-chart-flex align-items-center">
                                            <div id="total_orders">                                                
                                                {{$total_orders}}                                             
                                            </div>       
                                            <div class="widget-title ml-auto font-size-lg font-weight-normal text-muted">
                                                <div class="circle-progress circle-progress-gradient-alt-sm d-inline-block">
                                                    <!-- <small></small> -->
                                                    <span><i style="font-size: 25px;" class="fa fa-shopping-basket" aria-hidden="true"></i></span>
                                                </div>
                                            </div>                                     
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="widget-chart widget-chart2 text-left mb-3 card-btm-border card-shadow-warning border-warning card">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content">
                                    <div class="widget-title opacity-5 text-uppercase">TOTAL SALES ({{$currency->currency_code}})</div>
                                    <div class="widget-numbers mt-2 fsize-4 mb-0 w-100">
                                        <div class="widget-chart-flex align-items-center">
                                            <div id="total_sales">
                                                {{number_format($total_sales, $decimalNumber, $decimalSeparator, $thousandSeparator)}}
                                            </div> 
                                            <div class="widget-title ml-auto font-size-lg font-weight-normal text-muted">
                                                <div class="circle-progress circle-progress-gradient-alt-sm d-inline-block">
                                                    <!-- <small></small> -->
                                                    <span><i style="font-size: 25px;" class="fa fa-fw" aria-hidden="true"></i></span>
                                                </div>
                                            </div>                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-6 col-lg-3">
                        <div class="widget-chart widget-chart2 text-left mb-3 card-btm-border card-shadow-success border-success card">
                            <div class="widget-chat-wrapper-outer">
                                <div class="widget-chart-content">
                                    <div class="widget-title opacity-5 text-uppercase">TOTAL ENQUIRIES</div>
                                    <div class="widget-numbers mt-2 fsize-4 mb-0 w-100">
                                        <div class="widget-chart-flex align-items-center">
                                            <div id="total_enquiry">
                                                {{$total_enquiry}}
                                            </div>
                                            <div class="widget-title ml-auto font-size-lg font-weight-normal text-muted">
                                                <div class="circle-progress circle-progress-gradient-alt-sm d-inline-block">
                                                    <!-- <small></small> -->
                                                    <span><i style="font-size: 25px;" class="fa fa-question-circle" aria-hidden="true"></i></span>
                                                </div>
                                            </div>                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                  
                </div>                            
                <!-- Section 3  -->
                <div class="row">
                    <div class="col-sm-12 col-md-7 col-lg-12">
                        <div class="mb-3 card">
                            <div class="card-header-tab card-header">
                                <div class="card-header-title font-size-lg text-capitalize font-weight-normal">Daily Sales</div>
                                <!-- <div class="btn-actions-pane-right text-capitalize">
                                    <button class="btn btn-warning">Actions</button>
                                </div> -->
                            </div>
                            <div class="pt-0 card-body">
                                <div id="daily-sales-graph"></div>
                            </div>
                        </div>
                    </div>                        
                </div>
                <!-- Section 4 -->
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        <div class="card-header-title font-size-lg text-capitalize font-weight-normal">Pending Enquiries</div>
                        <!-- <div class="btn-actions-pane-right">
                            <button type="button" class="btn-icon btn-wide btn-outline-2x btn btn-outline-focus btn-sm d-flex">
                                Actions Menu
                                <span class="pl-2 align-middle opacity-7">
                                    <i class="fa fa-angle-right"></i>
                                </span>
                            </button>
                        </div> -->
                    </div>
                    <div class="table-responsive">
                        <table class="align-middle text-truncate mb-0 table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Event Date</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Event</th>
                                    <th class="text-center">Package</th>
                                    <th class="text-center">Additional Package</th>
                                    <th class="text-center">Date of Enquiry</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $counter=0; @endphp
                                @foreach($pending_enquiries as $pending_enquirie)
                                @php $counter++ @endphp
                                <tr>
                                    <td class="text-center text-muted" style="width: 80px;">#{{$counter}}</td>                                
                                    <td class="text-center">
                                        <span class="pr-2 opacity-6">
                                            <i class="fa fa-business-time"></i>
                                        </span>
                                        {{date('d M Y', strtotime($pending_enquirie->event_date))}}
                                    </td>
                                    <td class="text-center">{{$pending_enquirie->first_name.' '.$pending_enquirie->last_name}}</td>
                                    <td class="text-center">{{$pending_enquirie->event_name}}</td>
                                    <td class="text-center">{{$pending_enquirie->package_name}}</td>
                                    <td class="text-center">{{($pending_enquirie->name != '') ? $pending_enquirie->name : '-----'}}</td>                                
                                    <td class="text-center">
                                        <span class="pr-2 opacity-6">
                                            <i class="fa fa-business-time"></i>
                                        </span>
                                        {{date('d M Y', strtotime($pending_enquirie->created_at))}}
                                    </td>
                                </tr>                           
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-block p-4 text-center card-footer">                        
                    </div>
                </div>
            </div>
            @include('admin.include.footer')   
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{asset('/public/assets/js/dashboard/daily-sales-graph.js')}}"></script>
<script src="{{asset('/public/assets/js/dashboard/dashboard.js')}}"></script>
@endpush

