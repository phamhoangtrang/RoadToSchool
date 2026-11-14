<div class="courses-search-hero">
    <div class="container">
        <div class="courses-hero-content">
            <h1 class="courses-hero-title">Khám Phá & Học Tập Cùng Road To School</h1>
            <p class="courses-hero-subtitle">Hơn hàng trăm khóa học lập trình, công nghệ và kỹ năng số được xây dựng bài bản</p>
        </div>
    </div>
</div>

@if(isset($categoryId))
    @php
        $currentCat = \App\Models\Category::find($categoryId);
        $parentCategory = null;
        $categoryList = collect();
        $activeCategoryId = null;

        if ($currentCat) {
            if ($currentCat->parent_id === 0) {
                $parentCategory = $currentCat;
                $categoryList = \App\Models\Category::where('parent_id', $currentCat->id)->get();
                $activeCategoryId = null;
            } else {
                $parentCategory = \App\Models\Category::find($currentCat->parent_id);
                if ($parentCategory) {
                    $categoryList = \App\Models\Category::where('parent_id', $parentCategory->id)->get();
                }
                $activeCategoryId = $currentCat->id;
            }
        }
    @endphp

    @if($parentCategory)
        <div class="category-filter-section">
            <div class="container">
                <div class="category-header-wrap text-center">
                    <span class="cat-pill-badge">
                        <i class="fa fa-folder-open text-primary mr-1"></i> DANH MỤC KHÓA HỌC
                    </span>
                    <h2 class="category-page-title">
                        Khóa học thuộc danh mục <span class="title-highlight">{{ $parentCategory->vi_title ?: $parentCategory->title }}</span>
                    </h2>
                    <p class="category-page-desc text-muted">
                        Lọc theo từng chuyên ngành dưới đây để dễ dàng tìm kiếm lộ trình học tập phù hợp nhất
                    </p>
                </div>

                <div class="subcat-chips-container">
                    <!-- Chip Tất cả -->
                    @php
                        $subCatIds = $categoryList->pluck('id')->toArray();
                        $allCatIds = array_merge([$parentCategory->id], $subCatIds);
                        $totalInParent = \App\Models\Course::where('is_accepted', 1)
                            ->whereIn('category_id', $allCatIds)
                            ->count();
                    @endphp
                    <a href="{{ route('courses.index', ['category_id' => $parentCategory->id]) }}"
                       class="subcat-chip {{ empty($activeCategoryId) ? 'active' : '' }}">
                        <span class="chip-name">Tất cả chuyên ngành</span>
                        <span class="chip-badge">{{ $totalInParent }}</span>
                    </a>

                    <!-- Các chuyên ngành con -->
                    @foreach($categoryList as $category)
                        @php
                            $courseCount = \App\Models\Course::where('is_accepted', 1)->where('category_id', $category->id)->count();
                            $isActive = (isset($activeCategoryId) && (int)$activeCategoryId === $category->id);
                        @endphp
                        <a href="{{ route('courses.index', ['sub_category_id' => $category->id]) }}"
                           class="subcat-chip {{ $isActive ? 'active' : '' }}">
                            <span class="chip-name">{{ $category->vi_title ?: $category->title }}</span>
                            <span class="chip-badge">{{ $courseCount }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endif

<style>
    /* Modern Search Hero */
    .courses-search-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #0f2744 100%);
        padding: 40px 0 36px 0;
        position: relative;
        overflow: hidden;
        color: #ffffff;
    }
    .courses-search-hero::before {
        content: '';
        position: absolute;
        top: -80px;
        right: -80px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.22) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .courses-search-hero::after {
        content: '';
        position: absolute;
        bottom: -60px;
        left: -60px;
        width: 260px;
        height: 260px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.18) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .courses-hero-content {
        text-align: center;
        margin-bottom: 24px;
        position: relative;
        z-index: 2;
    }
    .courses-hero-title {
        font-size: 26px;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 6px 0;
        letter-spacing: -0.3px;
    }
    .courses-hero-subtitle {
        font-size: 14.5px;
        color: #94a3b8;
        margin: 0;
        font-weight: 400;
    }

    /* Modern Search Card */
    .modern-search-card {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
        padding: 10px 14px;
        position: relative;
        z-index: 3;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .modern-search-form {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        flex-wrap: wrap;
    }
    .search-field-item {
        position: relative;
        display: flex;
        align-items: center;
    }
    .search-field-icon {
        position: absolute;
        left: 14px;
        color: #94a3b8;
        font-size: 14px;
        pointer-events: none;
        z-index: 2;
    }
    .search-field-control {
        width: 100%;
        height: 44px;
        padding: 8px 14px 8px 38px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #1e293b;
        font-size: 13.5px;
        transition: all 0.2s ease;
        outline: none;
        box-sizing: border-box;
    }
    .search-field-control:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .custom-select-styled {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2212%22%20height%3D%228%22%20viewBox%3D%220%200%2012%208%22%20fill%3D%22none%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M1%201.5L6%206.5L11%201.5%22%20stroke%3D%22%2364748B%22%20stroke-width%3D%221.8%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
        cursor: pointer;
    }

    .flex-keyword { flex: 3; min-width: 240px; }
    .flex-btn { flex: 1.2; min-width: 130px; }

    .search-submit-btn {
        width: 100%;
        height: 44px;
        border-radius: 10px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 14px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    .search-submit-btn:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
    }

    /* Category Filter Section */
    .category-filter-section {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 34px 0 30px 0;
    }
    .category-header-wrap {
        margin-bottom: 22px;
    }
    .cat-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        background: #eff6ff;
        color: #2563eb;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }
    .category-page-title {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px 0;
        line-height: 1.3;
    }
    .title-highlight {
        color: #2563eb;
        position: relative;
    }
    .category-page-desc {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    /* Subcategory Chips */
    .subcat-chips-container {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 10px;
        max-width: 1050px;
        margin: 0 auto;
    }
    .subcat-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        text-decoration: none !important;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }
    .subcat-chip:hover {
        background: #ffffff;
        color: #2563eb;
        border-color: #93c5fd;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.08);
    }
    .subcat-chip.active {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff !important;
        border-color: #2563eb;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        transform: translateY(-1px);
    }
    .subcat-chip .chip-name {
        line-height: 1;
    }
    .subcat-chip .chip-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 10px;
        background: #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        transition: all 0.2s ease;
    }
    .subcat-chip:hover .chip-badge {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .subcat-chip.active .chip-badge {
        background: rgba(255, 255, 255, 0.28);
        color: #ffffff;
    }

    @media (max-width: 991px) {
        .flex-keyword, .flex-btn {
            flex: 100%;
        }
        .courses-hero-title {
            font-size: 22px;
        }
        .category-page-title {
            font-size: 20px;
        }
    }
</style>
