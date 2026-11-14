<?php
/**
 * Created by PhpStorm.
 * User: hungphamhoang
 * Date: 31/12/2018
 * Time: 11:15
 */

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as Trail;

// Home
Breadcrumbs::for('home', function (Trail $trail) {
    $trail->push(__('titles.home'), route('home'));
});

// Home > Courses
Breadcrumbs::for('courses_index', function ($trail) {
    $trail->parent('home');
    $trail->push(__('titles.course'), route('courses.index'));
});

// Home > Courses > Course Name
Breadcrumbs::for('courses_show', function ($trail, $course) {
    $trail->parent('courses_index');
    $trail->push($course->title, route('courses.show', $course));
});

// Home > My Cart
Breadcrumbs::for('cart', function ($trail) {
    $trail->parent('home');
    $trail->push(__('titles.my_cart'), route('cart_items.index'));
});

// Home > My Course
Breadcrumbs::for('my_course', function ($trail, $userId) {
    $trail->parent('home');
    $trail->push(__('titles.my_course'), route('courses.my_course', $userId));
});

// Home > My Profile
Breadcrumbs::for('users_show', function ($trail, $user) {
    $trail->parent('home');
    $trail->push(__('titles.my_profile'), route('users.show', $user->id));
});

// Home > My Cart > Check Out
Breadcrumbs::for('checkout', function ($trail) {
    $trail->parent('cart');
    $trail->push(__('titles.checkout'), route('cart_items.checkout.get'));
});

// Home > Checkout Success
Breadcrumbs::for('checkout_success', function ($trail) {
    $trail->parent('home');
    $trail->push(__('titles.checkout_success'), route('home'));
});

// Home > Payment
Breadcrumbs::for('bills_payment', function ($trail, $bill) {
    $trail->parent('cart');
    $trail->push('Thanh toán đơn hàng #' . $bill->id, route('bills.payment', $bill->id));
});
