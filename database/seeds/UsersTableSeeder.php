<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        $files = glob('public/images/avatar/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        Schema::enableForeignKeyConstraints();

        $vnLastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý', 'Đinh', 'Đoàn'];
        $vnMiddleNames = ['Văn', 'Thị', 'Đức', 'Quốc', 'Thanh', 'Hữu', 'Đình', 'Xuân', 'Minh', 'Ngọc', 'Hải', 'Thành', 'Hoàng', 'Phương', 'Bảo', 'Anh'];
        $vnFirstNames = ['An', 'Bình', 'Cường', 'Dũng', 'Dương', 'Đạt', 'Hải', 'Hà', 'Hương', 'Huy', 'Khoa', 'Kiên', 'Lâm', 'Linh', 'Long', 'Mai', 'Minh', 'Nam', 'Nghĩa', 'Phong', 'Phúc', 'Quân', 'Quang', 'Sơn', 'Tâm', 'Thắng', 'Thành', 'Thảo', 'Thịnh', 'Trang', 'Trung', 'Tuấn', 'Tùng', 'Vinh', 'Vũ', 'Yến'];

        $vnPhonePrefixes = ['090', '091', '092', '093', '094', '096', '097', '098', '086', '088', '089', '032', '033', '034', '035', '036', '037', '038', '039', '070', '076', '077', '078', '079'];
        $getVnPhone = function () use ($vnPhonePrefixes) {
            return $vnPhonePrefixes[array_rand($vnPhonePrefixes)] . rand(1000000, 9999999);
        };

        $getVnName = function () use ($vnLastNames, $vnMiddleNames, $vnFirstNames) {
            return $vnLastNames[array_rand($vnLastNames)] . ' ' . $vnMiddleNames[array_rand($vnMiddleNames)] . ' ' . $vnFirstNames[array_rand($vnFirstNames)];
        };

        $vnAddresses = [
            'Số 15 Phố Huế, Quận Hai Bà Trưng, Hà Nội',
            'Số 42 Đường Giải Phóng, Quận Đống Đa, Hà Nội',
            'Số 88 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội',
            'Số 102 Đường Nguyễn Trãi, Quận Thanh Xuân, Hà Nội',
            'Số 256 Đường Lê Lợi, Quận 1, TP. Hồ Chí Minh',
            'Số 18 Đường Nguyễn Thị Minh Khai, Quận 3, TP. Hồ Chí Minh',
            'Số 75 Đường Trần Hưng Đạo, Quận Hải Châu, Đà Nẵng',
            'Số 120 Đường Hai Bà Trưng, Quận Ninh Kiều, Cần Thơ',
            'Số 35 Đường Điện Biên Phủ, Quận Hồng Bàng, Hải Phòng',
            'Số 45 Đường Hoàng Diệu, Quận Ba Đình, Hà Nội',
            'Số 90 Đường Điện Biên Phủ, Quận Bình Thạnh, TP. Hồ Chí Minh',
            'Số 12 Đường Lê Duẩn, Quận Thanh Khê, Đà Nẵng'
        ];

        $vnWorkplaces = [
            'Trường Đại học Bách Khoa Hà Nội, Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội',
            'Trường Đại học Công nghệ - ĐHQGHN, 144 Xuân Thủy, Cầu Giấy, Hà Nội',
            'Trường Đại học Bách Khoa TP.HCM, 268 Lý Thường Kiệt, Quận 10, TP.HCM',
            'Công ty TNHH Phần mềm FPT (FPT Software), Cầu Giấy, Hà Nội',
            'Tập đoàn Công nghiệp - Viễn thông Quân đội (Viettel), Cầu Giấy, Hà Nội',
            'Công ty Cổ phần VNG, Campus VNG, Quận 7, TP.HCM',
            'Tập đoàn Bưu chính Viễn thông Việt Nam (VNPT), Hà Nội',
            'Viện Công nghệ Thông tin - Viện Hàn lâm KH&CN Việt Nam',
            'Shopee Việt Nam, Tòa nhà Capital Place, Ba Đình, Hà Nội',
            'Công ty Cổ phần Tiki, Quận Phú Nhuận, TP.HCM'
        ];

        $vnInstructorBios = [
            'Giảng viên cao cấp với hơn 10 năm kinh nghiệm nghiên cứu và giảng dạy chuyên sâu trong lĩnh vực Công nghệ thông tin. Đã đào tạo hàng ngàn sinh viên và kỹ sư phần mềm làm việc tại các tập đoàn lớn.',
            'Chuyên gia phát triển phần mềm Full-stack và Kiến trúc sư hệ thống. Đam mê chia sẻ kiến thức thực chiến, giúp học viên làm chủ công nghệ mới và ứng dụng ngay vào dự án thực tế.',
            'Kỹ sư Trí tuệ Nhân tạo (AI) và Dữ liệu lớn (Big Data). Từng chủ trì nhiều dự án chuyển đổi số doanh nghiệp. Phương pháp giảng dạy trực quan, thực tiễn và truyền cảm hứng.',
            'Chuyên gia Thiết kế UI/UX và Phát triển sản phẩm số. Hơn 8 năm kinh nghiệm dẫn dắt các dự án thiết kế ứng dụng di động và website cho các thương hiệu hàng đầu.',
            'Lập trình viên kỳ cựu với chuyên môn sâu về Mobile App (Flutter, React Native, iOS). Nhiệt huyết trong công tác đào tạo và đồng hành cùng học viên từ cơ bản đến nâng cao.'
        ];

        $vnStudentBios = [
            'Học viên đam mê công nghệ, yêu thích khám phá lập trình và mong muốn trở thành kỹ sư phần mềm chuyên nghiệp.',
            'Sinh viên chuyên ngành CNTT, luôn chủ động học hỏi và cập nhật các kỹ năng số mới nhất để phát triển sự nghiệp.',
            'Người học chuyển ngành sang lập trình, kiên trì theo đuổi đam mê công nghệ và hoàn thiện kỹ năng thực tế qua các khóa học trực tuyến.',
            'Đang trau dồi thêm kỹ năng lập trình web và trí tuệ nhân tạo để phục vụ cho các dự án thực tế tại doanh nghiệp.'
        ];

        // 1. Super Admin
        User::create([
            'name' => 'Quản trị viên Hệ thống',
            'email' => 'super_admin@gmail.com',
            'email_verified_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'password' => Hash::make('123456'),
            'phone' => '0965818552',
            'birthday' => '1996-12-15',
            'address' => 'Số 1 Đại Cồ Việt, Quận Hai Bà Trưng, Hà Nội',
            'avatar' => 'images/default_avatar/admin.jpg',
            'is_admin' => '1',
            'personal_info' => 'Quản trị viên cấp cao của nền tảng RoadToSchool. Chịu trách nhiệm quản trị nội dung, kiểm duyệt khóa học và hỗ trợ giảng viên cùng học viên trên toàn hệ thống.',
            'working_place' => 'Trường Đại học Bách Khoa Hà Nội - Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội'
        ]);

        // 2..10. Admin Staff
        for ($temp = 2; $temp <= 10; $temp++) {
            User::create([
                'name' => $getVnName(),
                'email' => 'admin' . $temp . '@gmail.com',
                'email_verified_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                'password' => Hash::make('123456'),
                'phone' => $getVnPhone(),
                'birthday' => '199' . rand(0, 8) . '-' . sprintf('%02d', rand(1, 12)) . '-' . sprintf('%02d', rand(1, 28)),
                'address' => $vnAddresses[array_rand($vnAddresses)],
                'avatar' => 'images/default_avatar/default_avatar.jpg',
                'is_admin' => '1',
                'personal_info' => 'Thành viên ban quản trị hệ thống RoadToSchool. Phụ trách kiểm duyệt chất lượng bài giảng và hỗ trợ kỹ thuật vận hành.',
                'working_place' => $vnWorkplaces[array_rand($vnWorkplaces)],
            ]);
        }

        // 11. Instructor mẫu
        User::create([
            'name' => 'ThS. Nguyễn Văn Minh',
            'email' => 'instructor@gmail.com',
            'email_verified_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'password' => Hash::make('123456'),
            'phone' => '0912345678',
            'birthday' => '1990-08-20',
            'address' => 'Số 88 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội',
            'avatar' => 'images/default_avatar/teacher.jpg',
            'role' => 1,
            'is_admin' => '0',
            'personal_info' => 'Thạc sĩ Công nghệ Thông tin với hơn 10 năm kinh nghiệm giảng dạy lập trình Web, Di động và Trí tuệ Nhân tạo. Từng cố vấn kỹ thuật cho nhiều startup công nghệ thành công.',
            'working_place' => 'Trường Đại học Bách Khoa Hà Nội - Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội'
        ]);

        // 12. Andrew Ng
        User::create([
            'name' => 'GS. Andrew Ng (Ngô Ân)',
            'email' => 'andrew_ng@gmail.com',
            'email_verified_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'password' => Hash::make('123456'),
            'phone' => '0987654321',
            'birthday' => '1976-04-18',
            'address' => 'Quận Ba Đình, Hà Nội',
            'avatar' => 'images/default_avatar/andrew_ng.jpeg',
            'role' => 1,
            'is_admin' => '0',
            'personal_info' => '<p>Andrew Ng là Nhà đồng sáng lập Coursera và Giáo sư thỉnh giảng ngành Khoa học Máy tính tại Đại học Stanford. Khóa học Machine Learning nổi tiếng của ông đã giúp hàng triệu người trên toàn thế giới tiếp cận trí tuệ nhân tạo.<br>Năm 2011, ông dẫn dắt dự án Google Brain, phát triển các mạng nơ-ron học sâu quy mô lớn. Ông cũng từng giữ vị trí Giám đốc nhóm Trí tuệ nhân tạo tại Baidu và hiện là Nhà sáng lập Landing AI.</p>',
            'working_place' => 'Nhà sáng lập Landing AI; Đồng sáng lập Coursera; Giáo sư Đại học Stanford'
        ]);

        // 13..30. Các giảng viên khác (Instructors)
        for ($temp = 13; $temp <= 30; $temp++) {
            User::create([
                'name' => (rand(0, 1) ? 'ThS. ' : 'Kỹ sư ') . $getVnName(),
                'email' => 'instructor' . $temp . '@gmail.com',
                'email_verified_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                'password' => Hash::make('123456'),
                'phone' => $getVnPhone(),
                'birthday' => '198' . rand(2, 9) . '-' . sprintf('%02d', rand(1, 12)) . '-' . sprintf('%02d', rand(1, 28)),
                'address' => $vnAddresses[array_rand($vnAddresses)],
                'avatar' => 'images/default_avatar/default_avatar.jpg',
                'role' => 1,
                'is_admin' => '0',
                'personal_info' => $vnInstructorBios[array_rand($vnInstructorBios)],
                'working_place' => $vnWorkplaces[array_rand($vnWorkplaces)],
            ]);
        }

        // 31. Student mẫu
        User::create([
            'name' => 'Nguyễn Thu Trang',
            'email' => 'student@gmail.com',
            'email_verified_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'password' => Hash::make('123456'),
            'phone' => '0934567890',
            'birthday' => '2001-05-15',
            'address' => 'Số 102 Nguyễn Trãi, Quận Thanh Xuân, Hà Nội',
            'avatar' => 'images/default_avatar/student.jpeg',
            'role' => 2,
            'is_admin' => '0',
            'personal_info' => 'Học viên đam mê công nghệ, hiện đang theo học lộ trình Lập trình Web Full-Stack và Trí tuệ Nhân tạo tại RoadToSchool.',
            'working_place' => 'Sinh viên Trường Đại học Bách Khoa Hà Nội'
        ]);

        // 32..100. Học viên (Students)
        for ($temp = 32; $temp <= 100; $temp++) {
            User::create([
                'name' => $getVnName(),
                'email' => 'student' . $temp . '@gmail.com',
                'email_verified_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
                'password' => Hash::make('123456'),
                'phone' => $getVnPhone(),
                'birthday' => '200' . rand(0, 4) . '-' . sprintf('%02d', rand(1, 12)) . '-' . sprintf('%02d', rand(1, 28)),
                'address' => $vnAddresses[array_rand($vnAddresses)],
                'avatar' => 'images/default_avatar/default_avatar.jpg',
                'role' => 2,
                'is_admin' => '0',
                'personal_info' => $vnStudentBios[array_rand($vnStudentBios)],
                'working_place' => $vnWorkplaces[array_rand($vnWorkplaces)],
            ]);
        }
    }
}
