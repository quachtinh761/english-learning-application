<?php

namespace App\Http\Services\Interfaces;

use App\Exceptions\ModelNotFoundException;
use App\Models\Quiz;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

interface QuizServiceInterface
{
    public function getList(array $filter, array $sort, int $page, int $perPage, array|string $select = ['*']): Paginator|LengthAwarePaginator;

    /**
     * @throws ModelNotFoundException
     */
    public function getDetail(int $id, array|string $select = ['*']): Quiz;

    /**
     * @throws ModelNotFoundException
     */
    public function getByCode(string $code, array|string $select = ['*']): Quiz;

    /**
     * Start a quiz and return a code & list of questions
     *
     * @param Quiz $quiz
     * @return array
     *
     * Sample return: ['submission_code' => '123456', 'questions' => [['question' => '', 'options' => []], ['question' => '', 'options' => []]]]
     */
    public function startQuiz(Quiz $quiz): array;

    /**
     * Submit a quiz and return the result
     *
     * @param Quiz $quiz
     * @param string $submissionCode
     * @param array $answers
     * @return array
     */
    public function submitQuiz(Quiz $quiz, string $submissionCode, array $answers): array;

    /**
     * Get top xx quiz submissions
     *
     * @param Quiz $quiz
     * @param int $limit
     *
     * @return array
     */
    public function getTopSubmissions(Quiz $quiz, int $limit): array;
}
