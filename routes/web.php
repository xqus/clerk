<?php

use Illuminate\Support\Facades\Route;
use Boonei\Scaffold\Http\Controllers\ProfileController;

Route::get('/profile', [ProfileController::class, 'index'])
     ->name('scaffold.profile');

Route::patch('/profile', [ProfileController::class, 'save'])
      ->name('scaffold.profile.patch');

Route::patch('/profile/password', [ProfileController::class, 'savePassword'])
      ->name('scaffold.profile.password.patch');
