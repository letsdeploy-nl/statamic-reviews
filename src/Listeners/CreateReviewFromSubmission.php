<?php

namespace Letsdeploy\Reviews\Listeners;

use Letsdeploy\Reviews\DTO\WebsiteReviewDTO;
use Letsdeploy\Reviews\Repositories\ReviewRepository;
use Statamic\Events\SubmissionCreated;

class CreateReviewFromSubmission
{
    public function handle(SubmissionCreated $event): void
    {
        if ($event->submission->form()->handle() !== 'review') {
            return;
        }

        $review = WebsiteReviewDTO::fromSubmission($event->submission->data()->toArray());

        (new ReviewRepository())->updateOrCreate($review);
    }
}
