<div class="col-sm-3 page-sideabr">
    <aside>
        <div class="inner-box modern-user-sidebar">
            <div class="user-sidebar-profile">
                <div class="sidebar-user-avatar">
                    <img src="{{ \Auth::user()->avatar_url }}"
                         onerror="this.onerror=null; this.src='{{ asset('assets/img/avatar_users/default_avatar.png') }}';"
                         alt="{{ \Auth::user()->name }}">
                </div>
                <div class="sidebar-user-info">
                    <h5 class="sidebar-user-name">{{ \Auth::user()->name }}</h5>
                    <p class="sidebar-user-email">{{ \Auth::user()->email }}</p>
                </div>
            </div>

            <div class="user-panel-sidebar">
                <!-- Group 1: Thông tin cá nhân -->
                <div class="sidebar-group">
                    <h5 class="collapset-title">
                        <i class="fa fa-user-circle"></i>
                        <span>{{ __('titles.profile_info') }}</span>
                    </h5>
                    <ul class="acc-list">
                        <li id="personal-page" class="{{ request()->routeIs('users.show') ? 'active' : '' }}">
                            <a href="{{ route('users.show', Auth::user()->id) }}">
                                <i class="fa fa-user"></i>
                                <span class="sidebar-menu-text">{{ __('titles.personal_page') }}</span>
                            </a>
                        </li>
                        <li id="notification-page" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                            <a href="{{ route('notifications.index') }}">
                                <i class="fa fa-bell"></i>
                                <span class="sidebar-menu-text">{{ __('titles.notification') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 2: Khóa học -->
                <div class="sidebar-group">
                    <h5 class="collapset-title">
                        <i class="fa fa-graduation-cap"></i>
                        <span>{{ __('titles.course') }}</span>
                    </h5>
                    <ul class="acc-list">
                        <li id="my-course-link" class="{{ request()->routeIs('courses.my_course') ? 'active' : '' }}">
                            <a href="{{ route('courses.my_course', \Auth::user()->id) }}">
                                <i class="fa fa-book"></i>
                                <span class="sidebar-menu-text">{{ __('titles.my_course') }}</span>
                                @php
                                    $enrolledCount = \App\Models\CourseUser::where('user_id', \Auth::user()->id)->count();
                                @endphp
                                @if($enrolledCount > 0)
                                    <span class="badge sidebar-badge">{{ $enrolledCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 3: Giỏ hàng -->
                <div class="sidebar-group">
                    <h5 class="collapset-title">
                        <i class="fa fa-shopping-bag"></i>
                        <span>{{ __('titles.cart') }}</span>
                    </h5>
                    <ul class="acc-list">
                        <li id="my-cart" class="{{ (request()->routeIs('cart_items.*') && !request()->has('tab')) ? 'active' : '' }}">
                            <a href="{{ route('cart_items.index') }}">
                                <i class="fa fa-shopping-cart"></i>
                                <span class="sidebar-menu-text">{{ __('titles.my_cart') }}</span>
                                @php
                                    $cartCount = \App\Models\CartItem::where('user_id', \Auth::user()->id)->where('cart_item_type', 0)->count();
                                @endphp
                                @if($cartCount > 0)
                                    <span class="badge sidebar-badge">{{ $cartCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cart_items.index') }}#saved-for-later">
                                <i class="fa fa-bookmark-o"></i>
                                <span class="sidebar-menu-text">{{ __('titles.saved_for_later') }}</span>
                                @php
                                    $laterCount = \App\Models\CartItem::where('user_id', \Auth::user()->id)->where('cart_item_type', 1)->count();
                                @endphp
                                @if($laterCount > 0)
                                    <span class="badge sidebar-badge">{{ $laterCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('cart_items.index') }}#wishlist">
                                <i class="fa fa-heart-o"></i>
                                <span class="sidebar-menu-text">{{ __('titles.wishlist') }}</span>
                                @php
                                    $wishCount = \App\Models\CartItem::where('user_id', \Auth::user()->id)->where('cart_item_type', 2)->count();
                                @endphp
                                @if($wishCount > 0)
                                    <span class="badge sidebar-badge">{{ $wishCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>

<style>
    /* Clean Modern User Sidebar - Left Aligned */
    .modern-user-sidebar {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px 16px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        margin-bottom: 25px;
    }

    .user-sidebar-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 16px;
        margin-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
        text-align: left;
    }

    .sidebar-user-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        flex-shrink: 0;
        background: #f8fafc;
    }

    .sidebar-user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sidebar-user-info {
        min-width: 0;
        flex: 1;
        text-align: left;
    }

    .sidebar-user-name {
        font-size: 14.5px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
    }

    .sidebar-user-email {
        font-size: 12px;
        color: #64748b;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
    }

    .user-panel-sidebar .sidebar-group {
        margin-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 14px;
        text-align: left;
    }

    .user-panel-sidebar .sidebar-group:last-child {
        margin-bottom: 0;
        border-bottom: none;
        padding-bottom: 0;
    }

    .user-panel-sidebar .collapset-title {
        font-size: 12.5px;
        font-weight: 700;
        text-transform: uppercase;
        color: #475569;
        letter-spacing: 0.04em;
        margin: 0 0 8px;
        padding: 0 6px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        text-align: left;
        gap: 8px;
        border: none !important;
    }

    .user-panel-sidebar .collapset-title i {
        color: #2563eb;
        font-size: 14px;
        width: 16px;
        text-align: center;
    }

    .user-panel-sidebar .acc-list {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }

    .user-panel-sidebar .acc-list li {
        margin: 3px 0;
        text-align: left;
    }

    .user-panel-sidebar .acc-list li a {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        text-align: left !important;
        padding: 8px 12px !important;
        border-radius: 8px !important;
        font-size: 13.5px !important;
        font-weight: 500 !important;
        color: #334155 !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
        gap: 10px !important;
    }

    .user-panel-sidebar .acc-list li a i {
        width: 18px !important;
        font-size: 14.5px !important;
        color: #64748b !important;
        margin: 0 !important;
        text-align: center !important;
        flex-shrink: 0 !important;
        transition: color 0.2s ease !important;
    }

    .user-panel-sidebar .acc-list li a .sidebar-menu-text {
        text-align: left !important;
        flex: 1 !important;
        margin-right: auto !important;
    }

    .user-panel-sidebar .acc-list li a:hover {
        background: #f8fafc !important;
        color: #2563eb !important;
    }

    .user-panel-sidebar .acc-list li a:hover i {
        color: #2563eb !important;
    }

    .user-panel-sidebar .acc-list li.active a {
        background: #eff6ff !important;
        color: #2563eb !important;
        font-weight: 600 !important;
    }

    .user-panel-sidebar .acc-list li.active a i {
        color: #2563eb !important;
    }

    .sidebar-badge {
        background: #e2e8f0 !important;
        color: #334155 !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        padding: 2px 7px !important;
        border-radius: 10px !important;
        margin-left: auto !important;
        flex-shrink: 0 !important;
    }

    .user-panel-sidebar .acc-list li.active .sidebar-badge {
        background: #2563eb !important;
        color: #ffffff !important;
    }
</style>