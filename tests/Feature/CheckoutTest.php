<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_uses_server_prices_and_only_clears_the_authenticated_users_cart(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        $course = $this->createCourse(125, 80);
        $otherCourse = $this->createCourse(70, null);
        $cartItem = CartItem::create([
            'cart_item_type' => CartItem::IN_CART_TYPE,
            'course_id' => $course->id,
            'user_id' => $student->id,
        ]);
        $otherCartItem = CartItem::create([
            'cart_item_type' => CartItem::IN_CART_TYPE,
            'course_id' => $otherCourse->id,
            'user_id' => $otherStudent->id,
        ]);

        $this->actingAs($student)->post('/cart_items/checkout', [
            'customer_name' => 'Student Name',
            'customer_email' => 'student@example.com',
            'customer_phone' => '0900000000',
            'customer_address' => 'Hanoi',
            'customer_note' => 'Please activate soon',
            'course_id' => [$otherCourse->id],
            'price' => [1],
            'total_amount' => 1,
        ])->assertOk()->assertViewIs('user.cart_items.checkout_success');

        $bill = Bill::sole();
        $this->assertSame($student->id, $bill->user_id);
        $this->assertSame('80.00', $bill->total_amount);
        $this->assertDatabaseHas('bill_course', [
            'bill_id' => $bill->id,
            'course_id' => $course->id,
            'price' => 80,
        ]);
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
        $this->assertDatabaseHas('cart_items', ['id' => $otherCartItem->id]);
    }

    public function test_users_cannot_modify_another_users_cart_item(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        $course = $this->createCourse(100, null);
        $otherCartItem = CartItem::create([
            'cart_item_type' => CartItem::IN_CART_TYPE,
            'course_id' => $course->id,
            'user_id' => $otherStudent->id,
        ]);

        $this->actingAs($student)->post('/cart_items/remove', [
            'cartItemId' => $otherCartItem->id,
        ])->assertNotFound();

        $this->assertDatabaseHas('cart_items', [
            'id' => $otherCartItem->id,
            'user_id' => $otherStudent->id,
        ]);
    }

    public function test_only_accepted_courses_and_known_cart_types_can_be_added(): void
    {
        $student = User::factory()->create();
        $unacceptedCourse = $this->createCourse(100, null, false);

        $this->actingAs($student)->post('/cart_items/create', [
            'courseId' => $unacceptedCourse->id,
            'cartItemType' => 'add-to-cart',
        ])->assertSessionHasErrors('courseId');

        $this->actingAs($student)->post('/cart_items/create', [
            'courseId' => $unacceptedCourse->id,
            'cartItemType' => 'delete-everything',
        ])->assertSessionHasErrors(['courseId', 'cartItemType']);

        $this->assertDatabaseCount('cart_items', 0);
    }

    private function createCourse(int $originPrice, ?int $promotionPrice, bool $accepted = true): Course
    {
        $instructor = User::factory()->instructor()->create();
        $category = Category::create([
            'title' => fake()->unique()->word(),
            'vi_title' => 'Danh mục',
            'parent_id' => 0,
        ]);

        return Course::create([
            'title' => 'Course '.fake()->unique()->word(),
            'course_avatar' => 'course.jpg',
            'course_avatar_2' => 'course-2.jpg',
            'course_avatar_3' => 'course-3.jpg',
            'description' => 'Course description',
            'origin_price' => $originPrice,
            'promotion_price' => $promotionPrice,
            'lecture_numbers' => 0,
            'duration' => 0,
            'seller' => 0,
            'level' => 1,
            'course_rate' => 0,
            'is_accepted' => $accepted,
            'category_id' => $category->id,
            'user_id' => $instructor->id,
        ]);
    }
}
