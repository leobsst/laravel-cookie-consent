<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Leobsst\LaravelCookieConsent\Components\CookieBanner;
use Leobsst\LaravelCookieConsent\Components\Scripts;
use Leobsst\LaravelCookieConsent\Http\Middleware\HandleCookieConsent;

it('can instantiate the CookieBanner component', function () {
    $component = app()->make(CookieBanner::class);

    expect($component)->toBeInstanceOf(CookieBanner::class);
});

it('can instantiate the HandleCookieConsent middleware', function () {
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
        'POSTHOG_PROJECT_TOKEN',
        'POSTHOG_HOST',
        'POSTHOG_UI_HOST',
        'VISITOR_ID_COOKIE',
        'VISITOR_ID_COOKIE_LIFETIME_DAYS',
        'LEARN_MORE_LINK',
        'CONSENT_BANNER_VIEW',
        'ACCENT_COLOR',
    ]);
});

it('has default visitor id cookie config values', function () {
    expect(config('cookie-consent.VISITOR_ID_COOKIE'))->toBe('anonymous_visitor_id');
    expect(config('cookie-consent.VISITOR_ID_COOKIE_LIFETIME_DAYS'))->toBe(400);
});

it('has default config values', function () {
    expect(config('cookie-consent.LEARN_MORE_LINK'))->toBe('/privacy-policy');
    expect(config('cookie-consent.CONSENT_BANNER_VIEW'))->toBe('cookie-consent::components.cookie-banner');
    expect(config('cookie-consent.ACCENT_COLOR'))->toBe('#3490dc');
    expect(config('cookie-consent.POSTHOG_PROJECT_TOKEN'))->toBeNull();
    expect(config('cookie-consent.POSTHOG_HOST'))->toBe('https://eu.i.posthog.com');
    expect(config('cookie-consent.POSTHOG_UI_HOST'))->toBeNull();
});

it('cookieConsentScripts directive includes posthog config', function () {
    $directive = Blade::getCustomDirectives()['cookieConsentScripts'];
    $output = $directive();

    expect($output)->toContain("config('cookie-consent.POSTHOG_PROJECT_TOKEN')");
    expect($output)->toContain("config('cookie-consent.POSTHOG_HOST')");
    expect($output)->toContain("config('cookie-consent.POSTHOG_UI_HOST')");
});

it('loads package views', function () {
    expect(view()->exists('cookie-consent::components.cookie-banner'))->toBeTrue();
    expect(view()->exists('cookie-consent::components.scripts'))->toBeTrue();
});

it('loads package translations', function () {
    app()->setLocale('en');

    expect(__('Cookie Consent'))->toBeString();
});

it('registers Blade components', function () {
    $component = app()->make(Scripts::class);

    expect($component)->toBeInstanceOf(Scripts::class);
});
