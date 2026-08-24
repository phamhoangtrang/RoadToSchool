<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Lecture;
use App\Models\QuizElement;
use App\Models\QuizElementzQuizResult;
use App\Models\QuizResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_quiz_attempt_uses_authenticated_user_and_server_question_count(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        [$course, $quiz, $questions] = $this->createQuiz();
        CourseUser::create(['course_id' => $course->id, 'user_id' => $student->id]);

        $response = $this->actingAs($student)->post('/quiz_results/storeNewResult', [
            'lectureId' => $quiz->id,
            'userId' => $otherStudent->id,
            'questionCount' => 999,
        ])->assertOk();

        $quizResult = QuizResult::findOrFail((int) $response->getContent());
        $this->assertSame($student->id, $quizResult->user_id);
        $this->assertSame(count($questions), $quizResult->wrong_answer_count);
        $this->assertDatabaseCount('quiz_element_quiz_result', count($questions));

        $this->actingAs($student)->post('/quiz_results/storeNewResult', [
            'lectureId' => $quiz->id,
        ])->assertStatus(409);
    }

    public function test_students_cannot_submit_another_users_attempt_and_scoring_is_question_scoped(): void
    {
        $student = User::factory()->create();
        $otherStudent = User::factory()->create();
        [$course, $quiz, $questions, $rightAnswers] = $this->createQuiz();
        CourseUser::create(['course_id' => $course->id, 'user_id' => $student->id]);
        CourseUser::create(['course_id' => $course->id, 'user_id' => $otherStudent->id]);
        $otherResult = $this->createAttempt($otherStudent, $quiz, $questions);

        $this->actingAs($student)->post('/quiz_results', [
            'quizResultId' => $otherResult->id,
            'answer-'.$rightAnswers[0]->id => 'on',
        ])->assertNotFound();

        $response = $this->actingAs($student)->post('/quiz_results/storeNewResult', [
            'lectureId' => $quiz->id,
        ])->assertOk();
        $studentResult = QuizResult::findOrFail((int) $response->getContent());

        $this->actingAs($student)->post('/quiz_results', [
            'quizResultId' => $studentResult->id,
            'answer-'.$rightAnswers[0]->id => 'on',
            'answer-'.$rightAnswers[1]->id => 'on',
        ])->assertRedirect("/courses/{$course->id}/lectures/{$quiz->id}/getResult");

        $studentResult->refresh();
        $this->assertSame(2, $studentResult->right_answer_count);
        $this->assertSame(0, $studentResult->wrong_answer_count);

        $this->actingAs($student)
            ->get("/courses/{$course->id}/lectures/{$quiz->id}/getResult")
            ->assertOk()
            ->assertSee('2/2');
    }

    /** @return array{Course, Lecture, array<int, QuizElement>, array<int, QuizElement>} */
    private function createQuiz(): array
    {
        $instructor = User::factory()->instructor()->create();
        $category = Category::create([
            'title' => fake()->unique()->word(),
            'vi_title' => 'Danh mục',
            'parent_id' => 0,
        ]);
        $course = Course::create([
            'title' => 'Quiz Course',
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
        $quiz = Lecture::create([
            'title' => 'Secure Quiz',
            'description' => 'Quiz description',
            'duration' => '00:10:00',
            'week' => 1,
            'index' => 0,
            'is_lecture' => 0,
            'is_quiz' => 1,
            'is_accepted' => 1,
            'course_id' => $course->id,
        ]);

        $questions = [];
        $rightAnswers = [];
        foreach (['First question', 'Second question'] as $index => $content) {
            $question = QuizElement::create([
                'content' => $content,
                'is_question' => 1,
                'is_multiple_choice' => 0,
                'is_answer' => 0,
                'lecture_id' => $quiz->id,
            ]);
            QuizElement::create([
                'content' => 'Wrong answer '.$index,
                'is_question' => 0,
                'is_answer' => 1,
                'question_parent_id' => $question->id,
                'is_right_answer' => 0,
                'lecture_id' => $quiz->id,
            ]);
            $rightAnswers[] = QuizElement::create([
                'content' => 'Right answer '.$index,
                'is_question' => 0,
                'is_answer' => 1,
                'question_parent_id' => $question->id,
                'is_right_answer' => 1,
                'lecture_id' => $quiz->id,
            ]);
            $questions[] = $question;
        }

        return [$course, $quiz, $questions, $rightAnswers];
    }

    /** @param array<int, QuizElement> $questions */
    private function createAttempt(User $student, Lecture $quiz, array $questions): QuizResult
    {
        $result = QuizResult::create([
            'right_answer_count' => 0,
            'wrong_answer_count' => count($questions),
            'lecture_id' => $quiz->id,
            'user_id' => $student->id,
        ]);
        foreach ($questions as $question) {
            QuizElementzQuizResult::create([
                'quiz_element_id' => $question->id,
                'quiz_result_id' => $result->id,
            ]);
        }

        return $result;
    }
}
