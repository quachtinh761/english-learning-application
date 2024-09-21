<?php

namespace App\Http\Transformers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Database\Eloquent\Model;

class QuizQuestionTransformer extends BaseTransformer
{
    public static function transformItem(Model $quizQuestion): Model|array
    {
        /**
         * @var QuizQuestion $quizQuestion
         */
        return [
            'id' => $quizQuestion->id,
            'type' => $quizQuestion->type,
            'question' => $quizQuestion->question,
            'options' => $quizQuestion->options,
            'point' => $quizQuestion->point,
        ];
    }
}
