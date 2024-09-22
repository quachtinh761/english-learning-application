<?php

namespace App\Http\Services;

use App\Exceptions\InternalServerErrorException;
use App\Exceptions\ModelNotFoundException;
use App\Http\Repositories\QuizSubmissionRepository;
use App\Http\Services\Interfaces\QuizSubmissionServiceInterface;
use App\Models\Quiz;
use App\Models\QuizSubmission;

class QuizSubmissionService implements QuizSubmissionServiceInterface
{
    public function __construct(
        private QuizSubmissionRepository $quizSubmissionRepository
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getByCode(string $code, array|string $select = ['*']): QuizSubmission
    {
        $quiz = $this->quizSubmissionRepository->newQuery()->where('submission_code', $code)->first($select);

        if (!$quiz) {
            throw new ModelNotFoundException('Submission', $code);
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
                'submission_code' => $submissionCode = uniqid(),
                'started_at' => now(),
                'detail' => array_map(fn($question) => array_merge($question->toArray(), [
                    'user_answer' => null,
                    'is_correct' => null,
                ]), $questions),
            ]);
        } catch (\Exception $e) {
            throw new InternalServerErrorException('Failed to start quiz');
        }

        return [
            'submission_code' => $submissionCode,
            'questions' => array_map(fn($question) => [
                'question_id' => $question->id,
                'question' => $question->question,
                'options' => $question->options,
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
