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

    <head>
        <title>{{ $pageName }} | {{ $projectName}}</title>
        <meta name="description" content="@yield('description')">
        <meta name="keywords" content="@yield('keywords')">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <link rel="icon" href="{{ asset('public/assets/images/favicon.ico') }}" type="image/x-icon"/>

        @include('frontend.include.top',['visibility' => $visibility])
    </head>
    <body>
        <!-- Free Delivery Message -->
        @if(!empty($freeDeliveryMsg->value))
        <div class="FDM">
            {{ $freeDeliveryMsg->value }}
        </div>
        @endif
        @include('frontend.include.header')
        @yield('content')
        @include('frontend.include.footer')
        @include('frontend.include.bottom')
        @stack('scripts')
        <script>
        @if(Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}";
            switch(type){
                case 'info':
                    toastr.info("{{ Session::get('message') }}");
                    break;

                case 'warning':
                    toastr.warning("{{ Session::get('message') }}");
                    break;

                case 'success':
                    toastr.success("{{ Session::get('message') }}");
                    break;

                case 'error':
                    toastr.error("{{ Session::get('message') }}");
                    break;
            }
        @endif
        </script>
    </body>
</html>
