<div class="app-sidebar sidebar-shadow">
    <div class="app-header__logo">
        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                    data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>
    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu">
                <li class="app-sidebar__heading">Menu</li>
                @if(Auth::guard('admin')->check()  && Request::segment(1) == 'admin')
                    <li class="{{ request()->is('admin/dashboard') ? 'mm-active' : '' }}">
                        <a href="{{url('/admin/dashboard')}}">
                            <i class="active_icon metismenu-icon pe-7s-rocket"></i>
                            Dashboards
                        </a>
                    </li>
                {{--@else
                    <li class="{{ request()->is('photographer/dashboard') ? 'mm-active' : '' }}">
                        <a href="{{url('/photographer/dashboard')}}">
                            <i class="metismenu-icon pe-7s-rocket"></i>
                            Dashboards
                        </a>
                    </li>--}}
                @endif

                @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_role_view') === true || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_user_view') === true || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_customer_view') === true)
                <li>
                    <a href="#">
                        <i class="active_icon metismenu-icon pe-7s-users"></i>
                        Users
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul>
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_role_view') === true)
                        <li class="{{ request()->is('admin/user/role/list') ? 'mm-active' : '' }} || {{ request()->is('admin/user/role/edit/*') ? 'mm-active' : '' }} || {{ request()->is('admin/user/role/add') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/user/role/list')}}">
                                <i class="metismenu-icon"></i>
                                Roles
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_user_view') === true)
                        <li class="{{ request()->is('admin/user/list') ? 'mm-active' : '' }} || {{ request()->is('admin/user/add') ? 'mm-active' : '' }} || {{ request()->is('admin/user/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/user/list')}}">
                                <i class="metismenu-icon">
                                </i>
                                Users
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_customergroup_view') === true)
                        <li class="{{ request()->is('admin/custGroups') ? 'mm-active' : '' }} || {{ request()->is('admin/custGroups/addGroup') ? 'mm-active' : '' }} || {{ request()->is('admin/custGroups/editGroup/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/custGroups')}}">
                                <i class="metismenu-icon">
                                </i>
                                Customer Groups
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_customer_view') === true)
                        <li class="{{ request()->is('admin/customer/list') ? 'mm-active' : '' }} || {{ request()->is('admin/customer/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/customer/list')}}">
                                <i class="metismenu-icon">
                                </i>
                                Customers
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_banner') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_cmsPages') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_footer_generator') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_home_page_text') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_services') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_collection') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_home_page_content') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_home_page_photographer') === true)
                <li>
                    <a href="#">
                        <i class="active_icon metismenu-icon pe-7s-note2"></i>
                        CMS
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul>
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_banner') === true)
                        <li class="{{ request()->is('admin/banner') ? 'mm-active' : '' }}  || {{ request()->is('admin/banner/add') ? 'mm-active' : '' }} || {{ request()->is('admin/banner/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/banner')}}">
                                <i class="active_icon metismenu-icon pe-7s-display1"></i>
                                Home Page Banners
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_cmsPages') === true)
                        <li class="{{ request()->is('admin/cmsPages') ? 'mm-active' : '' }}  || {{ request()->is('admin/cmsPages/addPage') ? 'mm-active' : '' }} || {{ request()->is('admin/cmsPages/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/cmsPages')}}">
                                <i class="active_icon metismenu-icon pe-7s-settings"></i>
                                CMS Pages
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_sellers') === true)
                        <li class="{{ request()->is('admin/sellers') ? 'mm-active' : '' }}  || {{ request()->is('admin/seller/addSeller') ? 'mm-active' : '' }} || {{ request()->is('admin/seller/editSeller/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/sellers')}}">
                                <i class="active_icon metismenu-icon pe-7s-settings"></i>
                                Best Seller
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_books') === true)
                        <li class="{{ request()->is('admin/books') ? 'mm-active' : '' }} || {{ request()->is('admin/books/addBook') ? 'mm-active' : '' }} || {{ request()->is('admin/books/editBook/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/books')}}">
                                <i class="active_icon metismenu-icon pe-7s-settings"></i>
                                Photo Books
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_footer_generator') === true)
                        <li class="{{ request()->is('admin/footer-generator') ? 'mm-active' : '' }}  || {{ request()->is('admin/footer-generator/add') ? 'mm-active' : '' }} || {{ request()->is('admin/footer-generator/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/footer-generator')}}">
                                <i class="active_icon metismenu-icon pe-7s-settings"></i>
                                Footer Generator
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_home_page_text') === true)
                        <li class="{{ request()->is('admin/home-page-text') ? 'mm-active' : '' }}  || {{ request()->is('admin/home-page-text/add') ? 'mm-active' : '' }} || {{ request()->is('admin/home-page-text/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/home-page-text')}}">
                                <i class="active_icon metismenu-icon pe-7s-settings"></i>
                                Home Page Text
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_services') === true)
                        <li class="{{ request()->is('admin/services') ? 'mm-active' : '' }}  || {{ request()->is('admin/services/addService') ? 'mm-active' : '' }} || {{ request()->is('admin/services/editService/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/services')}}">
                                <i class="active_icon metismenu-icon pe-7s-photo"></i>
                                Our Services
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_collection') === true)
                        <li class="{{ request()->is('admin/collection') ? 'mm-active' : '' }}  || {{ request()->is('admin/collection/addCollection') ? 'mm-active' : '' }} || {{ request()->is('admin/collection/editCollection/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/collection')}}">
                                <i class="active_icon metismenu-icon pe-7s-photo"></i>
                                Our Collection
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_how_it_works') === true)
                        <li class="{{ request()->is('admin/howitWorks') ? 'mm-active' : '' }}  || {{ request()->is('admin/howitWorks/addHowitWorks') ? 'mm-active' : '' }} || {{ request()->is('admin/howitWorks/editHowItWorks/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/howitWorks')}}">
                                <i class="active_icon metismenu-icon pe-7s-photo"></i>
                                How It Works
                            </a>
                            <ul>
                                <li class="{{ request()->is('admin/how-it-works-banner') ? 'mm-active' : '' }}  || {{ request()->is('admin/how-it-works-banner/add') ? 'mm-active' : '' }} || {{ request()->is('admin/how-it-works-banner/edit/*') ? 'mm-active' : '' }}">
                                    <a href="{{url('admin/how-it-works-banner')}}">
                                        <i class="metismenu-icon"></i>
                                        How It Works Banner
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_home_page_content') === true)
                        <li class="{{ request()->is('admin/home-page-content') ? 'mm-active' : '' }}  || {{ request()->is('admin/home-page-content/add') ? 'mm-active' : '' }} || {{ request()->is('admin/home-page-content/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/home-page-content')}}">
                                <i class="active_icon metismenu-icon pe-7s-photo"></i>
                                Home Page Content
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_home_page_photographer') === true)
                        <li class="{{ request()->is('admin/home-page-photographer') ? 'mm-active' : '' }}  || {{ request()->is('admin/home-page-photographer/add') ? 'mm-active' : '' }} || {{ request()->is('admin/home-page-photographer/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/home-page-photographer')}}">
                                <i class="active_icon metismenu-icon pe-7s-photo"></i>
                                Home Page Photographer
                            </a>
                        </li>
                        @endif

                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_mega_menu') === true)
                        <li class="{{ request()->is('admin/mega-menu') ? 'mm-active' : '' }}  || {{ request()->is('admin/mega-menu/addMenu') ? 'mm-active' : '' }} || {{ request()->is('admin/mega-menu/editMenu/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/mega-menu')}}">
                                <i class="active_icon metismenu-icon pe-7s-photo"></i>
                                Mega Menu
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_category') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_brand') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_product') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_attribute_group') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_attribute') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_promotions') === true)
                <li>
                    <a href="#">
                        <i class="active_icon metismenu-icon pe-7s-cart"></i>
                        eCommerce
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul>
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_category') === true)
                        <li class="{{ request()->is('admin/categories') ? 'mm-active' : '' }} || {{ request()->is('admin/categories/addCategory') ? 'mm-active' : '' }} || {{ request()->is('admin/categories/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/categories')}}">
                                <i class="metismenu-icon"></i>
                                Category
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_brand') === true)
                        <li class="{{ request()->is('admin/manufacturers') ? 'mm-active' : '' }} || {{ request()->is('admin/manufacturers/add') ? 'mm-active' : '' }} || {{ request()->is('admin/manufacturers/edit/*') ? 'mm-active' : '' }} || {{ request()->is('admin/*/showBrand') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/manufacturers')}}">
                                <i class="metismenu-icon"></i>
                                Brand
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_product') === true)
                        <li class="{{ request()->is('admin/products') ? 'mm-active' : '' }} || {{ request()->is('admin/product/addProduct') ? 'mm-active' : '' }} || {{ request()->is('admin/product/editProduct/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/products')}}">
                                <i class="metismenu-icon"></i>
                                Product
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_attribute_group') === true)
                        <li class="{{ request()->is('admin/attributeGroup') ? 'mm-active' : '' }} || {{ request()->is('admin/attributeGroup/addAttributeGroup') ? 'mm-active' : '' }} || {{ request()->is('admin/attributeGroup/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/attributeGroup')}}">
                                <i class="metismenu-icon"></i>
                                Attribute Groups
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_attribute') === true)
                        <li class="{{ request()->is('admin/attribute') ? 'mm-active' : '' }} || {{ request()->is('admin/attribute/addAttribute') ? 'mm-active' : '' }} || {{ request()->is('admin/attribute/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/attribute')}}">
                                <i class="metismenu-icon">
                                </i>
                                Attributes
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_promotions') === true)
                        <li class="{{ request()->is('admin/promotions') ? 'mm-active' : '' }}  || {{ request()->is('admin/promotions/addPromotion') ? 'mm-active' : '' }} || {{ request()->is('admin/promotions/editPromotion/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/promotions')}}">
                                <i class="active_icon metismenu-icon pe-7s-settings"></i>
                                Promotions
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_orders') === true)
                <li>
                    <a href="#">
                        <i class="active_icon metismenu-icon pe-7s-cart"></i>
                        Sales
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul>
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_orders') === true)
                        <li class="{{ request()->is('admin/orders') ? 'mm-active' : '' }} || {{ request()->is('admin/orders/orderDetails/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/orders')}}">
                                <i class="metismenu-icon"></i>
                                Orders
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_invoices') === true)
                        <li class="{{ request()->is('admin/invoices') ? 'mm-active' : '' }} || {{ request()->is('admin/invoices/invoiceDetails/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/invoices')}}">
                                <i class="metismenu-icon"></i>
                                Invoices
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_eventPhotoSales') === true)
                        <li class="{{ request()->is('admin/eventPhotoSales') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/eventPhotoSales')}}">
                                <i class="metismenu-icon"></i>
                                Event Photos Sales
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_eventEnqPayment') === true)
                        <li class="{{ request()->is('admin/eventEnqPayment') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/eventEnqPayment')}}">
                                <i class="metismenu-icon"></i>
                                Event Enquiry Payments
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_event') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_package') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_additional_service') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_event_enq') === true)
                <li>
                    <a href="#">
                        <i class="active_icon metismenu-icon pe-7s-gift"></i>
                        Event Management
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul>
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_additional_service') === true)
                        <li class="{{ request()->is('admin/additional-service') ? 'mm-active' : '' }} || {{ request()->is('admin/additional-service/add') ? 'mm-active' : '' }} || {{ request()->is('admin/additional-service/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/additional-service')}}">
                                <i class="metismenu-icon"></i>
                                Additional Service
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_event') === true)
                        <li class="{{ request()->is('admin/event/list') ? 'mm-active' : '' }} || {{ request()->is('admin/event/addEvent') ? 'mm-active' : '' }} || {{ request()->is('admin/event/editEvent/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/event/list')}}">
                                <i class="metismenu-icon"></i>
                                Events
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_package') === true)
                        <li class="{{ request()->is('admin/package/list') ? 'mm-active' : '' }} || {{ request()->is('admin/package/addPackage') ? 'mm-active' : '' }} || {{ request()->is('admin/package/editPackage/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/package/list')}}">
                                <i class="metismenu-icon"></i>
                                Packages
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_event_enq') === true)
                        <li class="{{ request()->is('admin/eventEnq') ? 'mm-active' : '' }} || {{ request()->is('admin/eventEnq/viewEnqDetails/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/eventEnq')}}">
                                <i class="metismenu-icon"></i>
                                Event Enquiry
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_currency') === true || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_language') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_countries') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_faq') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_add_footerDetails') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_email_templates') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_contact_us') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_locale') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_store_location') === true
                || whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_holiday') === true)
                <li>
                    <a href="#">
                        <i class="active_icon metismenu-icon pe-7s-config"></i>
                        Settings
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul>
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_holiday') === true)
                        <li class="{{ request()->is('admin/holiday') ? 'mm-active' : '' }}  || {{ request()->is('admin/holiday/add') ? 'mm-active' : '' }} || {{ request()->is('admin/holiday/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/holiday')}}">
                                <i class="active_icon metismenu-icon pe-7s-settings"></i>
                                Holiday
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_store_location') === true)
                        <li class="{{ request()->is('admin/store-location') ? 'mm-active' : '' }}  || {{ request()->is('admin/store-location/add') ? 'mm-active' : '' }} || {{ request()->is('admin/store-location/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/store-location')}}">
                                <i class="active_icon metismenu-icon pe-7s-settings"></i>
                                Store Location
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_currency') === true)
                        <li class="{{ request()->is('admin/currency/list') ? 'mm-active' : '' }} || {{ request()->is('admin/currency/add') ? 'mm-active' : '' }} || {{ request()->is('admin/currency/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/currency/list')}}">
                                <i class="metismenu-icon"></i>
                                Currency
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_language') === true)
                        <li class="{{ request()->is('admin/language/list') ? 'mm-active' : '' }} || {{ request()->is('admin/language/add') ? 'mm-active' : '' }} || {{ request()->is('admin/language/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/language/list')}}">
                                <i class="metismenu-icon">
                                </i>
                                Language
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_countries') === true)
                        <li class="{{ request()->is('admin/countries') ? 'mm-active' : '' }} || {{ request()->is('admin/countries/addCountry') ? 'mm-active' : '' }} || {{ request()->is('admin/countries/editCountry/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/countries')}}">
                                <i class="metismenu-icon">
                                </i>
                                Country
                            </a>
                        </li>
                        @endif
                        {{--@if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_faq') === true)
                        <li class="{{ request()->is('admin/faq') ? 'mm-active' : '' }} || {{ request()->is('admin/faq/list') ? 'mm-active' : '' }} || {{ request()->is('admin/faq/addFaq') ? 'mm-active' : '' }} || {{ request()->is('admin/faq/editFaq/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/faq')}}">
                                <i class="metismenu-icon">
                                </i>
                                FAQs
                            </a>
                        </li>
                        @endif--}}
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_add_footerDetails') === true)
                        <li class="{{ request()->is('admin/footerDetails') ? 'mm-active' : '' }} || {{ request()->is('admin/updateFooterDetails') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/footerDetails')}}">
                                <i class="metismenu-icon">
                                </i>
                                General Settings
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_email_templates') === true)
                        <li class="{{ request()->is('admin/emailTemplates/list') ? 'mm-active' : '' }} || {{ request()->is('admin/emailTemplates/addTemplate') ? 'mm-active' : '' }} || {{ request()->is('admin/emailTemplates/editTemplate/*') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/emailTemplates/list')}}">
                                <i class="active_icon metismenu-icon pe-7s-mail"></i>
                                Email Templates
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_contact_us') === true)
                        <li class="{{ request()->is('admin/contactUs') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/contactUs')}}">
                                <i class="active_icon metismenu-icon pe-7s-user"></i>
                                Contact Us
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_locale') === true)
                        <li class="{{ request()->is('admin/locale') ? 'mm-active' : '' }}  || {{ request()->is('admin/locale/add') ? 'mm-active' : '' }} || {{ request()->is('admin/locale/edit/*') ? 'mm-active' : '' }}">
                            <a href="{{url('/admin/locale')}}">
                                <i class="active_icon metismenu-icon pe-7s-settings"></i>
                                Localization
                            </a>
                        </li>
                        @endif
                        @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_generate_megamenu') === true)
                        <li class="{{ request()->is('admin/generate-megamenu') ? 'mm-active' : '' }}">
                            <a href="{{url('admin/generate-megamenu')}}">
                                <i class="active_icon metismenu-icon pe-7s-settings"></i>
                                Generate Megamenu
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(whoCanCheck(config('app.arrWhoCanCheck'), 'admin_view_photographer') === true)
                <li class="{{ request()->is('admin/photgraphers') ? 'mm-active' : '' }} || {{ request()->is('admin/photgraphers/addPhotographer') ? 'mm-active' : '' }} || {{ request()->is('admin/photgraphers/edit/*') ? 'mm-active' : '' }}">
                    <a href="{{url('/admin/photgraphers')}}">
                        <i class="active_icon metismenu-icon pe-7s-photo"></i>
                        Bahrain Photographers
                    </a>
                </li>
                @endif
                @if(Auth::guard('photographer')->check()  && Request::segment(1) == 'photographer')
                    <li>
                        <a href="{{url('/photographer/profile')}}">
                            <i class="active_icon metismenu-icon pe-7s-user"></i>
                            Profile
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
