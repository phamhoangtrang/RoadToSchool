<?php

use App\Models\CourseUser;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use League\Csv\Writer;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('courses')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Machine Learning Course
        Course::create([
            'title' => 'Nhập môn Trí tuệ Nhân tạo & Machine Learning thực chiến',
            'course_avatar' => 'images/default_course_avatar/machine_learning_1.jpg',
            'course_avatar_2' => 'images/default_course_avatar/machine_learning_2.jpg',
            'course_avatar_3' => 'images/default_course_avatar/machine_learning_3.jpeg',
            'description' => '<p>Trí tuệ nhân tạo (AI) và Học máy (Machine Learning) đang làm thay đổi toàn diện thế giới công nghệ hiện đại. Từ xe tự hành, nhận diện khuôn mặt, tìm kiếm thông minh đến các hệ thống gợi ý sản phẩm hàng đầu.<br><br>Khóa học cung cấp kiến thức toàn diện từ lý thuyết nền tảng đến thực hành chuyên sâu: (1) Học có giám sát (Supervised Learning): Hồi quy tuyến tính, Logistic Regression, Cây quyết định (Decision Trees), Support Vector Machines (SVM), Mạng nơ-ron (Neural Networks). (2) Học không giám sát (Unsupervised Learning): Phân cụm dữ liệu K-Means, Giảm chiều dữ liệu PCA, Hệ thống khuyến nghị (Recommender Systems). (3) Tối ưu hóa mô hình và xử lý dữ liệu thực tế với Python, Scikit-Learn và TensorFlow.</p>',
            'lecture_numbers' => rand(8, 15),
            'duration' => rand(400, 900),
            'origin_price' => 990000,
            'promotion_price' => 499000,
            'seller' => rand(200, 1500),
            'level' => 2,
            'course_rate' => 4.8,
            'is_accepted' => '1',
            'category_id' => 9,
            'user_id' => 12, // Andrew Ng
        ]);

        // 2. Web Development Course
        Course::create([
            'title' => 'Lập trình Web Full-Stack hiện đại với Laravel & Vue.js',
            'course_avatar' => 'images/default_course_avatar/web_development_1.jpeg',
            'course_avatar_2' => 'images/default_course_avatar/web_development_2.png',
            'course_avatar_3' => 'images/default_course_avatar/web_development_3.jpg',
            'description' => '<p>Lộ trình đào tạo toàn diện để trở thành Lập trình viên Full-Stack Web chuyên nghiệp. Bạn sẽ tự tay xây dựng hệ thống web thương mại điện tử thực tế từ giao diện người dùng đến kiến trúc cơ sở dữ liệu và bảo mật máy chủ.<br><br>Nội dung khóa học bao gồm: Cấu trúc MVC, RESTful API với Laravel, Xác thực bảo mật JWT, Quản trị cơ sở dữ liệu MySQL, Tối ưu truy vấn, Xây dựng Single Page Application (SPA) với Vue.js 3, Quản lý State với Pinia, và Triển khai lên máy chủ Linux với Docker và CI/CD.</p>',
            'lecture_numbers' => rand(10, 20),
            'duration' => rand(500, 1200),
            'origin_price' => 1290000,
            'promotion_price' => 699000,
            'seller' => rand(300, 1200),
            'level' => 2,
            'course_rate' => 4.9,
            'is_accepted' => '1',
            'category_id' => 9,
            'user_id' => 11, // Nguyễn Văn Minh
        ]);

        // 3. Mobile Apps Course
        Course::create([
            'title' => 'Phát triển Ứng dụng Di động Đa nền tảng với Flutter & Dart',
            'course_avatar' => 'images/default_course_avatar/mobile_apps_1.jpg',
            'course_avatar_2' => 'images/default_course_avatar/mobile_apps_2.jpg',
            'course_avatar_3' => 'images/default_course_avatar/mobile_apps_3.jpg',
            'description' => '<p>Khóa học giúp bạn làm chủ framework Flutter của Google để xây dựng các ứng dụng di động hiệu năng cao trên cả hai nền tảng iOS và Android với một mã nguồn duy nhất.<br><br>Bạn sẽ học từ cú pháp ngôn ngữ Dart, hệ thống Widget phong phú của Flutter, Quản lý trạng thái với BLoC/Provider, Tích hợp REST API, Lưu trữ cục bộ SQLite, Tích hợp dịch vụ đám mây Firebase (Authentication, Firestore, Cloud Messaging) và hoàn thiện ứng dụng xuất bản lên App Store & Google Play.</p>',
            'lecture_numbers' => rand(8, 16),
            'duration' => rand(400, 1000),
            'origin_price' => 1490000,
            'promotion_price' => 799000,
            'seller' => rand(150, 900),
            'level' => 2,
            'course_rate' => 4.7,
            'is_accepted' => '1',
            'category_id' => 10,
            'user_id' => 13,
        ]);

        // 4. Programming Languages Course
        Course::create([
            'title' => 'Lập trình Python toàn diện từ con số 0 cho người mới bắt đầu',
            'course_avatar' => 'images/default_course_avatar/programming_languages_1.png',
            'course_avatar_2' => 'images/default_course_avatar/programming_languages_2.jpeg',
            'course_avatar_3' => 'images/default_course_avatar/programming_languages_3.jpg',
            'description' => '<p>Khóa học Python dễ hiểu nhất dành cho người mới bước chân vào ngành lập trình. Trang bị tư duy lập trình vững chắc và kỹ năng giải quyết bài toán thực tế.<br><br>Các chủ đề nổi bật: Cú pháp cơ bản, Cấu trúc dữ liệu (List, Tuple, Set, Dictionary), Lập trình hướng đối tượng (OOP), Xử lý File và Ngoại lệ, Tự động hóa công việc hàng ngày (Automation Scripting), Thu thập dữ liệu trang web (Web Scraping với BeautifulSoup) và Xử lý dữ liệu với Pandas.</p>',
            'lecture_numbers' => rand(10, 18),
            'duration' => rand(450, 850),
            'origin_price' => 890000,
            'promotion_price' => 399000,
            'seller' => rand(250, 1100),
            'level' => 1,
            'course_rate' => 4.8,
            'is_accepted' => '1',
            'category_id' => 11,
            'user_id' => 14,
        ]);

        // 5. Git & GitHub
        Course::create([
            'title' => 'Làm chủ Git & GitHub cho Lập trình viên chuyên nghiệp',
            'course_avatar' => 'images/course_avatar/git1.png',
            'course_avatar_2' => 'images/course_avatar/git2.png',
            'course_avatar_3' => 'images/course_avatar/git3.png',
            'description' => '<p>Thành thạo công cụ quản lý mã nguồn số 1 thế giới. Học cách làm việc nhóm chuyên nghiệp, giải quyết xung đột mã nguồn (merge conflict), phân nhánh tính năng (Git Flow), pull request review và tích hợp CI/CD tự động hóa kiểm thử phần mềm.</p>',
            'lecture_numbers' => 8,
            'duration' => 320,
            'origin_price' => 590000,
            'promotion_price' => 299000,
            'seller' => 450,
            'level' => 1,
            'course_rate' => 4.9,
            'is_accepted' => '1',
            'category_id' => 16, // Công cụ Lập trình
            'user_id' => 11,
        ]);

        // 6. MySQL Course
        Course::create([
            'title' => 'Thiết kế và Tối ưu hóa Cơ sở Dữ liệu Quan hệ MySQL',
            'course_avatar' => 'images/course_avatar/mysql1.jpg',
            'course_avatar_2' => 'images/course_avatar/mysql2.png',
            'course_avatar_3' => 'images/course_avatar/mysql3.png',
            'description' => '<p>Khóa học chuyên sâu về Cơ sở dữ liệu quan hệ (RDBMS). Học thiết kế lược đồ CSDL chuẩn hóa (1NF, 2NF, 3NF), viết câu lệnh SQL nâng cao (JOIN, Subquery, Window Functions), đánh chỉ mục Indexing, phân tích câu lệnh truy vấn EXPLAIN và xử lý hàng triệu bản ghi hiệu năng cao.</p>',
            'lecture_numbers' => 12,
            'duration' => 480,
            'origin_price' => 790000,
            'promotion_price' => 399000,
            'seller' => 380,
            'level' => 2,
            'course_rate' => 4.6,
            'is_accepted' => '1',
            'category_id' => 13, // Cơ sở Dữ liệu
            'user_id' => 13,
        ]);

        // 7. Node.js
        Course::create([
            'title' => 'Xây dựng RESTful API chuyên sâu với Node.js, Express & MongoDB',
            'course_avatar' => 'images/course_avatar/nodejs.png',
            'course_avatar_2' => 'images/course_avatar/nodejs2.jpeg',
            'course_avatar_3' => 'images/course_avatar/nodejs3.jpeg',
            'description' => '<p>Nắm vững kiến trúc Back-end hiện đại với hệ sinh thái Node.js. Tìm hiểu cơ chế Non-blocking I/O, Event Loop, cấu trúc dự án chuẩn MVC/Clean Architecture, xác thực phân quyền với JWT, lưu trữ CSDL phi quan hệ MongoDB với Mongoose và deploy lên môi trường Production.</p>',
            'lecture_numbers' => 14,
            'duration' => 600,
            'origin_price' => 1190000,
            'promotion_price' => 599000,
            'seller' => 620,
            'level' => 2,
            'course_rate' => 4.8,
            'is_accepted' => '1',
            'category_id' => 9,
            'user_id' => 14,
        ]);

        // 8. HTML5 & CSS3
        Course::create([
            'title' => 'Thiết kế Giao diện Web Chuẩn Responsive với HTML5 & CSS3',
            'course_avatar' => 'images/course_avatar/html.png',
            'course_avatar_2' => 'images/course_avatar/html2.png',
            'course_avatar_3' => 'images/course_avatar/html3.jpeg',
            'description' => '<p>Khóa học nền tảng không thể thiếu cho bất kỳ ai bước chân vào ngành Lập trình Web. Nắm vững cấu trúc semantic HTML5 chuẩn SEO, tạo bố cục hiện đại với Flexbox & CSS Grid, hiệu ứng chuyển động CSS Animation và giao diện tối ưu hiển thị mượt mà trên mọi thiết bị.</p>',
            'lecture_numbers' => 10,
            'duration' => 360,
            'origin_price' => 490000,
            'promotion_price' => 249000,
            'seller' => 850,
            'level' => 1,
            'course_rate' => 4.9,
            'is_accepted' => '1',
            'category_id' => 9,
            'user_id' => 15,
        ]);

        // 9. JavaScript ES6+
        Course::create([
            'title' => 'JavaScript ES6+ Toàn diện: Từ Cốt lõi đến Lập trình Nâng cao',
            'course_avatar' => 'images/course_avatar/es6.jpeg',
            'course_avatar_2' => 'images/course_avatar/es6_2.png',
            'course_avatar_3' => 'images/course_avatar/es6_3.jpeg',
            'description' => '<p>Đào sâu bản chất hoạt động của JavaScript: Scope, Closure, Hoisting, Prototype, This keyword, Event Loop, Asynchronous programming với Promise và Async/Await. Nắm trọn các cú pháp mới nhất của ES6+ để tự tin làm việc với React, Vue hoặc Node.js.</p>',
            'lecture_numbers' => 15,
            'duration' => 520,
            'origin_price' => 890000,
            'promotion_price' => 450000,
            'seller' => 740,
            'level' => 2,
            'course_rate' => 4.8,
            'is_accepted' => '1',
            'category_id' => 11,
            'user_id' => 16,
        ]);

        // Seed thêm các khóa học theo từng chuyên mục con tiếng Việt
        $courseTemplates = [
            'Khóa học %s từ Cơ bản đến Nâng cao',
            'Thực chiến %s: Xây dựng Dự án Thực tế',
            'Làm chủ %s chuyên nghiệp cho Người đi làm',
            'Cẩm nang Toàn diện về %s từ A đến Z',
            'Bí quyết và Kỹ năng Thực hành %s Đỉnh cao',
        ];

        $courseDescriptions = [
            '<p>Khóa học cung cấp lộ trình bài bản và có hệ thống về <strong>%s</strong>. Học viên sẽ được trang bị nền tảng lý thuyết vững chắc cùng hàng loạt bài tập thực hành bám sát nhu cầu tuyển dụng của doanh nghiệp.<br><br>Sau khóa học, bạn sẽ tự tin áp dụng ngay vào công việc và phát triển sự nghiệp cá nhân một cách vững chắc.</p>',
            '<p>Được thiết kế bởi các chuyên gia đầu ngành, khóa học <strong>%s</strong> tập trung vào tính ứng dụng và giải quyết các bài toán thực tiễn trong doanh nghiệp. Hướng dẫn chi tiết từng bước với sự đồng hành tận tâm của giảng viên.</p>',
            '<p>Khóa học giúp bạn nhanh chóng làm chủ <strong>%s</strong> trong thời gian ngắn nhất. Giáo trình cô đọng, dễ hiểu, cập nhật các xu hướng và công nghệ mới nhất hiện nay.</p>',
        ];

        $subCategories = Category::where('parent_id', '<>', 0)->pluck('title', 'id');
        $instructorIds = \App\Models\User::where('role', 1)->pluck('id')->toArray();

        foreach ($subCategories as $catId => $catTitle) {
            $randomCount = rand(3, 5); // Mỗi danh mục 3-5 khóa học chuẩn
            for ($temp = 0; $temp < $randomCount; $temp++) {
                $originPrice = rand(39, 189) * 10000; // 390.000 đến 1.890.000 VNĐ
                $hasDiscount = (rand(0, 1) === 1);
                $promotionPrice = $hasDiscount ? (rand(19, intval($originPrice / 10000) - 2) * 10000) : $originPrice;

                $templateTitle = $courseTemplates[array_rand($courseTemplates)];
                $title = sprintf($templateTitle, $catTitle);

                $templateDesc = $courseDescriptions[array_rand($courseDescriptions)];
                $description = sprintf($templateDesc, $catTitle);

                $courseAvatar = 'images/default_course_avatar/web_development_1.jpeg';
                $courseAvatar2 = 'images/default_course_avatar/web_development_2.png';
                $courseAvatar3 = 'images/default_course_avatar/web_development_3.jpg';

                Course::create([
                    'title' => $title,
                    'course_avatar' => $courseAvatar,
                    'course_avatar_2' => $courseAvatar2,
                    'course_avatar_3' => $courseAvatar3,
                    'description' => $description,
                    'lecture_numbers' => rand(6, 20),
                    'duration' => rand(180, 800),
                    'origin_price' => $originPrice,
                    'promotion_price' => $promotionPrice,
                    'seller' => rand(50, 800),
                    'level' => rand(1, 3),
                    'course_rate' => rand(40, 50) / 10,
                    'is_accepted' => 1,
                    'category_id' => $catId,
                    'user_id' => !empty($instructorIds) ? $instructorIds[array_rand($instructorIds)] : 11,
                ]);
            }
        }

        // Tạo dữ liệu đánh giá và quan hệ học viên - khóa học (CourseUser)
        Schema::disableForeignKeyConstraints();
        DB::table('course_user')->truncate();
        Schema::enableForeignKeyConstraints();

        $userIdList = \App\Models\User::where('role', 2)->pluck('id')->toArray();
        $courseIdList = \App\Models\Course::where('is_accepted', 1)->pluck('id')->toArray();

        $appreciateList = [
            0 => 'Khóa học chất lượng chưa tốt, cần bổ sung nội dung.',
            1 => 'Nội dung hơi sơ sài, cần thêm nhiều bài tập thực hành hơn.',
            2 => 'Kiến thức khá ổn định, bài giảng cần thêm ví dụ thực tế.',
            3 => 'Khóa học bổ ích, giảng viên nhiệt tình hỗ trợ giải đáp thắc mắc.',
            4 => 'Khóa học rất hay và dễ hiểu, áp dụng được ngay vào công việc!',
            5 => 'Xuất sắc! Bài giảng cực kỳ chi tiết và chất lượng, rất đáng để học!',
        ];

        // Tạo đánh giá cho học viên
        if (!empty($userIdList) && !empty($courseIdList)) {
            $totalReviews = min(500, count($userIdList) * 10);
            for ($temp = 0; $temp < $totalReviews; $temp++) {
                $randUserId = $userIdList[array_rand($userIdList)];
                $randCourseId = $courseIdList[array_rand($courseIdList)];

                $exists = CourseUser::where('course_id', $randCourseId)->where('user_id', $randUserId)->exists();
                if (!$exists) {
                    $rate = rand(3, 5); // Đánh giá thực tế chủ yếu từ 3 đến 5 sao
                    CourseUser::create([
                        'course_id' => $randCourseId,
                        'user_id' => $randUserId,
                        'rate' => $rate,
                        'appreciate' => $appreciateList[$rate] ?? 'Khóa học tốt',
                    ]);
                }
            }
        }
    }
}
