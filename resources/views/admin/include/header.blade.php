
<div class="app-header header-shadow bg-night-sky header-text-light">
    <div class="app-header__logo">
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
    <div class="app-header__content">

        <div class="app-header-right">

            <div class="header-btn-lg">
                <div class="widget-content">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="btn-group">

                                <div tabindex="-1" role="menu" aria-hidden="true"
                                    class="rm-pointers dropdown-menu-lg dropdown-menu dropdown-menu-right">

                                    <div class="scroll-area-xs">
                                        <div class="scrollbar-container">
                                            <ul class="nav flex-column">

                                                <li class="nav-item">
                                                    <a href="{{url('/admin/profile')}}" class="nav-link">My Profile</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{url('/admin/change/password')}}" class="nav-link">Change Password
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                <a href="{{url('/admin/logout')}}" class="nav-link">Logout
                                                        
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">

                            <div class="widget-content-left  ml-3 header-user-info">
                                <div class="widget-heading">
                                    {{ Session::get('username') }}
                                    <i class="fa fa-angle-down ml-2 opacity-8"></i>

                                </div>
                                <div class="widget-subheading">
                                    <!-- VP People Manager -->
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
