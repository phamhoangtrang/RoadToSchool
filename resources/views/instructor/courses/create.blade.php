@extends('admin.layouts.default')

@section('title')
    {{ __('titles.add_new_course') }}
@endsection

@section('inline_styles')
    <!-- page css -->
    <link href="{{ asset('assets/admin/vendor/selectize/dist/css/selectize.default.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/vendor/summernote/dist/summernote-bs4.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/vendor/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css') }}"
          rel="stylesheet">
    <style>
        .create-course-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .form-section-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e2e8f0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-section-title i {
            color: #2563eb;
            font-size: 18px;
        }
        .custom-form-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }
        .custom-form-control {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            font-size: 14px;
            color: #1e293b;
            transition: all 0.2s;
        }
        .custom-form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .image-upload-box {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            background: #f8fafc;
            transition: all 0.2s;
            position: relative;
            margin-bottom: 16px;
        }
        .image-upload-box:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .image-upload-box input[type="file"] {
            margin-top: 8px;
            width: 100%;
            font-size: 12px;
        }
        .btn-submit-course {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);
            transition: all 0.2s;
        }
        .btn-submit-course:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
            color: #fff;
        }
        .btn-reset-course {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }
        .btn-reset-course:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
    </style>
@endsection

@section('content')
    <!-- Content Wrapper START -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="page-header m-b-20">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="header-title" style="font-weight: 700; color: #1e293b;">Tạo khóa học mới</h2>
                        <div class="header-sub-title">
                            <nav class="breadcrumb breadcrumb-dash" style="background: transparent; padding: 0;">
                                <a href="{{ route('instructor.dashboard') }}" class="breadcrumb-item" style="color: #64748b;">
                                    <i class="ti-home p-r-5"></i>{{ __('titles.r2s_dashboard') }}
                                </a>
                                <a class="breadcrumb-item" href="{{ route('instructor.courses.index') }}" style="color: #64748b;">
                                    Khóa học của tôi
                                </a>
                                <span class="breadcrumb-item active" style="color: #334155; font-weight: 500;">
                                    Tạo khóa học mới
                                </span>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Form -->
            <div class="card create-course-card">
                <div class="card-header border-bottom p-4" style="background: #f8fafc;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title m-b-0" style="font-weight: 700; color: #1e293b;">Thiết lập thông tin khóa học</h4>
                            <p class="text-muted m-b-0" style="font-size: 13px;">Nhập đầy đủ thông tin để gửi phê duyệt tới Quản trị viên trước khi khóa học được phát hành công khai.</p>
                        </div>
                        <span class="badge badge-info" style="font-size: 12px; padding: 6px 12px; border-radius: 20px;">
                            <i class="ti-info-alt m-r-5"></i> Cần kiểm duyệt
                        </span>
                    </div>
                </div>

                @if (isset($errors) && $errors->any())
                    <div class="alert alert-danger m-3" style="border-radius: 10px;">
                        <ul class="m-b-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card-body p-4">
                    @include('flash::message')
                    {!! Form::open(['route' => 'instructor.courses.store', 'method' => 'post', 'files' => true, 'id' => 'form-create-course']) !!} 
                        @csrf

                        <!-- 1. THÔNG TIN CƠ BẢN -->
                        <div class="form-section-title">
                            <i class="ti-book"></i> 1. Thông tin cơ bản
                        </div>
                        <div class="row">
                            <div class="col-md-12 m-b-20">
                                <label class="custom-form-label" for="input-course-title">Tiêu đề khóa học *</label>
                                <input type="text" class="form-control custom-form-control" id="input-course-title"
                                       name="title" placeholder="VD: Khóa học Lập trình Web Fullstack từ cơ bản đến nâng cao..." required>
                            </div>
                            <div class="col-md-4 m-b-20">
                                <label class="custom-form-label" for="input-course-parent-category">Danh mục chính *</label>
                                <select class="form-control custom-form-control" id="input-course-parent-category" required>
                                    <option disabled="disabled" selected>-- Chọn danh mục chính --</option>
                                    @foreach($parentCategoryList as $category)
                                        <option value="{{ $category->id }}">{{ $category->vi_title ?: $category->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 m-b-20">
                                <label class="custom-form-label" for="input-course-category">Danh mục con *</label>
                                <select class="form-control custom-form-control" id="input-course-category" name="category_id" required>
                                    <option disabled="disabled" selected>-- Chọn danh mục chính trước --</option>
                                </select>
                            </div>
                            <div class="col-md-4 m-b-20">
                                <label class="custom-form-label">Cấp độ phù hợp *</label>
                                <select class="form-control custom-form-control" id="input-course-level" name="level" required>
                                    <option disabled="disabled" selected>-- Chọn cấp độ --</option>
                                    <option value="1">Cơ bản (Người mới bắt đầu)</option>
                                    <option value="2">Trung cấp (Đã có nền tảng)</option>
                                    <option value="3">Nâng cao (Chuyên sâu)</option>
                                </select>
                            </div>
                        </div>

                        <!-- 2. HỌC PHÍ & GIÁ BÁN KHÓA HỌC -->
                        <div class="form-section-title m-t-15">
                            <i class="ti-money"></i> 2. Học phí & Thiết lập giá bán
                        </div>
                        <div class="row">
                            <div class="col-md-6 m-b-20">
                                <label class="custom-form-label" for="input-origin-price">Giá gốc niêm yết (VNĐ) *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control custom-form-control" id="input-origin-price"
                                           name="origin_price" min="0" step="1000" placeholder="VD: 399000 (Nhập 0 nếu miễn phí)" value="{{ old('origin_price', 0) }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold" style="background: #f1f5f9; border-color: #cbd5e1; color: #475569;">VNĐ</span>
                                    </div>
                                </div>
                                <small class="text-muted">Nhập <strong>0</strong> nếu bạn muốn phát hành khóa học hoàn toàn miễn phí.</small>
                            </div>
                            <div class="col-md-6 m-b-20">
                                <label class="custom-form-label" for="input-promotion-price">Giá ưu đãi / khuyến mãi (VNĐ) - Tùy chọn</label>
                                <div class="input-group">
                                    <input type="number" class="form-control custom-form-control" id="input-promotion-price"
                                           name="promotion_price" min="0" step="1000" placeholder="VD: 199000 (để trống nếu không giảm giá)" value="{{ old('promotion_price') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold" style="background: #f1f5f9; border-color: #cbd5e1; color: #475569;">VNĐ</span>
                                    </div>
                                </div>
                                <small class="text-muted">Giá ưu đãi dành cho học viên. Để trống nếu bán đúng giá niêm yết.</small>
                            </div>
                        </div>

                        <!-- 3. HÌNH ẢNH KHÓA HỌC -->
                        <div class="form-section-title m-t-15">
                            <i class="ti-image"></i> 3. Hình ảnh khóa học (Banner / Slides)
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="image-upload-box" id="box-course-avatar">
                                    <div class="upload-icon" style="font-size: 28px; color: #2563eb; margin-bottom: 8px;">
                                        <i class="ti-cloud-up"></i>
                                    </div>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 13px;">Ảnh bìa chính (Slide 1)</div>
                                    <small class="text-muted d-block m-b-10">Kích thước chuẩn: 1200x675 (16:9)</small>
                                    <div class="preview-area m-b-10" style="display: none;">
                                        <img src="" style="max-width: 100%; height: 90px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1;">
                                    </div>
                                    {!! Form::file('course_avatar', ['id' => 'file-course-avatar', 'accept' => 'image/*']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="image-upload-box" id="box-course-avatar-2">
                                    <div class="upload-icon" style="font-size: 28px; color: #64748b; margin-bottom: 8px;">
                                        <i class="ti-image"></i>
                                    </div>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 13px;">Ảnh phụ giới thiệu (Slide 2)</div>
                                    <small class="text-muted d-block m-b-10">Ảnh banner phụ hoặc ảnh xem trước</small>
                                    <div class="preview-area m-b-10" style="display: none;">
                                        <img src="" style="max-width: 100%; height: 90px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1;">
                                    </div>
                                    {!! Form::file('course_avatar_2', ['id' => 'file-course-avatar-2', 'accept' => 'image/*']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="image-upload-box" id="box-course-avatar-3">
                                    <div class="upload-icon" style="font-size: 28px; color: #64748b; margin-bottom: 8px;">
                                        <i class="ti-gallery"></i>
                                    </div>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 13px;">Ảnh minh họa giáo trình (Slide 3)</div>
                                    <small class="text-muted d-block m-b-10">Ảnh giáo trình hoặc kết quả đạt được</small>
                                    <div class="preview-area m-b-10" style="display: none;">
                                        <img src="" style="max-width: 100%; height: 90px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1;">
                                    </div>
                                    {!! Form::file('course_avatar_3', ['id' => 'file-course-avatar-3', 'accept' => 'image/*']) !!}
                                </div>
                            </div>
                        </div>

                        <!-- 4. MÔ TẢ CHI TIẾT -->
                        <div class="form-section-title m-t-15">
                            <i class="ti-file"></i> 4. Giới thiệu & Mô tả nội dung khóa học
                        </div>
                        <div class="row">
                            <div class="col-12 m-b-25">
                                <label class="custom-form-label" for="text-course-description">Nội dung chi tiết khóa học *</label>
                                <div class="m-t-5" id="text-course-description">
                                    <div id="summernote-standard"></div>
                                    <input type="hidden" id="input-course-description" name="description">
                                </div>
                            </div>
                        </div>

                        <!-- NÚT THAO TÁC -->
                        <div class="row m-t-20 border-top p-t-20">
                            <div class="col-12 text-right">
                                <button type="button" class="btn btn-reset-course m-r-10" id="btn-reset">
                                    <i class="ti-reload m-r-5"></i> Nhập lại
                                </button>
                                <button type="submit" class="btn btn-submit-course" id="btn-submit-course">
                                    <i class="ti-check m-r-5"></i> Hoàn tất & Gửi phê duyệt
                                </button>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <!-- Content Wrapper END -->
@endsection

@include('admin.layouts.delete_modal')

@section('inline_scripts')
    <script src="{{ asset('assets/admin/vendor/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/selectize/dist/js/standalone/selectize.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/js/forms/form-elements.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Summernote sync
            $('#form-create-course').on('submit', function () {
                var htmlContent = $('#summernote-standard').summernote('code');
                $('#input-course-description').val(htmlContent);
            });

            $('.note-editor').on('mouseout keyup', function () {
                $('#input-course-description').val($('#summernote-standard').summernote('code'));
            });

            $('#btn-reset').on('click', function (event) {
                event.preventDefault();
                $('#input-course-title').val('');
                $('#input-origin-price').val(0);
                $('#input-promotion-price').val('');
                $('#input-course-parent-category').prop('selectedIndex', 0);
                $('#input-course-category').empty().append(new Option('-- Chọn danh mục chính trước --', ''));
                $('#input-course-level').prop('selectedIndex', 0);
                $('#summernote-standard').summernote('reset');
                $('#input-course-description').val('');
                $('.preview-area').hide().find('img').attr('src', '');
                $('.upload-icon').show();
            });

            // Xem trước ảnh khi chọn
            function setupImagePreview(inputId, boxId) {
                $(inputId).on('change', function () {
                    var file = this.files[0];
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $(boxId + ' .preview-area img').attr('src', e.target.result);
                            $(boxId + ' .preview-area').show();
                            $(boxId + ' .upload-icon').hide();
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
            setupImagePreview('#file-course-avatar', '#box-course-avatar');
            setupImagePreview('#file-course-avatar-2', '#box-course-avatar-2');
            setupImagePreview('#file-course-avatar-3', '#box-course-avatar-3');

            $('#input-course-parent-category').on('change', function () {
                var parentId = $(this).val();
                if (!parentId) return;

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/categories/' + parentId + '/getSubCategoryList',
                    type: 'post',
                    success: function (response) {
                        var responseData = jQuery.parseJSON(response);
                        $('#input-course-category').empty();
                        if (responseData.length === 0) {
                            $('#input-course-category').append(new Option('-- Không có danh mục con --', ''));
                        } else {
                            $('#input-course-category').append(new Option('-- Chọn danh mục con --', ''));
                            responseData.forEach(function (element) {
                                var catTitle = element.vi_title || element.title;
                                $('#input-course-category').append(new Option(catTitle, element.id));
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection