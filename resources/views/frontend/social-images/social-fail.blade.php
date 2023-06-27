@extends('frontend.social-images.layout-fail')
@section('content')
<section class="facebook-main-layout">
	<div class="row">
		<div class="col-12 col-sm-12 col-md-4">
			<div class="fb-side-bar">
				<div class="fb-side-header">
					<p class="s1"><span class="selectedCounts">0</span> {{$platform}} Photo(s) selected</p>
				</div>
				<div class="fb-dropdowns">
					My Account
				</div>
				{{-- <ul class="fb-dropdowns-toggles nav nav-tabs">
					@foreach($data as $key=>$row)
                		<li class="{{($key==0)?'active':''}}">
							<a data-toggle="tab" href="#menu{{$key}}">{{$row['album']}}</a>
						</li>
            		@endforeach

				</ul> --}}
			</div>
		</div>
		<div class="col-12 col-sm-12 col-md-8">
			<div class="fb-right-side tab-content">
				<p>Please Provide sufficiant Permission to access your {{$platform}}.</p>
			</div>
		</div>
	</div>
</section>
@endsection
