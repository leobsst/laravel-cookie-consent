<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Leobsst\LaravelCookieConsent\Http\Middleware\HandleCookieConsent;
use Leobsst\LaravelCookieConsent\Livewire\CookieConsent;
use Livewire\Livewire;

it('registers the Livewire component', function () {
    // Test that the Livewire component can be instantiated
    $component = app()->make(CookieConsent::class);

    expect($component)->toBeInstanceOf(CookieConsent::class);
});

it('can instantiate the HandleCookieConsent middleware', function () {
    // Test that the middleware can be instantiated
    $middleware = app()->make(HandleCookieConsent::class);

    expect($middleware)->toBeInstanceOf(HandleCookieConsent::class);
});

it('registers the cookieConsentScripts Blade directive', function () {
    $directives = Blade::getCustomDirectives();

    expect($directives)->toHaveKey('cookieConsentScripts');
    expect($directives['cookieConsentScripts'])->toBeCallable();
});

it('cookieConsentScripts directive renders the scripts component', function () {
    config(['cookie-consent.GOOGLE_TAG_MANAGER_ID' => 'GTM-TEST123']);

    $directive = Blade::getCustomDirectives()['cookieConsentScripts'];
    $output = $directive();

    expect($output)->toContain("view('cookie-consent::components.scripts'");
    expect($output)->toContain("config('cookie-consent.GOOGLE_TAG_MANAGER_ID')");
});

it('loads package config file', function () {
    expect(config('cookie-consent'))->toBeArray();
    expect(config('cookie-consent.GOOGLE_TAG_MANAGER_ID'))->toBeNull();
});

it('has correct config structure', function () {
    $config = config('cookie-consent');

    expect($config)->toHaveKeys([
        'GOOGLE_TAG_MANAGER_ID',
        'LEARN_MORE_LINK',
        'CONSENT_BANNER_VIEW',
        'ACCENT_COLOR',
    ]);
});

it('has default config values', function () {
    expect(config('cookie-consent.LEARN_MORE_LINK'))->toBe('/privacy-policy');
    expect(config('cookie-consent.CONSENT_BANNER_VIEW'))->toBe('cookie-consent::livewire.cookie-consent');
    expect(config('cookie-consent.ACCENT_COLOR'))->toBe('#3490dc');
});

it('loads package views', function () {
    expect(view()->exists('cookie-consent::livewire.cookie-consent'))->toBeTrue();
    expect(view()->exists('cookie-consent::components.scripts'))->toBeTrue();
});

it('loads package translations', function () {
    app()->setLocale('en');

    // Test that translation keys are available
    expect(__('Cookie Consent'))->toBeString();
});

it('registers Blade components', function () {
    // Test that the component can be instantiated
    $component = app()->make(\Leobsst\LaravelCookieConsent\Components\Scripts::class);

    expect($component)->toBeInstanceOf(\Leobsst\LaravelCookieConsent\Components\Scripts::class);
});
