<!DOCTYPE html>
@php
    $visibility = App\Models\GlobalLanguage::checkVisibility($lang_id);
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
    <style>
        @font-face {
        font-family: M_bold;
        src: url(../public/assets/frontend/fonts/Montserrat-Bold.ttf);
        }
        @font-face {
        font-family: M_Sbold;
        src: url(../public/assets/frontend/fonts/Montserrat-SemiBold.ttf);
        }
        @font-face {
        font-family: M_medium;
        src: url(../public/assets/frontend/fonts/Montserrat-Medium.ttf);
        }
        @font-face {
        font-family: O_Regular;
        src: url(../public/assets/frontend/fonts/OpenSans-Regular.ttf);
        }
        @font-face {
        font-family: O_Sbold;
        src: url(../public/assets/frontend/fonts/OpenSans-SemiBold.ttf);
        }
        @font-face {
        font-family: M_Regular;
        src: url(../public/assets/frontend/fonts/Montserrat-Regular.ttf);
        }

        @media (min-width: 1366px)
        {
            .container {
                max-width: 1224px !important;
                width: 1224px!important;
                padding-left: 0!important;
                padding-right: 0!important;
            }
        }
        .container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl {
            width: 100%;
            padding-right: auto;
            padding-left: auto;
            margin-right: auto;
            margin-left: auto;
        }
        body {
            text-align: justify;
            font-family: O_Regular;
            color: rgba(0,0,0,0.6);
            font-size: 16px;
            letter-spacing: 0.5px;
            line-height: 22px;
        }
        h4 {
            color: #212121;
            font-size: 33px;
            font-weight: bold;
            letter-spacing: 0.25px;
            line-height: 40px;
            font-family: M_bold;
        }
        .dividers {
            width: 100%;
            height: 1px;
            background: #E7E7ED;
        }
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
        .about-banner h4 {
        margin-bottom: 7px;
        }
        .about-banner .dividers {
            margin-bottom: 21px;
        }
    </style>
</head>
<body>
    <section class="about-banner">    
            <div class="container">                
                <h4>{{ $aboutData->title }}</h4>
                <div class="dividers"></div>
                @if(!empty($aboutData->description))
                    {!! $aboutData->description !!}
                @endif
            </div>
    </section>    
</body>
</html>