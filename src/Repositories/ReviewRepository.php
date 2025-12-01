<?php

namespace Letsdeploy\Reviews\Repositories;

use Illuminate\Support\Carbon;
use Letsdeploy\Reviews\Interfaces\Reviewable;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;

class ReviewRepository
{
    public function updateOrCreate(Reviewable $review)
    {
        $entry = Entry::whereCollection('reviews')
            ->where('review_id', $review->reviewId)
            ->first();

        if ($entry) {
            $entry->set('title', $review->title);
            $entry->set('content', $review->content);
            $entry->set('stars', $review->stars);
            $entry->set('raw_json', $review->rawJson);
            $entry->save();
            return;
        };

        Entry::make()
            ->collection('reviews')
            ->data([
                'title' => $review->title,
                'content' => $review->content,
                'stars' => $review->stars,
                'name' => $review->name,
                'reviewer_photo_url' => $review->reviewerPhotoUrl,
                'created_at' => Carbon::parse($review->createdAt),
                'review_origin' => $review->reviewOrigin,
                'review_id' => $review->reviewId,
                'review_url' => $review->reviewUrl,
                'review_image_urls' => $review->reviewImageUrls->toArray(),
                'raw_json' => $review->rawJson,
            ])
            ->save();
    }

    public function updateGlobal(Reviewable $review)
    {
        $set = GlobalSet::findByHandle('review');
        $variables = $set->inDefaultSite();
        $variables->data([
            'total_score' => round($review->totalScore),
            'reviews_count' => $review->reviewsCount
        ]);
        $variables->save();
    }
}