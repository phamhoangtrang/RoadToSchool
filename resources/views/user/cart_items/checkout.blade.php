@extends('user.layouts.default')

@section('title')
    {{ __('titles.checkout') }}
@endsection

@section('inline_styles')
    <style>
        /* Modern Clean Checkout Page */
        .checkout-section {
            padding: 30px 0 60px;
        }

        .checkout-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px 26px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            margin-bottom: 24px;
        }

        .checkout-box-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 14px;
            margin: 0 0 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkout-box-title i {
            color: #2563eb;
            font-size: 18px;
        }

        /* Course List in Checkout */
        .checkout-course-list {
            list-style: none;
            padding: 0;
            margin: 0 0 20px;
        }

        .checkout-course-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .checkout-course-item:last-child {
            border-bottom: none;
        }

        .checkout-course-thumb {
            width: 82px;
            height: 52px;
            border-radius: 6px;
            overflow: hidden;
            background: #0f172a;
            flex-shrink: 0;
        }

        .checkout-course-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .checkout-course-info {
            flex: 1;
            min-width: 0;
            padding-right: 10px;
        }

        .checkout-course-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 4px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .checkout-course-author {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }

        .checkout-course-price {
            text-align: right;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .price-sale {
            font-size: 15px;
            font-weight: 700;
            color: #dc2626;
            display: block;
            line-height: 1.2;
        }

        .price-strike {
            font-size: 12px;
            color: #94a3b8;
            text-decoration: line-through;
            display: block;
            margin-top: 2px;
        }

        .price-single {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            display: block;
            line-height: 1.2;
        }

        .checkout-notification-check {
            margin: 15px 0 18px;
        }

        .checkout-notification-check label {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            font-size: 13px !important;
            color: #64748b !important;
            cursor: pointer !important;
            margin: 0 !important;
            padding: 0 !important;
            font-weight: 500 !important;
        }

        .checkout-notification-check input[type="checkbox"] {
            width: 17px !important;
            height: 17px !important;
            margin: 0 !important;
            position: static !important;
            cursor: pointer !important;
            flex-shrink: 0 !important;
        }

        /* Summary Total Card */
        .checkout-total-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 18px;
            margin-top: 15px;
        }

        .total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13.5px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .total-row.final {
            border-top: 1px dashed #cbd5e1;
            padding-top: 10px;
            margin-top: 10px;
            margin-bottom: 0;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .total-row.final .final-price {
            font-size: 22px;
            font-weight: 800;
            color: #dc2626;
        }

        /* Form Controls */
        .checkout-form-group {
            margin-bottom: 16px;
        }

        .checkout-form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
            display: block;
        }

        .checkout-input {
            width: 100%;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 8px 14px;
            font-size: 13.5px;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }

        .checkout-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .checkout-submit-btn {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            padding: 13px 24px;
            border-radius: 8px;
            border: none;
            width: 100%;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: uppercase;
        }

        .checkout-submit-btn:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.45);
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: #15803d;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 9px 12px;
            margin-bottom: 16px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        {{ Breadcrumbs::render('checkout') }}
    </div>

    <section id="content" class="checkout-section">
        <div class="container">
            <div class="row">
                <form action="{{ route('cart_items.checkout.post') }}" method="POST">
                    @csrf
                    <!-- Left: Selected Courses -->
                    <div class="col-md-7 col-sm-12">
                        <div class="checkout-box">
                            <h2 class="checkout-box-title">
                                <i class="fa fa-shopping-bag"></i> Khóa học đã chọn ({{ count($courseRelations) }})
                            </h2>
                            <ul class="checkout-course-list">
                                @php
                                    $calcTotalOrigin = 0;
                                    $calcTotalFinal = 0;
                                @endphp
                                @foreach($courseRelations as $courseRelation)
                                    @php
                                        $originP = (float)$courseRelation->course->origin_price;
                                        $promoP = (!empty($courseRelation->course->promotion_price) && $courseRelation->course->promotion_price < $originP) ? (float)$courseRelation->course->promotion_price : $originP;
                                        $calcTotalOrigin += $originP;
                                        $calcTotalFinal += $promoP;
                                    @endphp
                                    <li class="checkout-course-item">
                                        <div class="checkout-course-thumb">
                                            <img src="{{ str_replace('public/', '', asset($courseRelation->course->course_avatar)) }}"
                                                 onerror="this.src='{{ asset('images/default_course_avatar/web_development_1.jpeg') }}';"
                                                 alt="{{ $courseRelation->course->title }}">
                                        </div>
                                        <div class="checkout-course-info">
                                            <h4 class="checkout-course-title">{{ $courseRelation->course->title }}</h4>
                                            <p class="checkout-course-author">
                                                <i class="fa fa-user-circle-o"></i> {{ $courseRelation->course->user->name }}
                                            </p>
                                        </div>
                                        <div class="checkout-course-price">
                                            @if($courseRelation->course->promotion_price && $courseRelation->course->promotion_price < $courseRelation->course->origin_price)
                                                <span class="price-sale">{{ format_price($courseRelation->course->promotion_price) }}</span>
                                                <span class="price-strike">{{ format_price($courseRelation->course->origin_price) }}</span>
                                            @else
                                                <span class="price-single">{{ format_price($courseRelation->course->origin_price) }}</span>
                                            @endif
                                        </div>
                                        <input type="hidden" name="course_id[]" value="{{ $courseRelation->course->id }}">
                                        <input type="hidden" name="price[]" value="{{ $promoP }}">
                                    </li>
                                @endforeach
                            </ul>

                            <div class="checkout-total-card">
                                <div class="total-row">
                                    <span>Tạm tính (Giá gốc):</span>
                                    <span>{{ format_price($calcTotalOrigin) }}</span>
                                </div>
                                @if($calcTotalOrigin > $calcTotalFinal)
                                    <div class="total-row" style="color: #16a34a;">
                                        <span>Giảm giá khuyến mãi:</span>
                                        <span>-{{ format_price($calcTotalOrigin - $calcTotalFinal) }}</span>
                                    </div>
                                @endif
                                <div class="total-row final">
                                    <span>Tổng thanh toán:</span>
                                    <span class="final-price">{{ format_price($calcTotalFinal) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Customer Info & Confirmation -->
                    <div class="col-md-5 col-sm-12">
                        <div class="checkout-box">
                            <h2 class="checkout-box-title">
                                <i class="fa fa-user-circle"></i> Thông tin khách hàng
                            </h2>

                            @if ($errors->any())
                                <div class="alert alert-danger" style="border-radius: 8px; font-size: 13px;">
                                    <ul style="margin-bottom: 0; padding-left: 18px;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="checkout-form-group">
                                <label for="customer_name">Họ và tên <span class="text-danger">*</span></label>
                                <input class="checkout-input" id="customer_name" name="customer_name" placeholder="Ví dụ: Nguyễn Văn A"
                                       type="text" value="{{ old('customer_name', \Auth::user()->name) }}" required>
                            </div>

                            <div class="checkout-form-group">
                                <label for="customer_email">Địa chỉ Email <span class="text-danger">*</span></label>
                                <input class="checkout-input" id="customer_email" name="customer_email" placeholder="Email nhận thông tin khóa học"
                                       type="email" pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                                       title="Vui lòng nhập địa chỉ email hợp lệ"
                                       value="{{ old('customer_email', \Auth::user()->email) }}" required>
                            </div>

                            <div class="checkout-form-group">
                                <label for="customer_phone">Số điện thoại <span class="text-danger">*</span></label>
                                <input class="checkout-input" id="customer_phone" name="customer_phone" placeholder="Số điện thoại liên hệ"
                                       type="tel" inputmode="numeric" maxlength="10" pattern="[0-9]{10}"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                       title="Số điện thoại phải gồm đúng 10 chữ số"
                                       value="{{ old('customer_phone', \Auth::user()->phone) }}" required>
                            </div>

                            <div class="checkout-notification-check">
                                <label for="get_ads">
                                    <input type="checkbox" id="get_ads" name="get_ads" value="1" checked>
                                    <span>Nhận thông báo ưu đãi và khóa học mới qua email</span>
                                </label>
                            </div>

                            <input type="hidden" name="total_amount" value="{{ $calcTotalFinal }}">

                            <div class="security-badge">
                                <i class="fa fa-shield"></i>
                                <span>Thanh toán bảo mật & tiện lợi qua mã QR chuyển khoản</span>
                            </div>

                            <button class="checkout-submit-btn" type="submit">
                                <i class="fa fa-lock"></i> Xác nhận & Tiếp tục thanh toán
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection