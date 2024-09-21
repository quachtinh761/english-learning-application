<?php

namespace App\Http\Transformers;

use App\Models\QuizSubmission;
use Illuminate\Database\Eloquent\Model;

class QuizSubmissionTransformer extends BaseTransformer
{
    public static function transformItem(Model $quizSubmission): Model | array
    {
        /**
         * @var QuizSubmission $quizSubmission
         */
        return [
            'id' => $quizSubmission->id,
            'quiz_id' => $quizSubmission->quiz_id,
            'submission_code' => $quizSubmission->submission_code,
            'user_id' => $quizSubmission->user_id,
            'user_name' => $quizSubmission->user_name,
            'user_email' => $quizSubmission->user_email,
            'number_of_corrections' => $quizSubmission->number_of_corrections,
            'total_points' => $quizSubmission->total_points,
            'detail' => $quizSubmission->detail,
            'rank' => $quizSubmission->rank,
        ];
    }
}
