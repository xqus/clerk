<?php

namespace xqus\Clerk\Tests;

use xqus\Clerk\ClerkServiceProvider;
use Illuminate\Support\Facades\Artisan;

class TestCase extends \Orchestra\Testbench\TestCase {

    protected function getPackageProviders($app) {
        return [
            ClerkServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app) {
        // perform environment setup
    }
}
