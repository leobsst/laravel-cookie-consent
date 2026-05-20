<?php

declare(strict_types=1);

use Illuminate\View\View;
use Leobsst\LaravelCookieConsent\Components\CookieBanner;

it('can instantiate the component', function () {
    $component = new CookieBanner;

    expect($component)->toBeInstanceOf(CookieBanner::class);
});

it('sets loadScript to true when Google Tag Manager ID is configured', function () {
    config(['cookie-consent.GOOGLE_TAG_MANAGER_ID' => 'GTM-XXXXXXX']);

    $component = new CookieBanner;

    expect($component->loadScript)->toBeTrue();
});

it('sets loadScript to false when Google Tag Manager ID is not configured', function () {
    config(['cookie-consent.GOOGLE_TAG_MANAGER_ID' => null]);

    $component = new CookieBanner;

    expect($component->loadScript)->toBeFalse();
});

it('sets loadScript to true when PostHog token is configured', function () {
    config(['cookie-consent.GOOGLE_TAG_MANAGER_ID' => null]);
    config(['cookie-consent.POSTHOG_PROJECT_TOKEN' => 'phc_test']);

    $component = new CookieBanner;

    expect($component->loadScript)->toBeTrue();
});

it('sets loadScript to false when neither GTM nor PostHog is configured', function () {
    config(['cookie-consent.GOOGLE_TAG_MANAGER_ID' => null]);
    config(['cookie-consent.POSTHOG_PROJECT_TOKEN' => null]);

    $component = new CookieBanner;

    expect($component->loadScript)->toBeFalse();
});

it('passes posthog config from config file', function () {
    config(['cookie-consent.POSTHOG_PROJECT_TOKEN' => 'phc_test']);
    config(['cookie-consent.POSTHOG_HOST' => 'https://eu.i.posthog.com']);
    config(['cookie-consent.POSTHOG_UI_HOST' => 'https://eu.posthog.com']);

    $component = new CookieBanner;

    expect($component->posthogProjectToken)->toBe('phc_test');
    expect($component->posthogHost)->toBe('https://eu.i.posthog.com');
    expect($component->posthogUiHost)->toBe('https://eu.posthog.com');
});

it('passes posthog values to the view', function () {
    config(['cookie-consent.POSTHOG_PROJECT_TOKEN' => 'phc_test']);
    config(['cookie-consent.POSTHOG_HOST' => 'https://eu.i.posthog.com']);
    config(['cookie-consent.POSTHOG_UI_HOST' => null]);

    $component = new CookieBanner;
    $data = $component->render()->getData();

    expect($data)->toHaveKeys(['posthogProjectToken', 'posthogHost', 'posthogUiHost']);
    expect($data['posthogUiHost'])->toBeNull();
});

it('sets learn more link from config as string when route does not exist', function () {
    config(['cookie-consent.LEARN_MORE_LINK' => 'non.existent.route']);

    $component = new CookieBanner;

    expect($component->learnMoreLink)->toBe('non.existent.route');
});

it('sets learn more link from config when it is a URL', function () {
    $url = 'https://example.com/privacy';
    config(['cookie-consent.LEARN_MORE_LINK' => $url]);

    $component = new CookieBanner;

    expect($component->learnMoreLink)->toBe($url);
});

it('sets learn more link to null when not configured', function () {
    config(['cookie-consent.LEARN_MORE_LINK' => null]);

    $component = new CookieBanner;

    expect($component->learnMoreLink)->toBeNull();
});

it('passes accent color from config', function () {
    config(['cookie-consent.ACCENT_COLOR' => '#ff0000']);

    $component = new CookieBanner;

    expect($component->accentColor)->toBe('#ff0000');
});

it('converts cookie duration from minutes to seconds', function () {
    config(['cookie-consent.duration' => 60]); // 60 minutes

    $component = new CookieBanner;

    expect($component->cookieDuration)->toBe(3600); // 60 * 60 = 3600 seconds
});

it('passes same_site from config', function () {
    config(['cookie-consent.same_site' => 'Strict']);

    $component = new CookieBanner;

    expect($component->sameSite)->toBe('Strict');
});

it('renders the correct default view', function () {
    $component = new CookieBanner;
    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($view->name())->toBe('cookie-consent::components.cookie-banner');
});

it('uses custom view from config', function () {
    config(['cookie-consent.CONSENT_BANNER_VIEW' => 'cookie-consent::components.cookie-banner']);

    $component = new CookieBanner;
    $view = $component->render();

    expect($view->name())->toBe('cookie-consent::components.cookie-banner');
});

it('can resolve component from container', function () {
    $component = app(CookieBanner::class);

    expect($component)->toBeInstanceOf(CookieBanner::class);
});
