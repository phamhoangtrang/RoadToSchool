<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorLectureManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_lecture_positions_are_kept_contiguous(): void
    {
        $instructor = $this->createInstructor();
        $course = $this->createCourse($instructor);
        $first = $this->createLecture($course, 'Existing first', 0);
        $second = $this->createLecture($course, 'Existing second', 1);
        $this->grantLecturePermission($instructor);

        $this->actingAs($instructor)->post("/instructor/courses/{$course->id}/lectures/store", [
            ...$this->lecturePayload('Inserted after first', 1),
            'positionValue' => $first->id,
        ])->assertRedirect("/instructor/courses/{$course->id}");

        $this->assertWeekOutline($course, 1, [
            'Existing first',
            'Inserted after first',
            'Existing second',
        ]);

        $this->actingAs($instructor)->post("/instructor/courses/{$course->id}/lectures/store", [
            ...$this->lecturePayload('Inserted at beginning', 0),
            'positionValue' => 0,
        ])->assertRedirect("/instructor/courses/{$course->id}");

        $this->assertWeekOutline($course, 1, [
            'Inserted at beginning',
            'Existing first',
            'Inserted after first',
            'Existing second',
        ]);

        $this->actingAs($instructor)->post("/instructor/courses/{$course->id}/lectures/store", [
            ...$this->lecturePayload('First item in week two', 2),
            'positionValue' => -1,
        ])->assertRedirect("/instructor/courses/{$course->id}");

        $this->assertWeekOutline($course, 2, ['First item in week two']);
        $this->assertSame(3, $second->fresh()->index);
    }

    public function test_quiz_and_answers_are_created_atomically(): void
    {
        $instructor = $this->createInstructor();
        $course = $this->createCourse($instructor);
        $this->grantLecturePermission($instructor);

        $this->actingAs($instructor)->post("/instructor/courses/{$course->id}/lectures/store", [
            'title' => 'Knowledge check',
            'description' => 'Quiz description',
            'duration' => '15:00',
            'week' => 1,
            'quizPositionValue' => -1,
            'question' => ['Which answer is correct?'],
            'is_single_choice_question_1' => 'on',
            'question_1_answer_1' => 'Correct',
            'question_1_answer_2' => 'Wrong one',
            'question_1_answer_3' => 'Wrong two',
            'question_1_answer_4' => 'Wrong three',
            'checkbox_question_1_answer_1' => 'on',
        ])->assertRedirect("/instructor/courses/{$course->id}");

        $quiz = Lecture::where('course_id', $course->id)->sole();

        $this->assertSame('00:15:00', $quiz->duration);
        $this->assertSame(1, $quiz->is_quiz);
        $this->assertDatabaseCount('quiz_elements', 5);
        $this->assertDatabaseHas('quiz_elements', [
            'lecture_id' => $quiz->id,
            'content' => 'Correct',
            'is_right_answer' => 1,
        ]);
    }

    public function test_instructor_cannot_manage_another_instructors_course(): void
    {
        $owner = $this->createInstructor();
        $otherInstructor = $this->createInstructor();
        $course = $this->createCourse($owner);
        $this->grantLecturePermission($otherInstructor);
        $this->grant($otherInstructor, "Show a instructor's course");

        $this->actingAs($otherInstructor)
            ->get("/instructor/courses/{$course->id}")
            ->assertNotFound();

        $this->actingAs($otherInstructor)
            ->get("/instructor/courses/{$course->id}/lectures/create")
            ->assertNotFound();

        $this->actingAs($otherInstructor)->post("/instructor/courses/{$course->id}/lectures/store", [
            ...$this->lecturePayload('Unauthorized lecture', 1),
            'positionValue' => -1,
        ])->assertNotFound();

        $this->assertDatabaseMissing('lectures', ['title' => 'Unauthorized lecture']);
    }

    /** @return array<string, mixed> */
    private function lecturePayload(string $title, int $week): array
    {
        return [
            'title' => $title,
            'description' => 'Lecture description',
            'video_link' => 'https://www.youtube.com/watch?v=PNp1prcWbkM',
            'duration' => '10:00',
            'week' => $week,
        ];
    }

    /** @param array<int, string> $titles */
    private function assertWeekOutline(Course $course, int $week, array $titles): void
    {
        $outline = $course->lectures()
            ->where('week', $week)
            ->orderBy('index')
            ->get(['title', 'index']);

        $this->assertSame($titles, $outline->pluck('title')->all());
        $this->assertSame(range(0, count($titles) - 1), $outline->pluck('index')->all());
    }

    private function createInstructor(): User
    {
        return User::forceCreate([
            'name' => 'Test Instructor',
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'avatar' => 'images/default_avatar/teacher.jpg',
            'personal_info' => '',
            'role' => User::ROLE_TEACHER,
            'is_admin' => 0,
        ]);
    }

    private function createCourse(User $instructor): Course
    {
        $category = Category::forceCreate([
            'title' => 'Development',
            'vi_title' => 'Phát triển',
            'parent_id' => 0,
        ]);

        return Course::forceCreate([
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
            'is_accepted' => 1,
            'category_id' => $category->id,
            'user_id' => $instructor->id,
        ]);
    }

    private function createLecture(Course $course, string $title, int $index): Lecture
    {
        return Lecture::create([
            'title' => $title,
            'description' => 'Existing lecture',
            'video_link' => 'https://www.youtube.com/watch?v=PNp1prcWbkM',
            'duration' => '00:10:00',
            'week' => 1,
            'index' => $index,
            'is_lecture' => 1,
            'is_quiz' => 0,
            'is_accepted' => 1,
            'course_id' => $course->id,
        ]);
    }

    private function grantLecturePermission(User $user): void
    {
        $this->grant($user, 'Create a lecture in course');
    }

    private function grant(User $user, string $content): void
    {
        $permission = Permission::firstOrCreate([
            'content' => $content,
        ], [
            'group_permission' => Permission::INSTRUCTOR_GROUP_PERMISSION,
        ]);

        PermissionUser::create([
            'permission_id' => $permission->id,
            'user_id' => $user->id,
        ]);
    }
}
