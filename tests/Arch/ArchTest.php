<?php

declare(strict_types=1);

arch('it ensures no debugging functions are left in the code')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'print_r'])
    ->not->toBeUsed();

arch('it ensures strict types are declared in most files')
    ->expect('Leobsst\LaravelCookieConsent')
    ->toUseStrictTypes()
    ->ignoring([
        'Leobsst\LaravelCookieConsent\LaravelCookieConsentServiceProvider',
    ]);

arch('it ensures classes are final or abstract')
    ->expect('Leobsst\LaravelCookieConsent')
    ->classes()
    ->toBeFinal()
    ->ignoring([
        'Leobsst\LaravelCookieConsent\LaravelCookieConsentServiceProvider',
    ]);

arch('it ensures no extends are used except for base classes')
    ->expect('Leobsst\LaravelCookieConsent')
    ->classes()
    ->toExtend('Spatie\LaravelPackageTools\PackageServiceProvider')
    ->ignoring([
        'Leobsst\LaravelCookieConsent\Components\CookieBanner',
        'Leobsst\LaravelCookieConsent\Components\Scripts',
        'Leobsst\LaravelCookieConsent\Http\Middleware\HandleCookieConsent',
    ]);

arch('Blade components extend Component')
    ->expect('Leobsst\LaravelCookieConsent\Components')
    ->toExtend('Illuminate\View\Component');

arch('middleware classes are in the Http\Middleware namespace')
    ->expect('Leobsst\LaravelCookieConsent\Http\Middleware')
    ->toBeClasses();

arch('it ensures no die or exit statements are used')
    ->expect('Leobsst\LaravelCookieConsent')
    ->not->toUse(['die', 'exit']);

arch('it ensures all classes have correct namespace')
    ->expect('Leobsst\LaravelCookieConsent')
    ->classes()
    ->toBeClasses();

arch('service providers are properly configured')
    ->expect('Leobsst\LaravelCookieConsent\LaravelCookieConsentServiceProvider')
    ->toExtend('Spatie\LaravelPackageTools\PackageServiceProvider');

arch('it does not use env helper outside of config files')
    ->expect('Leobsst\LaravelCookieConsent')
    ->not->toUse('env');

arch('globals')
    ->expect(['Leobsst\LaravelCookieConsent'])
    ->not->toUse(['die', 'dd', 'dump', 'echo', 'print', 'sleep']);
