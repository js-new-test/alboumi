<style>
.sidebar-navigation ul li a{
    border-bottom: 1px solid #E7E7ED !important;
}
.sidebar-navigation > ul > li:last-child a {
    border: 0;
    padding: 18.5px 0;
}
.language-change_mb .dropdown-menu {
    min-width: 100%;
    border: none;
}
.drop-arrow::after{
    margin-left:0
}
.language-change_mb .dropdown-item.active, .language-change_mb .dropdown-item:active {
    color: #000 !important;
    background: #fff !important;
}
.iconImg{
    height: 27px;
    width: 27px;
}
</style>
@php
    $cart_master_id = Session::get('cart_master_id');

    $totalCartItems = App\Models\Cart::select('id')->where('cart_master_id',$cart_master_id)->get();
    $totalCartItemsCount = count($totalCartItems);

    $langId = Session::get('language_id');

    $codes = ['SIGN_UP', 'MYACCOUNTLABEL','MYPHOTOS','MYPROJECTS','TRACKORDER','ENTERSEARCHVAL','RECENT_TITLE'];
    $labels = getCodesMsg($langId, $codes);
@endphp

<header class="desktop-header">
    <nav class="main-nav navbar navbar-expand-sm bg-dark navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{asset('public/assets/frontend/img/Alboumi_Logo.png')}}" alt="logo">
            </a>
            <ul class="navbar-nav m-auto">
                <li class="nav-item">
                    <form class="header-search" id="prodSearchForm" action = "{{ url('search') }}" method="post">
                    @csrf
                        <input type="text" placeholder="{{ $labels['RECENT_TITLE'] }}" name="searchVal" id="searchVal">
                        <button type="submit" id="searchProdBtn">
                            <img src="{{asset('public/assets/frontend/img/search.svg')}}" alt="search">
                        </button>
                    </form>
                </li>
            </ul>
            <ul class="navbar-nav">
                @if(Auth::guard('customer')->check())
                    <li class="nav-item center-line">
                        <a class="nav-link" href="{{url('customer/myEventGallery')}}">{{ $labels['MYPHOTOS'] }}</a>
                    </li>
                    <li class="nav-item center-line">
                        <a class="nav-link" href="{{ url('customer/myEventEnquiries') }}">{{ $labels['MYPROJECTS'] }}</a>
                    </li>
                @endif
                <li class="nav-item center-line track-order">
                    <a class="nav-link" href="{{ url('trackOrder') }}">{{ $labels['TRACKORDER'] }}</a>
                </li>
                <!-- <li class="nav-item dropdown center-line language-change">
                    <a class="nav-link dropdown-toggle" href="#" id="language" data-toggle="dropdown">
                        لعربية
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item active" href="#">لعربية</a>
                        <a class="dropdown-item" href="#">English</a>
                    </div>
                </li> -->
                <li class="nav-item dropdown center-line language-change selectedLang" id="selectedLang">
					<a class="nav-link dropdown-toggle" href="#"  data-toggle="dropdown" aria-expanded="true" id=""></a>
					<div class="dropdown-menu languages" style="cursor: pointer;" id="languages">
					</div>
                </li>
                <!-- <li class="nav-item dropdown" id="selectedCurr">
					<a class="nav-link dropdown-toggle" href="#"  data-toggle="dropdown" id=""></a>
					<div class="dropdown-menu" id="currencies">
					</div>
                </li> -->
            </ul>
        </div>
    </nav>

    <nav class="sub-nav navbar navbar-expand-sm bg-dark navbar-dark">
        <div class="container">
            @if(View::exists('admin.generatedMegamenu.' . $megamenuFileName))
                @include('admin.generatedMegamenu.' . $megamenuFileName)
                <ul class="navbar-nav ml-auto right-icon">
                    <li class="nav-item">
                        @if(Auth::guard('customer')->check())
                        <a class="nav-link" href="{{asset('/customer/my-account')}}">
                            <img src="{{asset('public/assets/frontend/img/User.png')}}">
                        </a>
                        @else
                        <a class="nav-link" href="{{asset('/login')}}">
                            <img src="{{asset('public/assets/frontend/img/User.png')}}">
                        </a>
                        @endif
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('/shopping-cart')}}">
                            <!-- cart badge start -->
                            <div class="cart-badge">
                                <div class="badge-border">

                                    <div class="badge-icon">{{ $totalCartItemsCount }}</div>
                                </div>
                                <img src="{{asset('public/assets/frontend/img/cart.png')}}">
                            </div>
                            <!-- cart badge end -->
                        </a>
                    </li>
                </ul>
            @else
            <ul class='navbar-nav mr-auto'>
            </ul>
                <ul class="navbar-nav ml-auto right-icon">
                    <li class="nav-item">
                        @if(Auth::guard('customer')->check())
                        <a class="nav-link" href="{{asset('/customer/my-account')}}">
                            <img src="{{asset('public/assets/frontend/img/User.png')}}">
                        </a>
                        @else
                        <a class="nav-link" href="{{asset('/login')}}">
                            <img src="{{asset('public/assets/frontend/img/User.png')}}">
                        </a>
                        @endif
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{url('/shopping-cart')}}">
                            <!-- cart badge start -->
                            <div class="cart-badge">
                                <div class="badge-border">
                                    <div class="badge-icon">{{ $totalCartItemsCount }}</div>
                                </div>
                                <img src="{{asset('public/assets/frontend/img/cart.png')}}">
                            </div>
                            <!-- cart badge end -->
                        </a>
                    </li>
                </ul>
            @endif
        </div>
    </nav>
