<div class="col-12 col-sm-12 col-md-4 col-lg-3">
    <div class="pr-24">
        <div class="left-side-bar">
            <div class="dropdowns">
                My Account
            </div>
            @php
            $codes = ['SIDEBARLABEL1','SIDEBARLABEL2','SIDEBARLABEL3','SIDEBARLABEL4','SIDEBARLABEL5','SIDEBARLABEL6'
            ,'SIDEBARLABEL7','SIDEBARLABEL8','SIDEBARLABEL9'];
            $logoutLabels = getCodesMsg(Session::get('language_id'), $codes);
            @endphp
            <ul class="dropdowns-toggles">
                <!-- <li class="active">
                    <a href="{{url('customer/dashboard')}}">{{$logoutLabels['SIDEBARLABEL8']}}</a>
                </li> -->
                <li class="{{ request()->is('customer/my-account') ? 'active' : '' }}">
                    <a href="{{url('customer/my-account')}}">{{$logoutLabels['SIDEBARLABEL1']}}</a>
                </li>
                <li class="{{ request()->is('customer/change-password') ? 'active' : '' }}">
                    <a href="{{url('customer/change-password')}}">{{$logoutLabels['SIDEBARLABEL2']}}</a>
                </li>
                <li class="{{ request()->is('customer/my-orders') ? 'active' : '' }} || {{ request()->is('customer/orderdetails') ? 'active' : ''}}">
                    <a href="{{url('customer/my-orders')}}">{{$logoutLabels['SIDEBARLABEL3']}}</a>
                </li>
                <li class="{{ request()->is('customer/my-address') ? 'active' : '' }}">
                    <a href="{{url('customer/my-address')}}">{{$logoutLabels['SIDEBARLABEL4']}}</a>
                </li>
                <li class="{{ request()->is('customer/billing-address') ? 'active' : '' }}">
                    <a href="{{url('customer/billing-address')}}">{{$logoutLabels['SIDEBARLABEL9']}}</a>
                </li>
                <li class="{{ request()->is('customer/myEventEnquiries') ? 'active' : '' }}">
                    <a href="{{url('customer/myEventEnquiries')}}">{{$logoutLabels['SIDEBARLABEL5']}}</a>
                </li>
                <li class="{{ request()->is('customer/myEventGallery') ? 'active' : '' }}">
                    <a href="{{url('customer/myEventGallery')}}">{{$logoutLabels['SIDEBARLABEL6']}}</a>
                </li>
                <li class="{{ request()->is('customer/logout') ? 'active' : '' }}">
                    <a href="{{url('customer/logout')}}">{{$logoutLabels['SIDEBARLABEL7']}}</a>
                </li>
            </ul>
        </div>
    </div>
</div>
