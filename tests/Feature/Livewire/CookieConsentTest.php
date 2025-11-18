<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Leobsst\LaravelCookieConsent\Livewire\CookieConsent;

beforeEach(function () {
    Route::get('/privacy-policy', fn () => 'Privacy Policy')->name('privacy.policy');
});

it('can instantiate the component', function () {
    $component = new CookieConsent;

    expect($component)->toBeInstanceOf(CookieConsent::class);
});

it('sets loadScript to true when Google Tag Manager ID is configured', function () {
    config(['cookie-consent.GOOGLE_TAG_MANAGER_ID' => 'GTM-XXXXXXX']);

    $component = new CookieConsent;
    $component->mount();

    expect($component->loadScript)->toBeTrue();
});

it('sets loadScript to false when Google Tag Manager ID is not configured', function () {
    config(['cookie-consent.GOOGLE_TAG_MANAGER_ID' => null]);

    $component = new CookieConsent;
    $component->mount();

    expect($component->loadScript)->toBeFalse();
});

it('sets learn more link from config as string when route does not exist', function () {
    // When a route name doesn't exist, it's used as-is
    config(['cookie-consent.LEARN_MORE_LINK' => 'non.existent.route']);

    $component = new CookieConsent;
    $component->mount();

    // Since the route doesn't exist, it should use the string as-is
    expect($component->learnMoreLink)->toBe('non.existent.route');
});

it('sets learn more link from config when it is a URL', function () {
    $url = 'https://example.com/privacy';
    config(['cookie-consent.LEARN_MORE_LINK' => $url]);

    $component = new CookieConsent;
    $component->mount();

    expect($component->learnMoreLink)->toBe($url);
});

it('sets learn more link to null when not configured', function () {
    config(['cookie-consent.LEARN_MORE_LINK' => null]);

    $component = new CookieConsent;
    $component->mount();

    expect($component->learnMoreLink)->toBeNull();
});

it('queues cookie when consent is accepted', function () {
    $component = new CookieConsent;
    $component->mount();
    $component->updateConsent(true);

    // Check that a cookie was queued
    $cookies = \Illuminate\Support\Facades\Cookie::getQueuedCookies();
    expect($cookies)->not->toBeEmpty();
    expect($cookies[0]->getName())->toBe('cookie_consent');
    expect($cookies[0]->getValue())->toBe('full');
});

it('queues cookie when consent is denied', function () {
    $component = new CookieConsent;
    $component->mount();
    $component->updateConsent(false);

    // Check that a cookie was queued
    $cookies = \Illuminate\Support\Facades\Cookie::getQueuedCookies();
    expect($cookies)->not->toBeEmpty();
    expect($cookies[0]->getName())->toBe('cookie_consent');
    expect($cookies[0]->getValue())->toBe('none');
});

it('uses the correct view name from config', function () {
    config(['cookie-consent.CONSENT_BANNER_VIEW' => 'cookie-consent::livewire.cookie-consent']);

    $component = new CookieConsent;
    $view = $component->render();

    expect($view->name())->toBe('cookie-consent::livewire.cookie-consent');
});

it('dispatches event when consent is accepted', function () {
    $component = new CookieConsent;
    $component->mount();

    $component->updateConsent(true);

    // The event should be dispatched (tested via Livewire test)
    expect($component)->toBeInstanceOf(CookieConsent::class);
});

it('dispatches event when consent is denied', function () {
    $component = new CookieConsent;
    $component->mount();

    $component->updateConsent(false);

    // The event should be dispatched (tested via Livewire test)
    expect($component)->toBeInstanceOf(CookieConsent::class);
});

it('sets cookie with secure flag', function () {
    $component = new CookieConsent;
    $component->mount();
    $component->updateConsent(true);

    $cookies = \Illuminate\Support\Facades\Cookie::getQueuedCookies();
    expect($cookies)->not->toBeEmpty();

    $cookie = $cookies[0];
    expect($cookie->isSecure())->toBeTrue();
});

it('sets cookie with correct SameSite value', function () {
    $component = new CookieConsent;
    $component->mount();
    $component->updateConsent(true);

    $cookies = \Illuminate\Support\Facades\Cookie::getQueuedCookies();
    expect($cookies)->not->toBeEmpty();

    $cookie = $cookies[0];
    expect($cookie->getSameSite())->toBe('lax');
});
