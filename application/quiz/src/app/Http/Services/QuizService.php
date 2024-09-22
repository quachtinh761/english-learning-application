<?php

namespace App\Http\Services;

use App\Exceptions\InternalServerErrorException;
use App\Exceptions\ModelNotFoundException;
use App\Http\Repositories\QuizRepository;
use App\Http\Repositories\QuizSubmissionRepository;
use App\Http\Services\Interfaces\QuizServiceInterface;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        $quiz = $this->quizRepository->newQuery()->with(['quizQuestions'])->find($id, $select);

        if (!$quiz) {
            throw new ModelNotFoundException('Quiz', $id);
        }

        return $quiz;
    }

    /**
     * @inheritDoc
     */
    public function getByCode(string $code, array|string $select = ['*']): Quiz
    {
        $quiz = $this->quizRepository->newQuery()->with(['quizQuestions'])->where('code', $code)->first($select);

        if (!$quiz) {
            throw new ModelNotFoundException('Quiz', $code);
        }

        return $quiz;
    }

    /**
     * @inheritDoc
     */
    public function startQuiz(Quiz $quiz): array
    {
        $questions = $quiz->quizQuestions->all();

        shuffle($questions);

        try {
            $this->quizSubmissionRepository->create([
                'quiz_id' => $quiz->id,
                'submission_code' => $submissionCode = Str::upper(uniqid()),
                'started_at' => now(),
                'detail' => array_map(fn ($question) => [
                    'id' => $question->id,
                    'question' => $question->question,
                    'options' => $question->options,
                    'answer' => $question->answer,
                    'point' => $question->point,
                    'user_answer' => null,
                    'is_correct' => null,
                ], $questions),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to start quiz', ['quiz' => $quiz, 'exception' => $e]);
            throw new InternalServerErrorException('Failed to start quiz');
        }

        return [
            'submission_code' => $submissionCode,
            'questions' => array_map(fn ($question) => [
                'question_id' => $question->id,
                'question' => $question->question,
                'options' => $question->options,
            ], $questions),
        ];
    }

    /**
     * @inheritDoc
     */
    public function submitQuiz(Quiz $quiz, string $submissionCode, array $userSubmission): array
    {
        $submission = $this->validateSubmission(quiz: $quiz, submissionCode: $submissionCode);

        try {
            $result = $this->mapAnswersAndCalculatePoints($submission->detail, $userSubmission['answers']);

            $submission->update([
                'submitted_at' => now(),
                'number_of_corrections' => $result['number_of_correct_answers'],
                'total_points' => $result['total_points'],
                'detail' => $result['detail'],
                'user_email' => $userSubmission['user_email'] ?? '',
                'user_name' => $userSubmission['user_name'] ?? '',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to submit quiz', [
                'quiz' => $quiz->id,
                'submissionCode' => $submissionCode,
                'userSubmission' => $userSubmission,
                'exception' => $e->getTrace()
            ]);
            throw new InternalServerErrorException('Failed to submit quiz');
        }

        return [
            'number_of_corrections' => $result['number_of_correct_answers'],
            'total_points' => $result['total_points'],
        ];
    }

    /**
     * To validate submission for a quiz
     * @param Quiz $quiz
     * @param string $submissionCode
     * @return QuizSubmission
     *
     * @throws ModelNotFoundException
     */
    private function validateSubmission(Quiz $quiz, string $submissionCode): QuizSubmission
    {
        $submission = $quiz->quizSubmissions()
            ->where('submission_code', $submissionCode)
            ->whereNull('submitted_at')
            ->first();

        if (!$submission) {
            Log::warning('Submission code not found for quiz', ['quiz_id' => $quiz->id, 'submissionCode' => $submissionCode]);
            throw new ModelNotFoundException('Submission', $submissionCode);
        }

        return $submission;
    }

    /**
     * To map answers and calculate points for a quiz
     * @param array $questions
     * @param array $answers
     * @return array ['detail' => array, 'total_points' => int, 'number_of_correct_answers' => int]
     */
    private function mapAnswersAndCalculatePoints(array $questions, array $answers): array
    {
        $totalPoints = 0;
        $numberCorrectAnswers = 0;
        $answers = array_column($answers, 'answer', 'question_id');

        foreach ($questions as $key => $question) {
            $questions[$key]['user_answer'] = $answers[$question['id']] ?? '';

            // TODO: temporarily work for single choice question only, need to expand for multiple choice question
            $questions[$key]['is_correct'] = $questions[$key]['user_answer'] === $question['answer'];

            if ($questions[$key]['is_correct']) {
                $totalPoints += $question['point'];
                $numberCorrectAnswers++;
            }
        }

        return [
            'detail' => $questions,
            'total_points' => $totalPoints,
            'number_of_correct_answers' => $numberCorrectAnswers,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTopSubmissions(Quiz $quiz, int $limit): array
    {
        return $quiz->quizSubmissions()->orderByDesc('total_points')->limit($limit)->get()->all();
    }
}
