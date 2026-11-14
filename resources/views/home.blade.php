@extends('user.layouts.default')

@section('title')
    {{ __('titles.road_to_school_home_page') }}
@endsection

@section('inline_styles')
    <style>
        .category-box {
            background-color: #fff;
            margin: 35px 0 25px;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(0, 0, 0, 0.06);
            position: relative;
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: visible;
            display: flex;
            flex-direction: column;
            height: calc(100% - 60px);
            min-height: 480px;
        }

        .category-box:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.12);
        }

        .category-box .icon {
            text-align: center;
            position: absolute;
            top: -24px;
            left: 0;
            right: 0;
            margin: 0 auto;
            width: 100%;
            z-index: 3;
            pointer-events: none;
        }

        .category-box .icon i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            color: #fff;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
            border: 3px solid #fff;
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .category-box:hover .icon i {
            transform: scale(1.08);
        }

        .category-box .category-header {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 85px;
            padding: 34px 14px 14px;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
            transition: all 0.28s ease;
            position: relative;
        }

        .category-box .category-header h4 {
            font-size: 15.5px;
            font-weight: 700;
            line-height: 1.35;
            margin: 0;
            color: #ffffff !important;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            letter-spacing: -0.01em;
        }

        .category-box .category-content {
            padding: 10px 10px 14px;
            background: #ffffff;
            border-bottom-left-radius: 14px;
            border-bottom-right-radius: 14px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .subcat-list {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
        }

        .subcat-item {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            padding: 7px 8px !important;
            margin: 3px 0 !important;
            border-radius: 8px;
            transition: all 0.18s ease;
            width: 100% !important;
            float: none !important;
            min-height: 48px;
        }

        .subcat-title {
            flex: 1;
            min-width: 0;
            font-size: 13px;
            font-weight: 500;
            color: #334155 !important;
            text-decoration: none !important;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.35;
            max-height: 2.7em;
            margin-left: 0 !important;
            float: none !important;
            transition: color 0.18s ease;
        }

        .category-counter-pill {
            font-size: 11px;
            font-weight: 600;
            border-width: 1px;
            border-style: solid;
            padding: 2px 7px;
            border-radius: 12px;
            white-space: nowrap;
            flex-shrink: 0;
            margin-left: 8px;
            float: none !important;
            transition: all 0.18s ease;
            line-height: 1.35;
        }

        .category-row-flex {
            display: flex;
            flex-wrap: wrap;
        }

        .category-row-flex > [class*="col-"] {
            display: flex;
            flex-direction: column;
        }

        .category-view-all {
            margin-top: auto;
            padding-top: 10px;
            border-top: 1px dashed #e2e8f0;
            text-align: center;
        }

        .cat-view-all-link {
            font-size: 12.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none !important;
            transition: all 0.2s ease;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .cat-view-all-link i {
            font-size: 11px;
            transition: transform 0.2s ease;
        }

        .cat-view-all-link:hover i {
            transform: translateX(3px);
        }

        /* Palette 1: Sapphire Blue (Lập trình & Phát triển) */
        .cat-theme-1 .category-header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important; }
        .cat-theme-1 .icon i { background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important; }
        .cat-theme-1 { border-bottom: 3px solid #3b82f6 !important; }
        .cat-theme-1 .category-counter-pill { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
        .cat-theme-1 .subcat-item:hover { background: #f0f7ff; }
        .cat-theme-1 .subcat-item:hover .subcat-title { color: #1d4ed8 !important; }
        .cat-theme-1 .cat-view-all-link { color: #2563eb; }
        .cat-theme-1 .cat-view-all-link:hover { background: #eff6ff; color: #1d4ed8; }

        /* Palette 2: Emerald Teal (Kinh doanh & Khởi nghiệp) */
        .cat-theme-2 .category-header { background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%) !important; }
        .cat-theme-2 .icon i { background: linear-gradient(135deg, #14b8a6, #0f766e) !important; }
        .cat-theme-2 { border-bottom: 3px solid #14b8a6 !important; }
        .cat-theme-2 .category-counter-pill { background: #f0fdfa; color: #0f766e; border-color: #99f6e4; }
        .cat-theme-2 .subcat-item:hover { background: #f0fdfa; }
        .cat-theme-2 .subcat-item:hover .subcat-title { color: #0f766e !important; }
        .cat-theme-2 .cat-view-all-link { color: #0d9488; }
        .cat-theme-2 .cat-view-all-link:hover { background: #f0fdfa; color: #0f766e; }

        /* Palette 3: Coral Rose (CNTT & Phần mềm) */
        .cat-theme-3 .category-header { background: linear-gradient(135deg, #be123c 0%, #f43f5e 100%) !important; }
        .cat-theme-3 .icon i { background: linear-gradient(135deg, #fb7185, #be123c) !important; }
        .cat-theme-3 { border-bottom: 3px solid #f43f5e !important; }
        .cat-theme-3 .category-counter-pill { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
        .cat-theme-3 .subcat-item:hover { background: #fff1f2; }
        .cat-theme-3 .subcat-item:hover .subcat-title { color: #be123c !important; }
        .cat-theme-3 .cat-view-all-link { color: #e11d48; }
        .cat-theme-3 .cat-view-all-link:hover { background: #fff1f2; color: #be123c; }

        /* Palette 4: Creative Violet (Thiết kế sáng tạo) */
        .cat-theme-4 .category-header { background: linear-gradient(135deg, #6d28d9 0%, #8b5cf6 100%) !important; }
        .cat-theme-4 .icon i { background: linear-gradient(135deg, #8b5cf6, #6d28d9) !important; }
        .cat-theme-4 { border-bottom: 3px solid #8b5cf6 !important; }
        .cat-theme-4 .category-counter-pill { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
        .cat-theme-4 .subcat-item:hover { background: #f5f3ff; }
        .cat-theme-4 .subcat-item:hover .subcat-title { color: #6d28d9 !important; }
        .cat-theme-4 .cat-view-all-link { color: #7c3aed; }
        .cat-theme-4 .cat-view-all-link:hover { background: #f5f3ff; color: #6d28d9; }

        /* Palette 5: Sky Blue (Marketing & Tiếp thị) */
        .cat-theme-5 .category-header { background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%) !important; }
        .cat-theme-5 .icon i { background: linear-gradient(135deg, #0ea5e9, #0369a1) !important; }
        .cat-theme-5 { border-bottom: 3px solid #0ea5e9 !important; }
        .cat-theme-5 .category-counter-pill { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }
        .cat-theme-5 .subcat-item:hover { background: #f0f9ff; }
        .cat-theme-5 .subcat-item:hover .subcat-title { color: #0369a1 !important; }
        .cat-theme-5 .cat-view-all-link { color: #0284c7; }
        .cat-theme-5 .cat-view-all-link:hover { background: #f0f9ff; color: #0369a1; }

        /* Palette 6: Sunset Amber (Kỹ năng sống) */
        .cat-theme-6 .category-header { background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%) !important; }
        .cat-theme-6 .icon i { background: linear-gradient(135deg, #f59e0b, #b45309) !important; }
        .cat-theme-6 { border-bottom: 3px solid #f59e0b !important; }
        .cat-theme-6 .category-counter-pill { background: #fffbeb; color: #b45309; border-color: #fde68a; }
        .cat-theme-6 .subcat-item:hover { background: #fffbeb; }
        .cat-theme-6 .subcat-item:hover .subcat-title { color: #b45309 !important; }
        .cat-theme-6 .cat-view-all-link { color: #d97706; }
        .cat-theme-6 .cat-view-all-link:hover { background: #fffbeb; color: #b45309; }

        /* Palette 7: Fresh Mint (Nhiếp ảnh & Video) */
        .cat-theme-7 .category-header { background: linear-gradient(135deg, #047857 0%, #10b981 100%) !important; }
        .cat-theme-7 .icon i { background: linear-gradient(135deg, #10b981, #047857) !important; }
        .cat-theme-7 { border-bottom: 3px solid #10b981 !important; }
        .cat-theme-7 .category-counter-pill { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
        .cat-theme-7 .subcat-item:hover { background: #ecfdf5; }
        .cat-theme-7 .subcat-item:hover .subcat-title { color: #047857 !important; }
        .cat-theme-7 .cat-view-all-link { color: #059669; }
        .cat-theme-7 .cat-view-all-link:hover { background: #ecfdf5; color: #047857; }

        /* Palette 8: Berry Rose (Sức khỏe & Thể hình) */
        .cat-theme-8 .category-header { background: linear-gradient(135deg, #9d174d 0%, #ec4899 100%) !important; }
        .cat-theme-8 .icon i { background: linear-gradient(135deg, #ec4899, #9d174d) !important; }
        .cat-theme-8 { border-bottom: 3px solid #ec4899 !important; }
        .cat-theme-8 .category-counter-pill { background: #fdf2f8; color: #9d174d; border-color: #fbcfe8; }
        .cat-theme-8 .subcat-item:hover { background: #fdf2f8; }
        .cat-theme-8 .subcat-item:hover .subcat-title { color: #9d174d !important; }
        .cat-theme-8 .cat-view-all-link { color: #db2777; }
        .cat-theme-8 .cat-view-all-link:hover { background: #fdf2f8; color: #9d174d; }

        /* Modern Hero Search Bar */
        .hero-search-wrapper {
            max-width: 780px;
            margin: 25px auto 0;
            width: 100%;
        }

        .hero-search-box {
            background: #ffffff;
            border-radius: 50px;
            padding: 6px 7px 6px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.28), 0 0 0 1px rgba(255, 255, 255, 0.25);
            border: 2px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .hero-search-box:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.35), 0 0 0 4px rgba(59, 130, 246, 0.25);
            transform: translateY(-2px);
        }

        .hero-search-input-group {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
            margin-right: 12px;
        }

        .hero-search-icon {
            font-size: 19px;
            color: #3b82f6;
            margin-right: 14px;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .hero-search-box:focus-within .hero-search-icon {
            transform: scale(1.15);
        }

        .hero-search-input {
            width: 100%;
            border: none !important;
            outline: none !important;
            background: transparent !important;
            font-size: 16px;
            font-weight: 500;
            color: #1e293b !important;
            padding: 10px 0;
            line-height: 1.5;
            box-shadow: none !important;
        }

        .hero-search-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .hero-search-btn {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff !important;
            border: none;
            outline: none;
            border-radius: 50px;
            padding: 12px 32px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
            cursor: pointer;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .hero-search-btn:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(37, 99, 235, 0.55);
        }

        .hero-search-btn:active {
            transform: translateY(1px);
        }

        .hero-search-btn i {
            font-size: 14px;
        }

        /* Quick Tags */
        .hero-search-quicktags {
            margin-top: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            text-transform: none;
        }

        .quicktags-label {
            color: rgba(255, 255, 255, 0.95);
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-right: 4px;
        }

        .quicktags-label i {
            color: #fbbf24;
        }

        .quicktag-item {
            color: rgba(255, 255, 255, 0.95) !important;
            font-size: 12.5px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 20px;
            padding: 4px 14px;
            backdrop-filter: blur(8px);
            text-decoration: none !important;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .quicktag-item:hover {
            background: #ffffff;
            color: #1d4ed8 !important;
            border-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .hero-search-box {
                padding: 6px 8px 6px 16px;
                border-radius: 16px;
                flex-direction: column;
                gap: 10px;
            }
            .hero-search-input-group {
                width: 100%;
                margin-right: 0;
            }
            .hero-search-btn {
                width: 100%;
                justify-content: center;
                border-radius: 10px;
                padding: 10px;
            }
        }

        /* Modern Course Carousel & Card Styling */
        .featured-lis {
            padding: 35px 0 25px;
            position: relative;
        }

        .featured-lis-alt {
            background: #f8fafc;
            border-top: 1px solid #edf2f7;
            border-bottom: 1px solid #edf2f7;
        }

        .modern-section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 15px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 14px;
        }

        .modern-section-header .header-content {
            flex: 1;
            min-width: 260px;
        }

        .header-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 8px;
        }

        .pill-recommend {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .pill-bestseller {
            background: #fff7ed;
            color: #ea580c;
            border: 1px solid #fed7aa;
        }

        .modern-heading {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 4px;
            letter-spacing: -0.01em;
            text-transform: uppercase;
        }

        .modern-subheading {
            font-size: 13.5px;
            color: #64748b;
            margin: 0;
            line-height: 1.45;
        }

        .modern-view-all {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #2563eb !important;
            text-decoration: none !important;
            padding: 7px 16px;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
        }

        .modern-view-all:hover {
            background: #2563eb;
            color: #ffffff !important;
            border-color: #2563eb;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
            transform: translateX(2px);
        }

        /* Modern Course Card */
        .modern-course-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            margin: 6px 7px 16px;
            position: relative;
            text-align: left;
        }

        .modern-course-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.12);
            border-color: #cbd5e1;
        }

        .card-thumb-wrap {
            position: relative;
            display: block;
            width: 100%;
            height: 155px;
            overflow: hidden;
            background: #0f172a;
        }

        .card-thumb-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1);
            display: block;
        }

        .modern-course-card:hover .card-thumb-img {
            transform: scale(1.08);
        }

        .card-cat-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(15, 23, 42, 0.78);
            backdrop-filter: blur(4px);
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 6px;
            z-index: 2;
            pointer-events: none;
            max-width: 70%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        }

        .card-discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            box-shadow: 0 3px 8px rgba(220, 38, 38, 0.45);
            z-index: 2;
            pointer-events: none;
            letter-spacing: 0.02em;
        }

        .card-thumb-hover {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.42);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.25s ease;
            z-index: 3;
        }

        .modern-course-card:hover .card-thumb-hover {
            opacity: 1;
        }

        .btn-quick-view {
            background: #ffffff;
            color: #1e293b;
            font-size: 12px;
            font-weight: 700;
            padding: 7px 16px;
            border-radius: 20px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transform: translateY(6px);
            transition: transform 0.25s ease;
        }

        .modern-course-card:hover .btn-quick-view {
            transform: translateY(0);
        }

        .card-body-content {
            padding: 13px 14px 15px;
            display: flex;
            flex-direction: column;
            flex: 1;
            background: #ffffff;
        }

        .card-instructor {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-instructor i {
            font-size: 13px;
            color: #94a3b8;
        }

        .card-title {
            font-size: 14px;
            font-weight: 700;
            line-height: 1.42;
            margin: 0 0 10px;
            min-height: 40px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-title a {
            color: #1e293b !important;
            text-decoration: none !important;
            transition: color 0.18s ease;
        }

        .card-title a:hover {
            color: #2563eb !important;
        }

        .card-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 11px;
            padding-bottom: 9px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .card-rating {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .star-score {
            font-weight: 800;
            color: #b45309;
            font-size: 12.5px;
        }

        .star-icons {
            display: inline-flex;
            gap: 1px;
        }

        .star-icons i.fa-star,
        .star-icons i.fa-star-half-o {
            color: #f59e0b;
            font-size: 11px;
        }

        .star-icons i.fa-star-o {
            color: #cbd5e1;
            font-size: 11px;
        }

        .card-students {
            color: #64748b;
            font-size: 11.5px;
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 500;
        }

        .card-price-row {
            margin-top: auto;
            display: flex;
            align-items: baseline;
            justify-content: space-between;
        }

        .price-wrap {
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 7px;
        }

        .price-current {
            font-size: 16px;
            font-weight: 800;
            color: #dc2626;
            letter-spacing: -0.01em;
        }

        .price-current.price-normal {
            color: #0f172a;
        }

        .price-original {
            font-size: 12.5px;
            color: #94a3b8;
            text-decoration: line-through;
            font-weight: 500;
        }

        /* Sleek Modern Owl Carousel Controls Override */
        #new-products.owl-carousel,
        #best-products.owl-carousel {
            position: relative;
            padding: 8px 0 15px;
        }

        #new-products .owl-controls,
        #best-products .owl-controls {
            margin-top: 5px;
        }

        #new-products .owl-controls .owl-buttons,
        #best-products .owl-controls .owl-buttons {
            position: absolute !important;
            top: 40% !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 0 !important;
            pointer-events: none !important;
            margin: 0 !important;
            z-index: 10 !important;
        }

        #new-products .owl-controls .owl-buttons div,
        #best-products .owl-controls .owl-buttons div {
            pointer-events: auto !important;
            width: 44px !important;
            height: 44px !important;
            border-radius: 50% !important;
            background: #ffffff !important;
            border: 2px solid #2563eb !important;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.22) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            opacity: 1 !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            padding: 0 !important;
            position: absolute !important;
            cursor: pointer !important;
            text-align: center !important;
        }

        #new-products .owl-controls .owl-buttons div i,
        #best-products .owl-controls .owl-buttons div i {
            color: #2563eb !important; /* Explicit bold blue so arrows are clearly visible at all times */
            font-size: 22px !important;
            font-weight: 900 !important;
            line-height: 40px !important;
            display: inline-block !important;
            transition: color 0.2s ease !important;
        }

        #new-products .owl-controls .owl-buttons div:hover,
        #best-products .owl-controls .owl-buttons div:hover {
            background: #2563eb !important;
            border-color: #2563eb !important;
            transform: scale(1.1) !important;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.45) !important;
        }

        #new-products .owl-controls .owl-buttons div:hover i,
        #best-products .owl-controls .owl-buttons div:hover i {
            color: #ffffff !important;
        }

        #new-products .owl-controls .owl-buttons div.owl-prev,
        #best-products .owl-controls .owl-buttons div.owl-prev {
            left: -22px !important;
            margin: 0 !important;
            float: none !important;
        }

        #new-products .owl-controls .owl-buttons div.owl-next,
        #best-products .owl-controls .owl-buttons div.owl-next {
            right: -22px !important;
            margin: 0 !important;
            float: none !important;
        }

        @media (max-width: 768px) {
            #new-products .owl-controls .owl-buttons div.owl-prev,
            #best-products .owl-controls .owl-buttons div.owl-prev {
                left: 2px !important;
            }
            #new-products .owl-controls .owl-buttons div.owl-next,
            #best-products .owl-controls .owl-buttons div.owl-next {
                right: 2px !important;
            }
            .modern-section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .modern-view-all {
                align-self: flex-start;
            }
        }

        /* Modern Features Section with Rich Blue Background */
        .features {
            padding: 65px 0 45px;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 45%, #2563eb 100%);
            position: relative;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .features-box {
            background: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 14px;
            padding: 24px 22px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.14);
            min-height: 116px;
        }

        .features-box:hover {
            border-color: #ffffff;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.25);
            transform: translateY(-5px);
        }

        .features-box .features-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            float: none !important;
            padding: 0 !important;
            margin: 0 !important;
            transition: all 0.25s ease;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.15);
        }

        .features-box:hover .features-icon {
            background: #2563eb;
            color: #ffffff;
            transform: scale(1.08);
        }

        .features-box .features-content {
            padding-left: 0 !important;
            flex: 1;
        }

        .features-box .features-content h4 {
            font-size: 15.5px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 6px;
            line-height: 1.35;
        }

        .features-box .features-content p {
            font-size: 13px;
            color: #64748b;
            margin: 0;
            line-height: 1.55;
        }

        /* Modern Hero Banner */
        .section-intro {
            position: relative;
            background: linear-gradient(135deg, #090e1a 0%, #0f172a 40%, #1e3a8a 85%, #2563eb 100%) !important;
            padding: 75px 0 85px !important;
            overflow: hidden;
            text-align: center;
        }

        .section-intro::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 50% 15%, rgba(59, 130, 246, 0.28), transparent 65%),
                        radial-gradient(circle at 85% 75%, rgba(37, 99, 235, 0.22), transparent 50%);
            pointer-events: none;
        }

        .section-intro .overlay {
            background: transparent !important;
        }

        .section-intro .main-text {
            margin: 0 auto !important;
            text-transform: none !important;
            max-width: 900px;
            position: relative;
            z-index: 2;
        }

        .hero-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 18px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 30px;
            color: #93c5fd;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 18px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            letter-spacing: 0.2px;
        }

        .section-intro .main-text .intro-title {
            font-size: 46px !important;
            font-weight: 800 !important;
            color: #ffffff !important;
            line-height: 1.25 !important;
            letter-spacing: -0.02em !important;
            margin-bottom: 14px !important;
            text-transform: none !important;
            white-space: normal !important;
        }

        .section-intro .main-text .intro-title span.hero-brand {
            background: linear-gradient(135deg, #60a5fa 0%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
        }

        .section-intro .main-text .sub-title {
            font-size: 16px !important;
            font-weight: 400 !important;
            color: #cbd5e1 !important;
            line-height: 1.6 !important;
            text-transform: none !important;
            margin: 0 auto 26px !important;
            padding: 0 !important;
            max-width: 680px !important;
        }

        .hero-features-stats {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 36px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .hero-stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #e2e8f0;
            font-size: 13.5px;
            font-weight: 500;
        }

        .hero-stat-item i {
            color: #60a5fa;
            font-size: 15px;
        }
    </style>
@endsection

@section('content')
    <section id="intro" class="section-intro">
        <div class="overlay">
            <div class="container">
                <div class="main-text">
                    <div class="hero-badge-pill">
                        <i class="fa fa-graduation-cap"></i>
                        <span>Nền tảng học trực tuyến chất lượng cao</span>
                    </div>
                    <h1 class="intro-title">
                        Chào mừng tới <span class="hero-brand">Road To School</span>
                    </h1>
                    <p class="sub-title">
                        Để tạo ra môi trường học tập tuyệt vời nhất, chúng tôi luôn nỗ lực mang lại trải nghiệm tối ưu cho người học.
                    </p>

                    <div class="hero-search-wrapper">
                        <form class="hero-search-box" method="get" action="{{ route('courses.index') }}">
                            <div class="hero-search-input-group">
                                <i class="fa fa-search hero-search-icon"></i>
                                <input class="hero-search-input" name="keyword" value="{{ request('keyword') }}"
                                       placeholder="Tìm kiếm khóa học, kỹ năng, giảng viên bạn muốn học..." type="text" autocomplete="off">
                            </div>
                            <button class="hero-search-btn" type="submit">
                                <i class="fa fa-search"></i>
                                <span>Tìm kiếm</span>
                            </button>
                        </form>
                        <div class="hero-search-quicktags">
                            <span class="quicktags-label">Gợi ý:</span>
                            <a href="{{ route('courses.index', ['keyword' => 'Lập trình']) }}" class="quicktag-item">Lập trình Web</a>
                            <a href="{{ route('courses.index', ['keyword' => 'Python']) }}" class="quicktag-item">Python</a>
                            <a href="{{ route('courses.index', ['keyword' => 'Thiết kế']) }}" class="quicktag-item">Thiết kế UI/UX</a>
                            <a href="{{ route('courses.index', ['keyword' => 'Marketing']) }}" class="quicktag-item">Marketing</a>
                        </div>
                    </div>

                    <div class="hero-features-stats">
                        <div class="hero-stat-item">
                            <i class="fa fa-check-circle"></i>
                            <span>Học tập mọi lúc mọi nơi</span>
                        </div>
                        <div class="hero-stat-item">
                            <i class="fa fa-trophy"></i>
                            <span>Giảng viên chuyên môn cao</span>
                        </div>
                        <div class="hero-stat-item">
                            <i class="fa fa-certificate"></i>
                            <span>Chứng chỉ hoàn thành uy tín</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="wrapper">
        <section id="categories-homepage">
            <div class="container">
                <div class="row category-row-flex">
                    <div class="col-md-12">
                        <div class="modern-section-header" style="margin-bottom: 25px;">
                            <div class="header-content">
                                <h3 class="modern-heading" style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fa fa-th-large" style="color: #2563eb;"></i>
                                    Khám phá theo danh mục
                                </h3>
                                <p class="modern-subheading">Lựa chọn các lĩnh vực học tập đa dạng để nâng cao kiến thức và phát triển kỹ năng của bạn</p>
                            </div>
                            <a href="{{ route('courses.index') }}" class="modern-view-all">
                                <span>Xem tất cả khóa học</span>
                                <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    @foreach(\App\Models\Category::where('parent_id', 0)->get() as $category)
                        @php
                            $colorNumber = preg_match('/color-(\d+)/', $category->css_classes, $matches) ? $matches[1] : (($loop->index % 8) + 1);
                        @endphp
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="category-box cat-theme-{{ $colorNumber }} wow fadeInUpQuick" data-wow-delay="0.3s">
                                <div class="icon">
                                    <i class="{{$category->css_classes}}"></i>
                                </div>
                                <div class="category-header">
                                    <a href="{{ route('courses.index', ['category_id' => $category->id]) }}" style="color: inherit; text-decoration: none; display: block; width: 100%;">
                                        <h4> {{ $category->vi_title ?: $category->title }} </h4>
                                    </a>
                                </div>
                                <div class="category-content">
                                    <ul class="subcat-list">
                                        @foreach(\App\Models\Category::where('parent_id', $category->id)->limit(6)->get() as $subCategory)
                                            @php
                                                $courseCount = \App\Models\Course::where('is_accepted', 1)->where('category_id', $subCategory->id)->count();
                                            @endphp
                                            <li class="subcat-item">
                                                <a href="{{ route('courses.index', ['sub_category_id' => $subCategory->id]) }}" class="subcat-title" title="{{ $subCategory->vi_title ?: $subCategory->title }}">
                                                    {{ $subCategory->vi_title ?: $subCategory->title }}
                                                </a>
                                                <span class="category-counter-pill" title="{{ $courseCount }} khóa học">
                                                    {{ $courseCount }} khóa học
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="category-view-all">
                                        <a href="{{ route('courses.index', ['category_id' => $category->id]) }}" class="cat-view-all-link">
                                            <span>Xem tất cả danh mục con</span>
                                            <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
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
                    <div class="col-md-12 wow fadeIn" data-wow-delay="0.3s">
                        <div class="modern-section-header">
                            <div class="header-content">
                                <h3 class="modern-heading">{{ __('titles.recommended_courses') }}</h3>
                                <p class="modern-subheading">Khóa học được tuyển chọn theo độ hài lòng cao nhất và phù hợp với xu hướng học tập</p>
                            </div>
                            <a href="{{ route('courses.index') }}" class="modern-view-all">
                                <span>Xem tất cả</span>
                                <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>

                        <div id="new-products" class="owl-carousel">
                            @foreach($recommmendCourseList as $trendCourse)
                                <div class="item">
                                    <div class="modern-course-card">
                                        <a href="{{ route('courses.show', $trendCourse->id) }}" class="card-thumb-wrap">
                                            <img src="{{ str_replace('public/', '', asset($trendCourse->course_avatar)) }}"
                                                 alt="{{ $trendCourse->title }}"
                                                 class="card-thumb-img">
                                            @if($trendCourse->category)
                                                <span class="card-cat-badge">
                                                    {{ $trendCourse->category->vi_title ?: $trendCourse->category->title }}
                                                </span>
                                            @endif
                                            @if($trendCourse->promotion_price && $trendCourse->promotion_price < $trendCourse->origin_price)
                                                @php
                                                    $discountPercent = round((($trendCourse->origin_price - $trendCourse->promotion_price) / $trendCourse->origin_price) * 100);
                                                @endphp
                                                @if($discountPercent > 0)
                                                    <span class="card-discount-badge">-{{ $discountPercent }}%</span>
                                                @endif
                                            @endif
                                            <div class="card-thumb-hover">
                                                <span class="btn-quick-view"><i class="fa fa-play-circle"></i> Xem chi tiết</span>
                                            </div>
                                        </a>

                                        <div class="card-body-content">
                                            <div class="card-instructor">
                                                <i class="fa fa-user-circle-o"></i>
                                                <span>{{ $trendCourse->user->name ?? 'Giảng viên RoadToSchool' }}</span>
                                            </div>

                                            <h4 class="card-title">
                                                <a href="{{ route('courses.show', $trendCourse->id) }}" title="{{ $trendCourse->title }}">
                                                    {{ $trendCourse->title }}
                                                </a>
                                            </h4>

                                            <div class="card-meta-row">
                                                <div class="card-rating">
                                                    <span class="star-score">{{ number_format($trendCourse->course_rate ?: 5.0, 1) }}</span>
                                                    <span class="star-icons">
                                                        @php
                                                            $rateScore = $trendCourse->course_rate ?: 5.0;
                                                        @endphp
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($rateScore >= $i)
                                                                <i class="fa fa-star text-warning"></i>
                                                            @elseif($rateScore >= $i - 0.5)
                                                                <i class="fa fa-star-half-o text-warning"></i>
                                                            @else
                                                                <i class="fa fa-star-o text-muted"></i>
                                                            @endif
                                                        @endfor
                                                    </span>
                                                </div>
                                                <div class="card-students" title="{{ number_format($trendCourse->seller ?: 0) }} học viên">
                                                    <i class="fa fa-users"></i>
                                                    <span>{{ number_format($trendCourse->seller ?: 0) }}</span>
                                                </div>
                                            </div>

                                            <div class="card-price-row">
                                                @if($trendCourse->promotion_price && $trendCourse->promotion_price < $trendCourse->origin_price)
                                                    <div class="price-wrap">
                                                        <span class="price-current">{{ format_price($trendCourse->promotion_price) }}</span>
                                                        <span class="price-original">{{ format_price($trendCourse->origin_price) }}</span>
                                                    </div>
                                                @else
                                                    <div class="price-wrap">
                                                        <span class="price-current price-normal">{{ format_price($trendCourse->origin_price) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="featured-lis featured-lis-alt">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 wow fadeIn" data-wow-delay="0.3s">
                        <div class="modern-section-header">
                            <div class="header-content">
                                <h3 class="modern-heading">{{ __('titles.most_seller_courses') }}</h3>
                                <p class="modern-subheading">Những khóa học dẫn đầu về số lượng học viên đăng ký nhiều nhất trên hệ thống</p>
                            </div>
                            <a href="{{ route('courses.index', ['filter' => 'seller|desc']) }}" class="modern-view-all">
                                <span>Xem tất cả</span>
                                <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>

                        <div id="best-products" class="owl-carousel">
                            @foreach(\App\Models\Course::orderBy('seller', 'desc')->where('is_accepted', 1)->limit(20)->get() as $trendCourse)
                                <div class="item">
                                    <div class="modern-course-card">
                                        <a href="{{ route('courses.show', $trendCourse->id) }}" class="card-thumb-wrap">
                                            <img src="{{ str_replace('public/', '', asset($trendCourse->course_avatar)) }}"
                                                 alt="{{ $trendCourse->title }}"
                                                 class="card-thumb-img">
                                            @if($trendCourse->category)
                                                <span class="card-cat-badge">
                                                    {{ $trendCourse->category->vi_title ?: $trendCourse->category->title }}
                                                </span>
                                            @endif
                                            @if($trendCourse->promotion_price && $trendCourse->promotion_price < $trendCourse->origin_price)
                                                @php
                                                    $discountPercent = round((($trendCourse->origin_price - $trendCourse->promotion_price) / $trendCourse->origin_price) * 100);
                                                @endphp
                                                @if($discountPercent > 0)
                                                    <span class="card-discount-badge">-{{ $discountPercent }}%</span>
                                                @endif
                                            @endif
                                            <div class="card-thumb-hover">
                                                <span class="btn-quick-view"><i class="fa fa-play-circle"></i> Xem chi tiết</span>
                                            </div>
                                        </a>

                                        <div class="card-body-content">
                                            <div class="card-instructor">
                                                <i class="fa fa-user-circle-o"></i>
                                                <span>{{ $trendCourse->user->name ?? 'Giảng viên RoadToSchool' }}</span>
                                            </div>

                                            <h4 class="card-title">
                                                <a href="{{ route('courses.show', $trendCourse->id) }}" title="{{ $trendCourse->title }}">
                                                    {{ $trendCourse->title }}
                                                </a>
                                            </h4>

                                            <div class="card-meta-row">
                                                <div class="card-rating">
                                                    <span class="star-score">{{ number_format($trendCourse->course_rate ?: 5.0, 1) }}</span>
                                                    <span class="star-icons">
                                                        @php
                                                            $rateScore = $trendCourse->course_rate ?: 5.0;
                                                        @endphp
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($rateScore >= $i)
                                                                <i class="fa fa-star text-warning"></i>
                                                            @elseif($rateScore >= $i - 0.5)
                                                                <i class="fa fa-star-half-o text-warning"></i>
                                                            @else
                                                                <i class="fa fa-star-o text-muted"></i>
                                                            @endif
                                                        @endfor
                                                    </span>
                                                </div>
                                                <div class="card-students" title="{{ number_format($trendCourse->seller ?: 0) }} học viên">
                                                    <i class="fa fa-users"></i>
                                                    <span>{{ number_format($trendCourse->seller ?: 0) }}</span>
                                                </div>
                                            </div>

                                            <div class="card-price-row">
                                                @if($trendCourse->promotion_price && $trendCourse->promotion_price < $trendCourse->origin_price)
                                                    <div class="price-wrap">
                                                        <span class="price-current">{{ format_price($trendCourse->promotion_price) }}</span>
                                                        <span class="price-original">{{ format_price($trendCourse->origin_price) }}</span>
                                                    </div>
                                                @else
                                                    <div class="price-wrap">
                                                        <span class="price-current price-normal">{{ format_price($trendCourse->origin_price) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
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
                    <div class="col-md-12 text-center wow fadeInDownQuick" style="margin-bottom: 35px;">
                        <h3 style="color: #ffffff; font-size: 24px; font-weight: 800; text-transform: uppercase; letter-spacing: -0.01em; margin: 0 0 8px;">
                            Giá trị khác biệt tại RoadToSchool
                        </h3>
                        <p style="color: rgba(255, 255, 255, 0.85); font-size: 14.5px; margin: 0; font-weight: 400;">
                            Hệ sinh thái đào tạo trực tuyến chuẩn chất lượng, đồng hành trọn đời cùng học viên
                        </p>
                    </div>
                </div>
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
