@extends('user.layouts.default')

@section('title')
@endsection

@section('inline_styles')
@endsection

@section('content')
    <section id="content">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-10 col-md-offset-1">
                    <div class="page-ads box">
                        <h2 class="title-2">{{ $lecture->title }}</h2>
                        <div class="row search-bar mb30 red-bg">
                            <div class="advanced-search">
                                <div class="col-md-6 col-sm-12 search-col">
                                    <span class="hidden-sm hidden-xs"
                                          style="color: white">Course: <strong>{{ $lecture->course->title }}</strong> </span>

                                </div>
                                <div class="col-md-3 col-sm-12 search-col">
                                    <span class="hidden-sm hidden-xs"
                                          style="color: white">Week: <strong>{{ $lecture->week }}</strong> </span>
                                </div>
                                <div class="col-md-3 col-sm-12 search-col">
                                    <span class="hidden-sm hidden-xs"
                                          style="color: white">Your result: <strong>{{ $quizResult->right_answer_count . '/' . ($quizResult->right_answer_count + $quizResult->wrong_answer_count) }}</strong> </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @foreach($allQuestion as $key => $question)
                        <br>
                        <div class="form-group">
                            <div class="page-ads box">
                                <div class="form-group mb30 clearfix" style="padding: 15px 30px 15px 30px">
                                    <label class="control-label" for="textarea">
                                        <h3>
                                            Quiz {{ $key + 1 }} [{!! $question->checkResult ? '<span style="color:green"><i class="fa fa-check" aria-hidden="true"></i> True</span>' : '<span style="color:red"><i class="fa fa-times" aria-hidden="true"></i> False</span>' !!}]
                                        </h3>
                                    </label>
                                    <p id="question-{{ $question->id }}-content" class="question-content"
                                       data-multi="{{ $question->is_multiple_choice }}">{{ $question->content }}</p>
                                    <div class="form-inline">
                                        @foreach($question->answer as $answer)
                                            <div class="checkbox form-control-static">
                                                <label
                                                        style="@if(in_array($answer->id, $question->trueAnswer)) {{ 'color:green' }} @elseif(!empty($question->userChoice) && in_array($answer->id, $question->userChoice)) {{ 'color:red' }} @endif">
                                                    <input type="checkbox" disabled {{ (in_array($answer->id, $question->trueAnswer) || (!empty($question->userChoice) && in_array($answer->id, $question->userChoice))) ? 'checked' : '' }}>
                                                    <span class="checkbox-material answer-{{ $question->id }}">
                                                    </span>{{ $answer->content }}
                                                </label>
                                            </div>
                                            <br>
                                        @endforeach
                                    </div>
                                    {!! empty($question->userChoice) ? '<h4><i class="fa fa-times" aria-hidden="true"></i> <i>You did not choose the answer to this question</i></h4>' : '' !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <a class="btn btn-common"
                                style="background-color: Tomato; float: right;"
                                href="{{ route('courses.show', $lecture->course_id) }}#course-{{ $lecture->course_id }}-outline">
                            Back To Course
                        </a>
                </div>
            </div>
        </div>
    </section>
@endsection
