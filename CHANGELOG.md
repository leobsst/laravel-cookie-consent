# CHANGELOG

## v2.0.0 - 2026-03-18

### Breaking Changes

- **Livewire dependency removed.** The package no longer requires `livewire/livewire`. Remove it from your project if it was only installed for this package.
- The cookie consent banner is now a **pure Blade component**. Replace `<livewire:laravel-cookie-consent::cookie-consent />` with `<x-cookie-consent::cookie-banner />` in your layout.
- The cookie is now set **client-side** (non-HttpOnly, readable by JavaScript) to support GTM Consent Mode v2. Previously it was set server-side via Laravel's encrypted cookie layer.
- Default `CONSENT_BANNER_VIEW` config value changed from `cookie-consent::livewire.cookie-consent` to `cookie-consent::components.cookie-banner`.

### Migration from v1.x

1. Replace `<livewire:laravel-cookie-consent::cookie-consent />` with `<x-cookie-consent::cookie-banner />` in your layout.
2. Re-publish assets: `php artisan vendor:publish --tag=cookie-consent-assets --force`
3. Remove `livewire/livewire` from your `composer.json` if it was only required for this package.

### Added

- New `CookieBanner` Blade component as a zero-dependency replacement for the Livewire component.
- Dark mode support out of the box.
- `SameSite` value configurable via the `cookie-consent.same_site` config key.

### Changed

- `HandleCookieConsent` middleware now reads the cookie directly from `$_COOKIE` instead of going through Laravel's encrypted cookie layer.
- All classes are now `final`.
- Updated CI dependencies: `ramsey/composer-install` v3 → v4, `actions/upload-artifact` v6 → v7.

### Removed

- `src/Livewire/CookieConsent.php` — replaced by `src/Components/CookieBanner.php`.
- `resources/views/livewire/cookie-consent.blade.php` — replaced by `resources/views/components/cookie-banner.blade.php`.
- Livewire registration from `LaravelCookieConsentServiceProvider`.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v1.0.7...v2.0.0

## v1.0.7 - 2025-11-18

### What's Changed

* Add cookie consent configuration

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v1.0.6...v1.0.7

## v1.0.5 + v1.0.6 - 2025-11-17

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v1.0.4...v1.0.6

## v1.0.4 - 2025-11-17

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v1.0.3...v1.0.4

## v1.0.3 - 2025-11-17

### What's Changed

* Fix Livewire missing root tag issue

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v1.0.2...v1.0.3

## v1.0.2 - 2025-11-12

### What's Changed

* Fix user consent not retrieved after page refresh

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v1.0.1...v1.0.2

## v1.0.1 - 2025-11-12

### What's Changed

* Removed shared property `loadTrackingScripts` based on use consent to load default user consent mode

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v1.0.0...v1.0.1

## v1.0.0 - 2025-11-04

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/commits/v1.0.0
