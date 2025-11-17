<?php

namespace Letsdeploy\Reviews\Commands;

use Illuminate\Console\Command;
use Letsdeploy\Reviews\DTO\GoogleReviewDTO;
use Letsdeploy\Reviews\Repositories\ReviewRepository;
use Letsdeploy\Reviews\Services\ApifyService;
use Letsdeploy\Reviews\Services\ConfigService;

class ReviewsInit extends Command
{
    protected $signature = 'reviews:init';

    protected $description = 'Fetches your Google reviews with Apify';

    public function handle(
        ApifyService     $apifyService,
        ConfigService $configService,
        ReviewRepository $reviewRepository
    ): void
    {
        $maxReviews = $this->ask('How many reviews do you have?', 10);

        $reviews = $apifyService->getGoogleReviews(
            maxReviews: $maxReviews,
            placeIds: $configService->getGooglePlaceIds(),
        );

        $reviews->each(fn (GoogleReviewDTO $review) => $reviewRepository->updateOrCreate($review));
    }
}
