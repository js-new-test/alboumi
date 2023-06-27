@extends('admin.layouts.master')
<title>{{ $projectName }} | {{$pageTitle }}</title>
@section('content')
<!-- <style type="text/css">
    #tableManufacturers_filter
    {
        display: none;
    }
</style> -->
<script type="text/javascript">
    var baseUrl = <?php echo json_encode($baseUrl);?>;
</script>
<div class="app-container body-tabs-shadow fixed-header fixed-sidebar app-theme-gray closed-sidebar">
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
                                        <i class="pe-7s-cart opacity-6"></i>
                                    </span>
                                    <span class="d-inline-block">Edit Promotions</span>
                                </div>
                                <div class="page-title-subheading opacity-10">
                                    <nav class="" aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="javascript:void(0);">Promotion</a></li>
                                            <li class="active breadcrumb-item" aria-current="page"><a href="{{url('/admin/promotions')}}">Promotion List</a></li>
                                            <li class="active breadcrumb-item" aria-current="page">Edit Promotion</li>
                                        </ol>
                                    </nav> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <!-- <h5 class="card-title">Edit Promotion</h5> -->
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a data-toggle="tab" href="#editPromotion" class="active nav-link">Edit Promotion</a>
                            </li>
                            <li class="nav-item">
                                <a data-toggle="tab" href="#promotionConditions" class="nav-link">Conditions</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="editPromotion" role="tabpanel">

                                <form method="post" action="{{url('/admin/promotions/editPromotion')}}" name="editPromotion">
                                    @csrf
                                    <input type="hidden" name="promotionId" value="{{ $promotion->id}}" readonly="true">
                                    <div class="form-row">

                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label> Title <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="position-relative form-group">
                                                <input name="couponTitle" id="couponTitle" placeholder="Title" type="text" class="form-control" value="{{ $promotion->title }}">
                                            </div>
                                        </div>


                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label> Term & Conditions<span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="position-relative form-group">
                                                <textarea name="termsConditions" id="termsConditions" type="text" class="form-control ckeditor">{{ $promotion->terms_conditions }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label> Custom Title (Display Title)<span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="position-relative form-group">
                                                <input name="customTitle" id="customTitle" placeholder="Custom Title" type="text" class="form-control" value="{{ $promotion->custom_title }}">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label> Coupon Code<span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="position-relative form-group">
                                                <input name="couponCode" id="couponCode" placeholder="Coupon Code" type="text" class="form-control" value="{{ $promotion->coupon_code }}">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label> Discount Type <span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="position-relative form-group">
                                                <select class="form-control" name="discountType" id="discountType">
                                                    <option value="Percentage" {{ $promotion->discount_type == 'Percentage' ? 'selected' : ''}}>Percentage of the Amount</option>
                                                    <option value="Fixed" {{ $promotion->discount_type == 'Fixed' ? 'selected' : ''}}>Fixed Amount</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label> Discount Amount<span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="position-relative form-group">
                                                <input name="discountAmount" id="discountAmount" placeholder="Discount Amount" type="text" class="form-control" value="{{ $promotion->discount_amount }}">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label> Coupon Use Type</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="position-relative form-group">
                                                <select class="form-control" name="discountUserType" id="discountUserType">
                                                    <option value="Single Use" {{ $promotion->coupon_user_types == 'Single Use' ? 'selected' : ''}}>Single Use</option>
                                                    <option value="Multiple Use" {{ $promotion->coupon_user_types == 'Multiple Use' ? 'selected' : ''}}>Multiple Use</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label>Coupon Usage Limit</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="position-relative form-group">
                                                <input name="couponUsageLimit" id="couponUsageLimit" placeholder="Coupon Usage Limit" type="text" class="form-control" value="{{ $promotion->coupon_usage_limit }}">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label>Active From </label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="position-relative form-group">
                                                <input type="text" class="form-control" name="activeFrom" id="activeFrom" data-toggle="datepicker" value="{{ $promotion->startdate }}">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label>Active Till </label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="position-relative form-group">
                                                <input type="text" class="form-control" name="activeTill" id="activeTill" data-toggle="datepicker" value="{{ $promotion->enddate }}" data-date-format="dd/mm/yyyy">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label> Status</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="position-relative form-group">
                                                <select name="status" id="status" class="form-control">
                                                    <option {{ ( $promotion->status == 'Active' ) ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option {{ ( $promotion->status == 'Inactive' ) ? 'selected' : '' }}>
                                                        Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="offset-md-3 col-md-9">
                                            <button type="submit" class="btn btn-primary">Update Promotion</button>
                                            <a href="{{url('/admin/promotions')}}"> <button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button> </a>
                                        </div>

                                    </div>
                                </form>
                            </div>
                            
                            @include('admin.promotions.promotionConditions')

                            
                        </div>
                    </div>
                </div>
            </div>
            @include('admin.include.footer')
        </div>
    </div>    
    @include('admin.component.brandComponent')
    @include('admin.component.categoryComponent')
    @include('admin.component.productComponent')

</div>
@push('scripts')
    <!-- <script src="{{asset('public/assets/js/promotions/promotions.js')}}"></script> -->
    <script type="text/javascript">
		CKEDITOR.replace('termsConditions', {                
			filebrowserUploadUrl: "{{route('ckeditor.upload_promotion_image', ['_token' => csrf_token() ])}}",
			filebrowserUploadMethod: 'form'
		});		 
	</script>
@endpush
@endsection
