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

    public function getCheckout()
    {
        $courseRelations = CartItem::where('user_id', Auth::user()->id)->where('cart_item_type', CartItem::IN_CART_TYPE)->get();
        if ($courseRelations->isEmpty()) {
            abort(404);  //404 page
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
        $request->validate([
            'customer_name' => 'required|string|max:191',
            'customer_email' => 'required|email|max:191',
            'customer_phone' => 'required|string|max:191',
            'customer_address' => 'required|string|max:191',
            'course_id' => 'required|array|min:1',
            'price' => 'required|array|min:1',
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

        CartItem::where('user_id', Auth::id())->where('cart_item_type', CartItem::IN_CART_TYPE)->delete();
        if (!$result) {
            return redirect()->back()->with('error', 'Checkout failed, please try again.');
        }

        return view('user.cart_items.checkout_success');
    }
}
