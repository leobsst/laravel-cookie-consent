<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Leobsst\LaravelCookieConsent\Livewire\CookieConsent;

beforeEach(function () {
    Route::get('/privacy-policy', fn () => 'Privacy Policy')->name('privacy.policy');
});

it('can instantiate the component', function () {
    $component = new CookieConsent;

    expect($component)->toBeInstanceOf(CookieConsent::class);
});

it('initializes with consent from cookie', function () {
    $request = Request::create('/', 'GET');
    $request->cookies->set('cookie_consent', '1');

    $component = new CookieConsent;
    $component->mount($request);

    expect($component->consent)->toBe('1');
});

it('initializes with null consent when no cookie value exists', function () {
    $request = Request::create('/', 'GET');

    $component = new CookieConsent;
    $component->mount($request);

    expect($component->consent)->toBeNull();
});

it('sets loadScript to true when Google Tag Manager ID is configured', function () {
    config(['cookie-consent.GOOGLE_TAG_MANAGER_ID' => 'GTM-XXXXXXX']);

    $request = Request::create('/', 'GET');
    $component = new CookieConsent;
    $component->mount($request);

    expect($component->loadScript)->toBeTrue();
});

it('sets loadScript to false when Google Tag Manager ID is not configured', function () {
    config(['cookie-consent.GOOGLE_TAG_MANAGER_ID' => null]);

    $request = Request::create('/', 'GET');
    $component = new CookieConsent;
    $component->mount($request);

    expect($component->loadScript)->toBeFalse();
});

it('sets learn more link from config as string when route does not exist', function () {
    // When a route name doesn't exist, it's used as-is
    config(['cookie-consent.LEARN_MORE_LINK' => 'non.existent.route']);

    $request = Request::create('/', 'GET');
    $component = new CookieConsent;
    $component->mount($request);

    // Since the route doesn't exist, it should use the string as-is
    expect($component->learnMoreLink)->toBe('non.existent.route');
});

it('sets learn more link from config when it is a URL', function () {
    $url = 'https://example.com/privacy';
    config(['cookie-consent.LEARN_MORE_LINK' => $url]);

    $request = Request::create('/', 'GET');
    $component = new CookieConsent;
    $component->mount($request);

    expect($component->learnMoreLink)->toBe($url);
});

it('sets learn more link to null when not configured', function () {
    config(['cookie-consent.LEARN_MORE_LINK' => null]);

    $request = Request::create('/', 'GET');
    $component = new CookieConsent;
    $component->mount($request);

    expect($component->learnMoreLink)->toBeNull();
});

it('queues cookie when consent is changed', function () {
    $request = Request::create('/', 'GET');
    $component = new CookieConsent;
    $component->mount($request);
    $component->consent = '1';
    $component->updatedConsent();

    // Check that a cookie was queued
    $cookies = \Illuminate\Support\Facades\Cookie::getQueuedCookies();
    expect($cookies)->not->toBeEmpty();
    expect($cookies[0]->getName())->toBe('cookie_consent');
    expect($cookies[0]->getValue())->toBe('1');
});

it('queues cookie when consent is denied', function () {
    $request = Request::create('/', 'GET');
    $component = new CookieConsent;
    $component->mount($request);
    $component->consent = '0';
    $component->updatedConsent();

    // Check that a cookie was queued
    $cookies = \Illuminate\Support\Facades\Cookie::getQueuedCookies();
    expect($cookies)->not->toBeEmpty();
    expect($cookies[0]->getName())->toBe('cookie_consent');
    expect($cookies[0]->getValue())->toBe('0');
});

it('uses the correct view name from config', function () {
    config(['cookie-consent.CONSENT_BANNER_VIEW' => 'cookie-consent::livewire.cookie-consent']);

    $component = new CookieConsent;
    $view = $component->render();

    expect($view->name())->toBe('cookie-consent::livewire.cookie-consent');
});

it('can change consent to accept', function () {
    $request = Request::create('/', 'GET');
    $component = new CookieConsent;
    $component->mount($request);

    expect($component->consent)->toBeNull();

    $component->consent = '1';
    $component->updatedConsent();

    expect($component->consent)->toBe('1');
});

it('can change consent to deny', function () {
    $request = Request::create('/', 'GET');
    $component = new CookieConsent;
    $component->mount($request);

    expect($component->consent)->toBeNull();

    $component->consent = '0';
    $component->updatedConsent();

    expect($component->consent)->toBe('0');
});

it('maintains consent value when cookie is set', function () {
    $request = Request::create('/', 'GET');
    $request->cookies->set('cookie_consent', '1');

    $component = new CookieConsent;
    $component->mount($request);

    expect($component->consent)->toBe('1');
});

it('initializes with false consent from cookie when denied', function () {
    $request = Request::create('/', 'GET');
    $request->cookies->set('cookie_consent', '0');

    $component = new CookieConsent;
    $component->mount($request);

    expect($component->consent)->toBe('0');
});
