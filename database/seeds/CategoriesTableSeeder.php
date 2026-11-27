<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('categories')->truncate();
        Schema::enableForeignKeyConstraints();

        // Danh mục cha (Parent categories)
        Category::create([
            'title' => 'Lập trình & Phát triển',
            'vi_title' => 'Lập trình & Phát triển',
            'parent_id' => '0',
            'css_classes' => 'lnr lnr-code color-8'
        ]);
        Category::create([
            'title' => 'Kinh doanh & Khởi nghiệp',
            'vi_title' => 'Kinh doanh & Khởi nghiệp',
            'parent_id' => '0',
            'css_classes' => 'lnr lnr-briefcase color-5'
        ]);
        Category::create([
            'title' => 'CNTT & Phần mềm',
            'vi_title' => 'CNTT & Phần mềm',
            'parent_id' => '0',
            'css_classes' => 'lnr lnr-laptop-phone color-2'
        ]);
        Category::create([
            'title' => 'Thiết kế sáng tạo',
            'vi_title' => 'Thiết kế sáng tạo',
            'parent_id' => '0',
            'css_classes' => 'lnr lnr-pencil color-7'
        ]);
        Category::create([
            'title' => 'Marketing & Tiếp thị',
            'vi_title' => 'Marketing & Tiếp thị',
            'parent_id' => '0',
            'css_classes' => 'lnr lnr-briefcase color-1'
        ]);
        Category::create([
            'title' => 'Kỹ năng sống & Phát triển cá nhân',
            'vi_title' => 'Kỹ năng sống & Phát triển cá nhân',
            'parent_id' => '0',
            'css_classes' => 'lnr lnr-magic-wand color-4'
        ]);
        Category::create([
            'title' => 'Nhiếp ảnh & Quay dựng Video',
            'vi_title' => 'Nhiếp ảnh & Quay dựng Video',
            'parent_id' => '0',
            'css_classes' => 'lnr lnr-camera color-3'
        ]);
        Category::create([
            'title' => 'Sức khỏe & Thể hình',
            'vi_title' => 'Sức khỏe & Thể hình',
            'parent_id' => '0',
            'css_classes' => 'lnr lnr-heart-pulse color-6'
        ]);

        // Danh mục con: Lập trình (Parent ID = 1)
        Category::create([
            'title' => 'Phát triển Web',
            'vi_title' => 'Phát triển Web',
            'parent_id' => '1',
        ]);
        Category::create([
            'title' => 'Ứng dụng Di động',
            'vi_title' => 'Ứng dụng Di động',
            'parent_id' => '1',
        ]);
        Category::create([
            'title' => 'Ngôn ngữ Lập trình',
            'vi_title' => 'Ngôn ngữ Lập trình',
            'parent_id' => '1',
        ]);
        Category::create([
            'title' => 'Phát triển Game',
            'vi_title' => 'Phát triển Game',
            'parent_id' => '1',
        ]);
        Category::create([
            'title' => 'Cơ sở Dữ liệu',
            'vi_title' => 'Cơ sở Dữ liệu',
            'parent_id' => '1',
        ]);
        Category::create([
            'title' => 'Kiểm thử Phần mềm',
            'vi_title' => 'Kiểm thử Phần mềm',
            'parent_id' => '1',
        ]);
        Category::create([
            'title' => 'Kỹ nghệ Phần mềm',
            'vi_title' => 'Kỹ nghệ Phần mềm',
            'parent_id' => '1',
        ]);
        Category::create([
            'title' => 'Công cụ Lập trình',
            'vi_title' => 'Công cụ Lập trình',
            'parent_id' => '1',
        ]);
        Category::create([
            'title' => 'Thương mại Điện tử',
            'vi_title' => 'Thương mại Điện tử',
            'parent_id' => '1',
        ]);

        // Danh mục con: Kinh doanh (Parent ID = 2)
        Category::create([
            'title' => 'Tài chính & Đầu tư',
            'vi_title' => 'Tài chính & Đầu tư',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Khởi nghiệp & Startup',
            'vi_title' => 'Khởi nghiệp & Startup',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Kỹ năng Giao tiếp',
            'vi_title' => 'Kỹ năng Giao tiếp',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Quản trị Doanh nghiệp',
            'vi_title' => 'Quản trị Doanh nghiệp',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Kỹ năng Bán hàng',
            'vi_title' => 'Kỹ năng Bán hàng',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Chiến lược Kinh doanh',
            'vi_title' => 'Chiến lược Kinh doanh',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Quản lý Vận hành',
            'vi_title' => 'Quản lý Vận hành',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Quản lý Dự án',
            'vi_title' => 'Quản lý Dự án',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Pháp lý Doanh nghiệp',
            'vi_title' => 'Pháp lý Doanh nghiệp',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Phân tích Dữ liệu Kinh doanh',
            'vi_title' => 'Phân tích Dữ liệu Kinh doanh',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Kinh doanh Trực tuyến',
            'vi_title' => 'Kinh doanh Trực tuyến',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Quản trị Nhân sự (HR)',
            'vi_title' => 'Quản trị Nhân sự (HR)',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Sản xuất & Chuỗi cung ứng',
            'vi_title' => 'Sản xuất & Chuỗi cung ứng',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Truyền thông & Báo chí',
            'vi_title' => 'Truyền thông & Báo chí',
            'parent_id' => '2',
        ]);
        Category::create([
            'title' => 'Bất động sản',
            'vi_title' => 'Bất động sản',
            'parent_id' => '2',
        ]);

        // Danh mục con: CNTT & Phần mềm (Parent ID = 3)
        Category::create([
            'title' => 'Chứng chỉ CNTT (AWS, Cisco)',
            'vi_title' => 'Chứng chỉ CNTT (AWS, Cisco)',
            'parent_id' => '3',
        ]);
        Category::create([
            'title' => 'Mạng máy tính & Bảo mật',
            'vi_title' => 'Mạng máy tính & Bảo mật',
            'parent_id' => '3',
        ]);
        Category::create([
            'title' => 'Phần cứng & Thiết bị IoT',
            'vi_title' => 'Phần cứng & Thiết bị IoT',
            'parent_id' => '3',
        ]);
        Category::create([
            'title' => 'Hệ điều hành (Linux, Windows)',
            'vi_title' => 'Hệ điều hành (Linux, Windows)',
            'parent_id' => '3',
        ]);

        // Danh mục con: Thiết kế (Parent ID = 4)
        Category::create([
            'title' => 'Thiết kế Web (UI)',
            'vi_title' => 'Thiết kế Web (UI)',
            'parent_id' => '4',
        ]);
        Category::create([
            'title' => 'Thiết kế Đồ họa',
            'vi_title' => 'Thiết kế Đồ họa',
            'parent_id' => '4',
        ]);
        Category::create([
            'title' => 'Công cụ Thiết kế (Figma, Photoshop)',
            'vi_title' => 'Công cụ Thiết kế (Figma, Photoshop)',
            'parent_id' => '4',
        ]);
        Category::create([
            'title' => 'Trải nghiệm Người dùng (UX)',
            'vi_title' => 'Trải nghiệm Người dùng (UX)',
            'parent_id' => '4',
        ]);
        Category::create([
            'title' => 'Thiết kế Game',
            'vi_title' => 'Thiết kế Game',
            'parent_id' => '4',
        ]);
        Category::create([
            'title' => 'Tư duy Thiết kế (Design Thinking)',
            'vi_title' => 'Tư duy Thiết kế (Design Thinking)',
            'parent_id' => '4',
        ]);
        Category::create([
            'title' => 'Đồ họa 3D & Hoạt hình',
            'vi_title' => 'Đồ họa 3D & Hoạt hình',
            'parent_id' => '4',
        ]);
        Category::create([
            'title' => 'Thiết kế Thời trang',
            'vi_title' => 'Thiết kế Thời trang',
            'parent_id' => '4',
        ]);
        Category::create([
            'title' => 'Thiết kế Kiến trúc',
            'vi_title' => 'Thiết kế Kiến trúc',
            'parent_id' => '4',
        ]);
        Category::create([
            'title' => 'Thiết kế Nội thất',
            'vi_title' => 'Thiết kế Nội thất',
            'parent_id' => '4',
        ]);

        // Danh mục con: Marketing (Parent ID = 5)
        Category::create([
            'title' => 'Tiếp thị Kỹ thuật số (Digital Marketing)',
            'vi_title' => 'Tiếp thị Kỹ thuật số (Digital Marketing)',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Tối ưu hóa Tìm kiếm (SEO)',
            'vi_title' => 'Tối ưu hóa Tìm kiếm (SEO)',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Tiếp thị Mạng xã hội',
            'vi_title' => 'Tiếp thị Mạng xã hội',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Xây dựng Thương hiệu (Branding)',
            'vi_title' => 'Xây dựng Thương hiệu (Branding)',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Nhập môn Marketing',
            'vi_title' => 'Nhập môn Marketing',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Phân tích & Tự động hóa Tiếp thị',
            'vi_title' => 'Phân tích & Tự động hóa Tiếp thị',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Quan hệ Công chúng (PR)',
            'vi_title' => 'Quan hệ Công chúng (PR)',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Quảng cáo Trực tuyến (Ads)',
            'vi_title' => 'Quảng cáo Trực tuyến (Ads)',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Tiếp thị Video & Di động',
            'vi_title' => 'Tiếp thị Video & Di động',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Tiếp thị Nội dung (Content Marketing)',
            'vi_title' => 'Tiếp thị Nội dung (Content Marketing)',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Chiến lược Tăng trưởng (Growth Hacking)',
            'vi_title' => 'Chiến lược Tăng trưởng (Growth Hacking)',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Tiếp thị Liên kết (Affiliate)',
            'vi_title' => 'Tiếp thị Liên kết (Affiliate)',
            'parent_id' => '5',
        ]);
        Category::create([
            'title' => 'Tiếp thị Sản phẩm (Product Marketing)',
            'vi_title' => 'Tiếp thị Sản phẩm (Product Marketing)',
            'parent_id' => '5',
        ]);

        // Danh mục con: Kỹ năng sống (Parent ID = 6)
        Category::create([
            'title' => 'Nghệ thuật & Thủ công',
            'vi_title' => 'Nghệ thuật & Thủ công',
            'parent_id' => '6',
        ]);
        Category::create([
            'title' => 'Ẩm thực & Pha chế',
            'vi_title' => 'Ẩm thực & Pha chế',
            'parent_id' => '6',
        ]);
        Category::create([
            'title' => 'Làm đẹp & Chăm sóc Bản thân',
            'vi_title' => 'Làm đẹp & Chăm sóc Bản thân',
            'parent_id' => '6',
        ]);
        Category::create([
            'title' => 'Du lịch & Trải nghiệm',
            'vi_title' => 'Du lịch & Trải nghiệm',
            'parent_id' => '6',
        ]);
        Category::create([
            'title' => 'Kỹ năng Gaming & Streamer',
            'vi_title' => 'Kỹ năng Gaming & Streamer',
            'parent_id' => '6',
        ]);
        Category::create([
            'title' => 'Trang trí & Cải tạo Nhà cửa',
            'vi_title' => 'Trang trí & Cải tạo Nhà cửa',
            'parent_id' => '6',
        ]);
        Category::create([
            'title' => 'Chăm sóc & Huấn luyện Thú cưng',
            'vi_title' => 'Chăm sóc & Huấn luyện Thú cưng',
            'parent_id' => '6',
        ]);

        // Danh mục con: Nhiếp ảnh (Parent ID = 7)
        Category::create([
            'title' => 'Nhiếp ảnh Kỹ thuật số',
            'vi_title' => 'Nhiếp ảnh Kỹ thuật số',
            'parent_id' => '7',
        ]);
        Category::create([
            'title' => 'Kỹ thuật Nhiếp ảnh Cơ bản',
            'vi_title' => 'Kỹ thuật Nhiếp ảnh Cơ bản',
            'parent_id' => '7',
        ]);
        Category::create([
            'title' => 'Chụp ảnh Chân dung',
            'vi_title' => 'Chụp ảnh Chân dung',
            'parent_id' => '7',
        ]);
        Category::create([
            'title' => 'Công cụ Xử lý Ảnh (Lightroom)',
            'vi_title' => 'Công cụ Xử lý Ảnh (Lightroom)',
            'parent_id' => '7',
        ]);
        Category::create([
            'title' => 'Nhiếp ảnh Thương mại & Sản phẩm',
            'vi_title' => 'Nhiếp ảnh Thương mại & Sản phẩm',
            'parent_id' => '7',
        ]);
        Category::create([
            'title' => 'Quay phim & Dựng Video',
            'vi_title' => 'Quay phim & Dựng Video',
            'parent_id' => '7',
        ]);

        // Danh mục con: Sức khỏe & Thể hình (Parent ID = 8)
        Category::create([
            'title' => 'Thể hình & Gym',
            'vi_title' => 'Thể hình & Gym',
            'parent_id' => '8',
        ]);
        Category::create([
            'title' => 'Sức khỏe Tổng quát',
            'vi_title' => 'Sức khỏe Tổng quát',
            'parent_id' => '8',
        ]);
        Category::create([
            'title' => 'Thể thao Phong trào',
            'vi_title' => 'Thể thao Phong trào',
            'parent_id' => '8',
        ]);
        Category::create([
            'title' => 'Dinh dưỡng Khoa học',
            'vi_title' => 'Dinh dưỡng Khoa học',
            'parent_id' => '8',
        ]);
        Category::create([
            'title' => 'Yoga Cơ bản & Nâng cao',
            'vi_title' => 'Yoga Cơ bản & Nâng cao',
            'parent_id' => '8',
        ]);
        Category::create([
            'title' => 'Sức khỏe Tinh thần',
            'vi_title' => 'Sức khỏe Tinh thần',
            'parent_id' => '8',
        ]);
        Category::create([
            'title' => 'Chế độ Ăn kiêng Lành mạnh',
            'vi_title' => 'Chế độ Ăn kiêng Lành mạnh',
            'parent_id' => '8',
        ]);
        Category::create([
            'title' => 'Võ thuật & Kỹ năng Tự vệ',
            'vi_title' => 'Võ thuật & Kỹ năng Tự vệ',
            'parent_id' => '8',
        ]);
        Category::create([
            'title' => 'Kỹ năng Sơ cấp cứu Thoát hiểm',
            'vi_title' => 'Kỹ năng Sơ cấp cứu Thoát hiểm',
            'parent_id' => '8',
        ]);
        Category::create([
            'title' => 'Khiêu vũ & Nhảy Hiện đại',
            'vi_title' => 'Khiêu vũ & Nhảy Hiện đại',
            'parent_id' => '8',
        ]);
        Category::create([
            'title' => 'Thiền định & Thư giãn',
            'vi_title' => 'Thiền định & Thư giãn',
            'parent_id' => '8',
        ]);
    }
}
