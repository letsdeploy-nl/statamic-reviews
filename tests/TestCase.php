<?php

namespace Letsdeploy\Reviews\Tests;

use Letsdeploy\Reviews\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
