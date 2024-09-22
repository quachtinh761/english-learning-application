<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Exceptions\ModelNotFoundException;
use App\Http\Services\Interfaces\QuizServiceInterface;
use App\Models\Quiz;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class QuizServiceTest extends TestCase
{
    public function test_validate_quiz_submission_throws_model_not_found_exception_if_submission_code_not_found()
    {
        $quiz = Quiz::factory()->create();

        /**
         * @var QuizServiceInterface $quizService
         */
        $quizService = $this->app->make(QuizServiceInterface::class);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Model Submission with term invalid-submission-code not found');
        $quizService->submitQuiz($quiz, 'invalid-submission-code', []);
    }

    // TODO: Add more tests for other scenarios, I don't have the time to do it now
}
