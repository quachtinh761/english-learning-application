<?php

namespace App\Http\Repositories;

use App\Models\QuizSubmission;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class QuizSubmissionRepository extends BaseRepository
{
    public function getModel(): Model
    {
        return new QuizSubmission();
    }
}
