<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Bill extends Model
{
    protected $table = 'bills';
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_note',
        'payment',
        'get_ads',
        'status',
        'total_amount',
        'user_id'
    ];

    const PAYMENT_BY_CARD = 1;
    const CASH_ON_DELIVERY = 2;
    const PAYMENT_VIETQR = 3;

    public static $roles = [
        self::PAYMENT_BY_CARD => 'Thanh toán qua thẻ',
        self::CASH_ON_DELIVERY => 'Thanh toán khi nhận hàng (COD)',
        self::PAYMENT_VIETQR => 'Chuyển khoản VietQR',
    ];

    const PENDING = 0;
    const IN_TRANSPORT = 1;
    const ALREADY_PAID = 2;
    const ACTIVATED = 3;
    const CANCELED = 4;

    public static $status = [
        self::PENDING => 'Chờ thanh toán',
        self::IN_TRANSPORT => 'Đang xử lý',
        self::ALREADY_PAID => 'Đã thanh toán',
        self::ACTIVATED => 'Đã kích hoạt',
        self::CANCELED => 'Đã hủy',
    ];

    public function billCourses()
    {
        return $this->hasMany(BillCourse::class, 'bill_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Tạo hóa đơn từ giỏ hàng.
     * - Giá và tổng tiền luôn tính ở máy chủ từ bảng courses (không tin giá do trình duyệt gửi).
     * - Nếu học viên đã có hóa đơn "Chờ thanh toán" với đúng cùng tập khóa học thì dùng lại hóa đơn đó
     *   (giữ nguyên mã đơn RTS và mã QR) thay vì tạo hóa đơn trùng.
     */
    public function addBill($data)
    {
        $data['payment'] = self::PAYMENT_VIETQR;
        $data['status'] = self::PENDING;
        $data['get_ads'] = isset($data['get_ads']);
        if (\Auth::check()) {
            $data['user_id'] = Auth::user()->id;
        }

        // 1. Giá lấy từ cơ sở dữ liệu
        $courseIds = array_values(array_unique(array_map('intval', (array)($data['course_id'] ?? []))));
        sort($courseIds);
        if (empty($courseIds)) {
            return false;
        }
        $courses = Course::whereIn('id', $courseIds)->get()->keyBy('id');
        if ($courses->count() !== count($courseIds)) {
            return false;
        }
        $prices = [];
        foreach ($courseIds as $courseId) {
            $course = $courses[$courseId];
            $prices[] = ($course->promotion_price > 0 && $course->promotion_price < $course->origin_price)
                ? $course->promotion_price
                : $course->origin_price;
        }
        $data['total_amount'] = array_sum($prices);

        // 2. Dùng lại hóa đơn đang chờ có cùng tập khóa học
        if (!empty($data['user_id'])) {
            $pendingBills = self::where('user_id', $data['user_id'])
                ->where('status', self::PENDING)
                ->with('billCourses')
                ->orderBy('id', 'desc')
                ->get();
            foreach ($pendingBills as $pendingBill) {
                $billCourseIds = $pendingBill->billCourses->pluck('course_id')->map(function ($id) {
                    return (int)$id;
                })->unique()->sort()->values()->all();
                if ($billCourseIds === $courseIds) {
                    \DB::transaction(function () use ($pendingBill, $data, $courseIds, $prices) {
                        $pendingBill->update(array_merge(
                            array_intersect_key($data, array_flip([
                                'customer_name', 'customer_email', 'customer_phone',
                                'customer_address', 'customer_note', 'get_ads', 'total_amount',
                            ]))
                        ));
                        foreach ($pendingBill->billCourses as $billCourse) {
                            $index = array_search((int)$billCourse->course_id, $courseIds, true);
                            $billCourse->update(['price' => $prices[$index]]);
                        }
                    });

                    return $pendingBill->fresh();
                }
            }
        }

        // 3. Không có hóa đơn phù hợp: tạo mới
        $createBillResult = self::create($data);
        if (!$createBillResult) {
            return false;
        }
        $createBillCourseResult = BillCourse::createNewBillCourse($createBillResult->id, $courseIds, $prices);
        if (!$createBillCourseResult) {
            return false;
        }

        return $createBillResult;
    }

    public function activateBill()
    {
        if (in_array((int)$this->status, [self::ALREADY_PAID, self::ACTIVATED])) {
            return true;
        }

        $this->update(['status' => self::ALREADY_PAID]);
        $userId = $this->user_id;

        if ($userId) {
            $courseIdList = BillCourse::where('bill_id', $this->id)->pluck('course_id');
            foreach ($courseIdList as $courseId) {
                $checkExist = CourseUser::where('course_id', $courseId)->where('user_id', $userId)->first();
                if (!$checkExist) {
                    CourseUser::create(['user_id' => $userId, 'course_id' => $courseId]);
                    $course = Course::find($courseId);
                    if ($course) {
                        $course->update(['seller' => $course->seller + 1]);
                        $allLectures = $course->lectures()->where('is_accepted', 1)->get();
                        foreach ($allLectures as $lecture) {
                            Process::firstOrCreate([
                                'lecture_id' => $lecture->id,
                                'user_id' => $userId,
                            ], [
                                'status' => 0,
                            ]);
                        }
                    }
                }
            }

            // Xóa các khóa học đã thanh toán thành công khỏi giỏ hàng
            CartItem::where('user_id', $userId)->whereIn('course_id', $courseIdList)->delete();
        }

        return true;
    }
}
