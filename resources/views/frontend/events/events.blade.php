@extends('frontend.layouts.master')

@php
	$seoDesc = $cmsPageDetails->seo_description;
	if($seoDesc == null)
		$seoDesc = '';

	$seoKeywords = $cmsPageDetails->seo_keyword;
	if($seoKeywords == null)
		$seoKeywords = '';
@endphp

@section('description', $seoDesc )
@section('keywords', $seoKeywords )

@section('content')
<title>@if(!empty($cmsPageDetails->seo_title)) {{ $cmsPageDetails->seo_title }} @else {{ $pageName }} @endif| {{ $projectName}}</title>

<script>
    var baseUrl = <?php echo json_encode($baseUrl); ?>;
    var langVisibility = <?php echo json_encode($langVisibility->visibility); ?>;
</script>
<section class="wedding-banner">
    @if(Session::get('language_id') == $defaultLangId)
        @if(empty($cmsPageDetails->cms_banner))
            <img src="{{asset('public/assets/frontend/img/Wedding.jpg')}}" class="desktop-img">
        @else
            <img src="{{asset('public/assets/images/cms/banner/'.$cmsPageDetails->cms_banner)}}" class="desktop-img">
        @endif

        @if(empty($cmsPageDetails->cms_mobile_banner))
            <img src="{{asset('public/assets/frontend/img/Wedding_mobile.png')}}" class="mobile-img">
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
			    <img src="{{asset('public/assets/frontend/img/Wedding_mobile.png')}}" class="mobile-img">
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
				<h3>@if(!empty($cmsPageDetails->title)) {{ $cmsPageDetails->title }} @endif</h3>
				<p class="m-0">@if(!empty($cmsPageDetails->description)) {!! $cmsPageDetails->description !!} @endif</p>
			</div>
		</div>
	</div>--}}
</section>

<section class="menu-section">
	<div class="container">
		<div class="row">
            @if(!empty($eventsData))
            @foreach($eventsData as $event)
			<div class="col-6 col-sm-6 col-md-4 col-lg-3">
                <a href="{{ url('events/'.$event->id) }}">
				    <img src="{{asset('public/assets/images/events/'.$event->event_image)}}">
                </a>
				<a href="{{ url('events/'.$event->id) }}"><h6 class="text-center">{{ $event->event_name}}</h6></a>
            </div>
            @endforeach
            @endif
		</div>
	</div>
</section>

@endsection
@push('scripts')
    <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
    <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
    <script src="{{asset('public/assets/frontend/js/events/event.js')}}"></script>


@endpush