<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Conversation;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\Notification;
use App\Models\Process;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class InteractionSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_realtime_posts_use_authenticated_identity_and_escape_content(): void
    {
        Event::fake();
        $student = User::factory()->create();
        $impersonatedUser = User::factory()->create();
        [$course, $lecture] = $this->createCourseAndLecture();

        $this->actingAs($student)->post("/courses/{$course->id}/pusher/postComment", [
            'content' => '<script>alert(1)</script>',
            'userId' => $impersonatedUser->id,
        ])->assertOk();

        $this->assertDatabaseHas('comments', [
            'course_id' => $course->id,
            'user_id' => $student->id,
            'content' => '&lt;script&gt;alert(1)&lt;/script&gt;',
        ]);

        $this->actingAs($student)->post("/lectures/{$lecture->id}/pusher/postComment", [
            'content' => '<img src=x onerror=alert(1)>',
            'userId' => $impersonatedUser->id,
        ])->assertOk();

        $this->assertDatabaseHas('lecture_comments', [
            'lecture_id' => $lecture->id,
            'user_id' => $student->id,
            'content' => '&lt;img src=x onerror=alert(1)&gt;',
        ]);

        $this->actingAs($student)->post('/discussions/pusher/pushNewDiscussion', [
            'content' => '<b>unsafe</b>',
            'lectureId' => $lecture->id,
            'userId' => $impersonatedUser->id,
        ])->assertCreated();

        $this->assertDatabaseHas('discussions', [
            'lecture_id' => $lecture->id,
            'user_id' => $student->id,
            'content' => '&lt;b&gt;unsafe&lt;/b&gt;',
        ]);
    }

    public function test_progress_and_conversations_ignore_client_supplied_user_ids(): void
    {
        Event::fake();
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        [, $lecture] = $this->createCourseAndLecture();
        Process::create(['status' => 0, 'lecture_id' => $lecture->id, 'user_id' => $student->id]);
        Process::create(['status' => 0, 'lecture_id' => $lecture->id, 'user_id' => $otherStudent->id]);

        $this->actingAs($student)
            ->post("/lectures/{$lecture->id}/user/{$otherStudent->id}/changeProcessStatus")
            ->assertOk();

        $this->assertDatabaseHas('processes', [
            'lecture_id' => $lecture->id,
            'user_id' => $student->id,
            'status' => 1,
        ]);
        $this->assertDatabaseHas('processes', [
            'lecture_id' => $lecture->id,
            'user_id' => $otherStudent->id,
            'status' => 0,
        ]);

        $this->actingAs($student)->post('/conversations/store', [
            'user_sender_id' => $otherStudent->id,
            'content' => '<script>message</script>',
        ])->assertOk();

        $conversation = Conversation::sole();
        $this->assertSame($student->id, $conversation->user_sender_id);
        $this->assertDatabaseHas('conversation_messages', [
            'conversation_id' => $conversation->id,
            'from_id' => $student->id,
            'content' => '&lt;script&gt;message&lt;/script&gt;',
        ]);

        $this->actingAs($otherStudent)
            ->post("/conversations/{$conversation->id}/storeNewMessage", ['content' => 'Not allowed'])
            ->assertForbidden();
    }

    public function test_notifications_can_only_be_read_by_their_owner_and_ignore_client_targets(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        [$course] = $this->createCourseAndLecture();
        $commentAuthor = User::factory()->create();
        $comment = Comment::create([
            'content' => 'Course comment',
            'course_id' => $course->id,
            'user_id' => $commentAuthor->id,
        ]);
        $notification = Notification::create([
            'type' => Notification::COMMENT,
            'content' => 'New comment',
            'status' => Notification::NOT_SEEN,
            'course_id' => $course->id,
            'comment_id' => $comment->id,
            'user_id' => $student->id,
        ]);

        $this->actingAs($otherStudent)
            ->post("/notifications/{$notification->id}/changeStatus")
            ->assertNotFound();
        $this->assertSame(Notification::NOT_SEEN, $notification->fresh()->status);

        $response = $this->actingAs($student)
            ->post("/notifications/{$notification->id}/changeStatus", [
                'courseId' => 999,
                'commentId' => 999,
            ])
            ->assertOk();

        $this->assertSame(Notification::SEEN, $notification->fresh()->status);
        $this->assertSame(
            route('courses.show', $course->id).'#li-comment-'.$comment->id,
            $response->json('redirect_url'),
        );
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
            'title' => 'Secure Course',
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
            'title' => 'Secure Lecture',
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
