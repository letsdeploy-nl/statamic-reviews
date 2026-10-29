<?php

namespace Letsdeploy\Reviews\Tests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Letsdeploy\Reviews\DTO\GoogleReviewDTO;
use Letsdeploy\Reviews\Repositories\ReviewRepository;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

class ReviewAssetSyncTest extends TestCase
{
    use PreventsSavingStacheItemsToDisk;

    protected string $diskRoot;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->diskRoot = sys_get_temp_dir().'/statamic-reviews-test-'.uniqid();

        $app['config']->set('filesystems.disks.reviews', [
            'driver' => 'local',
            'root' => $this->diskRoot,
            'url' => '/reviews',
            'visibility' => 'public',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        File::ensureDirectoryExists($this->diskRoot);

        Collection::make('reviews')->save();
        AssetContainer::make('reviews')->disk('reviews')->save();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->diskRoot);

        parent::tearDown();
    }

    public function test_it_stores_avatar_and_photos_as_assets_and_only_downloads_once(): void
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

        Http::fake([
            'lh3.googleusercontent.com/*' => Http::response($png, 200, ['Content-Type' => 'image/png']),
            'photos.example/*' => Http::response('jpegbytes', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $repository = app(ReviewRepository::class);

        $repository->updateOrCreate($this->review());

        $entry = Entry::whereCollection('reviews')->where('review_id', 'ChdDSUhNMG9n')->first();

        $this->assertNotNull($entry);
        $this->assertSame('ChdDSUhNMG9n/avatar.png', $entry->get('reviewer_photo'));
        $this->assertSame(['ChdDSUhNMG9n/photo-1.jpg', 'ChdDSUhNMG9n/photo-2.jpg'], $entry->get('review_images'));
        $this->assertFileExists($this->diskRoot.'/ChdDSUhNMG9n/avatar.png');
        $this->assertFileExists($this->diskRoot.'/ChdDSUhNMG9n/photo-1.jpg');
        $this->assertFileExists($this->diskRoot.'/ChdDSUhNMG9n/photo-2.jpg');

        $container = AssetContainer::findByHandle('reviews');
        $this->assertNotNull($container->asset('ChdDSUhNMG9n/avatar.png'));
        $this->assertNotNull($container->asset('ChdDSUhNMG9n/photo-2.jpg'));

        Http::assertSentCount(3);

        $repository->updateOrCreate($this->review(content: 'Updated text', stars: 4));

        $entry = Entry::whereCollection('reviews')->where('review_id', 'ChdDSUhNMG9n')->first();

        $this->assertSame('Updated text', $entry->get('content'));
        $this->assertSame(4, $entry->get('stars'));
        $this->assertSame('ChdDSUhNMG9n/avatar.png', $entry->get('reviewer_photo'));
        $this->assertCount(1, Entry::whereCollection('reviews'));

        Http::assertSentCount(3);
    }

    public function test_a_failed_download_does_not_break_the_sync(): void
    {
        Http::fake([
            'lh3.googleusercontent.com/*' => Http::response('', 404),
            'photos.example/*' => Http::response('jpegbytes', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        app(ReviewRepository::class)->updateOrCreate($this->review());

        $entry = Entry::whereCollection('reviews')->where('review_id', 'ChdDSUhNMG9n')->first();

        $this->assertNotNull($entry);
        $this->assertNull($entry->get('reviewer_photo'));
        $this->assertSame(['ChdDSUhNMG9n/photo-1.jpg', 'ChdDSUhNMG9n/photo-2.jpg'], $entry->get('review_images'));
    }

    protected function review(string $content = 'Great place', int $stars = 5): GoogleReviewDTO
    {
        return GoogleReviewDTO::fromArray([
            'text' => $content,
            'stars' => $stars,
            'name' => 'Jane Doe',
            'reviewerPhotoUrl' => 'https://lh3.googleusercontent.com/a/ACg8ocK-no-extension',
            'publishedAtDate' => '2026-01-15T10:00:00.000Z',
            'reviewOrigin' => 'Google',
            'reviewId' => 'ChdDSUhNMG9n',
            'reviewUrl' => 'https://www.google.com/maps/reviews/ChdDSUhNMG9n',
            'reviewImageUrls' => [
                'https://photos.example/one.jpg',
                'https://photos.example/two',
            ],
            'reviewsCount' => 12,
            'totalScore' => 4.7,
        ]);
    }
}
