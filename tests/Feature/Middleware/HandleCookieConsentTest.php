<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Leobsst\LaravelCookieConsent\Http\Middleware\HandleCookieConsent;

beforeEach(function () {
    $this->middleware = new HandleCookieConsent;
    unset($_COOKIE['cookie_consent']);
});

afterEach(function () {
    unset($_COOKIE['cookie_consent']);
});

it('shares cookie consent status with views when consent is null', function () {
    $request = Request::create('/', 'GET');

    $response = $this->middleware->handle($request, function ($req) {
        expect(view()->shared('cookieConsentStatus'))->toBeNull();

        return response('OK');
    });

    expect($response->getContent())->toBe('OK');
});

it('shares cookie consent status as full when consent is accepted', function () {
    $_COOKIE['cookie_consent'] = 'full';
    $request = Request::create('/', 'GET');

    $response = $this->middleware->handle($request, function ($req) {
        expect(view()->shared('cookieConsentStatus'))->toBe('full');

        return response('OK');
    });

    expect($response->getContent())->toBe('OK');
});

it('shares cookie consent status as none when consent is denied', function () {
    $_COOKIE['cookie_consent'] = 'none';
    $request = Request::create('/', 'GET');

    $response = $this->middleware->handle($request, function ($req) {
        expect(view()->shared('cookieConsentStatus'))->toBe('none');

        return response('OK');
    });

    expect($response->getContent())->toBe('OK');
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

it('returns the response from the next middleware', function () {
    $request = Request::create('/', 'GET');

    $response = $this->middleware->handle($request, function ($req) {
        return response('Custom response', 201);
    });

    expect($response->getContent())->toBe('Custom response');
    expect($response->getStatusCode())->toBe(201);
});
