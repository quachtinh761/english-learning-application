<?php

namespace App\Jobs;

use App\Enums\CacheKeysEnum;
use App\Helpers\RealtimeNotification;
use App\Http\Services\Interfaces\QuizServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Mpbarlow\LaravelQueueDebouncer\Traits\Debounceable;

class RankingSubmissionForQuizzes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Debounceable;

    /**
     * Create a new job instance.
     */
    public function __construct(private int $quizId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
        QuizServiceInterface $quizService,
        RealtimeNotification $realtimeNotification
    ): void
    {
        Log::info('RankingSubmissionForQuizzes', ['quizId' => $this->quizId]);
        $quiz = $quizService->getDetail($this->quizId, ['id']);
        $limit = 100;

        $topRanks = $quizService->getTopSubmissionsFromDb(quiz: $quiz, limit: $limit);

        $cacheKey = sprintf(CacheKeysEnum::TopQuizSubmission->value, $quiz->id, $limit);
        Cache::set($cacheKey, $topRanks);

        Redis::publish('quiz-rank-updated', json_encode([
            'quiz_id' => $this->quizId,
            'top_ranks' => $topRanks,
        ]));

        // Send to firebase
        $realtimeNotification->send(
            'global',
            'Quiz Rank Updated',
            'Quiz rank has been updated',
            [
                'quiz_id' => $this->quizId,
                'top_ranks' => $topRanks,
            ]
        );
    }
}
