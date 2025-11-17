<?php

namespace Letsdeploy\Reviews\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ConfigService
{
    public function getGooglePlaceIds(): Collection
    {
        $rawPlaceIds = config('services.google_reviews.place_ids');

        if (!$rawPlaceIds) throw new \Exception('No Google Places IDs found in .env');

        return Str::of($rawPlaceIds)->explode(',');
    }
}