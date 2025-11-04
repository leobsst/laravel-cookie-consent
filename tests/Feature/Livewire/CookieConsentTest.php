<?php

declare(strict_types=1);

use Leobsst\LaravelCookieConsent\Livewire\CookieConsent;

beforeEach(function () {
    Route::get('/privacy-policy', fn () => 'Privacy Policy')->name('privacy.policy');
});

it('can instantiate the component', function () {
    $component = new CookieConsent;

    expect($component)->toBeInstanceOf(CookieConsent::class);
});

it('initializes with consent from session', function () {
    session(['cookie_consent' => true]);

    $component = new CookieConsent;
    $component->mount();

    expect($component->consent)->toBeTrue();
});

it('initializes with null consent when no session value exists', function () {
    $component = new CookieConsent;
    $component->mount();

    expect($component->consent)->toBeNull();
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

it('updates session when consent is changed', function () {
    $component = new CookieConsent;
    $component->mount();
    $component->consent = true;
    $component->updatedConsent();

    expect(session('cookie_consent'))->toBeTrue();
});

it('updates session when consent is denied', function () {
    $component = new CookieConsent;
    $component->mount();
    $component->consent = false;
    $component->updatedConsent();

    expect(session('cookie_consent'))->toBeFalse();
});

it('uses the correct view name from config', function () {
    config(['cookie-consent.CONSENT_BANNER_VIEW' => 'cookie-consent::livewire.cookie-consent']);

    $component = new CookieConsent;
    $view = $component->render();

    expect($view->name())->toBe('cookie-consent::livewire.cookie-consent');
});

it('can change consent to accept', function () {
    $component = new CookieConsent;
    $component->mount();

    expect($component->consent)->toBeNull();

    $component->consent = true;
    $component->updatedConsent();

    expect($component->consent)->toBeTrue();
    expect(session('cookie_consent'))->toBeTrue();
});

it('can change consent to deny', function () {
    $component = new CookieConsent;
    $component->mount();

    expect($component->consent)->toBeNull();

    $component->consent = false;
    $component->updatedConsent();

    expect($component->consent)->toBeFalse();
    expect(session('cookie_consent'))->toBeFalse();
});

it('maintains consent value when session is set', function () {
    session(['cookie_consent' => true]);

    $component = new CookieConsent;
    $component->mount();

    expect($component->consent)->toBeTrue();
});
