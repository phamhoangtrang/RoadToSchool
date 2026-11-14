@extends('user.layouts.default')

@section('title')
    {{ __('titles.course_list') }}
@endsection

@section('inline_styles')
    <link rel="stylesheet" href="{{ asset('assets/css/custom/courses_index.css') }}">
@endsection

@section('content')
    {{ Breadcrumbs::render('courses_index') }}
    @include('user.courses.partials.search_sidebar')
    <div class="main-container">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between flex-wrap m-b-20 p-b-15 border-bottom" style="gap: 15px;">
                        <div>
                            <h3 class="m-0" style="font-size: 20px; font-weight: 800; color: #1e293b;">
                                Danh sách <span class="text-primary">{{ $courses->total() }}</span> khóa học
                            </h3>
                            <small class="text-muted" style="font-size: 13px;">Các khóa học chất lượng cao, cập nhật mới nhất trên hệ thống Road To School</small>
                        </div>
                        <div class="product-filter" style="margin: 0; padding: 0; border: none; background: transparent;">
                            <div class="grid-list-count" style="margin: 0;">
                                <a class="grid switchToList active" id="switch-now" href="#" title="Xem dạng lưới"><i class="fa fa-th-large"></i></a>
                                <a class="list switchToGrid" href="#" title="Xem dạng danh sách"><i class="fa fa-list"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 page-content">
                    @if($courses->isEmpty())
                        <div class="text-center p-5 bg-white" style="border-radius: 12px; border: 1px dashed #cbd5e1; margin: 30px 0; padding: 50px 20px;">
                            <i class="fa fa-search fa-3x text-muted m-b-15 d-block"></i>
                            <h4 style="font-weight: 700; color: #334155;">Không tìm thấy khóa học nào phù hợp</h4>
                            <p class="text-muted" style="max-width: 480px; margin: 10px auto 20px auto; font-size: 14px;">Rất tiếc, hiện tại không có khóa học nào khớp với bộ lọc tìm kiếm của bạn. Bạn hãy thử chọn danh mục hoặc từ khóa khác nhé.</p>
                            <a href="{{ route('courses.index') }}" class="btn btn-primary" style="border-radius: 8px; font-weight: 600; padding: 10px 24px; box-shadow: 0 4px 12px rgba(37,99,235,0.25);">
                                <i class="fa fa-refresh mr-1"></i> Xem tất cả khóa học
                            </a>
                        </div>
                    @else
                    <div class="adds-wrapper">
                        @foreach ($courses as $selectedCourse)
                            <div class="item-list">
                                <div class="col-sm-2 no-padding photobox">
                                    <div class="add-image">
                                        <a href="#"><img
                                                    src="{{ str_replace('public/', '', asset($selectedCourse->course_avatar)) }}"
                                                    onerror="this.onerror=null; this.src='{{ asset('images/default_course_avatar/web_development_1.jpeg') }}';"
                                                    alt="{{ $selectedCourse->title }}"></a>
                                        <span class="photo-count"><i class="fa fa-eye"></i> {{ $selectedCourse->seller }} </span>
                                    </div>
                                </div>
                                <div class="col-sm-7 add-desc-box">
                                    <div class="add-details">
                                        <h5 class="add-title"><a
                                                    href="{{ route('courses.show', $selectedCourse->id) }}"> {{ $selectedCourse->title }} </a>
                                        </h5>
                                        <div class="info">
                                            {{ __('titles.category') . ': ' . ($selectedCourse->category ? ($selectedCourse->category->vi_title ?: $selectedCourse->category->title) : '') }}
                                            <br>
                                            {{ __('titles.rate') }} &emsp;
                                            @for ($temp = 0; $temp < $selectedCourse->course_rate; $temp++)
                                                <span class="add-type"><i class="icon fa fa-star"></i></span>
                                            @endfor
                                            <br>
                                            <span class="category"> {{ __('titles.level') . $selectedCourse->level }}  </span>
                                            -
                                            <span class="item-location"><i class="fa fa-map-book"></i> {{ $selectedCourse->lecture_numbers . ' ' . __('titles.lectures') . ' ' . __('titles.in') . ' ' . $selectedCourse->duration . ' ' . __('titles.hours') }} </span>
                                            <h3 class="price">@if(isset($selectedCourse->promotion_price) && $selectedCourse->promotion_price < $selectedCourse->origin_price)
                                                    <b
                                                            style="color:red">{{ format_price($selectedCourse->promotion_price) }}
                                                    </b><strike> {{ format_price($selectedCourse->origin_price) }}</strike>@else {{ format_price($selectedCourse->origin_price) }}
                                                    @endif</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 text-right price-box">
                                    <h2 class="item-price"> {{ $selectedCourse->teacher_name }} </h2>
                                    <a class="btn btn-common btn-sm add-to-cart" href=""
                                       data-id="{{ $selectedCourse->id }}"><i
                                                class=" fa fa-cart-plus"></i><span> {{ __('titles.add_to_cart') }} </span></a>
                                    <a class="btn btn-sm btn-danger add-to-wishlist" href=""
                                       data-id="{{ $selectedCourse->id }}" title="{{ __('titles.add_to_wishlist') }}"><i
                                                class="fa fa-heart"></i></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="pagination-bar">
                        <ul class="pagination">
                            <li> {{ $courses->links() }} </li>
                        </ul>
                    </div>
                    @endif
                    {{--@if (Auth::user()->role == 1)--}}
                    {{--<div class="post-promo text-center">--}}
                    {{--<h2> {{ __('titles.create_course_p1') }} </h2>--}}
                    {{--<h5> {{ __('titles.create_course_p2') }} </h5>--}}
                    {{--<a href="post-ads.html"--}}
                    {{--class="btn btn-post btn-danger">  {{ __('titles.create_course_p3') }} </a>--}}
                    {{--</div>--}}
                    {{--@endif--}}
                </div>
            </div>
        </div>
    </div>
@endsection

@include('user.courses.partials.javascript_common')
@section('inline_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#switch-now').click();

            $('.add-to-cart').on('click', function (event) {
                event.preventDefault();
                var courseId = $(this).data('id');
                addCourseToCart(courseId, '{{ __('messages.add_to_cart_success') }}', '{{ __('messages.in_cart_already_failed') }}', '{{ __('messages.my_course_already_failed') }}', '{{ __('messages.my_bill_already_failed') }}', null, 'add-to-cart');
            });
            $('.add-to-wishlist').on('click', function (event) {
                event.preventDefault();
                var courseId = $(this).data('id');
                addCourseToCart(courseId, '{{ __('messages.add_to_wishlist_success') }}', '{{ __('messages.in_cart_already_failed') }}', '{{ __('messages.my_course_already_failed') }}', '{{ __('messages.my_bill_already_failed') }}', null, 'add-to-wishlist');
            });
        });
    </script>
@endsection
