<?php

declare(strict_types=1);

it('can load the package configuration', function () {
    expect(config('cookie-consent'))->toBeArray();
});

it('has all required config keys', function () {
    $config = config('cookie-consent');

    expect($config)->toHaveKey('GOOGLE_TAG_MANAGER_ID')
        ->and($config)->toHaveKey('LEARN_MORE_LINK')
        ->and($config)->toHaveKey('CONSENT_BANNER_VIEW')
        ->and($config)->toHaveKey('ACCENT_COLOR');
});
