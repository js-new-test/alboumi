@extends('frontend.layouts.master')
@section('content')
<script>
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
</script>
<section class="order-confirmation text-center">
	<div class="container">
		<img src="{{asset('public/assets/frontend/img/Sucessbadge.svg')}}">
		<h5>{{ $orderCnfLabels['EVENTENQORDERSUCCESS'] }}</h5>
        <p>{{ $orderCnfLabels['ORDERID'] }} : {{ $event_order_id }}</p>
		<div class="small-divider"></div>
		<a class="fill-btn" href="{{ url('/') }}">{{ $orderCnfLabels['RETURNHOME'] }}</a>
	</div>
</section>
@endsection
@push('scripts')
    <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
    <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
@endpush
