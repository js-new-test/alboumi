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
    <title>@if(!empty($policyData->seo_title)) {{ $policyData->seo_title }} @else {{ $pageName }} @endif | {{ $projectName}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="@if(!empty($policyData->seo_description)) <?php echo $policyData->seo_description ?> @endif">
    <meta name="keywords" content="@if(!empty($policyData->seo_keyword)) <?php echo $policyData->seo_keyword ?> @endif">
    @include('frontend.include.top',['visibility' => $visibility])
    @include('frontend.include.header')
    <script>        
        var baseUrl = <?php echo json_encode($baseUrl); ?>;
    </script>
</head>
<body>

    <div class="thumb-nav tb-11">
        <div class="container">
            <a href="{{ url('/') }}">Home</a>
            <span>{{ $pageName }}</span>
        </div>
    </div>


    <section class="privacy">
        <div class="container">
            <h4>{{ $pageName }}</h4>
            <div class="dividers"></div>
            @if(!empty($policyData->description))
                {!! $policyData->description !!}
            @endif
        </div>
    </section>
    @include('frontend.include.bottom')
    @include('frontend.include.footer')
    <script src="{{asset('public/assets/frontend/js/header/header.js')}}"></script>
    <script src="{{asset('public/assets/frontend/js/footer/footer.js')}}"></script>
</body>
</html>
