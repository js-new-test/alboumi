@extends('admin.layouts.master')
<title>{{$pageTitle }} | {{ $projectName }} </title>
@section('content')
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
                                    <span class="d-inline-block">Add Promotions</span>
                                </div>
                                <div class="page-title-subheading opacity-10">
                                    <nav class="" aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="javascript:void(0);">Promotion</a></li>
                                            <li class="active breadcrumb-item" aria-current="page"><a href="{{url('/admin/promotions')}}">Promotion List</a></li>
                                            <li class="active breadcrumb-item" aria-current="page">Add Promotion</li>
                                        </ol>
                                    </nav> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main-card mb-3 card">
                    <div class="card-body">
                        <h5 class="card-title">Add Promotion</h5>
                        <form method="post" action="addPromotion" name="addPromotion">
                            @csrf
                            <div class="form-row">

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label> Title <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="position-relative form-group">
                                        <input name="couponTitle" id="couponTitle" placeholder="Title" type="text" class="form-control">
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label> Term & Conditions<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="position-relative form-group">
                                        <textarea name="termsConditions" id="termsConditions" type="text" class="form-control ckeditor"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label> Custom Title (Display Title)<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="position-relative form-group">
                                        <input name="customTitle" id="customTitle" placeholder="Custom Title" type="text" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label> Coupon Code<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <input name="couponCode" id="couponCode" placeholder="Coupon Code" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="position-relative form-group">
                                        <button type="button" class="btn btn-info" id="generatePromotionCode">Generate Code</button>
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
                                            <option value="Percentage">Percentage of the Amount</option>
                                            <option value="Flat">Fixed Amount</option>
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
                                        <input name="discountAmount" id="discountAmount" placeholder="Discount Amount" type="text" class="form-control">
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
                                            <option value="Single Use">Single Use</option>
                                            <option value="Multiple Use">Multiple Use</option>
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
                                        <input name="couponUsageLimit" id="couponUsageLimit" placeholder="Coupon Usage Limit" type="text" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label>Active From </label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="position-relative form-group">
                                        <input type="text" class="form-control" name="activeFrom" id="activeFrom" data-toggle="datepicker"/>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label>Active Till </label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="position-relative form-group">
                                        <input type="text" class="form-control" name="activeTill" id="activeTill" data-toggle="datepicker"/>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label> Status</label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="position-relative form-group">
                                        <select class="form-control" name="status" id="status">
                                            <option>Active</option>
                                            <option>InActive</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="offset-md-3 col-md-9">
                                    <button type="submit" class="btn btn-primary">Add Promotion</button>
                                    <a href="{{url('/admin/promotions')}}"> <button type="button" class="btn btn-light" name="cancel" value="Cancel">Cancel</button> </a>
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
    <script src="{{asset('public/assets/js/promotions/addPromotions.js')}}"></script>
    <script type="text/javascript">
		CKEDITOR.replace('termsConditions', {                
			filebrowserUploadUrl: "{{route('ckeditor.upload_promotion_image', ['_token' => csrf_token() ])}}",
			filebrowserUploadMethod: 'form'
		});		 
	</script>
@endpush
@endsection
