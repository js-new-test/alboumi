@extends('frontend.layouts.master')
@section('content')
<script>
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
</script>
<section class="order-confirmation text-center">
	<div class="container">
		<img src="{{asset('public/assets/frontend/img/Sucessbadge.svg')}}">
		<h5>{{ $notFoundLabels['ENQSENTSUCCESS'] }}</h5>
		<div class="small-divider"></div>
		<p>{{ $notFoundLabels['DESCRIPTION2'] }}</p>
		<a class="fill-btn" href="{{ url('/') }}">{{ $notFoundLabels['RETURNHOME'] }}</a>
	</div>
</section>
@endsection
@push('scripts')
    <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
    <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
@endpush
