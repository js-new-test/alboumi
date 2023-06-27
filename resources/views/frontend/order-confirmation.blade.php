@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>
</script>
@section('content')
<section class="order-confirmation text-center">
	<div class="container">
		<img src="{{asset('/public/assets/frontend/img/Sucessbadge.svg')}}">
		<h5>{{$orderCnfLabels['ORDERPLACEDSUCC']}}</h5>
		<p>{{$orderCnfLabels['ORDERID']}}: {{$display_merchant_order_id}}</p>
		<div class="small-divider"></div>
		<p>{{$orderCnfLabels['ORDERCONFIRMATIONMSG']}}</p>
		<a class="fill-btn" href="{{url('/')}}">{{$orderCnfLabels['CONTINUESHOPPING']}}</a>
	</div>
</section>
@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
@endpush
