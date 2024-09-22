<?php

namespace App\Http\Transformers;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Model;

class QuizTransformer extends BaseTransformer
{
    public static function transformItem(Model $quiz): Model|array
    {
        /**
         * @var Quiz $quiz
         */
        return [
            'id' => $quiz->id,
            'code' => $quiz->code,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'instruction' => $quiz->instruction,
            'number_of_questions' => $quiz->number_of_questions,
            'time_limit' => $quiz->time_limit,
            'start_time' => $quiz->start_time,
            'end_time' => $quiz->end_time,
            'duration_in_minutes' => $quiz->duration_in_minutes,
            'maximum_point' => $quiz->maximum_point,
            'is_anonymous' => $quiz->is_anonymous,
            'questions' => $quiz->relationLoaded('quizQuestions') ? QuizQuestionTransformer::transformItems($quiz->quizQuestions->all()) : [],
        ];
    }
}
