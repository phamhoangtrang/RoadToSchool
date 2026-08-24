<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Lecture;
use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_edit_and_delete_an_empty_category(): void
    {
        $admin = User::factory()->admin()->create();
        $this->grant($admin, 'View all categories');

        $this->actingAs($admin)->post('/admin/categories', [
            'title' => 'New Category',
            'parent_id' => 0,
        ])->assertRedirect('/admin/categories');

        $category = Category::where('title', 'New Category')->sole();
        $this->assertSame('New Category', $category->vi_title);

        $this->actingAs($admin)
            ->get("/admin/categories/{$category->id}/edit")
            ->assertOk();

        $this->actingAs($admin)->put("/admin/categories/{$category->id}", [
            'title' => 'Updated Category',
            'parent_id' => 0,
        ])->assertRedirect('/admin/categories');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'title' => 'Updated Category',
        ]);

        $this->actingAs($admin)
            ->delete("/admin/categories/{$category->id}")
            ->assertRedirect('/admin/categories');

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_admin_can_accept_and_soft_delete_a_course(): void
    {
        $admin = User::factory()->admin()->create();
        $instructor = User::factory()->instructor()->create();
        $category = Category::create([
            'title' => 'Development',
            'vi_title' => 'Phát triển',
            'parent_id' => 0,
        ]);
        $course = $this->createCourse($category, $instructor);
        $this->grant($admin, 'Active a course');
        $this->grant($admin, 'View all course information');

        $this->actingAs($admin)
            ->post("/admin/courses/{$course->id}/active")
            ->assertOk()
            ->assertJsonPath('is_accepted', 1);

        $this->actingAs($admin)
            ->delete("/admin/courses/{$course->id}")
            ->assertRedirect('/admin/courses');

        $this->assertSoftDeleted('courses', ['id' => $course->id]);
    }

    public function test_lecture_acceptance_is_atomic_and_idempotent(): void
    {
        $admin = User::factory()->admin()->create();
        $instructor = User::factory()->instructor()->create();
        $category = Category::create([
            'title' => 'Development',
            'vi_title' => 'Phát triển',
            'parent_id' => 0,
        ]);
        $course = $this->createCourse($category, $instructor);
        $existingLecture = $this->createLecture($course, 'Existing lecture', 0, true);
        $pendingLecture = $this->createLecture($course, 'Pending lecture', 0, false);
        foreach (User::factory()->count(2)->create() as $student) {
            CourseUser::create(['course_id' => $course->id, 'user_id' => $student->id]);
        }
        $this->grant($admin, 'Accept a lecture request');

        $this->actingAs($admin)->post('/admin/lectures/postAccept', [
            'lectureId' => $pendingLecture->id,
        ])->assertCreated();
        $this->actingAs($admin)->post('/admin/lectures/postAccept', [
            'lectureId' => $pendingLecture->id,
        ])->assertOk();

        $this->assertSame(1, $pendingLecture->fresh()->is_accepted);
        $this->assertSame(0, $pendingLecture->fresh()->index);
        $this->assertSame(1, $existingLecture->fresh()->index);
        $this->assertDatabaseCount('processes', 2);
    }

    private function createCourse(Category $category, User $instructor): Course
    {
        return Course::create([
            'title' => 'Test Course',
            'course_avatar' => 'course.jpg',
            'course_avatar_2' => 'course-2.jpg',
            'course_avatar_3' => 'course-3.jpg',
            'description' => 'Test description',
            'origin_price' => 100,
            'promotion_price' => 50,
            'lecture_numbers' => 0,
            'duration' => 0,
            'seller' => 0,
            'level' => 1,
            'course_rate' => 0,
            'is_accepted' => 0,
            'category_id' => $category->id,
            'user_id' => $instructor->id,
        ]);
    }

    private function createLecture(Course $course, string $title, int $index, bool $accepted): Lecture
    {
        return Lecture::create([
            'title' => $title,
            'description' => 'Lecture description',
            'video_link' => 'https://www.youtube.com/watch?v=PNp1prcWbkM',
            'duration' => '00:10:00',
            'week' => 1,
            'index' => $index,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => $accepted,
            'course_id' => $course->id,
        ]);
    }

    private function grant(User $user, string $content): void
    {
        $permission = Permission::firstOrCreate([
            'content' => $content,
        ], [
            'group_permission' => Permission::ADMIN_GROUP_PERMISSION,
        ]);

        PermissionUser::create([
            'permission_id' => $permission->id,
            'user_id' => $user->id,
        ]);
    }
}
