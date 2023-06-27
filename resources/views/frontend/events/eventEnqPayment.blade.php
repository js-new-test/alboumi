@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$paymentMethodLabels['PAYMENMETHOD']}}">
<meta name="keywords" content="{{$paymentMethodLabels['PAYMENMETHOD']}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>
</script>
@section('content')
<link rel="stylesheet" href="{{asset('public/assets/frontend/css/loader-style.css')}}">

<div id="ajax-loader">
  <div class="cv-spinner">
  	<img src="{{ asset('public/assets/frontend/img/Loader.svg') }}" class="spinner">
  </div>
</div>
<div class="thumb-nav tb-11">
	<div class="container">
		<a href="{{url('/')}}">{{$paymentMethodLabels['HOME']}}</a>
		<span>{{$paymentMethodLabels['EVENTENQPAYMENT']}}</span>
	</div>
</div>

<section class="Payment-Method">
	<div class="container">
		<h4>{{$paymentMethodLabels['PAYMENMETHOD']}}</h4>
		<div class="dividers"></div>
        <input type="hidden" name="eventEnquiryId" id="eventEnquiryId" value="{{ $eventEnquiryId }}">
		<div class="row">
			<div class="col-12">
				<div class="Payment-types">
					<div class="row d-lg-flex align-items-center">
						<div class="col-12 col-sm-12 col-md-12 col-lg-9">
							<label class="rd"><img style="height: 75px" src="{{asset('public/assets/frontend/img/jcb.jpg')}}"><img style="height: 75px" src="{{asset('public/assets/frontend/img/visa.jpg')}}"><img style="height: 75px" src="{{asset('public/assets/frontend/img/master_card.png')}}">
								<input type="radio" checked="checked" class="credit_card_method" name="pm" value="1">
								<span class="rd-checkmark"></span>
							</label>
						</div>
					</div>
				</div>
                <div class="Payment-types">
					<div class="row d-lg-flex align-items-center">
						<div class="col-12 col-sm-12 col-md-12 col-lg-9">
							<label class="rd"><img style="height: 75px" src="{{asset('public/assets/frontend/img/benefit.jpg')}}">
							  <input type="radio" name="pm" class="debit_card_method" value="2">
							  <span class="rd-checkmark"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 text-center">
				<a class="fill-btn payment-continue" id="continueBtn" style="cursor:pointer;">{{$paymentMethodLabels['CONTINUE']}}</a>
			</div>
		</div>
	</div>
</section>

@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/events/eventEnqPayment.js')}}"></script>
@endpush
