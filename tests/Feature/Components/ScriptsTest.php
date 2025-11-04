<?php

declare(strict_types=1);

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

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
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
