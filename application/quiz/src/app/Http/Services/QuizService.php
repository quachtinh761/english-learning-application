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
        return $this->quizRepository->newQuery()->with(['quizQuestions'])->find($id, $select);
    }

    /**
     * @inheritDoc
     */
    public function getByCode(string $code, array|string $select = ['*']): Quiz
    {
        return $this->quizRepository->newQuery()->with(['quizQuestions'])->where('code', $code)->first($select);
    }

    /**
     * @inheritDoc
     */
    public function startQuiz(Quiz $quiz): array
    {
        $questions = $quiz->quizQuestions()->all();

        shuffle($questions);

        $this->quizSubmissionRepository->create([
            'quiz_id' => $quiz->id,
            'submission_code' => $submissionCode = uniqid(),
            'started_at' => now(),
            'detail' => array_map(fn ($question) => array_merge($question, [
                'user_answer' => null,
                'is_correct' => null,
            ]), $questions),
        ]);

        return [
            'submission_code' => $submissionCode,
            'questions' => array_map(fn ($question) => [
                'question_id' => $question->id,
                'question' => $question->question,
                'options' => $question->options->pluck('option')->all(),
            ], $questions),
        ];
    }

    /**
     * @inheritDoc
     */
    public function submitQuiz(Quiz $quiz, string $submissionCode, array $answers): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getTopSubmissions(Quiz $quiz, int $limit): array
    {
        return $quiz->quizSubmissions()->orderByDesc('total_points')->limit($limit)->get()->toArray();
    }
}
