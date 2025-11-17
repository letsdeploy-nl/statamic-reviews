<?php

namespace Letsdeploy\Reviews;

use Letsdeploy\Reviews\Commands\ReviewsInit;
use Letsdeploy\Reviews\Commands\ReviewsUpdate;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        ReviewsInit::class,
        ReviewsUpdate::class
    ];

    public function bootAddon(): void
    {
        $this->publishes([
            __DIR__.'/../content/collections/reviews.yaml' => base_path('content/collections/reviews.yaml'),
            __DIR__.'/../resources/blueprints/collections/reviews/review.yaml' => resource_path('blueprints/collections/reviews/review.yaml'),
            __DIR__ . '/../resources/views/components/_review.antlers.html' => resource_path('views/partials/components/_review.antlers.html'),
        ], 'letsdeploy-reviews');
    }
}
