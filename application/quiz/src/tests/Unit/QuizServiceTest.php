<?php

namespace Tests\Unit;

use App\Events\QuizSubmittedEvent;
use App\Exceptions\InternalServerErrorException;
use App\Exceptions\ModelNotFoundException;
use App\Http\Repositories\QuizRepository;
use App\Http\Repositories\QuizSubmissionRepository;
use App\Http\Services\QuizService;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QuizServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function initQuizService(
        $quizRepository = null,
        $quizSubmissionRepository = null
    ): QuizService {
        $mockQuizRepository = $this->createMock(QuizRepository::class);
        $mockQuizSubmissionRepository = $this->createMock(QuizSubmissionRepository::class);
        return new QuizService(
            $quizRepository ?: $mockQuizRepository,
            $quizSubmissionRepository ?: $mockQuizSubmissionRepository
        );
    }

    public function test_submit_quiz_throws_model_not_found_exception_if_submission_code_not_found()
    {
        $quizService = $this->initQuizService();

        $mockCollection = $this->createMock(Collection::class);
        $mockQuiz = $this->createMock(Quiz::class);
        $mockQuiz->method('quizSubmissions')->willReturn($mockCollection);
        $mockCollection->method('where')->willReturn($mockCollection);
        $mockCollection->method('whereNull')->willReturn($mockCollection);
        $mockCollection->method('first')->willReturn(null);
        /**
         * @var Quiz $mockQuiz
         */

        Log::shouldReceive('warning');
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Model Submission with term invalid-submission-code not found');
        $quizService->submitQuiz($mockQuiz, 'invalid-submission-code', []);

    }

    public function test_submit_quiz_throws_internal_server_error_when_error_happens()
    {
        $quizService = $this->initQuizService();

        $mockCollection = $this->createMock(Collection::class);
        $mockQuiz = $this->createMock(Quiz::class);
        $mockQuiz->method('quizSubmissions')->willReturn($mockCollection);
        $mockCollection->method('where')->willReturn($mockCollection);
        $mockCollection->method('whereNull')->willReturn($mockCollection);
        $mockCollection->method('first')->willReturn(new QuizSubmission([
            'detail' => [],
        ]));

        /**
         * @var Quiz $mockQuiz
         */

        Log::shouldReceive('error');
        $this->expectException(InternalServerErrorException::class);
        $this->expectExceptionMessage('Failed to submit quiz');
        $quizService->submitQuiz($mockQuiz, 'valid-code', []);
    }

    public function test_submit_quiz_throws_internal_server_error_when_saving_database_fails()
    {
        $quizService = $this->initQuizService();

        $mockCollection = $this->createMock(Collection::class);
        $mockQuiz = $this->createMock(Quiz::class);

        $mockSubmission = $this->createMock(QuizSubmission::class);
        $mockSubmission->method('__get')->with('detail')->willReturn([]);
        $mockSubmission->method('update')->willThrowException(new \Exception('Failed to save'));

        $mockQuiz->method('quizSubmissions')->willReturn($mockCollection);
        $mockCollection->method('where')->willReturn($mockCollection);
        $mockCollection->method('whereNull')->willReturn($mockCollection);
        $mockCollection->method('first')->willReturn($mockSubmission);

        $mockQuiz->id = 1;

        /**
         * @var Quiz $mockQuiz
         */

        Log::shouldReceive('error');
        $this->expectException(InternalServerErrorException::class);
        $this->expectExceptionMessage('Failed to submit quiz');
        $quizService->submitQuiz($mockQuiz, 'valid-code', ['answers' => []]);
    }

    public function test_submit_quiz_return_correct_data()
    {
        $quizService = $this->initQuizService();

        $mockCollection = $this->createMock(Collection::class);
        $mockQuiz = $this->getMockBuilder(Quiz::class)->disableOriginalConstructor()->getMock();

        $mockSubmission = $this->createMock(QuizSubmission::class);
        $mockSubmission->method('__get')->willReturnOnConsecutiveCalls([], 123);
        $mockSubmission->method('update')->willReturn(true);

        $mockQuiz->method('quizSubmissions')->willReturn($mockCollection);
        $mockQuiz->method('__get')->with('id')->willReturn(1);

        $mockCollection->method('where')->willReturn($mockCollection);
        $mockCollection->method('whereNull')->willReturn($mockCollection);
        $mockCollection->method('first')->willReturn($mockSubmission);

        /**
         * @var Quiz $mockQuiz
         */

        $result = $quizService->submitQuiz($mockQuiz, 'valid-code', ['answers' => []]);
        Event::assertDispatched(QuizSubmittedEvent::class);
        $this->assertArrayHasKey('number_of_corrections', $result);
        $this->assertArrayHasKey('total_points', $result);
    }
}
