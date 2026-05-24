<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Http\Controllers;

use Illuminate\Http\Response;

final class GetAdsBinaryFileController
{
    public function __invoke(): Response
    {
        return response()->view('cookie-consent::ads', [
            'clientId' => config('cookie-consent.GOOGLE_ADSENSE_CLIENT_ID'),
        ])->withHeaders(['Content-Type' => 'text/plain']);
    }
}
