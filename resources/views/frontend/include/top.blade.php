<meta name="facebook-domain-verification" content="cz3jboqof5pp5phq4v7zl6cptbw7nz" />

<link rel="preload" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"></noscript>

<link rel="preload" href="https://unpkg.com/bootstrap-select@1.13.8/dist/css/bootstrap-select.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://unpkg.com/bootstrap-select@1.13.8/dist/css/bootstrap-select.css"></noscript>

<link rel="stylesheet" href="{{asset('public/assets/frontend/css/owl.carousel.min.css')}}">
<link rel="stylesheet" href="{{asset('public/assets/frontend/css/owl.theme.default.min.css')}}">
@if(Request::path() != '/')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">


<!-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css"> -->

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet"/>
@endif
@if($visibility->visibility == 0)
    <link rel="preload" href="{{asset('public/assets/frontend/css/style.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<noscript><link rel="stylesheet" href="{{asset('public/assets/frontend/css/style.css')}}"></noscript>

    <link rel="stylesheet" href="{{asset('public/assets/frontend/css/responsive.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/frontend/css/custom.css')}}">
	
@endif
@if($visibility->visibility == 1)
    <link rel="preload" href="{{asset('public/assets/frontend/css/rtl/style.css')}}" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<noscript><link rel="stylesheet" href="{{asset('public/assets/frontend/css/rtl/style.css')}}"></noscript>

    <link rel="stylesheet" href="{{asset('public/assets/frontend/css/rtl/responsive.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/frontend/css/rtl/custom.css')}}">
@endif
