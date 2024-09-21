<?php

namespace App\Http\Services;

use App\Http\Repositories\QuizRepository;
use App\Http\Repositories\QuizSubmissionRepository;
use App\Http\Services\Interfaces\QuizServiceInterface;
use App\Models\Quiz;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

class QuizService implements QuizServiceInterface
{
    public function __construct(
        private QuizRepository $quizRepository,
        private QuizSubmissionRepository $quizSubmissionRepository
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getList(array $filter, array $sort, int $page, int $perPage, array|string $select = ['*']): Paginator|LengthAwarePaginator
    {
        return $this->quizRepository->getList($filter, $sort, $page, $perPage, $select);
    }

    /**
     * @inheritDoc
     */
    public function getDetail(int $id, array|string $select = ['*']): Quiz
    {
        return $this->quizRepository->newQuery()->with(['questions'])->find($id, $select);
    }

    /**
     * @inheritDoc
     */
    public function getByCode(string $code, array|string $select = ['*']): Quiz
    {
        return $this->quizRepository->newQuery()->with(['questions'])->where('code', $code)->first($select);
    }

    /**
     * @inheritDoc
     */
    public function startQuiz(Quiz $quiz): array
    {
        $questions = $quiz->quizQuestions()->map(function ($question) {
            return [
                'question' => $question->question,
                'options' => $question->options->pluck('option')->all(),
            ];
        })->all();

        shuffle($questions);

        $this->quizSubmissionRepository->create([
            'quiz_id' => $quiz->id,
            'submission_code' => $submissionCode = uniqid(),
            'started_at' => now(),
        ]);

        return [
            'submission_code' => $submissionCode,
            'questions' => $questions,
        ];
    }
}