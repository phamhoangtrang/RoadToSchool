<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\BillCourse;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBillStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_activation_and_cancellation_are_idempotent(): void
    {
        $admin = User::factory()->admin()->create();
        $this->grantUpdatePermission($admin);
        $student = User::factory()->create();
        [$course, $lecture] = $this->createCourseAndLecture();
        $bill = $this->createBill($student, $course);

        $this->actingAs($admin)->post('/admin/bills/status/update', [
            'billId' => $bill->id,
            'statusId' => Bill::ACTIVATED,
        ])->assertOk();
        $this->actingAs($admin)->post('/admin/bills/status/update', [
            'billId' => $bill->id,
            'statusId' => Bill::ACTIVATED,
        ])->assertOk();

        $this->assertDatabaseCount('course_user', 1);
        $this->assertDatabaseCount('processes', 1);
        $this->assertDatabaseHas('processes', [
            'lecture_id' => $lecture->id,
            'user_id' => $student->id,
            'status' => 0,
        ]);
        $this->assertSame(1, $course->fresh()->seller);

        $this->actingAs($admin)->post('/admin/bills/status/update', [
            'billId' => $bill->id,
            'statusId' => Bill::CANCELED,
        ])->assertOk();
        $this->actingAs($admin)->post('/admin/bills/status/update', [
            'billId' => $bill->id,
            'statusId' => Bill::CANCELED,
        ])->assertOk();

        $this->assertDatabaseCount('course_user', 0);
        $this->assertDatabaseCount('processes', 0);
        $this->assertSame(0, $course->fresh()->seller);
    }

    public function test_invalid_status_does_not_modify_the_bill(): void
    {
        $admin = User::factory()->admin()->create();
        $this->grantUpdatePermission($admin);
        $student = User::factory()->create();
        [$course] = $this->createCourseAndLecture();
        $bill = $this->createBill($student, $course);

        $this->actingAs($admin)->post('/admin/bills/status/update', [
            'billId' => $bill->id,
            'statusId' => 999,
        ])->assertSessionHasErrors('statusId');

        $this->assertSame(Bill::PENDING, $bill->fresh()->status);
        $this->assertDatabaseCount('course_user', 0);
    }

    private function grantUpdatePermission(User $admin): void
    {
        $permission = Permission::create([
            'content' => 'Update a bill status',
            'group_permission' => Permission::ADMIN_GROUP_PERMISSION,
        ]);
        PermissionUser::create([
            'permission_id' => $permission->id,
            'user_id' => $admin->id,
        ]);
    }

    /** @return array{Course, Lecture} */
    private function createCourseAndLecture(): array
    {
        $instructor = User::factory()->instructor()->create();
        $category = Category::create([
            'title' => fake()->unique()->word(),
            'vi_title' => 'Danh mục',
            'parent_id' => 0,
        ]);
        $course = Course::create([
            'title' => 'Bill Course',
            'course_avatar' => 'course.jpg',
            'course_avatar_2' => 'course-2.jpg',
            'course_avatar_3' => 'course-3.jpg',
            'description' => 'Course description',
            'origin_price' => 100,
            'promotion_price' => 50,
            'lecture_numbers' => 1,
            'duration' => 10,
            'seller' => 0,
            'level' => 1,
            'course_rate' => 0,
            'is_accepted' => 1,
            'category_id' => $category->id,
            'user_id' => $instructor->id,
        ]);
        $lecture = Lecture::create([
            'title' => 'Bill Lecture',
            'description' => 'Lecture description',
            'video_link' => 'https://www.youtube.com/watch?v=PNp1prcWbkM',
            'duration' => '00:10:00',
            'week' => 1,
            'index' => 0,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
            'course_id' => $course->id,
        ]);

        return [$course, $lecture];
    }

    private function createBill(User $student, Course $course): Bill
    {
        $bill = Bill::create([
            'customer_name' => $student->name,
            'customer_email' => $student->email,
            'customer_phone' => $student->phone,
            'customer_address' => $student->address,
            'customer_note' => null,
            'payment' => Bill::CASH_ON_DELIVERY,
            'get_ads' => false,
            'status' => Bill::PENDING,
            'total_amount' => 50,
            'user_id' => $student->id,
        ]);
        BillCourse::create([
            'bill_id' => $bill->id,
            'course_id' => $course->id,
            'price' => 50,
        ]);

        return $bill;
    }
}
