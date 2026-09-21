@extends('user.layouts.default')

@section('title')
    {{ __('titles.road_to_school_home_page') }}
@endsection

@section('content')
    <section id="intro" class="section-intro">
        <div class="overlay">
            <div class="container">
                <div class="main-text">
                    <h1 class="intro-title"> {{ __('titles.slogan_p1') }} <span
                                style="color: #3498db"> {{ __('titles.slogan_p2') }} </span></h1>
                    <p class="sub-title"> {{ __('titles.slogan_p3') }} </p>

                    <div class="row search-bar">
                        <div class="advanced-search">
                            <form class="search-form" method="get" action="{{ route('courses.index') }}">
                                <div class="col-md-3 col-sm-6 search-col">
                                    <input class="form-control keyword" name="keyword" value=""
                                           placeholder="{{ __('titles.keyword') }}" type="text">
                                    <i class="fa fa-search"></i>
                                </div>
                                <div class="col-md-3 col-sm-6 search-col">
                                    <div class="input-group-addon search-category-container">
                                        <label class="styled-select">
                                            <select class="dropdown-product selectpicker" name="filter_option">
                                                <option value="0"> {{ __('titles.select_filter_option') }} </option>
                                                @foreach (App\Models\Course::$filter_options as $key => $value)
                                                    <option value="{{ $key }}">
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 search-col">
                                    <div class="input-group-addon search-category-container">
                                        <label class="styled-select rate-select">
                                            <select class="dropdown-product selectpicker" name="course_rate">
                                                <option value="0"> {{ __('titles.select_rate') }} </option>
                                                @for ($temp=1; $temp<=4; $temp++)
                                                    <option value="{{ $temp }}">
                                                        @for ($tempStar=0; $tempStar<$temp; $tempStar++)
                                                            &#x2606;@endfor {{ __('titles.and_up') }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 search-col">
                                    <input class="btn btn-common btn-search btn-block" type="submit"
                                           value="{{ __('titles.submit') }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="wrapper">
        <section id="categories-homepage">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="section-title"> {{ __('titles.browse_course_from') . ' ' . App\Models\Category::where('parent_id', '<>', 0)->count() . ' ' . __('titles.l_categories') }}</h3>
                    </div>
                    @foreach(\App\Models\Category::where('parent_id', 0)->get() as $category)
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="category-box border-1 wow fadeInUpQuick" data-wow-delay="0.3s">
                                <div class="icon">
                                    <i class="{{$category->css_classes}}"></i>
                                </div>
                                <div class="category-header">
                                    <h4> {{ $category->vi_title ?: $category->title }} </h4>
                                </div>
                                <div class="category-content">
                                    <ul>
                                        @foreach(\App\Models\Category::where('parent_id', $category->id)->limit(6)->get() as $subCategory)
                                            <li>
                                                <a href="{{ route('courses.index', ['sub_category_id' => $subCategory->id]) }}">{{ $subCategory->vi_title ?: $subCategory->title }}</a>
                                                <span class="category-counter"> {{ \App\Models\Course::where('is_accepted', 1)->where('category_id', $subCategory->id)->count() }} </span>
                                            </li>
                                        @endforeach
                                        <li>
                                            <a href="{{ route('courses.index', ['category_id' => $category->id]) }}">Xem tất cả danh mục con →</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="featured-lis">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 wow fadeIn" data-wow-delay="0.5s">
                        <h3 class="section-title">{{ __('titles.recommended_courses') }}</h3>
                        <div id="new-products" class="owl-carousel">
                            @foreach($recommmendCourseList as $trendCourse)
                                <div class="item">
                                    <div class="product-item">
                                        <div class="carousel-thumb">
                                            <img src="{{ str_replace('public/', '', asset($trendCourse->course_avatar)) }}"
                                                 style="height:142px"
                                                 alt="">
                                            <div class="overlay">
                                                <a href="{{ route('courses.show', $trendCourse->id) }}"><i
                                                            class="fa fa-info-circle"></i></a>
                                            </div>
                                        </div>
                                        <a href="{{ route('courses.show', $trendCourse->id) }}"
                                           class="item-name"> {{ $trendCourse->title }} </a>
                                        <span class="price">
                                            @if($trendCourse->promotion_price && $trendCourse->promotion_price < $trendCourse->origin_price)
                                                <b style="color:red">{{ format_price($trendCourse->promotion_price) }} </b>
                                                <strike>{{ format_price($trendCourse->origin_price) }}</strike>
                                            @else
                                                <b>{{ format_price($trendCourse->origin_price) }}</b>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="featured-lis">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 wow fadeIn" data-wow-delay="0.5s">
                        <h3 class="section-title">{{ __('titles.most_seller_courses') }}</h3>
                        <div id="best-products" class="owl-carousel">
                            @foreach(\App\Models\Course::orderBy('seller', 'desc')->where('is_accepted', 1)->limit(20)->get() as $trendCourse)
                                <div class="item">
                                    <div class="product-item">
                                        <div class="carousel-thumb">
                                            <img src="{{ str_replace('public/', '', asset($trendCourse->course_avatar)) }}"
                                                 style="height:142px"
                                                 alt="">
                                            <div class="overlay">
                                                <a href="{{ route('courses.show', $trendCourse->id) }}"><i
                                                            class="fa fa-info-circle"></i></a>
                                            </div>
                                        </div>
                                        <a href="{{ route('courses.show', $trendCourse->id) }}"
                                           class="item-name"> {{ $trendCourse->title }} </a>
                                        <span class="price">
                                            @if($trendCourse->promotion_price && $trendCourse->promotion_price < $trendCourse->origin_price)
                                                <b style="color:red">{{ format_price($trendCourse->promotion_price) }} </b>
                                                <strike>{{ format_price($trendCourse->origin_price) }}</strike>
                                            @else
                                                <b>{{ format_price($trendCourse->origin_price) }}</b>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="features">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="features-box wow fadeInDownQuick" data-wow-delay="0.3s">
                            <div class="features-icon">
                                <i class="fa fa-book">
                                </i>
                            </div>
                            <div class="features-content">
                                <h4>
                                    Tài liệu chi tiết & Đầy đủ
                                </h4>
                                <p>
                                    Cung cấp trọn bộ giáo trình, slide bài giảng, mã nguồn mẫu và bài tập thực hành theo chuẩn dự án thực tế.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="features-box wow fadeInDownQuick" data-wow-delay="0.6s">
                            <div class="features-icon">
                                <i class="fa fa-paper-plane"></i>
                            </div>
                            <div class="features-content">
                                <h4>
                                    Giao diện hiện đại & Trực quan
                                </h4>
                                <p>
                                    Trải nghiệm học tập mượt mà với giao diện trực quan, dễ thao tác và tối ưu hóa tối đa cho người học.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="features-box wow fadeInDownQuick" data-wow-delay="0.9s">
                            <div class="features-icon">
                                <i class="fa fa-map"></i>
                            </div>
                            <div class="features-content">
                                <h4>
                                    Lộ trình học tập rõ ràng
                                </h4>
                                <p>
                                    Lộ trình bài bản từ cơ bản đến nâng cao, giúp bạn nắm vững kiến thức và tự tin áp dụng vào công việc.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="features-box wow fadeInDownQuick" data-wow-delay="1.2s">
                            <div class="features-icon">
                                <i class="fa fa-cogs"></i>
                            </div>
                            <div class="features-content">
                                <h4>
                                    Học tập chủ động & Linh hoạt
                                </h4>
                                <p>
                                    Chủ động sắp xếp thời gian và tốc độ học tập phù hợp với lịch trình bận rộn của riêng bạn.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="features-box wow fadeInDownQuick" data-wow-delay="1.5s">
                            <div class="features-icon">
                                <i class="fa fa-hourglass"></i>
                            </div>
                            <div class="features-content">
                                <h4>
                                    Tương thích đa nền tảng
                                </h4>
                                <p>
                                    Truy cập và theo dõi bài học mọi lúc mọi nơi trên điện thoại, máy tính bảng và máy tính bàn.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="features-box wow fadeInDownQuick" data-wow-delay="1.8s">
                            <div class="features-icon">
                                <i class="fa fa-hashtag"></i>
                            </div>
                            <div class="features-content">
                                <h4>
                                    Thân thiện & Dễ sử dụng
                                </h4>
                                <p>
                                    Hệ thống trắc nghiệm, đánh giá kết quả và phản hồi nhanh chóng từ giảng viên chỉ với vài cú nhấp chuột.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="features-box wow fadeInDownQuick" data-wow-delay="2.1s">
                            <div class="features-icon">
                                <i class="fa fa-newspaper-o"></i>
                            </div>
                            <div class="features-content">
                                <h4>
                                    Kho khóa học đa dạng
                                </h4>
                                <p>
                                    Hàng trăm khóa học chất lượng cao trải rộng khắp các lĩnh vực CNTT, Kinh doanh, Thiết kế và Đời sống.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="features-box wow fadeInDownQuick" data-wow-delay="2.4s">
                            <div class="features-icon">
                                <i class="fa fa-leaf"></i>
                            </div>
                            <div class="features-content">
                                <h4>
                                    Giảng viên chất lượng cao
                                </h4>
                                <p>
                                    Được hướng dẫn bởi các chuyên gia đầu ngành có nhiều năm kinh nghiệm giảng dạy và thực chiến.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="features-box wow fadeInDownQuick" data-wow-delay="2.7s">
                            <div class="features-icon">
                                <i class="fa fa-google"></i>
                            </div>
                            <div class="features-content">
                                <h4>
                                    Hỗ trợ & Đồng hành 24/7
                                </h4>
                                <p>
                                    Cộng đồng học viên sôi nổi cùng đội ngũ trợ giảng sẵn sàng giải đáp thắc mắc của bạn bất kỳ lúc nào.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="location">
            <div class="container">
                <div class="row localtion-list">
                    <div class="col-md-6 col-sm-6 col-xs-12 wow fadeInLeft" data-wow-delay="0.5s">
                        <h3 class="title-2"><i class="fa fa-envelope"></i> Đăng ký nhận thông báo</h3>
                        <form id="subscribe" action="#">
                            <p>Tham gia cùng hơn 10.000+ học viên để cập nhật những khóa học mới nhất, mã giảm giá và tài nguyên miễn phí hữu ích!</p>
                            <div class="subscribe">
                                <input class="form-control" name="EMAIL" placeholder="Nhập địa chỉ email của bạn..." required=""
                                       type="email">
                                <button class="btn btn-common" type="submit">Đăng ký</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12 wow fadeInRight" data-wow-delay="1s">
                        <h3 class="title-2"><i class="fa fa-search"></i> Tìm kiếm phổ biến</h3>
                        <ul class="cat-list col-sm-4">
                            <li><a href="{{ route('courses.index', ['keyword' => 'Lập trình Web']) }}">Lập trình Web</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Python']) }}">Python cơ bản</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Laravel']) }}">Laravel Framework</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'ReactJS']) }}">ReactJS & Redux</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Node.js']) }}">Node.js Backend</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Cơ sở dữ liệu']) }}">Cơ sở dữ liệu MySQL</a></li>
                        </ul>
                        <ul class="cat-list col-sm-4">
                            <li><a href="{{ route('courses.index', ['keyword' => 'Khởi nghiệp']) }}">Khởi nghiệp kinh doanh</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Tài chính']) }}">Đầu tư tài chính</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Marketing']) }}">Digital Marketing</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'SEO']) }}">SEO Website</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Giao tiếp']) }}">Kỹ năng giao tiếp</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Quản trị']) }}">Quản trị doanh nghiệp</a></li>
                        </ul>
                        <ul class="cat-list col-sm-4">
                            <li><a href="{{ route('courses.index', ['keyword' => 'Thiết kế Web']) }}">Thiết kế UI/UX</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Photoshop']) }}">Photoshop & Illustrator</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Quay dựng']) }}">Quay dựng Video</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Nhiếp ảnh']) }}">Nhiếp ảnh chuyên nghiệp</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Gym']) }}">Thể hình & Sức khỏe</a></li>
                            <li><a href="{{ route('courses.index', ['keyword' => 'Dinh dưỡng']) }}">Dinh dưỡng khoa học</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <section id="counter">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="counting wow fadeInDownQuick" data-wow-delay=".5s">
                        <div class="icon">
                        <span>
                            <i class="lnr lnr-graduation-hat"></i>
                        </span>
                        </div>
                        <div class="desc">
                            <h3 class="counter">23453</h3>
                            <p>Học viên</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="counting wow fadeInDownQuick" data-wow-delay="1s">
                        <div class="icon">
                        <span>
                            <i class="lnr lnr-map"></i>
                        </span>
                        </div>
                        <div class="desc">
                            <h3 class="counter">{{ \App\Models\Course::where('is_accepted', 1)->count() > 0 ? \App\Models\Course::where('is_accepted', 1)->count() : 4053 }}</h3>
                            <p>Khóa học</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="counting wow fadeInDownQuick" data-wow-delay="1.5s">
                        <div class="icon">
                        <span>
                            <i class="lnr lnr-user"></i>
                        </span>
                        </div>
                        <div class="desc">
                            <h3 class="counter">350</h3>
                            <p>Giảng viên</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="counting wow fadeInDownQuick" data-wow-delay="2s">
                        <div class="icon">
                        <span>
                            <i class="lnr lnr-film-play"></i>
                        </span>
                        </div>
                        <div class="desc">
                            <h3 class="counter">12090</h3>
                            <p>Phút bài giảng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
