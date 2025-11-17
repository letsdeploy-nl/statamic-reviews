<?php

namespace Letsdeploy\Reviews\DTO;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Letsdeploy\Reviews\Interfaces\Reviewable;

class GoogleReviewDTO implements Reviewable
{
    public string $title {
        get => "{$this->name} - {$this->stars}* - {$this->reviewOrigin}";
    }

    public function __construct(
        public ?string $content,
        public int    $stars,
        public string $name,
        public string $reviewerPhotoUrl,
        public Carbon $createdAt,
        public string $reviewOrigin,
        public string $reviewId,
        public string $reviewUrl,
        public Collection $reviewImageUrls,
        public string $rawJson
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            content: $data['text'],
            stars: $data['stars'],
            name: $data['name'],
            reviewerPhotoUrl: $data['reviewerPhotoUrl'],
            createdAt: Carbon::parse($data['publishedAtDate']),
            reviewOrigin: $data['reviewOrigin'],
            reviewId: $data['reviewId'],
            reviewUrl: $data['reviewUrl'],
            reviewImageUrls: collect($data['reviewImageUrls'])->map(fn(string $url) => ["review_image_url" => $url]),
            rawJson: json_encode($data)
        );
    }
}