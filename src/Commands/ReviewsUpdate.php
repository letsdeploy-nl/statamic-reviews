<?php

namespace Letsdeploy\Reviews\Commands;

use Illuminate\Console\Command;
use Letsdeploy\Reviews\DTO\GoogleReviewDTO;
use Letsdeploy\Reviews\Repositories\ReviewRepository;
use Letsdeploy\Reviews\Services\ApifyService;
use Letsdeploy\Reviews\Services\ConfigService;

class ReviewsUpdate extends Command
{
    protected $signature = 'reviews:update';

    protected $description = 'Fetches your latest Google reviews with Apify';

    public function handle(
        ApifyService     $apifyService,
        ConfigService $configService,
        ReviewRepository $reviewRepository
    ): void
    {
        $reviews = $apifyService->getGoogleReviews(
            maxReviews: config('reviews.google.max_reviews'),
            placeIds: $configService->getGooglePlaceIds(),
        );

        $reviews->each(fn (GoogleReviewDTO $review) => $reviewRepository->updateOrCreate($review));

        $reviewRepository->updateGlobal(
            review: $reviews->first()
        );
    }
}
