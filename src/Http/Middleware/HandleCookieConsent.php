<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCookieConsent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has given consent (either true or false)
        // null means no choice has been made yet
        // Share the consent status with all views
        view()->share('cookieConsentStatus', $request->cookie('cookie_consent'));

        return $next($request);
    }
}
