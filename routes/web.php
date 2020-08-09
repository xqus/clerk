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
});
