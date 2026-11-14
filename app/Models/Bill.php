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

    public function addBill($data)
    {
        $data['payment'] = self::PAYMENT_VIETQR;
        $data['status'] = self::PENDING;
        if (isset($data['get_ads'])) {
            $data['get_ads'] = true;
        } else {
            $data['get_ads'] = false;
        }
        if (\Auth::check()) {
            $data['user_id'] = Auth::user()->id;
        }
        if (empty($data['total_amount']) && isset($data['price']) && is_array($data['price'])) {
            $data['total_amount'] = array_sum($data['price']);
        }

        $createBillResult = Bill::create($data);
        if (!$createBillResult) {
            return false;
        }
        $createdBillId = $createBillResult->id;
        $createBillCourseResult = BillCourse::createNewBillCourse($createdBillId, $data['course_id'], $data['price']);

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
