<?php

declare(strict_types=1);

function renderScripts(array $data = []): string
{
    return view('cookie-consent::components.scripts', array_merge([
        'googleTagManagerId' => null,
        'googleAdsenseClientId' => null,
        'posthogProjectToken' => null,
        'posthogHost' => null,
        'posthogUiHost' => null,
        'cookieConsentStatus' => null,
    ], $data))->render();
}

it('bootstraps posthog-js with the shared visitor id when present', function () {
    $html = renderScripts([
        'posthogProjectToken' => 'phc_test',
        'posthogHost' => 'https://eu.i.posthog.com',
        'posthogVisitorId' => 'visitor-123',
    ]);

    expect($html)->toContain("visitorId: 'visitor-123'");
    expect($html)->toContain('bootstrap: window.__posthogConfig.visitorId ? { distinctID: window.__posthogConfig.visitorId } : undefined,');
});

it('does not set a bootstrap visitor id when none was shared', function () {
    $html = renderScripts([
        'posthogProjectToken' => 'phc_test',
        'posthogHost' => 'https://eu.i.posthog.com',
    ]);

    expect($html)->toContain('visitorId: null');
});

it('emits posthog.identify() when an identified id is shared', function () {
    $html = renderScripts([
        'posthogProjectToken' => 'phc_test',
        'posthogHost' => 'https://eu.i.posthog.com',
        'posthogIdentifiedId' => 'user@example.com',
        'posthogIdentifiedProperties' => ['email' => 'user@example.com', 'name' => 'Test User'],
    ]);

    expect($html)->toContain("posthog.identify('user@example.com'");
    expect($html)->toContain('email');
    expect($html)->toContain('user@example.com');
});

it('does not emit posthog.identify() when no identified id is shared', function () {
    $html = renderScripts([
        'posthogProjectToken' => 'phc_test',
        'posthogHost' => 'https://eu.i.posthog.com',
    ]);

    expect($html)->not->toContain('posthog.identify(');
});

it('renders no posthog script at all when the project token is missing', function () {
    $html = renderScripts();

    expect($html)->not->toContain('window.__posthogConfig');
});
