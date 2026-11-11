<?php

namespace Letsdeploy\Reviews\Tests;

use Letsdeploy\Reviews\Repositories\ReviewRepository;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Site;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

class ReviewGlobalTest extends TestCase
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
            'de' => ['name' => 'German', 'locale' => 'de_DE', 'url' => '/de/'],
        ]);

        Collection::make('reviews')->sites(['en', 'nl', 'de'])->propagate(true)->save();
        GlobalSet::make('review')->sites(['en' => null, 'nl' => 'en', 'de' => 'en'])->save();
    }

    public function test_localized_reviews_are_not_counted_again(): void
    {
        foreach ([5, 4, 3] as $stars) {
            Entry::make()->collection('reviews')->locale('en')->data(['stars' => $stars])->save();
        }

        $this->assertCount(9, Entry::query()->where('collection', 'reviews')->get());

        app(ReviewRepository::class)->updateGlobal();

        $variables = GlobalSet::findByHandle('review')->inDefaultSite();

        $this->assertSame(3, $variables->get('reviews_count'));
        $this->assertSame(4.0, $variables->get('total_score'));
    }
}
