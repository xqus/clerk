<?php

use xqus\Clerk\Http\Controllers\PaymentMethodController;
use Illuminate\Support\Facades\Route;
use xqus\Clerk\Http\Controllers\SubscriptionController;

Route::get('/subscription', [SubscriptionController::class, 'index'])
    ->name('clerk.subscription');

Route::group(['prefix' => 'api/v1'], function(){
    Route::get('/user/setup-intent', [SubscriptionController::class, 'intent']);

    Route::post('/user/payments', [PaymentMethodController::class, 'add']);
    Route::get('/user/payment-methods', [PaymentMethodController::class, 'get']);
    Route::post('/user/remove-payment', [PaymentMethodController::class, 'remove']);

    Route::put('/user/subscription', [SubscriptionController::class, 'update']);
    Route::get('/user/subscription-plans', [SubscriptionController::class, 'getPlans']);
});
