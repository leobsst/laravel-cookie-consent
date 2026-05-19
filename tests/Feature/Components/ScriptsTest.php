<?php

declare(strict_types=1);

use Illuminate\View\View;
use Leobsst\LaravelCookieConsent\Components\Scripts;

it('can instantiate the Scripts component', function () {
    $component = new Scripts;

    expect($component)->toBeInstanceOf(Scripts::class);
});

it('can instantiate with Google Tag Manager ID', function () {
    $gtmId = 'GTM-XXXXXXX';
    $component = new Scripts($gtmId);

    expect($component)->toBeInstanceOf(Scripts::class);
});

it('renders the scripts view', function () {
    $component = new Scripts;
    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
    expect($view->name())->toBe('cookie-consent::components.scripts');
});

it('passes Google Tag Manager ID to the view', function () {
    $gtmId = 'GTM-XXXXXXX';
    $component = new Scripts($gtmId);
    $view = $component->render();

    expect($view->getData())->toHaveKey('googleTagManagerId');
    expect($view->getData()['googleTagManagerId'])->toBe($gtmId);
});

it('passes null Google Tag Manager ID to the view when not provided', function () {
    $component = new Scripts;
    $view = $component->render();

    expect($view->getData())->toHaveKey('googleTagManagerId');
    expect($view->getData()['googleTagManagerId'])->toBeNull();
});

it('can resolve component from container', function () {
    $component = app(Scripts::class);

    expect($component)->toBeInstanceOf(Scripts::class);
});

it('renders view with correct data structure', function () {
    $gtmId = 'GTM-TEST123';
    $component = new Scripts($gtmId);
    $view = $component->render();
    $data = $view->getData();

    expect($data)->toBeArray();
    expect($data)->toHaveKey('googleTagManagerId');
});

it('passes posthog config to the view', function () {
    $component = new Scripts(
        posthogProjectToken: 'phc_test',
        posthogHost: 'https://eu.i.posthog.com',
        posthogUiHost: 'https://eu.posthog.com',
    );
    $data = $component->render()->getData();

    expect($data['posthogProjectToken'])->toBe('phc_test');
    expect($data['posthogHost'])->toBe('https://eu.i.posthog.com');
    expect($data['posthogUiHost'])->toBe('https://eu.posthog.com');
});

it('passes null posthog values when not provided', function () {
    $component = new Scripts;
    $data = $component->render()->getData();

    expect($data['posthogProjectToken'])->toBeNull();
    expect($data['posthogHost'])->toBeNull();
    expect($data['posthogUiHost'])->toBeNull();
});
