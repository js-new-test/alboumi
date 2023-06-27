@extends('frontend.social-images.layout')
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
				<ul class="fb-dropdowns-toggles nav nav-tabs">
            		<li class="active">
						<a data-toggle="tab" href="#menu1">All Photos</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="col-12 col-sm-12 col-md-8">
			<div class="fb-right-side tab-content">
				<div id="menu1" class="tab-pane fade in show active">
					<div class="mobile-upload-header">
						@if($selectionType!='single')
						<label class="ck">Select All
						  <input type="checkbox" class="selectAll">
						  <span class="checkmark"></span>
						</label>
						@endif
					</div>
					<div class="fb-imgs">
						<div class="row appendImages">
							@foreach($data['media']['data'] as $key2=>$row2)
								<div class="col-6 col-sm-4 col-md-4 col-lg-3">
									<div class="fb-img-ck">
										<label class="ck">
										  <input type="checkbox" name="selectedImages" class="singleselect" value="{{$row2['media_url']}}">
										  <span class="checkmark"></span>
										</label>
										<img src="{{$row2['media_url']}}">
									</div>
								</div>
			                @endforeach
						</div>
						@if(isset($data['media']['paging']['next']))
						<div class="row">
							<div class="col-12 text-center">
								<button nextURL="{{$data['media']['paging']['next']}}" type="button" class="fill-btn loadMore">Load More</button>
							</div>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
