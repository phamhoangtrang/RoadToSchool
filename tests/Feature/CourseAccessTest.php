<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Lecture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolled_student_can_view_course_and_lecture_when_progress_rows_are_missing(): void
    {
        $student = User::factory()->create();
        [$course, $lecture] = $this->createCourseAndLecture();
        CourseUser::create(['course_id' => $course->id, 'user_id' => $student->id]);

        $this->actingAs($student)
            ->get("/courses/{$course->id}")
            ->assertOk()
            ->assertSee('0%');

        $this->actingAs($student)
            ->get("/courses/{$course->id}/lectures/{$lecture->id}")
            ->assertOk()
            ->assertSee('0%');
    }

    public function test_lecture_must_belong_to_course_in_the_route(): void
    {
        $instructor = User::factory()->instructor()->create();
        [$course] = $this->createCourseAndLecture();
        [, $otherLecture] = $this->createCourseAndLecture();

        $this->actingAs($instructor)
            ->get("/courses/{$course->id}/lectures/{$otherLecture->id}")
            ->assertNotFound();
    }

    public function test_students_cannot_open_unaccepted_courses_by_guessing_the_url(): void
    {
        $student = User::factory()->create();
        [$course] = $this->createCourseAndLecture(false);

        $this->actingAs($student)
            ->get("/courses/{$course->id}")
            ->assertNotFound();
    }

    public function test_only_enrolled_students_can_rate_and_course_average_is_recalculated(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        [$course] = $this->createCourseAndLecture();
        CourseUser::create(['course_id' => $course->id, 'user_id' => $student->id]);

        $this->actingAs($otherStudent)->post("/courses/{$course->id}/postAppreciate", [
            'star' => 5,
            'appreciate_content' => 'Not enrolled',
        ])->assertNotFound();

        $this->actingAs($student)->post("/courses/{$course->id}/postAppreciate", [
            'star' => 4,
            'appreciate_content' => '<script>alert(1)</script>',
        ])->assertRedirect();

        $this->assertDatabaseHas('course_user', [
            'course_id' => $course->id,
            'user_id' => $student->id,
            'rate' => 4,
            'appreciate' => '&lt;script&gt;alert(1)&lt;/script&gt;',
        ]);
        $this->assertEquals(4, $course->fresh()->course_rate);
    }

    /** @return array{Course, Lecture} */
    private function createCourseAndLecture(bool $accepted = true): array
    {
        $instructor = User::factory()->instructor()->create();
        $category = Category::create([
            'title' => fake()->unique()->word(),
            'vi_title' => 'Danh mục',
            'parent_id' => 0,
        ]);
        $course = Course::create([
            'title' => 'Course '.fake()->unique()->word(),
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
            'is_accepted' => $accepted,
            'category_id' => $category->id,
            'user_id' => $instructor->id,
        ]);
        $lecture = Lecture::create([
            'title' => 'Lecture '.fake()->unique()->word(),
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
}
