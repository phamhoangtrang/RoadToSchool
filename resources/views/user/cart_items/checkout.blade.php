@extends('user.layouts.default')

@section('title')
    {{ __('titles.checkout') }}
@endsection

@section('inline_styles')
@endsection

@section('content')
    <div style="margin-left: 100px">
        {{ Breadcrumbs::render('checkout') }} <br>
    </div>
    <section id="content">
        <div class="container">
            <div class="row">
                <form action="{{route('cart_items.checkout.post')}}" method="POST">
                    @csrf
                    <div class="col-sm-6 col-md-6 col-md-offset-1">
                        <div class="box">
                            <h2 class="title-2">Khóa học đã chọn</h2>
                            <ul>
                                @foreach($courseRelations as $courseRelation)
                                    <li>
                                        <h3><img class="userimg"
                                                 src="{{ str_replace('public/', '', asset($courseRelation->course->course_avatar)) }}"
                                                 alt=""> {{ $courseRelation->course->title }}
                                            - {{ $courseRelation->course->user->name }}
                                            - @if($courseRelation->course->promotion_price && $courseRelation->course->promotion_price < $courseRelation->course->origin_price) <span
                                                    style="color:red">{{ format_price($courseRelation->course->promotion_price) }}</span>
                                            <strike>{{ format_price($courseRelation->course->origin_price) }} </strike> @else
                                                {{ format_price($courseRelation->course->origin_price) }} @endif </h3><br>
                                        <input type="hidden" name="course_id[]"
                                               value="{{ $courseRelation->course->id }}">
                                        <input type="hidden" name="price[]"
                                               value="{{ $courseRelation->course->promotion_price ?? $courseRelation->course->origin_price }}">
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-4 col-md-offset-1">
                        <div class="box">
                            <h2 class="title-2">Thông tin giao hàng</h2>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul style="margin-bottom: 0;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="control-label" for="textarea">Họ và tên <span class="text-danger">*</span></label>
                                <input class="form-control" name="customer_name" placeholder="Họ và tên của bạn"
                                       type="text" value="{{ old('customer_name', \Auth::user()->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="customer_email">Địa chỉ Email <span class="text-danger">*</span></label>
                                <input class="form-control" id="customer_email" name="customer_email" placeholder="Email nhận hàng (Ví dụ: name@gmail.com)"
                                       type="email" pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}"
                                       title="Vui lòng nhập địa chỉ email hợp lệ (Ví dụ: yourname@gmail.com)"
                                       value="{{ old('customer_email', \Auth::user()->email) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="customer_phone">Số điện thoại <span class="text-danger">*</span></label>
                                <input class="form-control" id="customer_phone" name="customer_phone" placeholder="Số điện thoại (Ví dụ: 0912345678)"
                                       type="tel" pattern="(0|\+84)?[0-9]{9,11}"
                                       title="Vui lòng nhập số điện thoại hợp lệ (9-11 chữ số)"
                                       value="{{ old('customer_phone', \Auth::user()->phone) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="textarea">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                                <input class="form-control" name="customer_address" placeholder="Địa chỉ giao hàng của bạn"
                                       type="text" value="{{ old('customer_address', \Auth::user()->address) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="textarea">Ghi chú</label>
                                <input class="form-control" name="customer_note" placeholder="Ghi chú thêm cho đơn hàng (tùy chọn)"
                                       type="text" value="{{ old('customer_note') }}">
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="get_ads" value="1">
                                    <small>Nhận thông báo ưu đãi và khóa học mới.</small>
                                </label>
                            </div>
                            <input type="hidden" name="total_amount"
                                   value="{{ ($totalOriginPriceInCart !== $totalPromotionPriceInCart) ? $totalPromotionPriceInCart : $totalOriginPriceInCart }}">
                        </div>
                        <div class="mb30"></div>
                        <div class="form-group">
                            <div class="page-ads box">
                                <p><i class="fa fa-shield"></i> &ensp; Kết nối an toàn & bảo mật</p>
                                <div class="checkbox">
                                    <i class="fa fa-check-square"></i> &ensp; Bằng việc đặt hàng, bạn đồng ý với <a href="#">Điều khoản dịch vụ</a> của chúng tôi
                                </div>
                                <br>
                                <input class="btn btn-common" type="submit" value="Xác nhận đặt hàng">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@section('inline_scripts')
    <script type="text/javascript">
    </script>
@endsection