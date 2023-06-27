@extends('frontend.layouts.master')
@section('content')
<script>
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
</script>
<section class="error-404">
	<div class="container">
		<div class="width-539">
			<img src="{{ asset('public/assets/frontend/img/error404.png') }}">
			<h6>{{ $notFoundLabels['404MSG'] }}</h6>
			<a href="{{ url('/') }}" class="fill-btn">{{ $notFoundLabels['RETURNHOME'] }}</a>
		</div>
	</div>
</section>
@endsection
@push('scripts')
    <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
    <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
@endpush
