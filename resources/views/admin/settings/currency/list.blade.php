@extends('admin.layouts.master')
<title>List Currency | Alboumi</title>

@section('content')
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
                                    <span class="d-inline-block">Currency</span>
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
                                                <a href="javascript:void(0);">Settings</a>
                                            </li>                                            
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Currency  
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>                                                     
                        </div>  
                        <div class="page-title-actions">                            
                            <div class="d-inline-block dropdown">                               
                                <a href="{{url('/admin/currency/add')}}"><button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i class="fa fa-plus btn-icon-wrapper"> </i>Add Currency</button></a>
                            </div>
                            <div class="d-inline-block dropdown">                               
                                <a href="{{url('/admin/currencyConversion')}}"><button class="mb-2 mr-2 btn-icon btn-square btn btn-primary btn-sm"><i class="fa fa-exchange btn-icon-wrapper"></i> Add Currency Conversion Rate</button></a>
                            </div>
                        </div>                                                  
                    </div>
                </div>  
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <!-- <h5 class="card-title">List Of Currency</h5>   -->
                        <table style="width: 100%;" id="currency_list" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Country Name</th>
                                    <th>Country Code</th>
                                    <th>Currency Symbol</th>
                                    <th>Created At</th>
                                    <th>Is Default</th>
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
    <div class="modal fade bd-example-modal-sm" id="currencyDeleteModel" tabindex="-1" role="dialog" aria-labelledby="currencyDeleteModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="currencyDeleteModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="currency_id" id="currency_id">
                    <!-- <input type="hidden" name="is_active" id="is_active"> -->
                    <p class="mb-0" id="message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="deleteCurrency">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->
    <!-- Modal Start -->
    <div class="modal fade bd-example-modal-sm" id="curruncyDefaultModel" tabindex="-1" role="dialog" aria-labelledby="curruncyDefaultModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="curruncyDefaultModelLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" name="curr_id" id="curr_id">
                    <input type="hidden" name="is_dflt" id="is_dflt">
                    <p class="mb-0" id="default_message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="currencyIsDefault">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Over -->    
</div>
@endsection
@push('scripts')
<script src="{{asset('public/assets/custom/datatables/settings/currency-list-datatable.js')}}"></script>
@endpush
