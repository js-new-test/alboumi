@extends('frontend.layouts.master')
<title>{{ $pageName }} | {{ $projectName}}</title>
<meta name="description" content="{{$dashboardLabels['DASHBOARDSEODESC']}}">
<meta name="keywords" content="{{$dashboardLabels['DASHBOARDSEOKEYWORD']}}">
<script>
	var baseUrl = <?php echo json_encode($baseUrl); ?>
</script>
@section('content')
    <!-- <section class="login">
        <div class="container">
            <h3>{{Auth::guard('customer')->user()->email}} Login Successfully!</h3>
            <a href="{{url('/customer/logout')}}"><h4>Logout</h4></a>
        </div>
    </section> -->
    <div class="thumb-nav tb-11">
		<div class="container">
			<a href="#">Home</a>
			<span>Dashboard</span>
		</div>
	</div>

    <section class="profile-pages">
		<div class="container">
			<div class="row">
				@include('frontend.include.sidebar')
                <div class="col-12 col-sm-12 col-md-8 col-lg-9">
					<div class="pl-24">
						<div class="right-side-items">
							<div class="change-psw">								
								<div class="row">
									<div class="col-sm-12 col-md-10 col-lg-6 col-xl-6">
										@if(Auth::guard('customer')->check())
                                            <h4>{{Auth::guard('customer')->user()->email}}</h4>
                                        @endif
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>				
			</div>
		</div>
	</section>
@endsection
@push('scripts')
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
@endpush
