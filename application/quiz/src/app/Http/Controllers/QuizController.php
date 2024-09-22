<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quiz\GetListQuizzesRequest;
use App\Http\Requests\Quiz\PostSubmitQuizRequest;
use App\Http\Responses\PaginationResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Services\Interfaces\QuizServiceInterface;
use App\Http\Transformers\QuizSubmissionTransformer;
use App\Http\Transformers\QuizTransformer;
use Illuminate\Http\JsonResponse;

class QuizController extends Controller
{
    public function getList(
        GetListQuizzesRequest $request,
        QuizServiceInterface $quizService
    ): JsonResponse
    {
        $result = $quizService->getList(
            filter: $request->validated(),
            sort: $request->get('sort', []),
            page: $request->get('page', 1),
            perPage: $request->get('per_page', 10)
        );

        return PaginationResponse::create(queryResult: $result, transformerClass: QuizTransformer::class);
    }

    public function getDetail(
        int $id,
        QuizServiceInterface $quizService
    ): JsonResponse
    {
        $quiz = $quizService->getDetail($id);

        return SuccessResponse::create(data: QuizTransformer::transformItem($quiz));
    }

    public function getByCode(
        string $code,
        QuizServiceInterface $quizService
    ): JsonResponse
    {
        $quiz = $quizService->getByCode($code);

        return SuccessResponse::create(data: QuizTransformer::transformItem($quiz));
    }

    public function startQuiz(
        string $code,
        QuizServiceInterface $quizService
    ): JsonResponse
    {
        $quiz = $quizService->getByCode($code);
        $result = $quizService->startQuiz($quiz);

        return SuccessResponse::create(data: $result);
    }

    public function submitQuiz(
        string $code,
        PostSubmitQuizRequest $request,
        QuizServiceInterface $quizService
    ): JsonResponse
    {
        $quiz = $quizService->getByCode($code);

        $result = $quizService->submitQuiz(
            quiz: $quiz,
            submissionCode: $request->validated()['submission_code'],
            userSubmission: $request->validated()
        );

        return SuccessResponse::create(data: $result);
    }

    public function getTopRank(
        string $code,
        QuizServiceInterface $quizService
    ): JsonResponse
    {
        $quiz = $quizService->getByCode($code);
        $result = $quizService->getTopSubmissions(quiz: $quiz, limit: 100);

        return SuccessResponse::create(data: QuizSubmissionTransformer::transformItems($result));
    }
}
