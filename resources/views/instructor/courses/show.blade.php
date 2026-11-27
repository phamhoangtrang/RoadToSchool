@extends('admin.layouts.default')

@section('title')
    {{ $selectedCourse->title }} - {{ __('titles.all_courses') }}
@endsection

@section('inline_styles')
    <link rel="stylesheet"
          href="{{ asset('assets/admin/vendor/datatables/media/css/dataTables.bootstrap4.min.css') }}"/>
    <style>
        .course-detail-header-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }
        .course-meta-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            margin-bottom: 24px;
            overflow: hidden;
        }
        .carousel-slide-box {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            background: #000;
        }
        .carousel-slide-box img {
            height: 280px;
            object-fit: cover;
            width: 100%;
        }
        .meta-list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }
        .meta-list-item:last-child {
            border-bottom: none;
        }
        .meta-list-label {
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .meta-list-value {
            color: #1e293b;
            font-weight: 600;
            text-align: right;
        }
        .week-container {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .week-header {
            background: #f8fafc;
            padding: 14px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-pill.accepted {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .status-pill.pending {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .type-badge.lecture {
            background-color: #eff6ff;
            color: #2563eb;
        }
        .type-badge.quiz {
            background-color: #fdf4ff;
            color: #9333ea;
        }
        .action-btn-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            text-decoration: none !important;
            margin: 0 2px;
        }
        .action-btn-circle.edit {
            background-color: #eff6ff;
            color: #2563eb;
        }
        .action-btn-circle.edit:hover {
            background-color: #3b82f6;
            color: #fff;
        }
        .action-btn-circle.delete {
            background-color: #fef2f2;
            color: #dc2626;
        }
        .action-btn-circle.delete:hover {
            background-color: #ef4444;
            color: #fff;
        }
        .student-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #e2e8f0;
        }
    </style>
@endsection

@section('content')
    <!-- Content Wrapper START -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header Section (Fixed Duplicate) -->
            <div class="page-header m-b-20">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="header-title" style="font-weight: 700; color: #1e293b;">{{ $selectedCourse->title }}</h2>
                        <div class="header-sub-title">
                            <nav class="breadcrumb breadcrumb-dash" style="background: transparent; padding: 0;">
                                <a href="{{ route('instructor.dashboard') }}" class="breadcrumb-item" style="color: #64748b;">
                                    <i class="ti-home p-r-5"></i>{{ __('titles.r2s_dashboard') }}
                                </a>
                                <a class="breadcrumb-item" href="{{ route('instructor.courses.index') }}" style="color: #64748b;">
                                    Khóa học của tôi
                                </a>
                                <span class="breadcrumb-item active" style="color: #334155; font-weight: 500;">
                                    Chi tiết khóa học
                                </span>
                            </nav>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('instructor.courses.lectures.create', $selectedCourse->id) }}" class="btn btn-primary" style="border-radius: 8px; font-weight: 600; padding: 10px 18px;">
                            <i class="ti-plus m-r-5"></i> Thêm bài học mới
                        </a>
                    </div>
                </div>
            </div>

            @include('flash::message')

            <!-- Row: Ảnh khóa học & Thông tin chi tiết -->
            <div class="row">
                <!-- Carousel Ảnh khóa học -->
                <div class="col-lg-6 col-12">
                    <div class="card course-meta-card">
                        <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between" style="background: #fff;">
                            <h5 class="m-b-0" style="font-weight: 700; color: #1e293b; font-size: 15px;">
                                <i class="ti-image text-primary m-r-5"></i> Hình ảnh giới thiệu khóa học
                            </h5>
                            <span class="badge badge-light">3 Slides</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="carousel-slide-box">
                                <div id="carouselExampleCaption" class="carousel slide" data-ride="carousel">
                                    <ol class="carousel-indicators">
                                        <li data-target="#carouselExampleCaption" data-slide-to="0" class="active"></li>
                                        <li data-target="#carouselExampleCaption" data-slide-to="1"></li>
                                        <li data-target="#carouselExampleCaption" data-slide-to="2"></li>
                                    </ol>
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <img class="d-block w-100"
                                                 src="{{ asset(str_replace('public/', '', $selectedCourse->course_avatar ?: 'assets/admin/images/others/default-course.png')) }}"
                                                 alt="Slide 1"
                                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80';">
                                            <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 8px; padding: 5px 15px;">
                                                <h6 class="text-white m-0">Ảnh chính khóa học</h6>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <img class="d-block w-100"
                                                 src="{{ asset(str_replace('public/', '', $selectedCourse->course_avatar_2 ?: 'assets/admin/images/others/default-course.png')) }}"
                                                 alt="Slide 2"
                                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800&auto=format&fit=crop&q=80';">
                                            <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 8px; padding: 5px 15px;">
                                                <h6 class="text-white m-0">Ảnh banner phụ</h6>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <img class="d-block w-100"
                                                 src="{{ asset(str_replace('public/', '', $selectedCourse->course_avatar_3 ?: 'assets/admin/images/others/default-course.png')) }}"
                                                 alt="Slide 3"
                                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&auto=format&fit=crop&q=80';">
                                            <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 8px; padding: 5px 15px;">
                                                <h6 class="text-white m-0">Ảnh minh họa giáo trình</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleCaption" role="button" data-slide="prev">
                                        <span class="mdi mdi-chevron-left font-size-35" aria-hidden="true"></span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselExampleCaption" role="button" data-slide="next">
                                        <span class="mdi mdi-chevron-right font-size-35" aria-hidden="true"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin chi tiết khóa học -->
                <div class="col-lg-6 col-12">
                    <div class="card course-meta-card">
                        <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between" style="background: #fff;">
                            <h5 class="m-b-0" style="font-weight: 700; color: #1e293b; font-size: 15px;">
                                <i class="ti-id-badge text-primary m-r-5"></i> Thông số & Tình trạng khóa học
                            </h5>
                            @if($selectedCourse->is_accepted)
                                <span class="status-pill accepted">
                                    <i class="ti-check"></i> Đã duyệt
                                </span>
                            @else
                                <span class="status-pill pending">
                                    <i class="ti-time"></i> Chờ quản trị viên duyệt
                                </span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <div class="meta-list-item">
                                <span class="meta-list-label"><i class="ti-user text-primary"></i> Giảng viên phụ trách:</span>
                                <span class="meta-list-value">{{ $selectedCourse->user ? $selectedCourse->user->name : 'N/A' }}</span>
                            </div>
                            <div class="meta-list-item">
                                <span class="meta-list-label"><i class="ti-folder text-primary"></i> Danh mục chuyên ngành:</span>
                                <span class="meta-list-value">{{ $selectedCourse->category ? ($selectedCourse->category->vi_title ?: $selectedCourse->category->title) : 'Chung' }}</span>
                            </div>
                            <div class="meta-list-item">
                                <span class="meta-list-label"><i class="ti-layers-alt text-primary"></i> Quy mô bài giảng:</span>
                                <span class="meta-list-value">{{ $selectedCourse->lectures->count() }} bài học</span>
                            </div>
                            <div class="meta-list-item">
                                <span class="meta-list-label"><i class="ti-time text-primary"></i> Tổng thời lượng ước tính:</span>
                                <span class="meta-list-value">{{ $selectedCourse->duration ?: 0 }} phút</span>
                            </div>
                            <div class="meta-list-item">
                                <span class="meta-list-label"><i class="ti-shopping-cart text-primary"></i> Lượt học viên mua:</span>
                                <span class="meta-list-value">{{ $selectedCourse->seller ?: 0 }} lượt</span>
                            </div>
                            <div class="meta-list-item">
                                <span class="meta-list-label"><i class="ti-money text-primary"></i> Giá bán niêm yết:</span>
                                <span class="meta-list-value text-primary">
                                    @if($selectedCourse->promotion_price > 0)
                                        {{ number_format($selectedCourse->promotion_price, 0, ',', '.') }} đ
                                    @elseif($selectedCourse->origin_price > 0)
                                        {{ number_format($selectedCourse->origin_price, 0, ',', '.') }} đ
                                    @else
                                        Miễn phí
                                    @endif
                                </span>
                            </div>
                            <div class="meta-list-item">
                                <span class="meta-list-label"><i class="ti-calendar text-primary"></i> Cập nhật gần nhất:</span>
                                <span class="meta-list-value">{{ $selectedCourse->updated_at ? $selectedCourse->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đề cương & Danh sách bài học theo tuần -->
            <div class="row">
                <div class="col-12">
                    <div class="card course-meta-card">
                        <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between" style="background: #fff;">
                            <div class="d-flex align-items-center">
                                <div class="m-r-10" style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    <i class="ti-view-list-alt"></i>
                                </div>
                                <h5 class="m-b-0" style="font-weight: 700; color: #1e293b; font-size: 16px;">Đề cương & Danh sách bài học</h5>
                            </div>
                            <a href="{{ route('instructor.courses.lectures.create', $selectedCourse->id) }}" class="btn btn-sm btn-primary" style="border-radius: 6px; font-weight: 600;">
                                <i class="ti-plus m-r-5"></i> Thêm bài học mới
                            </a>
                        </div>
                        <div class="card-body p-4">
                            @php
                                $tempLectureCount = 1;
                                $tempQuizCount = 1;
                            @endphp

                            @if($maxWeek == 0 || empty($lectureOutline))
                                <div class="text-center p-5 text-muted">
                                    <i class="ti-layout-list-post" style="font-size: 40px; color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
                                    <h6>Khóa học chưa có bài giảng nào</h6>
                                    <p class="text-muted" style="font-size: 13px;">Hãy thêm các bài giảng hoặc bài tập trắc nghiệm để hoàn thiện đề cương giảng dạy.</p>
                                    <a href="{{ route('instructor.courses.lectures.create', $selectedCourse->id) }}" class="btn btn-sm btn-primary m-t-10">
                                        <i class="ti-plus"></i> Thêm bài giảng đầu tiên
                                    </a>
                                </div>
                            @else
                                @for($i = 0; $i < $maxWeek; $i++)
                                    <div class="week-container">
                                        <div class="week-header">
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-primary m-r-10" style="font-size: 12px; padding: 4px 10px; border-radius: 6px;">
                                                    TUẦN {{ $i + 1 }}
                                                </span>
                                                <h6 class="m-b-0" style="font-weight: 700; color: #1e293b;">Nội dung học tập Tuần {{ $i + 1 }}</h6>
                                            </div>
                                            <span class="text-muted" style="font-size: 12px;">
                                                {{ count($lectureOutline[$i] ?? []) }} nội dung
                                            </span>
                                        </div>
                                        <div class="table-overflow">
                                            <table class="table table-hover m-b-0">
                                                <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 14%; text-align: center;">Trạng thái</th>
                                                    <th style="width: 16%;">Cấu trúc</th>
                                                    <th style="width: 44%;">Tiêu đề bài học / bài kiểm tra</th>
                                                    <th style="width: 14%; text-align: center;">Thời lượng</th>
                                                    <th style="width: 12%; text-align: center;">Thao tác</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($lectureOutline[$i] as $lecture)
                                                    <tr>
                                                        <td class="text-center">
                                                            @if($lecture->is_accepted)
                                                                <span class="status-pill accepted">
                                                                    <i class="ti-check"></i> Đã duyệt
                                                                </span>
                                                            @else
                                                                <span class="status-pill pending">
                                                                    <i class="ti-time"></i> Chờ duyệt
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($lecture->is_lecture == 1)
                                                                <span class="type-badge lecture">
                                                                    <i class="ti-video-clapper"></i> Bài giảng {{ $tempLectureCount++ }}
                                                                </span>
                                                            @elseif($lecture->is_quiz == 1)
                                                                <span class="type-badge quiz">
                                                                    <i class="ti-help-alt"></i> Trắc nghiệm {{ $tempQuizCount++ }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ '/courses/'. $lecture->course_id . '/lectures/' . $lecture->id }}" target="_blank" style="font-weight: 600; color: #1e293b;" title="{{ $lecture->title }}">
                                                                {{ $lecture->title }}
                                                            </a>
                                                        </td>
                                                        <td class="text-center" style="font-size: 13px; color: #64748b;">
                                                            <i class="ti-time m-r-5"></i>{{ $lecture->duration ?: '0' }} phút
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ '/courses/'. $lecture->course_id . '/lectures/' . $lecture->id }}" target="_blank" class="action-btn-circle edit" title="Xem trước bài giảng">
                                                                <i class="ti-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted p-3">Chưa có bài học nào trong tuần này</td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endfor
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách học viên tham gia -->
            <div class="row">
                <div class="col-12">
                    <div class="card course-meta-card">
                        <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between" style="background: #fff;">
                            <div class="d-flex align-items-center">
                                <div class="m-r-10" style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    <i class="ti-user"></i>
                                </div>
                                <h5 class="m-b-0" style="font-weight: 700; color: #1e293b; font-size: 16px;">
                                    Danh sách học viên tham gia khóa học ({{ $studentList->count() }} học viên)
                                </h5>
                            </div>
                            <span class="badge badge-light" style="font-size: 12px; padding: 5px 12px; border-radius: 8px;">
                                Đang học
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-overflow">
                                <table id="dt-opt" class="table table-hover table-xl" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 10%;">Mã HV</th>
                                        <th style="width: 34%;">Học viên</th>
                                        <th style="width: 20%;">Thời gian ghi danh</th>
                                        <th style="width: 24%;">Tiến độ hoàn thành</th>
                                        <th style="width: 12%; text-align: center;">Trạng thái</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($studentList as $student)
                                        @php
                                            $stuAvatar = $student->avatar 
                                                ? str_replace('public/', '', asset($student->avatar)) 
                                                : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=2563eb&color=fff';
                                        @endphp
                                        <tr>
                                            <td style="font-weight: 600; color: #64748b;">
                                                #{{ $student->id }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $stuAvatar }}" alt="{{ $student->name }}" class="student-avatar m-r-10"
                                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User&background=2563eb&color=fff';">
                                                    <div>
                                                        <div style="font-weight: 600; color: #1e293b; font-size: 13px;">{{ $student->name }}</div>
                                                        <small class="text-muted" style="font-size: 11px;">{{ $student->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="font-size: 13px; color: #475569;">
                                                <i class="ti-calendar m-r-5 text-muted"></i>
                                                {{ $student->enrollCourseTime ? \Carbon\Carbon::parse($student->enrollCourseTime)->format('d/m/Y H:i') : 'N/A' }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress m-b-0 m-r-10" style="height: 8px; flex: 1; border-radius: 4px; background: #e2e8f0;">
                                                        <div class="progress-bar {{ $student->progress >= 100 ? 'bg-success' : 'bg-primary' }}"
                                                             role="progressbar"
                                                             style="width: {{ $student->progress }}%;"
                                                             aria-valuenow="{{ $student->progress }}"
                                                             aria-valuemin="0"
                                                             aria-valuemax="100"></div>
                                                    </div>
                                                    <span style="font-weight: 700; font-size: 12px; min-width: 40px; color: #1e293b;">
                                                        {{ $student->progress }}%
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($student->progress >= 100)
                                                    <span class="badge badge-success" style="font-size: 11px; padding: 4px 10px; border-radius: 20px;">Hoàn thành</span>
                                                @else
                                                    <span class="badge badge-light" style="font-size: 11px; padding: 4px 10px; border-radius: 20px; color: #2563eb; background: #eff6ff;">Đang học</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Wrapper END -->
@endsection

@include('admin.layouts.delete_modal')

@section('inline_scripts')
    <script src="{{ asset('assets/admin/vendor/datatables/media/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/datatables/media/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/tables/data-table.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#delete-modal').on('show.bs.modal', function (e) {
                var url = $(e.relatedTarget).data('url');
                $('#form-delete').attr('action', url);
            });

            $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
        });
    </script>
@endsection
