@extends('admin.layouts.master')
<title>{{ $projectName }} | {{$pageTitle }}</title>

@section('content')
<script type="text/javascript">
    var currency = <?php echo json_encode($currency); ?>;
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
</script>
<div class="app-container body-tabs-shadow fixed-header fixed-sidebar app-theme-white closed-sidebar">
    @include('admin.include.header')    
    <div class="app-main">
        @include('admin.include.sidebar') 
        <div class="app-main__outer">
            <div class="app-main__inner">
                <div class="app-page-title app-page-title-simple">
                    <div class="page-title-wrapper">
                        <div class="page-title-heading">
                            <div>
                                <div class="page-title-head center-elem">
                                    <span class="d-inline-block pr-2">
                                        <i class="lnr-cog opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Currency Conversion Rate</span>
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
                                                <a href="{{url('/admin/currency/list')}}">Currency</a>
                                                
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Currency Conversion
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="page-title-actions">                            
                            <div class="d-inline-block dropdown">
                                <button class="btn btn-square btn-primary btn-sm" id="divFilterToggle"> <i aria-hidden="true" class="fa fa-filter"></i> Filter </button>
                            </div>
                        </div> -->
                    </div>
                </div>

                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title"> Currency Conversion Rate Information</h5>
                        <form class="form-inline">
                            <div class="mb-2 mr-sm-2 mb-sm-0 position-relative form-group">
                                <label for="filter_date" class="mr-sm-2">Select Currency</label>
                                <select class="form-control multiselect-dropdown" name="currency" id="currency">
                                    <option value=" ">Select Currency</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Currency Conversion Rate</h5>
                        <form id="currencyConversionForm" method="post" action="currencyConversion">
                            <input type="hidden" name="selectedCurrencyId" id="selectedCurrencyId" readonly="true">
                            @csrf
                            <table class="table table-hover table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Rate</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">

                                </tbody>
                            </table>

                            <div class="col-md-12 text-center">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3 offset-md-3">
                                            <button type="submit" id="send"
                                                class="btn btn-primary btn-shadow w-100">Add Currency Conversion Rate</button>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="{{ url('admin/currency/list') }}">
                                                <button type="button" class="btn btn-light btn-shadow w-100"
                                                    name="cancel" value="Cancel">Cancel</button>
                                            </a>
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
@push('scripts')
    <script src="{{asset('public/assets/js/currencyConversion/currencyConversion.js')}}"></script>
@endpush
@endsection