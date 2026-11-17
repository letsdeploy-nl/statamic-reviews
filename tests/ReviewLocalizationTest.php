<?php

namespace Letsdeploy\Reviews\Tests;

use Letsdeploy\Reviews\DTO\GoogleReviewDTO;
use Letsdeploy\Reviews\Repositories\ReviewRepository;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

class ReviewLocalizationTest extends TestCase
{
    use PreventsSavingStacheItemsToDisk;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('statamic.system.multisite', true);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Site::setSites([
            'en' => ['name' => 'English', 'locale' => 'en_US', 'url' => '/'],
            'nl' => ['name' => 'Dutch', 'locale' => 'nl_NL', 'url' => '/nl/'],
        ]);

        Collection::make('reviews')->sites(['en', 'nl'])->save();
    }

    public function test_an_existing_review_is_updated_on_its_origin_and_not_on_a_localization(): void
    {
        // The localization is saved before its origin, so it comes first in the collection.
        $localization = Entry::make()->collection('reviews')->locale('nl');
        $localization->save();

        $origin = Entry::make()->collection('reviews')->locale('en')->data([
            'review_id' => 'ChdDSUhNMG9n',
            'content' => 'Great place',
            'stars' => 5,
        ]);
        $origin->save();

        $localization->origin($origin)->save();

        $this->assertTrue(Entry::whereCollection('reviews')->first()->hasOrigin());

        app(ReviewRepository::class)->updateOrCreate($this->review(content: 'Updated text', stars: 4));

        $this->assertCount(2, Entry::whereCollection('reviews'));

        $origin = Entry::find($origin->id());

        $this->assertSame('Updated text', $origin->get('content'));
        $this->assertSame(4, $origin->get('stars'));

        // Localizations keep inheriting from the origin instead of getting their own copy.
        $localization = Entry::find($localization->id());

        $this->assertTrue($localization->data()->isEmpty());
        $this->assertSame('Updated text', $localization->value('content'));
    }

    protected function review(string $content, int $stars): GoogleReviewDTO
    {
        return GoogleReviewDTO::fromArray([
            'text' => $content,
            'stars' => $stars,
            'name' => 'Jane Doe',
            'reviewerPhotoUrl' => null,
            'publishedAtDate' => '2026-01-15T10:00:00.000Z',
            'reviewOrigin' => 'Google',
            'reviewId' => 'ChdDSUhNMG9n',
            'reviewUrl' => 'https://www.google.com/maps/reviews/ChdDSUhNMG9n',
            'reviewImageUrls' => [],
            'reviewsCount' => 12,
            'totalScore' => 4.7,
        ]);
    }
}
