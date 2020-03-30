<?php

use Illuminate\Support\Facades\Route;
use Boonei\Scaffold\Http\Controllers\ProfileController;

Route::get('/profile', [ProfileController::class, 'index'])
     ->name('scaffold.profile');
