<?php

use Illuminate\Support\Facades\Route;
use Boonei\Scaffold\Http\Controllers\ProfileController;

Route::get('/profile', [ProfileController::class, 'index'])
     ->name('scaffold.profile');

Route::group(['prefix' => 'api/v1'], function(){
    Route::get('/user/setup-intent', [ProfileController::class, 'getSetupIntent']);
    Route::get('/user/subscription-plans', [ProfileController::class, 'getSubscriptionPlans']);
    Route::post('/user/payments', [ProfileController::class, 'postPaymentMethods']);
    Route::get('/user/payment-methods', [ProfileController::class, 'getPaymentMethods']);
    Route::post('/user/remove-payment', [ProfileController::class, 'removePaymentMethod']);
    Route::put('/user/subscription', [ProfileController::class, 'updateSubscription']);

});
