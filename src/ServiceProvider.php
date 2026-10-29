<?php

namespace Letsdeploy\Reviews;

use Letsdeploy\Reviews\Commands\ReviewsSync;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        ReviewsSync::class
    ];

    public function bootAddon(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/reviews.php', 'reviews');

        $this->registerAssetDisk();

        $this->publishes([
            __DIR__.'/../content/collections/reviews.yaml' => base_path('content/collections/reviews.yaml'),
            __DIR__.'/../resources/blueprints/collections/reviews/review.yaml' => resource_path('blueprints/collections/reviews/review.yaml'),
        ], 'ldp-reviews-collections');

        $this->publishes([
            __DIR__.'/../content/globals/review.yaml' => base_path('content/globals/review.yaml'),
            __DIR__.'/../resources/blueprints/globals/review.yaml' => resource_path('blueprints/globals/review.yaml'),
        ], 'ldp-reviews-globals');

        $this->publishes([
            __DIR__.'/../content/assets/reviews.yaml' => base_path('content/assets/reviews.yaml'),
        ], 'ldp-reviews-assets');
    }

    protected function registerAssetDisk(): void
    {
        if (config()->has('filesystems.disks.reviews')) {
            return;
        }

        config(['filesystems.disks.reviews' => config('reviews.assets.disk')]);
    }
}
