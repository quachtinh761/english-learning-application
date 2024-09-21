<?php

namespace App\Http\Repositories;

use App\Models\Quiz;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class QuizRepository extends BaseRepository
{
    public function getModel(): Model
    {
        return new Quiz();
    }

    public function getList(array $filter, array $sort, int $page, int $perPage, array|string $select = ['*']): Paginator|LengthAwarePaginator
    {
        $query = $this->newQuery();

        $query->with(['quizQuestions']);

        return $query->paginate($perPage, $select, 'page', $page);
    }
}
