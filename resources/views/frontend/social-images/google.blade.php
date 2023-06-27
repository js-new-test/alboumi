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
					@foreach($data as $key=>$row)
                		<li class="{{($key==0)?'active':''}}">
							<a data-toggle="tab" href="#menu{{$key}}">{{$row['album']}}</a>
						</li>
            		@endforeach

				</ul>
			</div>
		</div>
		<div class="col-12 col-sm-12 col-md-8">
			<div class="fb-right-side tab-content">
				@foreach($data as $key=>$row)
				<div id="menu{{$key}}" class="tab-pane fade {{($key==0)?'in show active':''}}">
					<div class="mobile-upload-header">
						<h6>{{$row['album']}} (@if(!empty($row['photos']) && isset($row['photos']->mediaItems)){{count($row['photos']->mediaItems)}}@endif)</h6>
						@if($selectionType!='single')
						<label class="ck">Select All
						  <input type="checkbox" class="selectAll">
						  <span class="checkmark"></span>
						</label>
						@endif
					</div>
					<div class="fb-imgs">
						<div class="row">
							@if(!empty($row['photos']) && isset($row['photos']->mediaItems))
							@foreach($row['photos']->mediaItems as $key2=>$row2)
								<div class="col-6 col-sm-4 col-md-4 col-lg-3">
									<div class="fb-img-ck">
										<label class="ck">
										  <input type="checkbox" name="selectedImages" class="singleselect" value="{{$row2->baseUrl.'=w'.$row2->mediaMetadata->width.'-h'.$row2->mediaMetadata->height}}">
										  <span class="checkmark"></span>
										</label>
										<img src="{{$row2->baseUrl}}">
									</div>
								</div>
		          @endforeach
							@endif
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
</section>
@endsection
