<?php

namespace Letsdeploy\Reviews\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Statamic\Contracts\Assets\Asset;
use Statamic\Contracts\Assets\AssetContainer;
use Statamic\Facades\AssetContainer as AssetContainerFacade;

class ReviewAssetService
{
    protected const EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/avif' => 'avif',
        'image/svg+xml' => 'svg',
    ];

    public function storeAvatar(string $reviewId, ?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        return $this->store($reviewId, $url, 'avatar');
    }

    public function storePhotos(string $reviewId, Collection $urls): Collection
    {
        return $urls
            ->values()
            ->map(fn (string $url, int $index) => $this->store($reviewId, $url, 'photo-'.($index + 1)))
            ->filter()
            ->values();
    }

    public function exists(string|array|null $paths): bool
    {
        $paths = collect((array) $paths)->filter();

        if ($paths->isEmpty()) {
            return false;
        }

        $container = $this->container();

        return $paths->every(fn (string $path) => $container->asset($path) !== null);
    }

    public function forget(string $reviewId): void
    {
        $this->container()
            ->assets($this->folder($reviewId))
            ->each(fn (Asset $asset) => $asset->delete());
    }

    protected function store(string $reviewId, string $url, string $basename): ?string
    {
        try {
            $response = Http::timeout((int) config('reviews.assets.download_timeout', 30))
                ->accept('image/*')
                ->get($url);

            if (! $response->successful() || $response->body() === '') {
                Log::warning("[reviews] Could not download {$basename} for review {$reviewId}", [
                    'url' => $url,
                    'status' => $response->status(),
                ]);

                return null;
            }

            $path = $this->folder($reviewId).'/'.$basename.'.'.$this->extension($response, $url);

            $container = $this->container();

            if ($existing = $container->asset($path)) {
                $existing->delete();
            }

            $container->disk()->put($path, $response->body());

            $container->makeAsset($path)->save();

            return $path;
        } catch (\Throwable $e) {
            Log::warning("[reviews] Failed storing {$basename} for review {$reviewId}: {$e->getMessage()}", [
                'url' => $url,
            ]);

            return null;
        }
    }

    protected function container(): AssetContainer
    {
        $handle = config('reviews.assets.container', 'reviews');

        $container = AssetContainerFacade::findByHandle($handle);

        if (! $container) {
            throw new \RuntimeException(
                "Asset container [{$handle}] not found. Run: php artisan vendor:publish --tag=ldp-reviews-assets"
            );
        }

        return $container;
    }

    protected function folder(string $reviewId): string
    {
        return Str::of($reviewId)->replaceMatches('/[^A-Za-z0-9_-]+/', '-')->trim('-')->toString();
    }

    protected function extension(Response $response, string $url): string
    {
        $mime = Str::of((string) $response->header('Content-Type'))->before(';')->trim()->lower()->toString();

        if (isset(self::EXTENSIONS[$mime])) {
            return self::EXTENSIONS[$mime];
        }

        $fromUrl = strtolower(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        if (in_array($fromUrl, self::EXTENSIONS, true) || $fromUrl === 'jpeg') {
            return $fromUrl === 'jpeg' ? 'jpg' : $fromUrl;
        }

        return 'jpg';
    }
}
