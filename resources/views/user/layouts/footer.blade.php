<footer>
    <section class="footer-Content">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-sm-12 col-xs-12">
                    <div class="widget">
                        <h3 class="block-title">Road To School</h3>
                        <div class="textwidget">
                            <p>Nền tảng học trực tuyến giúp học viên tiếp cận các khóa học lập trình, công nghệ và kỹ năng số
                                do giảng viên giàu kinh nghiệm xây dựng. Học mọi lúc, mọi nơi, theo dõi tiến độ và nhận hỗ trợ ngay trong từng bài giảng.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="widget">
                        <h3 class="block-title">Liên kết nhanh</h3>
                        <ul class="menu">
                            <li><a href="{{ route('home') }}">{{ __('titles.home') }}</a></li>
                            <li><a href="{{ route('courses.index') }}">Tất cả khóa học</a></li>
                            @if(\Auth::user())
                                <li><a href="{{ route('courses.my_course', \Auth::user()->id) }}">Khóa học của tôi</a></li>
                                <li><a href="{{ route('cart_items.index') }}">Giỏ hàng</a></li>
                                <li><a href="{{ route('notifications.index') }}">{{ __('titles.notifications') }}</a></li>
                            @else
                                <li><a href="{{ route('login') }}">Đăng nhập</a></li>
                                <li><a href="{{ route('register') }}">Đăng ký</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="widget">
                        <h3 class="block-title">Danh mục khóa học</h3>
                        <ul class="menu">
                            @foreach(\App\Models\Category::where('parent_id', 0)->orderBy('id')->limit(6)->get() as $footerCategory)
                                <li><a href="{{ route('courses.index', ['category_id' => $footerCategory->id]) }}">{{ $footerCategory->vi_title ?: $footerCategory->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="copyright">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="site-info text-center">
                        <p> {{ __('titles.copyright_p1') }}&copy;{{ __('titles.copyright_p2') }} Pham Hoang Trang</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

@if(\Auth::user())
<a href="#" class="chat-with-admin" id="open-chat-with-admin">
    <i class="fa fa-weixin"></i><span>Trò chuyện với Quản trị viên</span>
</a>
@endif

<a href="#" class="back-to-top">
    <i class="fa fa-angle-up"></i>
</a>

<script src="//js.pusher.com/3.1/pusher.min.js"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery-min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/material.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/material-kit.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.parallax.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/wow.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/main.js') }}?v={{ time() }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/waypoints.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jasny-bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/form-validator.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/contact-form-script.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.themepunch.revolution.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.themepunch.tools.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/bootstrap-notify.js') }}"></script>
<script src="{{ asset('js/share.js') }}"></script>
