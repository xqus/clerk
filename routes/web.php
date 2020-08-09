<?php

use Illuminate\Support\Facades\Route;
use Boonei\Scaffold\Http\Controllers\ProfileController;

Route::get('/profile', [ProfileController::class, 'index'])
     ->name('scaffold.profile');

Route::patch('/profile', [ProfileController::class, 'save'])
      ->name('scaffold.profile.patch');

Route::patch('/profile/password', [ProfileController::class, 'savePassword'])
      ->name('scaffold.profile.password.patch');

/*Route::post('/profile/paymentmethod', [ProfileController::class, 'addPaymentMethod'])
      ->name('scaffold.profile.paymentmethod.post');

Route::delete('/profile/paymentmethod', [ProfileController::class, 'remPaymentMethod'])
      ->name('scaffold.profile.paymentmethod.delete');

Route::patch('/profile/subscription', [ProfileController::class, 'updateSubscription'])
      ->name('scaffold.profile.subscription.patch');
*/


Route::group(['prefix' => 'api/v1'], function(){
    Route::get('/user/setup-intent', [ProfileController::class, 'getSetupIntent']);
    Route::get('/user/subscription-plans', [ProfileController::class, 'getSubscriptionPlans']);
    Route::post('/user/payments', [ProfileController::class, 'postPaymentMethods']);
    Route::get('/user/payment-methods', [ProfileController::class, 'getPaymentMethods']);
    Route::post('/user/remove-payment', [ProfileController::class, 'removePaymentMethod']);
    Route::put('/user/subscription', [ProfileController::class, 'updateSubscription']);

});
