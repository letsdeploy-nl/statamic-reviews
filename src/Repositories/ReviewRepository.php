<?php

namespace Letsdeploy\Reviews\Repositories;

use Letsdeploy\Reviews\Interfaces\Reviewable;
use Letsdeploy\Reviews\Services\ReviewAssetService;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;

class ReviewRepository
{
    public function __construct(
        protected ReviewAssetService $assets
    ) {
    }

    public function updateOrCreate(Reviewable $review): void
    {
        $entry = Entry::whereCollection("reviews")
            ->where("review_id", $review->reviewId)
            ->first();

        if ($entry) {
            $entry->set("title", $review->title);
            $entry->set("content", $review->content);
            $entry->set("stars", $review->stars);
            $entry->set("reviewer_photo_url", $review->reviewerPhotoUrl);
            $entry->set("review_image_urls", $review->reviewImageUrls->toArray());
            $entry->set("raw_json", $review->rawJson);

            $this->syncAssets($entry, $review);
            $entry->save();

            return;
        }

        $entry = Entry::make()
            ->collection("reviews")
            ->data([
                "title" => $review->title,
                "content" => $review->content,
                "stars" => $review->stars,
                "name" => $review->name,
                "created_at" => $review->createdAt->timestamp,
                "review_origin" => $review->reviewOrigin,
                "review_id" => $review->reviewId,
                "review_url" => $review->reviewUrl,
                "raw_json" => $review->rawJson,
            ]);

        $this->syncAssets($entry, $review);
        $entry->save();
    }

    protected function syncAssets(EntryContract $entry, Reviewable $review): void
    {
        if (! $this->assets->exists($entry->get("reviewer_photo"))) {
            $entry->set(
                "reviewer_photo",
                $this->assets->storeAvatar($review->reviewId, $review->reviewerPhotoUrl)
            );
        }

        if ($review->reviewImageUrls->isNotEmpty() && ! $this->assets->exists($entry->get("review_images"))) {
            $entry->set(
                "review_images",
                $this->assets->storePhotos($review->reviewId, $review->reviewImageUrls)->all()
            );
        }
    }

    public function updateGlobal(): void
    {
        $reviews = Entry::query()
            ->where("collection", "reviews")
            ->get();

        $set = GlobalSet::findByHandle("review");
        $variables = $set->inDefaultSite();
        $variables->merge([
            "total_score" => $reviews->isEmpty() ? 0 : round($reviews->avg("stars"), 1),
            "reviews_count" => $reviews->count(),
        ]);
        $variables->save();
    }
}
