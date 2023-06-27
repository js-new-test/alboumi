<!DOCTYPE html>
@php
    $langId = Session::get('language_id');
    $visibility = App\Models\GlobalLanguage::checkVisibility($langId);
@endphp
@if($visibility->visibility == 0)
<html>
@endIf
@if($visibility->visibility == 1)
<html dir="rtl">
@endIf
<html>
<head>
    <title>@if(!empty($aboutData->seo_title)) {{ $aboutData->seo_title }} @else {{ $pageName }} @endif| {{ $projectName}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="@if(!empty($aboutData->seo_description)) <?php echo $aboutData->seo_description ?> @endif">
    <meta name="keywords" content="@if(!empty($aboutData->seo_keyword)) <?php echo $aboutData->seo_keyword ?> @endif">
    @include('frontend.include.top',['visibility' => $visibility])
    @include('frontend.include.header')
    <script>
        var baseUrl = <?php echo json_encode($baseUrl); ?>;
        var aboutUs = <?php echo json_encode($aboutData);?>;
    </script>
    <style>
        img
        {
            width : 100% !important;
        }
        @media only screen and (max-width: 768px) {
            img{
                width:100% !important;
            }
        }
        .footer-logo img {
            width: 98px !important;
            height: auto;
            margin-bottom: 0;
        }
        .bitmap img {
            height: 44px;
        }
        .mobile-logo img {
            height: 32px;
            width: 66px !important;
        }
    </style>
</head>
<body>
    <section class="about-banner">
    @if(Session::get('language_id') == $defaultLangId)
		@if(empty($aboutData->cms_banner))
			<img src="{{asset('public/assets/frontend/img/About-Us-Banner.jpg')}}" class="desktop-img">
		@else
			<img src="{{asset('public/assets/images/cms/banner/'.$aboutData->cms_banner)}}" class="desktop-img">
		@endif
		@if(empty($aboutData->cms_mobile_banner))
			<img src="{{asset('public/assets/frontend/img/MAboutUsBanner.jpg')}}" class="mobile-img">
		@else
			<img src="{{asset('public/assets/images/cms/mobile_banner/'.$aboutData->cms_mobile_banner)}}" class="mobile-img">
		@endif
	@else
		@if(empty($aboutData->banner_image))
            @if(empty($aboutData->cms_banner))
			    <img src="{{asset('public/assets/frontend/img/About-Us-Banner.jpg')}}" class="desktop-img">
            @else
                <img src="{{asset('public/assets/images/cms/banner/'.$aboutData->cms_banner)}}" class="desktop-img">
			@endif
		@else
			<img src="{{asset('public/assets/images/cms/banner/'.$aboutData->banner_image)}}" class="desktop-img">
		@endif
		@if(empty($aboutData->mobile_banner))
            @if(empty($aboutData->cms_mobile_banner))
				<img src="{{asset('public/assets/frontend/img/MAboutUsBanner.jpg')}}" class="mobile-img">
            @else
                <img src="{{asset('public/assets/images/cms/mobile_banner/'.$aboutData->cms_mobile_banner)}}" class="mobile-img">
			@endif
		@else
			<img src="{{asset('public/assets/images/cms/mobile_banner/'.$aboutData->mobile_banner)}}" class="mobile-img">
		@endif
	@endif
        <div class="overlay">
            <div class="container">

                <div class="w648">
                    <?php if(false)
                    {
                        ?><h3>@if(!empty($aboutData->title)) {{ $aboutData->title }} @endif</h3>
                        <p class="m-0">@if(!empty($aboutData->banner_text)) {{ $aboutData->banner_text }} @endif</p><?php
                    }
                    ?>
                </div>

            </div>
        </div>
    </section>
    <section class="about-us">
        <div class="container">
            <h5>{{ $pageName }}</h5>
            <div class="c-row">
                <div>
                    <p id="aboutDesc"></p>
                </div>
            </div>
        </div>
    </section>
    @include('frontend.include.bottom')
    @include('frontend.include.footer')
    <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
    <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
    <script>
        $(document).ready(function(){
            console.log(aboutUs);
            if(aboutUs != null)
            {
                $('#aboutDesc').html(aboutUs.description);
            }
        })
    </script>
</body>
</html>
