<?php

namespace App\Jobs;

use App\Enums\CacheKeysEnum;
use App\Helpers\RealtimeNotification;
use App\Http\Services\Interfaces\QuizServiceInterface;
use App\Http\Transformers\QuizSubmissionTransformer;
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

        try {
            $quiz = $quizService->getDetail($this->quizId, ['id']);
        } catch (\Exception $e) {
            Log::error('Error while getting quiz detail', ['error' => $e]);
            return;
        }
        $limit = 100;

        $topRanks = $quizService->getTopSubmissionsFromDb(quiz: $quiz, limit: $limit);
        $topRanks = QuizSubmissionTransformer::transformItems($topRanks);

        $this->updateCache(quizId: $this->quizId, topRanks: $topRanks);
        $this->sendToQueue(quizId: $this->quizId, topRanks: $topRanks);
        $this->sendToFirebase(realtimeNotification: $realtimeNotification, quizId: $this->quizId, topRanks: $topRanks);
    }

    private function updateCache(int $quizId, array $topRanks): void
    {
        try {
            $cacheKey = sprintf(CacheKeysEnum::TopQuizSubmission->value, $quizId, 100);
            Cache::set($cacheKey, $topRanks);
        } catch (\Exception $e) {
            Log::error('Error while updating cache', ['error' => $e]);
        }
    }

    private function sendToQueue(int $quizId, array $topRanks): void
    {
        try {
            Redis::publish('quiz-rank-updated', json_encode([
                'quiz_id' => $quizId,
                'top_ranks' => $topRanks,
            ]));
        } catch (\Exception $e) {
            Log::error('Error while publishing to redis', ['error' => $e]);
        }
    }

    private function sendToFirebase(RealtimeNotification $realtimeNotification, int $quizId, array $topRanks): void
    {
        try {
            $realtimeNotification->send(
                'quiz-ranking',
                'Quiz Rank Updated',
                'Quiz rank has been updated',
                [
                    'quiz_id' => $quizId,
                    'top_ranks' => json_encode($topRanks),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error while sending realtime notification', ['error' => $e]);
        }
    }
}
