<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Leobsst\LaravelCookieConsent\Http\Middleware\EnsureVisitorId;

beforeEach(function () {
    $this->middleware = new EnsureVisitorId;
});

it('mints a new visitor id and shares it with views when no cookie is present', function () {
    $request = Request::create('/', 'GET');

    $this->middleware->handle($request, function ($req) {
        expect(view()->shared('posthogVisitorId'))->toBeString();
        expect(view()->shared('posthogVisitorId'))->not->toBeEmpty();

        return response('OK');
    });
});

it('queues a cookie for the newly minted visitor id', function () {
    $request = Request::create('/', 'GET');

    $this->middleware->handle($request, fn ($req) => response('OK'));

    expect(Cookie::hasQueued('anonymous_visitor_id'))->toBeTrue();

    $sharedVisitorId = view()->shared('posthogVisitorId');
    $queuedCookie = Cookie::queued('anonymous_visitor_id');

    expect($queuedCookie->getValue())->toBe($sharedVisitorId);
});

it('reuses the existing visitor id cookie instead of minting a new one', function () {
    $request = Request::create('/', 'GET');
    $request->cookies->set('anonymous_visitor_id', 'existing-visitor-id');

    $this->middleware->handle($request, function ($req) {
        expect(view()->shared('posthogVisitorId'))->toBe('existing-visitor-id');

        return response('OK');
    });

    expect(Cookie::getQueuedCookies())->toBeEmpty();
});

it('respects a custom cookie name from config', function () {
    config(['cookie-consent.VISITOR_ID_COOKIE' => 'custom_visitor_cookie']);

    $request = Request::create('/', 'GET');
    $request->cookies->set('custom_visitor_cookie', 'custom-id');

    $this->middleware->handle($request, function ($req) {
        expect(view()->shared('posthogVisitorId'))->toBe('custom-id');

        return response('OK');
    });
});

it('passes the request through to the next middleware', function () {
    $request = Request::create('/', 'GET');
    $nextCalled = false;

    $this->middleware->handle($request, function ($req) use (&$nextCalled) {
        $nextCalled = true;

        return response('Next middleware called');
    });

    expect($nextCalled)->toBeTrue();
});
