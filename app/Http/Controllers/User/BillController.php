<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillCourse;
use App\Models\CartItem;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * @param  User  $users
     * @return void
     */
    public function __construct(Bill $bill, CartItem $cartItem)
    {
        $this->modelBill = $bill;
        $this->modelCartItem = $cartItem;
    }

    public function getCheckout()
    {
        $courseRelations = CartItem::where('user_id', Auth::user()->id)->where('cart_item_type', CartItem::IN_CART_TYPE)->get();
        if ($courseRelations->isEmpty()) {
            abort(404);  // 404 page
        }
        $totalPriceInCart = $this->modelCartItem->getTotalOriginPriceFollowType(CartItem::IN_CART_TYPE);
        $totalOriginPriceInCart = $totalPriceInCart['origin_price'];
        $totalPromotionPriceInCart = $totalPriceInCart['promotion_price'];

        return view('user.cart_items.checkout', compact(
            'courseRelations',
            'totalOriginPriceInCart',
            'totalPromotionPriceInCart'
        ));
    }

    public function postCheckout(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_address' => ['required', 'string', 'max:255'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $data): void {
            $cartItems = CartItem::query()
                ->with('course')
                ->where('user_id', $request->user()->id)
                ->where('cart_item_type', CartItem::IN_CART_TYPE)
                ->lockForUpdate()
                ->get();

            abort_if($cartItems->isEmpty(), 404);

            $totalAmount = $cartItems->sum(fn (CartItem $item) => $item->course->promotion_price ?: $item->course->origin_price);
            $bill = $this->modelBill->create([
                ...$data,
                'payment' => Bill::CASH_ON_DELIVERY,
                'get_ads' => $request->boolean('get_ads'),
                'status' => Bill::PENDING,
                'total_amount' => $totalAmount,
                'user_id' => $request->user()->id,
            ]);

            $now = now();
            BillCourse::insert($cartItems->map(fn (CartItem $item): array => [
                'bill_id' => $bill->id,
                'course_id' => $item->course_id,
                'price' => $item->course->promotion_price ?: $item->course->origin_price,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all());

            CartItem::whereKey($cartItems->pluck('id'))->delete();
        });

        return view('user.cart_items.checkout_success');
    }
}
