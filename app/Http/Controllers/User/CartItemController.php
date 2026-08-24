<?php

namespace App\Http\Controllers\User;

use App\Constants\CreateCartItemStatus;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillCourse;
use App\Models\CartItem;
use App\Models\CourseUser;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CartItemController extends Controller
{
    /**
     * The user model instance.
     */
    protected $modelCartItem;

    protected $modelBill;

    protected $modelCourseUser;

    protected $modelBillCourse;

    /**
     * Create a new controller instance.
     *
     * @param  User  $users
     * @return void
     */
    public function __construct(CartItem $cartItem, Bill $bill, CourseUser $courseUser, BillCourse $billCourse)
    {
        $this->modelCartItem = $cartItem;
        $this->modelBill = $bill;
        $this->modelCourseUser = $courseUser;
        $this->modelBillCourse = $billCourse;
    }

    public function index()
    {
        $courseRelationsInCart = CartItem::where('user_id', Auth::user()->id)->where('cart_item_type', CartItem::IN_CART_TYPE)->get();
        $courseRelationsInLater = CartItem::where('user_id', Auth::user()->id)->where('cart_item_type', CartItem::IN_LATER_TYPE)->get();
        $courseRelationsInWishlist = CartItem::where('user_id', Auth::user()->id)->where('cart_item_type', CartItem::IN_WISHLIST_TYPE)->get();
        $totalPriceInCart = $this->modelCartItem->getTotalOriginPriceFollowType(CartItem::IN_CART_TYPE);
        $totalOriginPriceInCart = $totalPriceInCart['origin_price'];
        $totalPromotionPriceInCart = $totalPriceInCart['promotion_price'];

        return view('user.cart_items.index', compact(
            'courseRelationsInCart',
            'courseRelationsInLater',
            'courseRelationsInWishlist',
            'totalOriginPriceInCart',
            'totalPromotionPriceInCart'
        ));
    }

    public function changeStatus(Request $requestAjax, $action)
    {
        $statusByAction = [
            'save_for_later' => CartItem::IN_LATER_TYPE,
            'move_to_wishlist' => CartItem::IN_WISHLIST_TYPE,
            'move_to_cart' => CartItem::IN_CART_TYPE,
        ];
        abort_unless($action === 'remove' || array_key_exists($action, $statusByAction), 404);
        $data = $requestAjax->validate(['cartItemId' => ['required', 'integer']]);
        $cartItem = $this->modelCartItem
            ->where('user_id', $requestAjax->user()->id)
            ->findOrFail($data['cartItemId']);

        if ($action === 'remove') {
            $cartItem->delete();

            return response()->json(true);
        }

        $cartItem->update(['cart_item_type' => $statusByAction[$action]]);

        return response()->json(['cartItem' => $cartItem->fresh()]);
    }

    public function createNewItem(Request $requestAjax)
    {
        $data = $requestAjax->validate([
            'courseId' => [
                'required',
                'integer',
                Rule::exists('courses', 'id')->where(fn ($query) => $query
                    ->where('is_accepted', 1)
                    ->whereNull('deleted_at')),
            ],
            'cartItemType' => ['required', Rule::in(['add-to-cart', 'add-to-wishlist'])],
        ]);
        $billIds = $this->modelBill
            ->where('user_id', $requestAjax->user()->id)
            ->where('status', '!=', Bill::CANCELED)
            ->pluck('id');
        $courseIds = $this->modelBillCourse->whereIn('bill_id', $billIds)->pluck('course_id')->toArray();

        if ($this->modelCartItem->where('course_id', $data['courseId'])->where('user_id', $requestAjax->user()->id)->exists()) {
            $result = CreateCartItemStatus::CART_ITEM_ALREADY;
        } elseif ($this->modelCourseUser->where('course_id', $data['courseId'])->where('user_id', $requestAjax->user()->id)->exists()) {
            $result = CreateCartItemStatus::MY_COURSE_ALREADY;
        } elseif (in_array($data['courseId'], $courseIds)) {
            $result = CreateCartItemStatus::MY_BILL_ALREADY;
        } else {
            $result = $this->modelCartItem->create([
                'course_id' => $data['courseId'],
                'user_id' => $requestAjax->user()->id,
                'cart_item_type' => $data['cartItemType'] === 'add-to-cart'
                    ? CartItem::IN_CART_TYPE
                    : CartItem::IN_WISHLIST_TYPE,
            ]);
        }

        return response()->json($result);
    }
}
