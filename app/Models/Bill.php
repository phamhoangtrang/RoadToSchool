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

    public static $roles = [
        self::PAYMENT_BY_CARD => 'Thanh toán qua thẻ',
        self::CASH_ON_DELIVERY => 'Thanh toán khi nhận hàng (COD)',
    ];

    const PENDING = 0;
    const IN_TRANSPORT = 1;
    const ALREADY_PAID = 2;
    const ACTIVATED = 3;
    const CANCELED = 4;

    public static $status = [
        self::PENDING => 'Chờ xử lý',
        self::IN_TRANSPORT => 'Đang giao hàng',
        self::ALREADY_PAID => 'Đã thanh toán',
        self::ACTIVATED => 'Đã kích hoạt',
        self::CANCELED => 'Đã hủy',
    ];

    public function addBill($data)
    {
        $data['payment'] = self::CASH_ON_DELIVERY;
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
}
