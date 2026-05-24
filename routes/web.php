<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Leobsst\LaravelCookieConsent\Http\Controllers\GetAdsBinaryFileController;
use Leobsst\LaravelCookieConsent\Http\Middleware\CheckGoogleAdsenseAvailability;

Route::get('/ads.txt', GetAdsBinaryFileController::class)
    ->middleware(CheckGoogleAdsenseAvailability::class)
    ->name('cookie-consent.ads');
