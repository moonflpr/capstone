<?php

namespace App\Observers;

use App\Models\QuizPublished;
use App\Models\StudentProgress;

class QuizPublishedObserver
{
    /**
     * Handle the QuizPublished "deleted" event.
     *
     * Removing the published quiz for a topic pulls the questions every
     * enrolled student answered to complete it, so their recorded
     * pre/post attempts for that topic can no longer be trusted. Clearing
     * those rows drops the topic back to "not done"; the modules page then
     * re-locks every later topic in sequence on the student's next visit,
     * because it only unlocks up to the first topic without a passed
     * post-test.
     */
    public function deleted(QuizPublished $quizPublished): void
    {
        StudentProgress::query()
            ->where('topic_key', $quizPublished->topic_key)
            ->whereIn('phase', StudentProgress::QUIZ_PHASES)
            ->delete();
    }
}
