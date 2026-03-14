<?php

use App\Http\Controllers\Api\AssetController;
use Illuminate\Support\Facades\Route;

Route::apiResource('assets', AssetController::class)->only(['store', 'show']);
