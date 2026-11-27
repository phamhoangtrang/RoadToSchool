<?php

namespace App\Http\Controllers\Instructor;

use App\Models\Course;
use App\Models\Lecture;
use App\Models\QuizElement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Embed\Embed;

class LectureController extends Controller
{
    /**
     * The dependency model instance.
     */
    protected $modelCourse;
    protected $modelLecture;
    protected $modelQuizElement;

    /**
     * Create a new controller instance.
     *
     * @param Course $course
     * @param Category $category
     * @return void
     */
    public function __construct(Course $course, Lecture $lecture, QuizElement $quizElement)
    {
        $this->modelCourse = $course;
        $this->modelLecture = $lecture;
        $this->modelQuizElement = $quizElement;
    }

    public function create($courseId)
    {
        $selectedCourse = $this->modelCourse->findOrFail($courseId);
        $allLectures = $selectedCourse->lectures;

        // Get lecture follow week and index
        $maxWeek = 0;
        foreach ($allLectures as $lecture) {
            if ($lecture->week > $maxWeek) {
                $maxWeek = $lecture->week;
            }
        }

        $lectureOutline = [];
        for ($i = 0; $i < $maxWeek; $i++) {
            $lectureOutline[$i] = $this->modelLecture->where('course_id', $courseId)->where('week', ($i + 1))->orderBy('index')->get();
        }

        return view('instructor.lectures.create', compact(
            'selectedCourse',
            'maxWeek',
            'lectureOutline'
        ));
    }

    public function getVideoDuration(Request $request)
    {
        return json_encode($this->fetchVideoMeta($request->input('url')));
    }

