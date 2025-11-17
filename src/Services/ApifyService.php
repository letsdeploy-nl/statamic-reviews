<?php

namespace Letsdeploy\Reviews\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Letsdeploy\Reviews\DTO\GoogleReviewDTO;

class ApifyService
{
    protected PendingRequest $client;

    public function __construct()
    {
        $apifyToken = config('reviews.apify.token');

        if(!$apifyToken) throw new \Exception(
            'Apify API token not found in .env'
        );

        $this->client = Http::withHeaders([
            'Authorization' => "Bearer $apifyToken"
        ])->baseUrl("https://api.apify.com/v2/acts");
    }

    public function getGoogleReviews(
        int $maxReviews,
        Collection $placeIds
    ): Collection
    {
        $response = $this->client->post('compass~google-maps-reviews-scraper/run-sync-get-dataset-items', [
            'maxReviews' => $maxReviews,
            'placeIds' => $placeIds->toArray(),
            "reviewsSort" => "newest"
        ]);

        $response->throw();

        return collect($response->json())->map(fn($review) => GoogleReviewDTO::fromArray($review));
    }
}