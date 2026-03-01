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
        $this->publishes([
            __DIR__ . '/../resources/views/components/_review.antlers.html' => resource_path('views/partials/components/_review.antlers.html'),
            __DIR__ . '/../resources/views/components/_review_masonry.antlers.html' => resource_path('views/partials/components/_review_masonry.antlers.html'),
            __DIR__ . '/../resources/views/components/_review_summary.antlers.html' => resource_path('views/partials/components/_review_summary.antlers.html'),
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

        $this->publishes([
            __DIR__.'/../resources/forms/review.yaml' => resource_path('forms/review.yaml'),
            __DIR__.'/../resources/blueprints/forms/review.yaml' => resource_path('blueprints/forms/review.yaml'),
        ], 'ldp-reviews-forms');
    }
}
