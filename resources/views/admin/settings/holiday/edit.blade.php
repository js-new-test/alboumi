@extends('admin.layouts.master')
<title>Edit Holiday | Alboumi</title>

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
                                    <span class="d-inline-block">Holiday</span>
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
                                                <a href="javascript:void(0);">Holiday</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <a href="{{url('/admin/holiday')}}">Holiday List</a>
                                            </li>
                                            <li class="active breadcrumb-item" aria-current="page">
                                                Edit Holiday  
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
                        <h5 class="card-title">Update Holiday</h5>                          
                        <form id="holidayForm" method="post" action="{{url('/admin/holiday/update')}}">
                            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}" />    
                            <input type="hidden" name="holiday_id" id="holiday_id" value="{{$holiday->id}}">                        
                            <div class="row">                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title"><strong>Date<span style="color:red">*</span></strong></label>
                                        <input type="text" name="holiday_date" id="holiday_date_edit" class="form-control" value="{{$holiday->date}}">
                                    </div>
                                </div>
                                <div class="col-md-6">                                    
                                    <div class="form-group">
                                        <label for="phone"><strong>Name<span style="color:red">*</span></strong></label>                                        
                                        <input type="text" name="name" id="name" class="form-control" value="{{$holiday->name}}">
                                    </div>
                                </div>
                            </div>
                        
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="update_holiday" id="update_holiday">Update Holiday</button>
                                <a href="{{url('/admin/holiday')}}"><button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button></a>
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
@push('scripts')
<script src="{{asset('public/assets/js/settings/holiday.js')}}"></script>
@endpush
