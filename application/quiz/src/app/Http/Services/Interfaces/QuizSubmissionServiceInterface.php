<?php

namespace App\Http\Services\Interfaces;

use App\Exceptions\ModelNotFoundException;
use App\Models\QuizSubmission;

interface QuizSubmissionServiceInterface
{
    /**
     * @throws ModelNotFoundException
     */
    public function getByCode(string $code, array|string $select = ['*']): QuizSubmission;
}
