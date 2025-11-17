<?php

namespace Letsdeploy\Reviews\Interfaces;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

interface Reviewable
{
    public string $title {
        get;
    }

    public string $content {
        get;
        set;
    }

    public int $stars {
        get;
        set;
    }

    public string $name {
        get;
        set;
    }

    public string $reviewerPhotoUrl {
        get;
        set;
    }

    public Carbon $createdAt {
        get;
        set;
    }

    public string $reviewOrigin {
        get;
        set;
    }

    public string $reviewId {
        get;
        set;
    }


    public string $reviewUrl {
        get;
        set;
    }

    public string $rawJson {
        get;
        set;
    }

    public Collection $reviewImageUrls {
        get;
        set;
    }
}