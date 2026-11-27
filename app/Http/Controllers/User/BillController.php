<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Bill;
use Auth;

class BillController extends Controller
{
    /**
     * The user model instance.
     */
    protected $modelBill;
    protected $modelCartItem;

    /**
     * Create a new controller instance.
     *
     * @param User $users
     * @return void
     */
    public function __construct(Bill $bill, CartItem $cartItem)
    {
        $this->modelBill = $bill;
        $this->modelCartItem = $cartItem;
    }

    public function getCheckout(Request $request)
    {
        $query = CartItem::where('user_id', Auth::user()->id)->where('cart_item_type', CartItem::IN_CART_TYPE);

        if ($request->has('items') && !empty($request->input('items'))) {
            $itemIds = is_array($request->input('items')) ? $request->input('items') : explode(',', $request->input('items'));
            $itemIds = array_filter(array_map('intval', $itemIds));
            if (!empty($itemIds)) {
                $query->whereIn('id', $itemIds);
            }
        }

        $courseRelations = $query->get();
        if ($courseRelations->isEmpty()) {
            return redirect()->route('cart_items.index')->with('error', 'Không tìm thấy khóa học được chọn.');
        }

        $totalOriginPriceInCart = 0;
        $totalPromotionPriceInCart = 0;
        foreach ($courseRelations as $cr) {
            $totalOriginPriceInCart += $cr->course->origin_price;
            if (!empty($cr->course->promotion_price) && $cr->course->promotion_price < $cr->course->origin_price) {
                $totalPromotionPriceInCart += $cr->course->promotion_price;
            } else {
                $totalPromotionPriceInCart += $cr->course->origin_price;
            }
        }

        return view('user.cart_items.checkout', compact(
            'courseRelations',
            'totalOriginPriceInCart',
            'totalPromotionPriceInCart'
        ));
    }

    public function postCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:191',
            'customer_email' => ['required', 'string', 'email:filter', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'max:191'],
            'customer_phone' => ['required', 'regex:/^[0-9]{10}$/'],
            'customer_address' => 'nullable|string|max:191',
            'course_id' => 'required|array|min:1',
            'price' => 'required|array|min:1',
        ], [
            'customer_email.regex' => 'Email address must be in a valid format (e.g., example@domain.com).',
            'customer_email.email' => 'Email address must be a valid email.',
            'customer_phone.regex' => 'Số điện thoại phải gồm đúng 10 chữ số (ví dụ: 0912345678).',
        ]);

        $data = $request->all();

        // Also update user profile with phone and address if empty
        $user = Auth::user();
        if ($user) {
            $userUpdates = [];
            if (empty($user->phone) && !empty($data['customer_phone'])) {
                $userUpdates['phone'] = $data['customer_phone'];
            }
            if (empty($user->address) && !empty($data['customer_address'])) {
                $userUpdates['address'] = $data['customer_address'];
            }
            if (!empty($userUpdates)) {
                $user->update($userUpdates);
            }
        }

        $result = $this->modelBill->addBill($data);

        if (!$result) {
            return redirect()->back()->with('error', 'Đặt hàng không thành công, vui lòng thử lại.');
        }

        return redirect()->route('bills.payment', $result->id);
    }

    public function getPayment($id)
    {
        $bill = Bill::with(['billCourses.course.user'])->findOrFail($id);

        if ($bill->user_id && Auth::check() && $bill->user_id !== Auth::id() && (!Auth::user()->can('check-admin'))) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        if (in_array((int)$bill->status, [Bill::ALREADY_PAID, Bill::ACTIVATED])) {
            return redirect()->route('bills.success', $bill->id);
        }

        $bankId = config('services.vietqr.bank_id', 'MB');
        $accountNo = config('services.vietqr.account_no', '44401287290165');
        $accountName = config('services.vietqr.account_name', 'PHAM HOANG TRANG');
        $orderCode = 'RTS' . $bill->id;
        $amount = (int)$bill->total_amount;

        $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo={$orderCode}&accountName=" . urlencode($accountName);

        return view('user.cart_items.payment', compact(
            'bill',
            'bankId',
            'accountNo',
            'accountName',
            'orderCode',
            'amount',
            'qrUrl'
        ));
    }

    public function checkStatus($id)
    {
        $bill = Bill::findOrFail($id);
        $isPaid = in_array((int)$bill->status, [Bill::ALREADY_PAID, Bill::ACTIVATED]);

        return response()->json([
            'status' => $bill->status,
            'is_paid' => $isPaid,
            'redirect_url' => route('bills.success', $bill->id),
        ]);
    }

    public function getSuccess($id)
    {
        $bill = Bill::with(['billCourses.course'])->findOrFail($id);

        if ($bill->user_id && Auth::check() && $bill->user_id !== Auth::id() && (!Auth::user()->can('check-admin'))) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        return view('user.cart_items.checkout_success', compact('bill'));
    }

    public function handleSepayWebhook(Request $request)
    {
        if ($request->isMethod('get')) {
            return response()->json([
                'success' => true,
                'message' => 'SePay Webhook endpoint is active and waiting for POST transactions.',
            ]);
        }

        \Log::info('SePay Webhook received:', $request->all());

        $apiKey = config('services.sepay.api_key');
        if (!empty($apiKey)) {
            $headerApiKey = $request->header('Authorization');
            if ($headerApiKey !== 'Apikey ' . $apiKey && $headerApiKey !== $apiKey) {
                \Log::warning('SePay Webhook unauthorized API key');
                return response()->json(['error' => 'Unauthorized API key'], 401);
            }
        }

        $content = (string)($request->input('content') ?? $request->input('description') ?? $request->input('code') ?? '');
        $transferAmount = (float)($request->input('transferAmount') ?? $request->input('amount_in') ?? 0);

        if (preg_match('/RTS\s*([0-9]+)/i', $content, $matches)) {
            $billId = (int)$matches[1];
            $bill = Bill::find($billId);

            if ($bill && $transferAmount >= (float)$bill->total_amount) {
                $bill->activateBill();
                \Log::info("SePay Webhook: Activated bill #{$billId} successfully");
                return response()->json([
                    'success' => true,
                    'message' => "Đã kích hoạt đơn hàng #{$billId}",
                ]);
            }
        }

        \Log::warning("SePay Webhook: No match for RTS order or amount insufficient. Content: '{$content}', Amount: {$transferAmount}");
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy đơn hàng khớp hoặc số tiền chuyển khoản không đủ',
        ]);
    }
}
