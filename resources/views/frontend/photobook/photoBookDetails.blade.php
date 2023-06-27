@extends('frontend.layouts.master')
@php
	$seoDesc = $cmsPageDetails->seo_description.'-'.$pageName;
	if($seoDesc == null)
		$seoDesc = $pageName;

	$seoKeywords = $cmsPageDetails->seo_keyword.'-'.$pageName;
	if($seoKeywords == null)
		$seoKeywords = $pageName;
@endphp

@section('description', $seoDesc )
@section('keywords', $seoKeywords )

@section('content')
<script>
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
		var langVisibility = <?php echo json_encode($langVisibility->visibility); ?>;
</script>
<section class="amazing-photo-book">
	@if(Session::get('language_id') == $defaultLangId)
		@if(empty($cmsPageDetails->cms_banner))
			<img src="{{asset('public/assets/frontend/img/Wedding.jpg')}}" class="desktop-img">
		@else
			<img src="{{asset('public/assets/images/cms/banner/'.$cmsPageDetails->cms_banner)}}" class="desktop-img">
		@endif
		@if(empty($cmsPageDetails->cms_mobile_banner))
			<img src="{{asset('public/assets/frontend/img/MAboutUsBanner.jpg')}}" class="mobile-img">
		@else
			<img src="{{asset('public/assets/images/cms/mobile_banner/'.$cmsPageDetails->cms_mobile_banner)}}" class="mobile-img">
		@endif
	@else
		@if(empty($cmsPageDetails->banner_image))
			@if(empty($cmsPageDetails->cms_banner))
				<img src="{{asset('public/assets/frontend/img/Wedding.jpg')}}" class="desktop-img">
			@else
				<img src="{{asset('public/assets/images/cms/banner/'.$cmsPageDetails->cms_banner)}}" class="desktop-img">
			@endif
		@else
			<img src="{{asset('public/assets/images/cms/banner/'.$cmsPageDetails->banner_image)}}" class="desktop-img">
		@endif
		@if(empty($cmsPageDetails->mobile_banner))
			@if(empty($cmsPageDetails->cms_mobile_banner))
				<img src="{{asset('public/assets/frontend/img/MAboutUsBanner.jpg')}}" class="mobile-img">
			@else
				<img src="{{asset('public/assets/images/cms/mobile_banner/'.$cmsPageDetails->cms_mobile_banner)}}" class="mobile-img">
			@endif
		@else
			<img src="{{asset('public/assets/images/cms/mobile_banner/'.$cmsPageDetails->mobile_banner)}}" class="mobile-img">
		@endif
	@endif

	{{--<div class="overlay">
		<div class="container">
			<div class="w808">
				<h3>{{$bookDetailLabels['AMZPHOTOBOOKS']}}</h3>
				<p class="m-0">@if(!empty($cmsPageDetails->description)){!! $cmsPageDetails->description !!}@endif</p>
			</div>
		</div>
	</div>--}}
</section>

<section class="select-book">
	<div class="container">
		<div class="text-center">
			<h5 class="blurColor">{{$bookDetailLabels['RECOMMENDEDCAT']}}</h5>
			<h4>{{$bookDetailLabels['SELECTBOOK']}}</h4>
		</div>
		<div class="row paddingManage">
      @if(!empty($booksData))
      @foreach($booksData as $data)
			<div class="col-sm-12 col-md-6">
				<div class="text-center book-box">
					<div class="imgBG">
						<img src="{{asset('public/assets/images/books/'.$data->image)}}">
					</div>
					<a href="{{$baseUrl}}/product/{{$data->product_slug}}"><h6>{{$data->title}}</h6></a>
					<!--<p>{{$data->description}}</p>-->
					<p class="s2">{{ $bookDetailLabels['FROM'] }} {{ $currencyCode }} {{ number_format($data->price * $conversionRate,$decimalNumber, $decimalSeparator, $thousandSeparator) }}</p>
				</div>
			</div>
      @endforeach
      @endif
		</div>
	</div>
</section>
@endsection
@push('scripts')
<script>
$(document).on('ready', function(){

	if(langVisibility == 0)
	    rtl = false;
	if(langVisibility == 1)
	    rtl = true;

});
</script>
<script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
<script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>

@endpush
