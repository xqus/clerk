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

    public function getEnvironmentSetUp($app)
    {
        include_once __DIR__ . '/migrations/create_users_table.php.stub';

        // run the up() method (perform the migration)
        (new \CreateUsersTable)->up();
    }
}
