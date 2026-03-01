<?php

namespace Letsdeploy\Reviews\DTO;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Letsdeploy\Reviews\Interfaces\Reviewable;

class WebsiteReviewDTO implements Reviewable
{
    public string $title {
        get => "{$this->name} - {$this->stars}* - {$this->reviewOrigin}";
    }

    public function __construct(
        public ?string    $content,
        public int        $stars,
        public string     $name,
        public string     $reviewerPhotoUrl,
        public Carbon     $createdAt,
        public string     $reviewOrigin,
        public string     $reviewId,
        public string     $reviewUrl,
        public Collection $reviewImageUrls,
        public string     $rawJson,
    )
    {
    }

    public static function fromSubmission(array $data): self
    {
        return new self(
            content: $data['content'] ?? null,
            stars: (int) $data['rating'],
            name: $data['name'],
            reviewerPhotoUrl: '',
            createdAt: Carbon::now(),
            reviewOrigin: 'Website',
            reviewId: Str::uuid()->toString(),
            reviewUrl: '',
            reviewImageUrls: collect(),
            rawJson: '',
        );
    }
}
