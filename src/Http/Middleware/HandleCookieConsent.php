<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class HandleCookieConsent
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has given consent (either true or false)
        // null means no choice has been made yet
        // Share the consent status with all views
        view()->share('cookieConsentStatus', $_COOKIE['cookie_consent'] ?? null);

        return $next($request);
    }
}
