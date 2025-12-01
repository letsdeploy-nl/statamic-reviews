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
            __DIR__ . '/../resources/views/components/_review.antlers.html' => resource_path('views/partials/components/_review.antlers.html'),
            __DIR__ . '/../resources/views/components/_review_masonry.antlers.html' => resource_path('views/partials/components/_review_masonry.antlers.html'),
        ], 'ldp-reviews-views');

        $this->publishes([
            __DIR__ . '/../resources/js/review.js' => resource_path('js/addons/review.js'),
        ], 'ldp-reviews-js');

        $this->publishes([
            __DIR__.'/../content/collections/reviews.yaml' => base_path('content/collections/reviews.yaml'),
            __DIR__.'/../resources/blueprints/collections/reviews/review.yaml' => resource_path('blueprints/collections/reviews/review.yaml'),
        ], 'ldp-reviews-collections');

        $this->publishes([
            __DIR__.'/../content/globals/review.yaml' => base_path('content/globals/review.yaml'),
            __DIR__.'/../resources/blueprints/globals/review.yaml' => resource_path('blueprints/globals/review.yaml'),
        ], 'ldp-reviews-globals');
    }
}
