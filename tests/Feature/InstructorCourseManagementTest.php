<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstructorCourseManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_creates_a_validated_course_with_safe_image_names(): void
    {
        Storage::fake('course_images');
        $instructor = User::factory()->instructor()->create();
        $this->grantCreatePermission($instructor);
        $parent = Category::create([
            'title' => 'Development',
            'vi_title' => 'Phát triển',
            'parent_id' => 0,
        ]);
        $category = Category::create([
            'title' => 'Web',
            'vi_title' => 'Web',
            'parent_id' => $parent->id,
        ]);

        $this->actingAs($instructor)->post('/instructor/courses', [
            'title' => 'Modern Laravel',
            'description' => '<p>Course description</p>',
            'category_id' => $category->id,
            'level' => 2,
            'course_avatar' => $this->fakeImage('unsafe name.png'),
            'course_avatar_2' => $this->fakeImage('second.png'),
            'course_avatar_3' => $this->fakeImage('third.png'),
            'user_id' => User::factory()->create()->id,
            'is_accepted' => 1,
        ])->assertRedirect('/instructor/courses');

        $course = Course::sole();
        $this->assertSame($instructor->id, $course->user_id);
        $this->assertSame(0, $course->is_accepted);
        $this->assertSame(0, $course->seller);
        foreach ([$course->course_avatar, $course->course_avatar_2, $course->course_avatar_3] as $path) {
            $this->assertStringStartsWith('public/images/course_avatar/', $path);
            $this->assertStringNotContainsString('unsafe name', $path);
            Storage::disk('course_images')->assertExists(basename($path));
        }
    }

    public function test_course_creation_rejects_parent_categories_and_non_images(): void
    {
        Storage::fake('course_images');
        $instructor = User::factory()->instructor()->create();
        $this->grantCreatePermission($instructor);
        $parent = Category::create([
            'title' => 'Development',
            'vi_title' => 'Phát triển',
            'parent_id' => 0,
        ]);

        $this->actingAs($instructor)->post('/instructor/courses', [
            'title' => 'Invalid course',
            'description' => 'Description',
            'category_id' => $parent->id,
            'level' => 9,
            'course_avatar' => UploadedFile::fake()->create('payload.php', 10, 'application/x-php'),
            'course_avatar_2' => $this->fakeImage('second.png'),
            'course_avatar_3' => $this->fakeImage('third.png'),
        ])->assertSessionHasErrors(['category_id', 'level', 'course_avatar']);

        $this->assertDatabaseCount('courses', 0);
    }

    private function grantCreatePermission(User $instructor): void
    {
        $permission = Permission::create([
            'content' => 'Create a course',
            'group_permission' => Permission::INSTRUCTOR_GROUP_PERMISSION,
        ]);
        PermissionUser::create([
            'permission_id' => $permission->id,
            'user_id' => $instructor->id,
        ]);
    }

    private function fakeImage(string $name): UploadedFile
    {
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
            true,
        );

        return UploadedFile::fake()->createWithContent($name, $png);
    }
}
