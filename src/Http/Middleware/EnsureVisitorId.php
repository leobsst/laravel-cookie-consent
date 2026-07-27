<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class EnsureVisitorId
{
    /**
     * Mint (or read) a stable anonymous visitor id and share it with all views,
     * so the PostHog JS snippet can bootstrap posthog-js with the same
     * distinct_id the backend uses for anonymous captures.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cookieName = config('cookie-consent.VISITOR_ID_COOKIE', 'anonymous_visitor_id');
        $visitorId = $request->cookie($cookieName);

        if (! $visitorId) {
            $visitorId = (string) Str::uuid();

            $lifetimeMinutes = 60 * 24 * config('cookie-consent.VISITOR_ID_COOKIE_LIFETIME_DAYS', 400);
            Cookie::queue(Cookie::make($cookieName, $visitorId, $lifetimeMinutes));
        }

        view()->share('posthogVisitorId', $visitorId);

        return $next($request);
    }
}
