<?php

namespace App\Listeners;

use App\Events\QuizSubmittedEvent;
use App\Jobs\RankingSubmissionForQuizzes;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateSubmissionRank implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(QuizSubmittedEvent $event): void
    {
        // Dispatch the job to update the submission rank after 5 seconds
        RankingSubmissionForQuizzes::debounce($event->quizId, 5);
    }
}