</header>

<!-- Mobile Header Start -->
<header class="Mobile-header">
    <div class="container">
        <a href="{{ url('/') }}" class="mobile-logo">
            <img src="{{asset('public/assets/frontend/img/Alboumi_Logo.png')}}" alt="logo">
        </a>
        <div class="menu-icons">
            <div class="menus">
                <img src="{{asset('public/assets/frontend/img/Menu.png')}}" class="menuIcons" alt="menu">
            </div>
            {{--<div class="side-icons w-100">
                <form class="header-search" id="prodSearchForm" action = "{{ url('search') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <img src="{{asset('public/assets/frontend/img/search.svg')}}" class="search-icon text-right" alt="search" style="float:right">
                            <input type="text" name="searchVal" id="searchVal" class="search-input ml-4" placeholder="{{ $labels['RECENT_TITLE'] }}" spellcheck="false">
                            <img src="{{asset('public/assets/frontend/img/Close.svg')}}" class="search-close" alt="close">
                        </div>
                    </div>
                </form>

                <a href="{{url('/shopping-cart')}}">
                    <!-- cart badge start -->
                    <div class="cart-badge">
                        <div class="badge-border">
                            <div class="badge-icon">{{ $totalCartItemsCount }}</div>
                        </div>
                        <img src="{{asset('public/assets/frontend/img/cart.png')}}" class="cart-icon" alt="cart">
                    </div>
                    <!-- cart badge end -->
                </a>
            </div>--}}

            <div class="side-icons">
                @if(Auth::guard('customer')->check())
                    <a class="nav-link login-icon" href="{{asset('/customer/my-account')}}" style="padding: .5rem 0.6rem;">
                        <img src="{{asset('public/assets/frontend/img/User.png')}}">
                    </a>
                    @else
                    <a class="nav-link login-icon" href="{{asset('/login')}}" style="padding: .5rem 0.6rem;">
                        <img src="{{asset('public/assets/frontend/img/User.png')}}" class="iconImg">
                    </a>
                @endif
                <form id="prodSearchForm" class="mb-0" action = "{{ url('search') }}" method="post">
                    @csrf
                    <img src="{{asset('public/assets/frontend/img/search.svg')}}" class="search-icon" alt="search">
                    <input type="text" name="searchVal" id="searchVal" class="search-input" placeholder="Search for products" spellcheck="false">
                    <img src="{{asset('public/assets/frontend/img/Close.svg')}}" class="search-close" alt="close">
                </form>
                <a href="{{url('/shopping-cart')}}">
                    <!-- cart badge start -->
                    <div class="cart-badge">
                        <div class="badge-border">
                            <div class="badge-icon">{{ $totalCartItemsCount }}</div>
                        </div>
                        <img src="{{asset('public/assets/frontend/img/cart.png')}}" class="cart-icon" alt="cart">
                    </div>
                    <!-- cart badge end -->
                </a>
            </div>
        </div>
    </div>
</header>

<div class="sideMenu">
    <div class="side-top-fixed">
  	    <img src="{{asset('public/assets/frontend/img/Close.svg')}}" class="closeIcons" alt="close">
    </div>
    <div class="sidebar-navigation">
        <!-- // language dropdown -->
        <ul>
            <li class="dropdown language-change_mb selectedLang" id="selectedLang">
            <a class="nav-link s1 drop-arrow dropdown-toggle" href="#"  data-toggle="dropdown" aria-expanded="true" id=""></a>
            <ul class="subMenuColor1 dropdown-menu languages" style="cursor: pointer;" id="languages">
            </ul></li>
        </ul>

        @if(Auth::guard('customer')->check())
            <ul><li>
                <a class="nav-link" href="{{url('customer/myEventGallery')}}">{{ $labels['MYPHOTOS'] }}</a>
            </li></ul>
            <ul><li>
                <a class="nav-link" href="{{ url('customer/myEventEnquiries') }}">{{ $labels['MYPROJECTS'] }}</a>
            </li></ul>
        @endif

        @if(View::exists('admin.generatedMegamenu.' . $mobileMegamenuFileName))
            @include('admin.generatedMegamenu.' . $mobileMegamenuFileName)
        @endif

        
    </div>
</div>
<!-- Mobile Header End -->
<script>
    var langId = <?php echo Session::get('language_id');?>;
    var langCode = '<?php echo Session::get('language_code');?>';
    var langName = '<?php echo Session::get('language_name');?>';
    var defaultLangId = <?php echo Session::get('default_lang_id');?>;

    var currId = <?php echo Session::get('currency_id');?>;
    var currSymbol = '<?php echo Session::get('currency_symbol');?>';
    var currName = '<?php echo Session::get('currency_name');?>';
    var defaultCurrId = <?php echo Session::get('default_curr_id');?>;
    var decimalNumber = '<?php echo Session::get('decimal_number');?>';
    var decimalSeparator = '<?php echo Session::get('decimal_separator');?>';
    var thousandSeparator = '<?php echo Session::get('thousand_separator');?>';

    var searchErrorMsg = '<?php echo $labels['ENTERSEARCHVAL'] ?>';
</script>
