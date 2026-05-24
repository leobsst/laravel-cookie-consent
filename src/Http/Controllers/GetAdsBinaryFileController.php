<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GetAdsBinaryFileController
{
    public function __invoke(Request $request): Response
    {
        return response()->view('cookie-consent::ads', [
            'clientId' => config('cookie-consent.GOOGLE_ADSENSE_CLIENT_ID'),
        ])->withHeaders(['Content-Type' => 'text/plain']);
    }
}
