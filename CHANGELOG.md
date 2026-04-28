# CHANGELOG

## v2.0.3 - 2026-04-28

### What's changed

**`resources/views/components/cookie-banner.blade.php`**

Added a minimal inline script inside the existing config block. This script immediately defines `window.__cookieConsent` (with `accept()` and `refuse()` methods) using the server-injected `window.CookieConsent` config.

The `if (!window.__cookieConsent)` guard ensures that if the vendor script loads normally, it takes over without conflict.


---

### Impact

| Scenario | Before | After |
|---|---|---|
| Vendor script loads normally | ✅ Works | ✅ Works (unchanged behavior, gtag included) |
| Vendor script blocked | ❌ `TypeError: Cannot read properties of undefined (reading 'accept')` — banner cannot be closed | ✅ Cookie written, banner hidden |

The fallback intentionally does not call `gtag` / GTM — those scripts are also blocked in this scenario. On the next page load, the `cookie_consent` cookie is read server-side and the banner is no longer displayed.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.2...v2.0.3

## v2.0.2 - 2026-04-16

### What's changed

- Added Laravel 13 support in `composer.json` (`illuminate/contracts`: `^13.0`)
- Added `orchestra/testbench` `^11.0` support
- Added `pestphp/pest`, `pestphp/pest-plugin-arch` and `pestphp/pest-plugin-laravel` `^4.0` support
- Updated CI matrix in `run-tests.yml` to test against Laravel 13 with PHP 8.3 and 8.4
- Excluded PHP 8.2 from Laravel 13 test matrix (Laravel 13 requires PHP 8.3+)
- Injected correct Pest version per Laravel version in CI (`^3.0` for L11/L12, `^4.0` for L13)

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.1...v2.0.2

## v2.0.1 - 2026-03-18

### Fixed

- Banner's JavaScript (`cookie-consent.js`) is now injected inline via `@once` instead of `@push('scripts')`. This fixes `window.__cookieConsent is undefined` errors in layouts that use the anonymous component pattern (`<x-layout>` with `{{ $slot }}`), where `@stack('scripts')` in the `<head>` is rendered before slot content is processed, silently dropping the pushed script tag.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.0...v2.0.1

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
