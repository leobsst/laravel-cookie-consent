<?php

declare(strict_types=1);

use Leobsst\LaravelCookieConsent\Http\Middleware\HandleCookieConsent;

beforeEach(function () {
    $this->middleware = new HandleCookieConsent;
});

it('shares cookie consent status with views when consent is null', function () {
    $request = request();

    $response = $this->middleware->handle($request, function ($req) {
        expect(view()->shared('cookieConsentStatus'))->toBeNull();
        expect(view()->shared('loadTrackingScripts'))->toBeFalse();

        return response('OK');
    });

    expect($response->getContent())->toBe('OK');
});

it('shares tracking scripts as true when consent is accepted', function () {
    session(['cookie_consent' => true]);

    $request = request();

    $response = $this->middleware->handle($request, function ($req) {
        expect(view()->shared('cookieConsentStatus'))->toBeTrue();
        expect(view()->shared('loadTrackingScripts'))->toBeTrue();

        return response('OK');
    });

    expect($response->getContent())->toBe('OK');
});

it('shares tracking scripts as false when consent is denied', function () {
    session(['cookie_consent' => false]);

    $request = request();

    $response = $this->middleware->handle($request, function ($req) {
        expect(view()->shared('cookieConsentStatus'))->toBeFalse();
        expect(view()->shared('loadTrackingScripts'))->toBeFalse();

        return response('OK');
    });

    expect($response->getContent())->toBe('OK');
});

it('passes the request through to the next middleware', function () {
    $request = request();
    $nextCalled = false;

    $this->middleware->handle($request, function ($req) use (&$nextCalled) {
        $nextCalled = true;

        return response('Next middleware called');
    });

    expect($nextCalled)->toBeTrue();
});

it('returns the response from the next middleware', function () {
    $request = request();

    $response = $this->middleware->handle($request, function ($req) {
        return response('Custom response', 201);
    });

    expect($response->getContent())->toBe('Custom response');
    expect($response->getStatusCode())->toBe(201);
});
