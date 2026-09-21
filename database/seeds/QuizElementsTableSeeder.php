<?php

use Illuminate\Database\Seeder;
use App\Models\QuizElement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QuizElementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('quiz_elements')->truncate();
        Schema::enableForeignKeyConstraints();

        // Câu hỏi 1
        QuizElement::create([
            'content' => 'Theo định nghĩa của Tom Mitchell: Một chương trình máy tính được xem là học từ kinh nghiệm E đối với tác vụ T và độ đo hiệu năng P nếu hiệu năng trên T (được đo bởi P) tăng lên theo kinh nghiệm E. Giả sử chúng ta nạp vào thuật toán một lượng lớn dữ liệu thời tiết lịch sử để học cách dự báo thời tiết ngày mai. Đại lượng P hợp lý nhất là gì?',
            'is_question' => 1,
            'is_multiple_choice' => 0,
            'is_answer' => 0,
            'lecture_id' => 6,
        ]);

        QuizElement::create([
            'content' => 'Tỷ lệ xác suất dự đoán chính xác thời tiết trong tương lai.',
            'is_question' => 0,
            'is_answer' => 1,
            'question_parent_id' => 1,
            'is_right_answer' => 1,
            'lecture_id' => 6,
        ]);

        QuizElement::create([
            'content' => 'Nhiệm vụ dự báo thời tiết.',
            'is_question' => 0,
            'is_answer' => 1,
            'question_parent_id' => 1,
            'is_right_answer' => 0,
            'lecture_id' => 6,
        ]);

        QuizElement::create([
            'content' => 'Quá trình thuật toán duyệt qua toàn bộ dữ liệu lịch sử thời tiết.',
            'is_question' => 0,
            'is_answer' => 1,
            'question_parent_id' => 1,
            'is_right_answer' => 0,
            'lecture_id' => 6,
        ]);

        QuizElement::create([
            'content' => 'Không có phương án nào đúng ở trên.',
            'is_question' => 0,
            'is_answer' => 1,
            'question_parent_id' => 1,
            'is_right_answer' => 0,
            'lecture_id' => 6,
        ]);

        // Câu hỏi 2
        QuizElement::create([
            'content' => 'Phát biểu nào sau đây là định nghĩa chuẩn xác nhất về Trí tuệ Nhân tạo & Machine Learning theo Arthur Samuel?',
            'is_question' => 1,
            'is_multiple_choice' => 0,
            'is_answer' => 0,
            'lecture_id' => 6,
        ]);

        QuizElement::create([
            'content' => 'Machine Learning là lĩnh vực nghiên cứu giúp máy tính có khả năng tự học hỏi từ dữ liệu mà không cần được lập trình tường minh từng bước.',
            'is_question' => 0,
            'is_answer' => 1,
            'question_parent_id' => 6,
            'is_right_answer' => 1,
            'lecture_id' => 6,
        ]);

        QuizElement::create([
            'content' => 'Machine Learning là ngành khoa học về lập trình hướng đối tượng cho máy tính.',
            'is_question' => 0,
            'is_answer' => 1,
            'question_parent_id' => 6,
            'is_right_answer' => 0,
            'lecture_id' => 6,
        ]);

        QuizElement::create([
            'content' => 'Machine Learning chỉ đơn thuần là việc gán nhãn dữ liệu thủ công bằng tay.',
            'is_question' => 0,
            'is_answer' => 1,
            'question_parent_id' => 6,
            'is_right_answer' => 0,
            'lecture_id' => 6,
        ]);

        QuizElement::create([
            'content' => 'Machine Learning là việc chế tạo phần cứng cho robot chuyển động.',
            'is_question' => 0,
            'is_answer' => 1,
            'question_parent_id' => 6,
            'is_right_answer' => 0,
            'lecture_id' => 6,
        ]);
    }
}