    /**
     * Lấy tiêu đề, mô tả, thời lượng video từ đường dẫn (Youtube...). Lỗi thì trả giá trị mặc định.
     */
    private function fetchVideoMeta($url)
    {
        $responseData = [
            'duration' => '00:00:00',
            'title' => '',
            'description' => ''
        ];

        if (empty($url)) {
            return $responseData;
        }

        try {
            $embed = new \Embed\Embed();
            $embedUrl = $embed->get($url);
            $responseData['title'] = (string)$embedUrl->title;
            $responseData['description'] = (string)$embedUrl->description;
            $oembed = $embedUrl->getOEmbed();
            if ($oembed) {
                $all = $oembed->all();
                if (!empty($all['duration'])) {
                    $responseData['duration'] = $this->covtime((string)$all['duration']);
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return $responseData;
    }

    public function store(Request $request, $courseId)
    {
        $data = $request->all();
        // Thời lượng bắt buộc có giá trị: nếu chưa lấy được thì tự lấy từ video, không được thì đặt 00:00:00
        if (empty($data['duration'])) {
            $data['duration'] = !empty($data['video_link'])
                ? $this->fetchVideoMeta($data['video_link'])['duration']
                : '00:00:00';
        }
        // Create lecture
        if (array_key_exists('video_link', $data)) {
            if ($data['positionValue'] == 0) {
                // At first
                $data['index'] = 0;
                $data['is_accepted'] = 0;
                $data['week'] = 1;
                $data['course_id'] = $courseId;
                $data['is_lecture'] = 1;
                $data['is_quiz'] = 0;
//            $lectureWeek1List = $this->modelLecture->where('course_id', $courseId)->where('week', 1)->get();
//            foreach($lectureWeek1List as $lecture) {
//                $lecture->update(['index' => ($lecture->index++)]);
//            }
                $result = $this->modelLecture->create($data);
            } else if ($data['positionValue'] == -1) {
                // At last
                $data['index'] = 0;
                $data['is_accepted'] = 0;
                $data['week'] = intval($data['week']) + 1;
                $data['course_id'] = $courseId;
                $data['is_lecture'] = 1;
                $data['is_quiz'] = 0;

                $result = $this->modelLecture->create($data);
            } else {
                $previousLecture = $this->modelLecture->findOrFail($data['positionValue']);
                $data['index'] = intval($previousLecture->index) + 1;
                $data['is_accepted'] = 0;
                $data['week'] = $previousLecture->week;
                $data['course_id'] = $courseId;
                $data['is_lecture'] = 1;
                $data['is_quiz'] = 0;

                $result = $this->modelLecture->create($data);
            }

            if ($result) {
                flash(__('messages.create_lecture_successfully'))->success();
            } else {
                flash(__('messages.create_lecture_failed'))->error();
            }
        } else {
            // Create quiz
            $createdLecture = null;
            if (isset($data['quizPositionValue']) && $data['quizPositionValue'] == 0) {
                // At first
                $data['index'] = 0;
                $data['is_accepted'] = 0;
                $data['week'] = 1;
                $data['course_id'] = $courseId;
                $data['is_lecture'] = 0;
                $data['is_quiz'] = 1;
                $createdLecture = $this->modelLecture->create($data);
            } else if (isset($data['quizPositionValue']) && $data['quizPositionValue'] == -1) {
                // At last
                $data['index'] = 0;
                $data['is_accepted'] = 0;
                $currentMaxWeek = $this->modelLecture->where('course_id', $courseId)->max('week') ?: 0;
                $data['week'] = $currentMaxWeek + 1;
                $data['course_id'] = $courseId;
                $data['is_lecture'] = 0;
                $data['is_quiz'] = 1;

                $createdLecture = $this->modelLecture->create($data);
            } else if (!empty($data['quizPositionValue'])) {
                $previousLecture = $this->modelLecture->findOrFail($data['quizPositionValue']);
                $data['index'] = intval($previousLecture->index) + 1;
                $data['is_accepted'] = 0;
                $data['week'] = $previousLecture->week;
                $data['course_id'] = $courseId;
                $data['is_lecture'] = 0;
                $data['is_quiz'] = 1;

                $createdLecture = $this->modelLecture->create($data);
            } else {
                // Vị trí mặc định nếu chưa chọn
                $currentMaxWeek = $this->modelLecture->where('course_id', $courseId)->max('week') ?: 0;
                $data['index'] = 0;
                $data['is_accepted'] = 0;
                $data['week'] = $currentMaxWeek > 0 ? $currentMaxWeek : 1;
                $data['course_id'] = $courseId;
                $data['is_lecture'] = 0;
                $data['is_quiz'] = 1;

                $createdLecture = $this->modelLecture->create($data);
            }

            $result = !empty($createdLecture);

            if ($createdLecture && !empty($data['question']) && is_array($data['question'])) {
                $questionList = $data['question'];
                foreach ($questionList as $key => $question) {
                    if (!is_null($question) && trim($question) !== '') {
                        $dataElement = [];
                        $dataElement['content'] = trim($question);
                        $dataElement['is_question'] = 1;
                        if (array_key_exists('is_multiple_choice_question_' . ($key + 1), $data)) {
                            $dataElement['is_multiple_choice'] = 1;
                        } else {
                            $dataElement['is_multiple_choice'] = 0;
                        }
                        $dataElement['is_answer'] = 0;
                        $dataElement['lecture_id'] = $createdLecture->id;
                        $createdQuestion = $this->modelQuizElement->create($dataElement);
                        $questionIndex = $key + 1;
                        for ($temp = 1; $temp <= 4; $temp++) {
                            $answerKey = 'question_' . $questionIndex . '_answer_' . $temp;
                            if (isset($data[$answerKey]) && trim($data[$answerKey]) !== '') {
                                $dataAnswer = [];
                                $dataAnswer['content'] = trim($data[$answerKey]);
                                $dataAnswer['is_question'] = 0;
                                $dataAnswer['is_answer'] = 1;
                                $dataAnswer['question_parent_id'] = $createdQuestion->id;
                                $checkboxKey = 'checkbox_question_' . $questionIndex . '_answer_' . $temp;
                                $dataAnswer['is_right_answer'] = array_key_exists($checkboxKey, $data) ? 1 : 0;
                                $dataAnswer['lecture_id'] = $createdLecture->id;
                                $this->modelQuizElement->create($dataAnswer);
                            }
                        }
                    }
                }
            }

            if ($result) {
                flash('Tạo bài trắc nghiệm thành công. Đang chờ phê duyệt từ Quản trị viên.')->success();
            } else {
                flash('Tạo bài trắc nghiệm không thành công, vui lòng thử lại.')->error();
            }
        }

        return redirect()->route('instructor.courses.show', $courseId);
    }

    function covtime($youtube_time)
    {
        preg_match_all('/(\d+)/', $youtube_time, $parts);

        // Put in zeros if we have less than 3 numbers.
        if (count($parts[0]) == 1) {
            array_unshift($parts[0], "0", "0");
        } elseif (count($parts[0]) == 2) {
            array_unshift($parts[0], "0");
        }

        $sec_init = $parts[0][2];
        $seconds = $sec_init % 60;
        $seconds_overflow = floor($sec_init / 60);

        $min_init = $parts[0][1] + $seconds_overflow;
        $minutes = ($min_init) % 60;
        $minutes_overflow = floor(($min_init) / 60);

        $hours = $parts[0][0] + $minutes_overflow;

        if ($hours != 0)
            return $hours . ':' . $minutes . ':' . $seconds;
        else
            return $minutes . ':' . $seconds;
    }
}
