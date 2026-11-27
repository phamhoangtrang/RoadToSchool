<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Course;

class CourseController extends Controller
{
    protected $modelCourse;

    public function __construct(Course $course)
    {
        $this->modelCourse = $course;
    }

    public function index()
    {
        // Ưu tiên hiển thị các khóa học chờ duyệt (is_accepted = 0) lên đầu danh sách
        $courses = Course::with(['category', 'user'])
            ->orderBy('is_accepted', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.courses.index', compact('courses'));
    }

    public function acceptCourse(Request $request, $courseId)
    {
        $selectedCourse = $this->modelCourse->findOrFail($courseId);
        $selectedCourse->update(['is_accepted' => 1]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'id' => $selectedCourse->id,
                'title' => $selectedCourse->title,
                'is_accepted' => 1,
            ]);
        }

        flash('Khóa học "' . $selectedCourse->title . '" đã được phê duyệt thành công!')->success();
        return redirect()->back();
    }

    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'origin_price' => 'required|numeric|min:0',
            'promotion_price' => 'nullable|numeric|min:0',
        ], [
            'origin_price.required' => 'Vui lòng nhập giá gốc.',
            'origin_price.numeric' => 'Giá gốc phải là số hợp lệ.',
            'origin_price.min' => 'Giá gốc không được là số âm.',
            'promotion_price.numeric' => 'Giá khuyến mãi phải là số hợp lệ.',
            'promotion_price.min' => 'Giá khuyến mãi không được là số âm.',
        ]);

        $course = Course::findOrFail($id);
        $originPrice = floatval($request->input('origin_price'));
        $promotionPrice = $request->filled('promotion_price') && floatval($request->input('promotion_price')) > 0
            ? floatval($request->input('promotion_price'))
            : null;

        if ($promotionPrice !== null && $promotionPrice >= $originPrice && $originPrice > 0) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Giá khuyến mãi (' . number_format($promotionPrice, 0, ',', '.') . ' đ) phải nhỏ hơn giá gốc (' . number_format($originPrice, 0, ',', '.') . ' đ).'
                ], 422);
            }
            flash('Giá khuyến mãi phải nhỏ hơn giá gốc.')->error();
            return redirect()->back();
        }

        $course->update([
            'origin_price' => $originPrice,
            'promotion_price' => $promotionPrice,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Đã cập nhật giá bán thành công cho khóa học: ' . $course->title,
                'course' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'origin_price' => $originPrice,
                    'promotion_price' => $promotionPrice,
                    'formatted_origin_price' => number_format($originPrice, 0, ',', '.') . ' đ',
                    'formatted_promotion_price' => $promotionPrice ? number_format($promotionPrice, 0, ',', '.') . ' đ' : null,
                    'is_free' => ($originPrice == 0 && empty($promotionPrice)),
                ]
            ]);
        }

        flash('Đã cập nhật giá bán khóa học thành công!')->success();
        return redirect()->back();
    }

    public function destroy($id)
    {
        try {
            $course = Course::findOrFail($id);
            $courseTitle = $course->title;

            // Xóa dọn dẹp các dữ liệu liên kết an toàn
            // 1. Bài giảng & tiến trình / quiz / bình luận con
            $lectures = $course->lectures;
            foreach ($lectures as $lecture) {
                \App\Models\Process::where('lecture_id', $lecture->id)->delete();
                \App\Models\LectureComment::where('lecture_id', $lecture->id)->delete();
                \App\Models\QuizElement::where('lecture_id', $lecture->id)->delete();
                $lecture->delete();
            }

            // 2. Bình luận khóa học
            \App\Models\Comment::where('course_id', $id)->delete();

            // 3. Học viên ghi danh
            \App\Models\CourseUser::where('course_id', $id)->delete();

            // 4. Giỏ hàng
            \App\Models\CartItem::where('course_id', $id)->delete();

            // 5. Chi tiết hóa đơn
            \App\Models\BillCourse::where('course_id', $id)->delete();

            // 6. Xóa khóa học
            $course->delete();

            flash('Đã xóa thành công khóa học: ' . $courseTitle)->success();
        } catch (\Exception $e) {
            flash('Không thể xóa khóa học: ' . $e->getMessage())->error();
        }

        return redirect()->route('admin.courses.index');
    }
}
