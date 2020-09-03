<?php

namespace xqus\Clerk\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Stripe\Stripe;
use xqus\Clerk\Tests\TestCase;
use xqus\Clerk\Tests\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabaseState;


class IntentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_setup_intent_is_created()
    {
        $user = factory(User::class)->create();

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $response = $this->actingAs($user)->get('/api/v1/user/setup-intent');

        $decodedResponse = json_decode($response->content());
        $this->assertTrue(isset($decodedResponse->client_secret));
    }
}
