@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$paymentMethodLabels['PAYMENMETHOD']}}">
<meta name="keywords" content="{{$paymentMethodLabels['PAYMENMETHOD']}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>
</script>
@section('content')
<div class="thumb-nav tb-11">
	<div class="container">
		<a href="{{url('/')}}">{{$paymentMethodLabels['HOME']}}</a>
		<span>{{$paymentMethodLabels['CHECKOUT']}}</span>
	</div>
</div>
<section>
	<div class="container">
		<div class="process-section">
			<div class="row">
				<div class="col-6 col-sm-6 col-md-3">
					<div class="blur-checked-process">{{$paymentMethodLabels['LOGINEMAIL']}}</div>
				</div>
				<div class="col-6 col-sm-6 col-md-3">
					<div class="blur-checked-process">{{$paymentMethodLabels['SHIPPINGADDRESS']}}</div>
				</div>
				<div class="col-6 col-sm-6 col-md-3">
					<div class="checked-process">{{$paymentMethodLabels['PAYMENMETHOD']}}</div>
				</div>
				<div class="col-6 col-sm-6 col-md-3">
					<div class="unchecked-process">{{$paymentMethodLabels['REVIEWORDER']}}</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="Payment-Method">
	<div class="container">
		<h4>{{$paymentMethodLabels['PAYMENMETHOD']}}</h4>
		<div class="dividers"></div>
        <input type="hidden" name="cart_master_id" id="cart_master_id" value="{{$cart_master_id}}">
        <!-- <input type="hidden" name="baseUrl" id="baseUrl" value="{{$baseUrl}}"> -->
		<div class="row">
			<div class="col-12">
				<div class="Payment-types">
					<div class="row d-lg-flex align-items-center">
						<div class="col-12 col-sm-12 col-md-12 col-lg-9">
							<label class="rd"><img style="height: 75px" src="{{asset('public/assets/frontend/img/jcb.jpg')}}"><img style="height: 75px" src="{{asset('public/assets/frontend/img/visa.jpg')}}"><img style="height: 75px" src="{{asset('public/assets/frontend/img/master_card.png')}}">
								{{-- {{$paymentMethodLabels['PAYONLINECREDIT']} --}}
								<input type="radio" checked="checked" class="credit_card_method" name="pm" value="1">
								<span class="rd-checkmark"></span>
							</label>
						</div>
						<!-- <div class="col-12 col-sm-12 col-md-12 col-lg-3 add-left-38 right-992" id="credit_opt_btn">
							<a class="border-btn">{{$paymentMethodLabels['PLACEORDER']}}</a>
						</div> -->
					</div>
				</div>
                <div class="Payment-types">
					<div class="row d-lg-flex align-items-center">
						<div class="col-12 col-sm-12 col-md-12 col-lg-9">
							<label class="rd"><img style="height: 75px" src="{{asset('public/assets/frontend/img/benefit.jpg')}}">
								{{--{{$paymentMethodLabels['PAYONLINEDEBIT']}}--}}
							  <input type="radio" name="pm" class="debit_card_method" value="2">
							  <span class="rd-checkmark"></span>
							</label>
						</div>
						<!-- <div class="col-12 col-sm-12 col-md-12 col-lg-3 add-left-38 right-992" id="debit_opt_btn">
							<a class="border-btn">{{$paymentMethodLabels['PLACEORDER']}}</a>
						</div> -->
					</div>
				</div>
				<!-- <div class="Payment-types img-radio">
					<div class="row d-lg-flex align-items-center">
						<div class="col-12 col-sm-12 col-md-12 col-lg-9">
							<label class="rd"><img src="img/PayPal.svg">
							  <input type="radio" checked="checked" name="pm">
							  <span class="rd-checkmark"></span>
							</label>
						</div>
						<div class="col-12 col-sm-12 col-md-12 col-lg-3 add-left-38 right-992">
							<a class="border-btn" href="review-order.html">Place Order</a>
						</div>
					</div>
				</div>
				<div class="Payment-types">
					<div class="row d-lg-flex align-items-center">
						<div class="col-12 col-sm-12 col-md-12 col-lg-9">
							<label class="rd">Cash on Delivery
							  <input type="radio" name="pm">
							  <span class="rd-checkmark"></span>
							</label>
						</div>
						<div class="col-12 col-sm-12 col-md-12 col-lg-3 add-left-38 right-992">
							<a class="border-btn" href="review-order.html">Place Order</a>
						</div>
					</div>
				</div> -->
			</div>
			<div class="col-12 text-center">
				<a class="fill-btn payment-continue" id="payment-continue-btn" style="cursor:pointer;">{{$paymentMethodLabels['CONTINUE']}}</a>
				 <!-- <button class="fill-btn payment-continue">Continue</button> -->
			</div>
		</div>
	</div>
</section>

@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/payment-method/payment-method.js')}}"></script>
@endpush
