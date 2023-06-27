@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$emailVerificationLabels['EMAILVERIFICATIONDESC']}}">
<meta name="keywords" content="{{$emailVerificationLabels['EMAILVERIFICATIONKEY']}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>
</script>
@section('content')
    <section class="login">
        <div class="container">
            <h3>{{$emailVerificationLabels['EMAILVERIFIEDSUCC']}}</h3>            
        </div>
    </section>
@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
@endpush
