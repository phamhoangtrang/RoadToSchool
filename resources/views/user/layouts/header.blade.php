<div class="header">
    <nav class="navbar navbar-default main-navigation" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand logo" href="/"><img src="/assets/img/logo.png" alt=""></a>
            </div>
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="nav navbar-nav navbar-right" id="top-nav-area">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('titles.login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('titles.register') }}</a>
                        </li>
                    @else
                        <input type="hidden" value="{{ \Auth::user()->id }}" id="current-user">
                        {{--Cart--}}
                        <li class="dropdown">
                            @php
                                $cartItemsQuery = \App\Models\CartItem::where('user_id', Auth::user()->id)->where('cart_item_type', \App\Models\CartItem::IN_CART_TYPE);
                                $cartItems = $cartItemsQuery->get();
                                $cartItemsCount =count($cartItems);
                                $totalOriginPrice = 0;
                                $totalPromotionPrice = 0;
                                foreach($cartItems as $cartItem) {
                                $totalOriginPrice += $cartItem->course->origin_price;
                                if(0 != $cartItem->course->promotion_price) {
                                $totalPromotionPrice += $cartItem->course->promotion_price;
                                } else {
                                $totalPromotionPrice += $cartItem->course->origin_price;
                                }
                                }
                            @endphp
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false"><i class="fa fa-shopping-cart"></i> {{ __('titles.cart') }} <span
                                        class="badge"> {{ $cartItemsCount }} </span></a>
                            <ul class="dropdown-menu dropdown-cart" role="menu">
                                @if($cartItems->isEmpty())
                                    <li class="empty-cart-state">
                                        <div class="empty-cart-icon"><i class="fa fa-shopping-basket"></i></div>
                                        <p style="color: #64748b; font-size: 14px; margin: 0;">{{ __('messages.your_cart_is_empty') }}</p>
                                        <a class="btn-keep-shopping" href="{{ route('home') }}">
                                            {{ __('titles.keep_shopping') }}
                                        </a>
                                    </li>
                                @else
                                    <li class="dropdown-cart-header">
                                        <span class="dropdown-cart-title">Khóa học trong giỏ ({{ $cartItemsCount }})</span>
                                    </li>
                                    <div class="dropdown-cart-items-scroll">
                                        @foreach($cartItems as $cartItem)
                                            <li class="cart-item-detail" style="cursor: pointer;"
                                                data-id="{{ $cartItem->course->id }}">
                                                <div class="cart-dropdown-item-inner">
                                                    <img class="cart-dropdown-thumb" src="{{ str_replace('public/', '', asset($cartItem->course->course_avatar)) }}"
                                                         onerror="this.onerror=null; this.src='{{ asset('assets/img/dummy.png') }}';" alt=""/>
                                                    <div class="cart-dropdown-info">
                                                        <h6 class="cart-dropdown-item-title">{{ \Illuminate\Support\Str::limit($cartItem->course->title, 28) }}</h6>
                                                        <div class="cart-dropdown-prices">
                                                            @if(isset($cartItem->course->promotion_price) && $cartItem->course->promotion_price < $cartItem->course->origin_price)
                                                                <span class="cart-dropdown-sale-price">{{ format_price($cartItem->course->promotion_price) }}</span>
                                                                <span class="cart-dropdown-origin-price">{{ format_price($cartItem->course->origin_price) }}</span>
                                                            @else
                                                                <span class="cart-dropdown-sale-price" style="color: #1e293b;">{{ format_price($cartItem->course->origin_price) }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </div>
                                    <li class="dropdown-cart-footer">
                                        <div class="dropdown-cart-total-row">
                                            <span class="dropdown-cart-total-label">{{ __('titles.total') }}:</span>
                                            <div class="dropdown-cart-total-values">
                                                @if(0 != $totalPromotionPrice && $totalPromotionPrice < $totalOriginPrice)
                                                    <span class="dropdown-cart-total-sale">{{ format_price($totalPromotionPrice) }}</span>
                                                    <span class="dropdown-cart-total-strike">{{ format_price($totalOriginPrice) }}</span>
                                                @else
                                                    <span class="dropdown-cart-total-sale">{{ format_price($totalOriginPrice) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <a class="btn-dropdown-checkout" href="{{ route('cart_items.index') }}">
                                            <span>{{ __('titles.checkout_cart') }}</span> <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        {{-- Notification --}}
                        @php
                            $notificationsList = \App\Models\Notification::where('user_id', \Auth::user()->id)->orderBy('created_at', 'desc');
                            $notifications = $notificationsList->limit(8)->get()
                        @endphp
{{--                    {{ dd(\Session::all()) }}--}}
                        <li class="dropdown" id="notification-area">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-expanded="false"><i class="fa fa-bell"></i> {{ __('titles.u_notification') }} <span
                                        class="badge"
                                        id="notification-count"> {{ $notificationsList->where('status', 0)->count() }} </span></a>
                            <ul class="dropdown-menu dropdown-cart" id="notification-list" role="menu"
                                style="width: 350px">
                                @if($notifications->isEmpty())
                                    <li class="text-center" id="none-dock">
                                        <h4> {{ __('messages.you_are_no_notification') }}</h4>
                                    </li>
                                @else
                                    @foreach($notifications as $notification)
                                        @if($notification->type == 1 || $notification->type == 2)
                                            <li class="notification-detail" data-id="{{ $notification->id }}"
                                                data-status="{{ $notification->status }}"
                                                style="cursor: pointer; {{ $notification->status ? '' : 'background: #eff6ff' }}"
                                                data-type="{{ $notification->type }}"
                                                data-course="{{ $notification->course_id }}"
                                                data-comment="{{ $notification->comment_id }}"
                                                data-parent="{{ $notification->course_id ? \App\Models\Comment::findOrFail($notification->comment_id)->parent_comment : \App\Models\LectureComment::findOrFail($notification->comment_id)->parent_comment }}"
                                                data-lectureCommentCourseId="{{ $notification->course_id ? 'undefined' : \App\Models\Lecture::findOrFail($notification->lecture_id)->course->id }}"
                                                data-lectureCommentLectureId="{{ $notification->course_id ? 'undefined' : $notification->lecture_id }}">
                                                <span class="item">
                                                    <span class="item-left">
                                                        @if($notification->type == \App\Models\Notification::WELCOME)
                                                            <img src="/assets/img/logo-short.png"
                                                                 style="height: 45px; width: 45px"
                                                                 alt=""/>
                                                        @elseif($notification->type == \App\Models\Notification::COMMENT || $notification->type == \App\Models\Notification::LECTURE_COMMENT)
                                                            <img src="{{ $notification->type == \App\Models\Notification::COMMENT ? str_replace('public/', '', asset(\App\Models\Comment::findOrFail($notification->comment_id)->user->avatar)) : str_replace('public/', '', asset(\App\Models\LectureComment::findOrFail($notification->comment_id)->user->avatar)) }}"
                                                                 style="height: 45px; width: 45px" alt=""/>
                                                        @endif
                                                       <span class="item-info">
                                                          <span> {!! \Illuminate\Support\Str::limit($notification->content, 80) !!} </span>
                                                       <span class="diff-time" id="diff-time-{{ $notification->id }}"
                                                             data-time="{{ strtotime($notification->created_at) }}">
                                                             @php
                                                                 $diffTime = \Carbon\Carbon::parse($notification->created_at)->locale('vi')->diffForHumans();
                                                             @endphp
                                                           {{ $diffTime }}
                                                          </span>
                                                       </span>
                                                    </span>
                                                </span>
                                            </li>
                                        @elseif($notification->type == 0)
                                            <li class="notification-detail" data-id="{{ $notification->id }}"
                                                data-status="{{ $notification->status }}"
                                                style="cursor: pointer; {{ $notification->status ? '' : 'background: #eff6ff' }}">
                                                <span class="item">
                                                    <span class="item-left">
                                                            <img src="/assets/img/logo-short.png"
                                                                 style="height: 45px; width: 45px"
                                                                 alt=""/>
                                                        <span class="item-info">
                                                           <span> {!! \Illuminate\Support\Str::limit($notification->content, 80) !!} </span>
                                                        <span class="diff-time" id="diff-time-{{ $notification->id }}"
                                                              data-time="{{ strtotime($notification->created_at) }}">
                                                              @php
                                                                  $diffTime = \Carbon\Carbon::parse($notification->created_at)->locale('vi')->diffForHumans();
                                                              @endphp
                                                            {{ $diffTime }}
                                                           </span>
                                                        </span>
                                                    </span>
                                                </span>
                                            </li>
                                        @endif
                                    @endforeach
                                    <li class="divider"></li>
                                    <li class="text-center" style="padding: 8px 0;">
                                        <a href="{{ route('notifications.index') }}" style="color: #2563eb; font-weight: 600; font-size: 13.5px; text-decoration: none;">
                                            {{ __('titles.see_all_noti') }} <i class="fa fa-angle-right"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        {{--Account--}}
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre
                               style="display: inline-flex; align-items: center; gap: 8px;">
                                <img class="header-user-avatar"
                                     src="{{ \Auth::user()->avatar_url }}"
                                     onerror="this.onerror=null; this.src='{{ asset('assets/img/avatar_users/default_avatar.png') }}';"
                                     alt="{{ \Auth::user()->name }}">
                                <span class="header-user-name">{{ Auth::user()->name }}</span>
                                <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right modern-user-dropdown" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('users.show', Auth::user()->id) }}">
                                    <i class="fa fa-user" style="color: #64748b; width: 16px;"></i> {{ __('titles.my_profile') }}
                                </a>
                                @if(Auth::user()->is_admin)
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fa fa-dashboard" style="color: #2563eb; width: 16px;"></i> {{ __('titles.r2s_admin') }}
                                    </a>
                                @elseif(Auth::user()->role == 1)
                                    <a class="dropdown-item" href="{{ route('instructor.dashboard') }}">
                                        <i class="fa fa-graduation-cap" style="color: #2563eb; width: 16px;"></i> {{ __('titles.r2s_instructor') }}
                                    </a>
                                @elseif(Auth::user()->role == 2)
                                    <a class="dropdown-item" href="{{ route('courses.my_course', Auth::user()->id) }}">
                                        <i class="fa fa-book" style="color: #2563eb; width: 16px;"></i> {{ __('titles.my_course') }}
                                    </a>
                                @endif
                                <div style="margin: 6px 0; border-top: 1px solid #f1f5f9;"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   style="color: #ef4444;">
                                    <i class="fa fa-sign-out" style="width: 16px;"></i> {{ __('titles.logout') }}
                                </a>
                                {{ Form::open(['method' => 'post', 'url' => 'logout', 'id' => 'logout-form', 'style' => 'display: none;']) }}
                                {{ Form::close() }}
                            </div>
                        </li>
                    @endguest
                    {{--
                    <li class="postadd">
                       <a class="btn btn-danger btn-post" href="post-ads.html"><span class="fa fa-plus-circle"></span> Post an Ad</a>
                    </li>
                    --}}
                </ul>
            </div>
        </div>
    </nav>
    <div class="navmenu navmenu-default navmenu-fixed-left offcanvas">
        <div class="close" data-toggle="offcanvas" data-target=".navmenu">
            <i class="fa fa-close"></i>
        </div>
        <h3 class="title-menu"> {{ __('titles.all_pages') }} </h3>
        <ul class="nav navmenu-nav">
            <li><a href=""> {{ __('titles.home') }} </a></li>
            <li><a href=""> {{ __('titles.register') }} </a></li>
            <li><a href=""> {{ __('titles.login') }} </a></li>
        </ul>
    </div>
</div>
{{--<div class="tbtn wow pulse" id="menu" data-wow-iteration="infinite" data-wow-duration="500ms" data-toggle="offcanvas"--}}
{{--     data-target=".navmenu">--}}
{{--    <p><i class="fa fa-file-text-o"></i> {{ __('titles.all_pages') }} </p>--}}
{{--</div>--}}
</div>
</div>
</div>