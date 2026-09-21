<?php

use Illuminate\Database\Seeder;
use App\Models\Lecture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LecturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('lectures')->truncate();
        Schema::enableForeignKeyConstraints();

        // Bài giảng Khóa học 1: Machine Learning
        Lecture::create([
            'title' => 'Chào mừng bạn đến với khóa học Machine Learning!',
            'description' => 'Trí tuệ nhân tạo và Machine Learning là gì? Mỗi ngày bạn sử dụng nó hàng chục lần mà không hề hay biết: từ tìm kiếm Google, nhận diện khuôn mặt trên điện thoại, đến bộ lọc spam email. Trong bài học mở đầu này, chúng ta sẽ cùng khám phá bức tranh toàn cảnh về Machine Learning và lộ trình học tập sắp tới.',
            'video_link' => 'https://www.youtube.com/watch?v=PNp1prcWbkM',
            'duration' => '0:8:18',
            'course_id' => 1,
            'week' => 1,
            'index' => 0,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);

        Lecture::create([
            'title' => 'Tổng quan về các thuật toán Học máy hiện đại',
            'description' => 'Tìm hiểu các khái niệm nền tảng trong Machine Learning: dữ liệu huấn luyện (training data), hàm mất mát (loss function), tối ưu Gradient Descent và cách đánh giá độ chính xác của mô hình dự đoán.',
            'video_link' => 'https://www.youtube.com/watch?v=r2WmaA8orfM',
            'duration' => '0:12:54',
            'course_id' => 1,
            'week' => 1,
            'index' => 1,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);

        Lecture::create([
            'title' => 'Định nghĩa và Phân loại Machine Learning',
            'description' => 'Phân biệt ba trường phái học máy chính: Học có giám sát (Supervised Learning), Học không giám sát (Unsupervised Learning) và Học tăng cường (Reinforcement Learning). Ứng dụng thực tế của từng phương pháp.',
            'video_link' => 'https://www.youtube.com/watch?v=LF2s-n2EAmE',
            'duration' => '0:10:14',
            'course_id' => 1,
            'week' => 1,
            'index' => 2,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);

        Lecture::create([
            'title' => 'Học có giám sát: Bài toán Hồi quy và Phân loại',
            'description' => 'Đi sâu vào Supervised Learning: Tìm hiểu Hồi quy tuyến tính (Linear Regression) để dự đoán giá nhà, doanh thu và Phân loại (Classification) để phát hiện gian lận thẻ tín dụng hoặc phân loại ảnh.',
            'video_link' => 'https://www.youtube.com/watch?v=LslruK1p-70',
            'duration' => '0:15:29',
            'course_id' => 1,
            'week' => 2,
            'index' => 0,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);

        Lecture::create([
            'title' => 'Học không giám sát: Phân cụm và Giảm chiều dữ liệu',
            'description' => 'Khám phá cấu trúc ẩn trong dữ liệu chưa được gán nhãn: Thuật toán K-Means Clustering trong phân khúc khách hàng và PCA (Principal Component Analysis) để trực quan hóa dữ liệu nhiều chiều.',
            'video_link' => 'https://www.youtube.com/watch?v=CCoQ49NASQ8',
            'duration' => '0:14:13',
            'course_id' => 1,
            'week' => 2,
            'index' => 1,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);

        Lecture::create([
            'title' => 'Bài kiểm tra trắc nghiệm: Kiến thức Nền tảng Machine Learning',
            'description' => 'Bài trắc nghiệm củng cố kiến thức đã học trong 2 tuần đầu tiên về Machine Learning.',
            'video_link' => '',
            'duration' => '0:30:00',
            'course_id' => 1,
            'week' => 2,
            'index' => 2,
            'is_lecture' => 0,
            'is_quiz' => 1,
            'is_accepted' => 1,
        ]);

        // Bài giảng Khóa học 2: Laravel & Vue.js
        Lecture::create([
            'title' => 'Tổng quan về Laravel Framework và Kiến trúc MVC',
            'description' => 'Giới thiệu về hệ sinh thái PHP hiện đại và sức mạnh của Laravel Framework. Tìm hiểu mô hình Model - View - Controller, luồng xử lý Request/Response trong Laravel.',
            'video_link' => 'https://www.youtube.com/watch?v=XJwhQumKCxU',
            'duration' => '0:15:55',
            'course_id' => 2,
            'week' => 1,
            'index' => 0,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);

        Lecture::create([
            'title' => 'Cài đặt môi trường Laravel và cấu hình CSDL MySQL',
            'description' => 'Hướng dẫn cài đặt Composer, PHP 8, tạo dự án Laravel mới, cấu hình file .env và kết nối đến hệ quản trị cơ sở dữ liệu MySQL.',
            'video_link' => 'https://www.youtube.com/watch?v=AbCsV68Kzrg',
            'duration' => '0:12:40',
            'course_id' => 2,
            'week' => 1,
            'index' => 1,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);

        Lecture::create([
            'title' => 'Routing, Controller và Blade Template Engine',
            'description' => 'Tìm hiểu cách định tuyến (Routes) trong web.php và api.php, truyền dữ liệu từ Controller sang View, sử dụng Blade Layouts, Directives và Components.',
            'video_link' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY',
            'duration' => '0:18:20',
            'course_id' => 2,
            'week' => 2,
            'index' => 0,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);

        Lecture::create([
            'title' => 'Eloquent ORM: Migration, Model và Quan hệ dữ liệu',
            'description' => 'Quản lý cấu trúc bảng với Migration và Seeder. Khai báo các quan hệ trong Eloquent ORM: One-to-One, One-to-Many, Many-to-Many.',
            'video_link' => 'https://www.youtube.com/watch?v=376MQeL0pgg',
            'duration' => '0:22:15',
            'course_id' => 2,
            'week' => 2,
            'index' => 1,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);

        // Bài giảng Khóa học 5: Git & GitHub
        Lecture::create([
            'title' => 'Giới thiệu về Hệ thống Quản lý Phiên bản Git',
            'description' => 'Hiểu rõ tại sao mọi lập trình viên cần sử dụng Git. Các khái niệm Working Directory, Staging Area và Repository.',
            'video_link' => 'https://www.youtube.com/watch?v=E1U3ckBaUN8',
            'duration' => '0:11:36',
            'course_id' => 5,
            'week' => 1,
            'index' => 0,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);

        Lecture::create([
            'title' => 'Khởi tạo Repository và Các lệnh Cơ bản',
            'description' => 'Thực hành các lệnh git init, git status, git add, git commit, git log và liên kết dự án lên GitHub từ máy cá nhân.',
            'video_link' => 'https://www.youtube.com/watch?v=uBVenaol5xw',
            'duration' => '0:14:50',
            'course_id' => 5,
            'week' => 1,
            'index' => 1,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);

        Lecture::create([
            'title' => 'Làm việc với Git Branch và Giải quyết Xung đột (Conflict)',
            'description' => 'Cách tạo nhánh tính năng (feature branch), chuyển đổi nhánh, gộp nhánh (git merge) và xử lý merge conflict khi làm việc nhóm.',
            'video_link' => 'https://www.youtube.com/watch?v=c217pCBw3Ws',
            'duration' => '0:16:08',
            'course_id' => 5,
            'week' => 2,
            'index' => 0,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
        ]);
    }
}
