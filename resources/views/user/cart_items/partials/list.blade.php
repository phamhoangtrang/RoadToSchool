<div class="col-sm-9 page-content" id="cart">
    <div class="inner-box">
        <h2 class="title-2"><i class="fa fa-shopping-cart"></i>&ensp;&ensp; {{ __('titles.my_cart') }} <span
                    class="badge"></span></h2>
        @if($courseRelationsInCart->isEmpty())
            <image src="/assets/img/empty_cart.png" alt="Empty cart"
                   style="display: block;margin-left: auto;margin-right: auto;width: 50%;"></image><br>
            <h4 class="text-center"> {{ __('messages.your_cart_is_empty_keep_shopping') }} </h4><br>
            <a class="btn btn-common btn-search btn-block" href="{{ route('home') }}"
               style="width: max-content;margin: 0 auto" ;><strong>{{ __('titles.keep_shopping') }}</strong></a>
        @else
            <div class="table-responsive">
                <div class="table-action">
                    <div class="checkbox">
                        <label for="checkAllInCart" style="font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            <input id="checkAllInCart" type="checkbox" checked style="margin: 0; position: static;">
                            <span>Chọn tất cả</span>
                            <button type="button" id="btn-delete-selected-cart" class="btn btn-xs btn-danger" style="margin-left: 10px; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa fa-trash"></i> Xóa mục đã chọn
                            </button>
                        </label>
                    </div>
                    <div class="table-search pull-right col-xs-7">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="col-xs-5 control-label text-right" style="line-height: 34px; margin-bottom: 0;">Tìm kiếm</label>
                            <div class="col-xs-7 searchpan">
                                <input class="form-control" id="filter" type="text" placeholder="Tìm kiếm...">
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-bordered add-manage-table" id="in-cart-item-table">
                    <thead>
                    <tr>
                        <th data-type="numeric"></th>
                        <th>{{ __('titles.image') }}</th>
                        <th>{{ __('titles.course_detail') }}</th>
                        <th>{{ __('titles.price') }}</th>
                        <th>{{ __('titles.option') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($courseRelationsInCart as $courseRelation)
                        @if($courseRelation->cart_item_type === \App\Models\CartItem::IN_CART_TYPE)
                            <tr id="cart-item-{{$courseRelation->id}}">
                                <td class="add-img-selector" id="checkbox-{{$courseRelation->id}}">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="cart-item-checkbox" value="{{ $courseRelation->id }}"
                                                   data-origin-price="{{ (float)$courseRelation->course->origin_price }}"
                                                   data-promotion-price="{{ (float)((!empty($courseRelation->course->promotion_price) && $courseRelation->course->promotion_price < $courseRelation->course->origin_price) ? $courseRelation->course->promotion_price : $courseRelation->course->origin_price) }}"
                                                   checked>
                                        </label>
                                    </div>
                                </td>
                                <td class="add-img-td">
                                    <a href="{{ route('courses.show', $courseRelation->course->id) }}">
                                        <img class="img-responsive"
                                             src="{{ str_replace('public/', '', asset($courseRelation->course->course_avatar)) }}"
                                             onerror="this.onerror=null; this.src='{{ asset('images/default_course_avatar/web_development_1.jpeg') }}';"
                                             style="border-radius: 4px; object-fit: cover;"
                                             alt="{{ $courseRelation->course->title }}">
                                    </a>
                                </td>
                                <td class="ads-details-td">
                                    <h4>
                                        <a href="{{ route('courses.show', $courseRelation->course->id) }}">{{ $courseRelation->course->title }}</a>
                                    </h4>
                                    <p><strong> {{ __('titles.instructor') }} </strong>:
                                        <a href="{{ route('users.show', $courseRelation->course->user->id) }}">{{ $courseRelation->course->user->name }}</a>
                                    </p>
                                    <p>
                                        <strong> {{ __('titles.seller') }} </strong>: {{ $courseRelation->course->seller }}
                                        <strong>{{ __('titles.schedule') }}
                                            : </strong> {{ $courseRelation->course->lecture_numbers . ' ' . __('titles.lectures') . ' ' . __('titles.in') . ' ' . $courseRelation->course->duration . ' ' . __('titles.minutes') }}
                                    </p>
                                </td>
                                <td class="price-td" style="vertical-align: middle; white-space: nowrap;">
                                    @if(isset($courseRelation->course->promotion_price) && $courseRelation->course->promotion_price < $courseRelation->course->origin_price)
                                        <div style="font-size: 16px; font-weight: 700; color: #dc2626; line-height: 1.3;">
                                            {{ format_price($courseRelation->course->promotion_price) }}
                                        </div>
                                        <div style="font-size: 13px; color: #94a3b8; text-decoration: line-through; margin-top: 2px;">
                                            {{ format_price($courseRelation->course->origin_price) }}
                                        </div>
                                    @else
                                        <div style="font-size: 16px; font-weight: 700; color: #334155;">
                                            {{ format_price($courseRelation->course->origin_price) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="action-td">
                                    <p><a class="btn btn-danger btn-xs remove" data-id="{{ $courseRelation->id }}"> <i
                                                    class="fa fa-trash"></i> {{ __('titles.remove') }} </a></p>
                                    <p><a class="btn btn-info btn-xs save-for-later" data-id="{{ $courseRelation->id }}"
                                          id="save-for-later-{{$courseRelation->id}}"> <i
                                                    class="fa fa-share-square-o"></i> {{ __('titles.save_for_later') }}
                                        </a>
                                    </p>
                                    <p><a class="btn btn-xs move-to-wishlist" style="background-color:#3498db"
                                          data-id="{{ $courseRelation->id }}"
                                          id="move-to-wishlist-{{$courseRelation->id}}"> <i
                                                    class="fa fa-chevron-circle-down"></i> {{ __('titles.move_to_wishlist') }}
                                        </a></p>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <div class="cart-summary-box" style="margin-top: 25px; padding: 22px 26px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div class="cart-summary-price" style="display: flex; flex-direction: column; gap: 6px;">
                        <span style="font-size: 14px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('titles.total') }}:</span>
                        <div id="cart-summary-price-container" style="display: flex; align-items: baseline; gap: 12px; flex-wrap: wrap;">
                            <span id="cart-total-pay" style="font-size: 30px; font-weight: 800; color: {{ ($totalOriginPriceInCart !== $totalPromotionPriceInCart) ? '#dc2626' : '#1e293b' }}; line-height: 1; white-space: nowrap;">
                                {{ format_price($totalPromotionPriceInCart) }}
                            </span>
                            <span id="cart-total-origin" style="font-size: 16px; color: #94a3b8; text-decoration: line-through; white-space: nowrap; {{ ($totalOriginPriceInCart !== $totalPromotionPriceInCart) ? '' : 'display: none;' }}">
                                {{ format_price($totalOriginPriceInCart) }}
                            </span>
                            <span id="cart-total-discount" style="background: #fee2e2; color: #dc2626; font-size: 13px; font-weight: 700; padding: 3px 10px; border-radius: 4px; white-space: nowrap; {{ ($totalOriginPriceInCart !== $totalPromotionPriceInCart && $totalOriginPriceInCart !== 0) ? '' : 'display: none;' }}">
                                @if($totalOriginPriceInCart !== 0 && $totalOriginPriceInCart !== $totalPromotionPriceInCart)
                                    Tiết kiệm {{ number_format((float)(1 - $totalPromotionPriceInCart/$totalOriginPriceInCart)*100, 1, '.', '') }}%
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="cart-summary-btn">
                        <a href="{{ route('cart_items.checkout.get') }}" id="btn-cart-checkout" class="btn btn-common" style="padding: 13px 36px; font-size: 16px; font-weight: 700; border-radius: 6px; background-color: #3498db; color: #fff; display: inline-flex; align-items: center; gap: 10px; text-transform: uppercase; box-shadow: 0 4px 14px rgba(52, 152, 219, 0.35); text-decoration: none;">
                            <i class="fa fa-credit-card"></i> {{ __('titles.checkout') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="col-sm-9 page-content" id="saved-for-later">
    <div class="inner-box">
        <h2 class="title-2"><i class="fa fa-shopping-cart"></i>&ensp;&ensp; {{ __('titles.saved_for_later') }} <span
                    class="badge"></span></h2>
        @if($courseRelationsInLater->isEmpty())
            <h4 class="text-center"> {{ __('messages.have_not_saved_any_courses_for_later') }} </h4><br>
        @else
            <div class="table-responsive">
                <div class="table-action">
                    <div class="checkbox">
                        <label for="checkAllInLater" style="font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            <input id="checkAllInLater" type="checkbox" style="margin: 0; position: static;">
                            <span>Chọn tất cả</span>
                            <button type="button" id="btn-delete-selected-later" class="btn btn-xs btn-danger" style="margin-left: 10px; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa fa-trash"></i> Xóa mục đã chọn
                            </button>
                        </label>
                    </div>
                    <div class="table-search pull-right col-xs-7">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="col-xs-5 control-label text-right" style="line-height: 34px; margin-bottom: 0;">Tìm kiếm</label>
                            <div class="col-xs-7 searchpan">
                                <input class="form-control" id="filter" type="text" placeholder="Tìm kiếm...">
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-bordered add-manage-table" id="save-for-later-item-table">
                    <thead>
                    <tr>
                        <th data-type="numeric"></th>
                        <th>{{ __('titles.image') }}</th>
                        <th>{{ __('titles.course_detail') }}</th>
                        <th>{{ __('titles.price') }}</th>
                        <th>{{ __('titles.option') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($courseRelationsInLater as $courseRelation)
                        @if($courseRelation->cart_item_type === \App\Models\CartItem::IN_LATER_TYPE)
                            <tr id="cart-item-{{$courseRelation->id}}">
                                <td class="add-img-selector">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="later-item-checkbox" value="{{ $courseRelation->id }}">
                                        </label>
                                    </div>
                                </td>
                                <td class="add-img-td">
                                    <a href="{{ route('courses.show', $courseRelation->course->id) }}">
                                        <img class="img-responsive"
                                             src="{{ str_replace('public/', '', asset($courseRelation->course->course_avatar)) }}"
                                             onerror="this.onerror=null; this.src='{{ asset('images/default_course_avatar/web_development_1.jpeg') }}';"
                                             style="border-radius: 4px; object-fit: cover;"
                                             alt="{{ $courseRelation->course->title }}">
                                    </a>
                                </td>
                                <td class="ads-details-td">
                                    <h4>
                                        <a href="{{ route('courses.show', $courseRelation->course->id) }}">{{ $courseRelation->course->title }}</a>
                                    </h4>
                                    <p><strong> {{ __('titles.instructor') }} </strong>:
                                        <a href="{{ route('users.show', $courseRelation->course->user->id) }}">{{ $courseRelation->course->user->name }}</a>
                                    </p>
                                    <p><strong> {{ __('titles.views') }} </strong>: {{ $courseRelation->course->views }}
                                        <strong>{{ __('titles.schedule') }}
                                            : </strong> {{ $courseRelation->course->lecture_numbers . ' ' . __('titles.lectures') . ' ' . __('titles.in') . ' ' . $courseRelation->course->duration . __('titles.hours') }}
                                    </p>
                                </td>
                                <td class="price-td">
                                    <strong>
                                        @if(isset($courseRelation->course->promotion_price) && $courseRelation->course->promotion_price < $courseRelation->course->origin_price)
                                            <p><b style="color:red">{{ format_price($courseRelation->course->promotion_price) }}
                                                    </b><br><strike> {{ format_price($courseRelation->course->origin_price) }}</strike></p>
                                        @else
                                            <b>{{ format_price($courseRelation->course->origin_price) }}</b>
                                        @endif
                                    </strong>
                                </td>
                                <td class="action-td" id="save-for-later-option">
                                    <p><a class="btn btn-danger btn-xs remove" data-id="{{ $courseRelation->id }}"> <i
                                                    class=" fa fa-trash"></i> {{ __('titles.remove') }} </a></p>
                                    <p><a class="btn btn-xs move-to-cart" style="background-color:#3498db"
                                          data-id="{{ $courseRelation->id }}"
                                          id="move-to-cart-{{ $courseRelation->id }}"> <i
                                                    class="fa fa-chevron-circle-down"></i> {{ __('titles.move_to_cart') }}
                                        </a></p>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="col-sm-9 page-content" id="wishlist">
    <div class="inner-box">
        <h2 class="title-2"><i class="fa fa-shopping-cart"></i>&ensp;&ensp; {{ __('titles.wishlist') }} <span
                    class="badge"></span></h2>
        @if($courseRelationsInWishlist->isEmpty())
            <h4 class="text-center"> {{ __('messages.haven_not_added_any_courses_to_your_wishlist') }} </h4><br>
        @else
            <div class="table-responsive">
                <div class="table-action">
                    <div class="checkbox">
                        <label for="checkAllInWishlist" style="font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                            <input id="checkAllInWishlist" type="checkbox" style="margin: 0; position: static;">
                            <span>Chọn tất cả</span>
                            <button type="button" id="btn-delete-selected-wishlist" class="btn btn-xs btn-danger" style="margin-left: 10px; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa fa-trash"></i> Xóa mục đã chọn
                            </button>
                        </label>
                    </div>
                    <div class="table-search pull-right col-xs-7">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="col-xs-5 control-label text-right" style="line-height: 34px; margin-bottom: 0;">Tìm kiếm</label>
                            <div class="col-xs-7 searchpan">
                                <input class="form-control" id="filter" type="text" placeholder="Tìm kiếm...">
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-bordered add-manage-table" id="move-to-wishlist-item-table">
                    <thead>
                    <tr>
                        <th data-type="numeric"></th>
                        <th>{{ __('titles.image') }}</th>
                        <th>{{ __('titles.course_detail') }}</th>
                        <th>{{ __('titles.price') }}</th>
                        <th>{{ __('titles.option') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($courseRelationsInWishlist as $courseRelation)
                        @if($courseRelation->cart_item_type === \App\Models\CartItem::IN_WISHLIST_TYPE)
                            <tr id="cart-item-{{$courseRelation->id}}">
                                <td class="add-img-selector">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="wishlist-item-checkbox" value="{{ $courseRelation->id }}">
                                        </label>
                                    </div>
                                </td>
                                <td class="add-img-td">
                                    <a href="{{ route('courses.show', $courseRelation->course->id) }}">
                                        <img class="img-responsive"
                                             src="{{ str_replace('public/', '', asset($courseRelation->course->course_avatar)) }}"
                                             onerror="this.onerror=null; this.src='{{ asset('images/default_course_avatar/web_development_1.jpeg') }}';"
                                             style="border-radius: 4px; object-fit: cover;"
                                             alt="{{ $courseRelation->course->title }}">
                                    </a>
                                </td>
                                <td class="ads-details-td">
                                    <h4>
                                        <a href="{{ route('courses.show', $courseRelation->course->id) }}">{{ $courseRelation->course->title }}</a>
                                    </h4>
                                    <p><strong> {{ __('titles.instructor') }} </strong>:
                                        <a href="{{ route('users.show', $courseRelation->course->user->id) }}">{{ $courseRelation->course->user->name }}</a>
                                    </p>
                                    <p><strong> {{ __('titles.views') }} </strong>: {{ $courseRelation->course->views }}
                                        <strong>{{ __('titles.schedule') }}
                                            : </strong> {{ $courseRelation->course->lecture_numbers . ' ' . __('titles.lectures') . ' ' . __('titles.in') . ' ' . $courseRelation->course->duration . __('titles.hours') }}
                                    </p>
                                </td>
                                <td class="price-td">
                                    <strong>
                                        @if(isset($courseRelation->course->promotion_price) && $courseRelation->course->promotion_price < $courseRelation->course->origin_price)
                                            <p><b style="color:red">{{ format_price($courseRelation->course->promotion_price) }}
                                                    </b><br><strike> {{ format_price($courseRelation->course->origin_price) }}</strike></p>
                                        @else
                                            <b>{{ format_price($courseRelation->course->origin_price) }}</b>
                                        @endif
                                    </strong>
                                </td>
                                <td class="action-td">
                                    <p><a class="btn btn-danger btn-xs remove" data-id="{{ $courseRelation->id }}"> <i
                                                    class=" fa fa-trash"></i> {{ __('titles.remove') }} </a></p>
                                    <p><a class="btn btn-xs move-to-cart" style="background-color:#3498db"
                                          data-id="{{ $courseRelation->id }}"
                                          id="move-to-cart-{{ $courseRelation->id }}"> <i
                                                    class="fa fa-chevron-circle-down"></i> {{ __('titles.move_to_cart') }}
                                        </a></p>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>