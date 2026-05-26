# CHANGELOG

## v2.0.24 - 2026-05-26

### What's changed

#### Fixes

- `HAS_ADSENSE()` now additionally checks for the presence of an `ins.adsbygoogle` element in the DOM before returning `true`. This prevents `window.location.reload()` from firing on pages where AdSense is globally configured but no ad slots are actually rendered (e.g. admin pages, pages without ad widgets).

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.23...v2.0.24

## v2.0.23 - 2026-05-26

### What's changed

#### New features

- `window.CookieConsent` now exposes `hasAdSense`, `hasGtm`, and `hasPosthog` boolean flags. `cookie-consent.js` uses these to activate only the relevant features per page. On accept, `window.location.reload()` is triggered only when `hasAdSense` is `true`, avoiding unnecessary reloads on GTM-only or PostHog-only setups.

#### Refactoring

- Replaced hardcoded TC strings in the IAB TCF 2.2 stub with spec-compliant strings generated at build time using `@iabtcf/core` with an inline minimal GVL — no network fetch required. `CMP_ID` updated to `28`, `tcfPolicyVersion` bumped to `4`.
- `updateGtag()` now guards on `hasGtm` or `hasAdSense`. `updatePosthog()` short-circuits when `hasPosthog` is `false`. The TCF stub is always installed so AdSense can find `window.__tcfapi` regardless of flag state.

#### Fixes

- Fixed Google Funding Choices loading its own consent banner alongside the custom one. `window.__tcfapi` is now always present with `displayStatus: 'hidden'`, signalling that a CMP is already active.
- Fixed feature flags being read before `window.CookieConsent` was defined. Flags are now set in `cookie-banner.blade.php` (rendered at `BODY_START`) and read lazily at call time rather than at script parse time.
- Fixed a crash (`TypeError: Cannot read properties of undefined (reading 'add')`) caused by vendor `755` (Google Advertising Products) declaring `specialPurposes: [1,2]` and `features: [1,2]` while those entries were absent from the top-level `GVL_DATA` maps, preventing TC strings from being generated.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.22...v2.0.23

## v2.0.22 - 2026-05-26

### What's changed

#### New features

- `window.CookieConsent` now exposes `hasAdSense`, `hasGtm`, and `hasPosthog` boolean flags, set by the Blade components (`cookie-banner.blade.php`, `scripts.blade.php`) so `cookie-consent.js` can conditionally activate only the features relevant to the current page setup.

#### Refactoring

- The TCF stub, `gtag`, and PostHog integration are now gated behind the `hasAdSense`, `hasGtm`, and `hasPosthog` feature flags, ensuring these scripts are only initialized when the corresponding service is actually present on the page.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.21...v2.0.22

## v2.0.21 - 2026-05-26

### What's changed

#### New features

- Ad slots (`ins.adsbygoogle`) are now hidden until consent is granted. On accept, slots are revealed and ads load without requiring a manual page reload. On refuse, slots remain hidden.

#### Refactoring

- Replaced hardcoded TC strings in the IAB TCF 2.2 stub with spec-compliant strings generated at build time using `@iabtcf/core`. An inline minimal GVL is bundled directly in `cookie-consent.js` — no network fetch required. `CMP_ID` updated to `28` (registered IAB CMP), `tcfPolicyVersion` bumped to `4`. `displayStatus` is now always `'hidden'` to prevent Google Funding Choices from loading its own consent banner alongside the custom one.

#### Fixes

- Fixed ads not loading after consent accept without a manual page reload. `flushAdSenseQueue()` is now delayed by 300ms after `__tcfapi._notify()` fires, giving AdSense time to process the updated TC string and flip its internal `FJPve` flag before slots are re-pushed.
- Fixed `TagError: adsbygoogle.push() error: No slot size for availableWidth=0`. When consent has not been granted, `window.adsbygoogle.push` is replaced with a no-op proxy before the AdSense script initializes. On accept, the real `Array.prototype.push` is restored and slots are re-pushed after a double `requestAnimationFrame` so the browser completes layout first.
- Fixed a crash (`TypeError: Cannot read properties of undefined (reading 'add')`) at page load caused by vendor `755` (Google Advertising Products) declaring `specialPurposes: [1,2]` and `features: [1,2]` while those entries were absent from the top-level `GVL_DATA` maps, preventing TC strings from being generated.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.20...v2.0.21

## v2.0.20 - 2026-05-26

### What's changed

#### New features

- Ad slots (`ins.adsbygoogle`) are now hidden until consent is granted. On accept, slots are revealed and ads load without requiring a manual page reload. On refuse, slots remain hidden.

#### Refactoring

- Replaced hardcoded TC strings in the IAB TCF 2.2 stub with spec-compliant strings generated at build time using `@iabtcf/core`. An inline minimal GVL is bundled directly in `cookie-consent.js` — no network fetch required. `CMP_ID` updated to `28` (registered IAB CMP), `tcfPolicyVersion` bumped to `4`. `displayStatus` is now always `'hidden'` to prevent Google Funding Choices from loading its own consent banner alongside the custom one.

#### Fixes

- Fixed `TagError: adsbygoogle.push() error: No slot size for availableWidth=0`. When consent has not been granted, `window.adsbygoogle.push` is replaced with a no-op proxy before the AdSense script initializes, silently dropping all `push()` calls. On accept, the real `Array.prototype.push` is restored, then slots are reset and re-pushed inside a double `requestAnimationFrame` so the browser completes layout before AdSense measures `availableWidth`.
- Fixed a crash (`TypeError: Cannot read properties of undefined (reading 'add')`) at page load caused by vendor `755` (Google Advertising Products) declaring `specialPurposes: [1,2]` and `features: [1,2]` while those entries were absent from the top-level `GVL_DATA` maps, preventing TC strings from being generated.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.19...v2.0.20

## v2.0.19 - 2026-05-26

## What's changed

### New features

- Ad slots (`ins.adsbygoogle`) are no longer shown until consent is granted. On accept, slots are revealed and re-pushed to `adsbygoogle` so ads load without requiring a manual page reload. On refuse, slots remain hidden.

### Refactoring

- Replaced hardcoded TC strings in the IAB TCF 2.2 stub with spec-compliant strings generated at build time using `@iabtcf/core`. An inline minimal GVL is bundled directly in `cookie-consent.js` — no network fetch required. `CMP_ID` updated to `28` (registered IAB CMP), `tcfPolicyVersion` bumped to `4`. `displayStatus` is now always `'hidden'` to prevent Google Funding Choices from loading its own consent banner alongside the custom one.

### Fixes

- Fixed repeated `TagError: adsbygoogle.push() error: No slot size for availableWidth=0`. When consent has not been granted, `window.adsbygoogle.push` is now replaced with a no-op proxy before the AdSense script initializes, silently dropping all `push()` calls. On accept, `flushAdSenseQueue()` resets each slot and calls the real `Array.prototype.push` inside a `requestAnimationFrame` so AdSense measures a valid `availableWidth` before initializing.
- Fixed a crash (`TypeError: Cannot read properties of undefined (reading 'add')`) at page load caused by vendor `755` (Google Advertising Products) declaring `specialPurposes: [1,2]` and `features: [1,2]` while those entries were absent from the top-level `GVL_DATA` maps. TC strings were never generated, leaving AdSense without valid consent data.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.18...v2.0.19

## v2.0.18 - 2026-05-26

## What's changed

### New features

- `ins.adsbygoogle` slots are now hidden on page load when consent has not been granted, eliminating blank white ad placeholders. On accept, slots are revealed and re-pushed to `adsbygoogle` so ads load without requiring a manual page reload. On refuse, slots remain hidden.

### Refactoring

- Replaced hardcoded TC strings in the IAB TCF 2.2 stub with spec-compliant strings generated at build time using `@iabtcf/core`. An inline minimal GVL is bundled directly in `cookie-consent.js` — no network fetch required. `CMP_ID` updated to `28` (registered IAB CMP), `tcfPolicyVersion` bumped to `4`. `displayStatus` is now always `'hidden'` to prevent Google Funding Choices from loading its own consent banner alongside the custom one.

### Fixes

- Fixed `TagError: adsbygoogle.push() error: No slot size for availableWidth=0` thrown when AdSense tried to initialize hidden slots. A `<style id="adsense-consent-hide">` tag is now injected synchronously at script load time — before AdSense parses any slot — so `availableWidth` is never measured as `0`. On accept, the style is removed and slots are re-pushed inside a `requestAnimationFrame` to allow the browser to recalculate dimensions first.
- Fixed a crash (`TypeError: Cannot read properties of undefined (reading 'add')`) at page load caused by vendor `755` (Google Advertising Products) declaring `specialPurposes: [1,2]` and `features: [1,2]` while those entries were absent from the top-level `GVL_DATA` maps. TC strings were never generated, leaving AdSense without valid consent data.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.17...v2.0.18

## v2.0.17 - 2026-05-26

## What's changed

### New features

- `ins.adsbygoogle` slots are now hidden on page load when consent has not been granted, eliminating blank white ad placeholders. On accept, slots are made visible and re-pushed to `adsbygoogle` so ads load without requiring a manual page reload. On refuse, slots remain hidden.

### Refactoring

- Replaced hardcoded TC strings in the IAB TCF 2.2 stub with spec-compliant strings generated at build time using `@iabtcf/core`. An inline minimal GVL is bundled directly in `cookie-consent.js` — no network fetch required. `CMP_ID` updated to `28` (registered IAB CMP), `tcfPolicyVersion` bumped to `4`. `displayStatus` is now always `'hidden'` to prevent Google Funding Choices from loading its own consent banner.

### Fixes

- Fixed a crash (`TypeError: Cannot read properties of undefined (reading 'add')`) at page load caused by vendor `755` (Google Advertising Products) declaring `specialPurposes: [1,2]` and `features: [1,2]` while those entries were absent from the top-level GVL maps. TC strings were never generated, leaving AdSense without valid consent data. The inline `GVL_DATA` now includes the required `specialPurposes` and `features` definitions.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.14...v2.0.15

## v2.0.16 - 2026-05-26

## What's changed

### Refactoring

- Replaced hardcoded TC strings in the IAB TCF 2.2 stub with spec-compliant strings generated at build time using `@iabtcf/core`. An inline minimal GVL is bundled directly in `cookie-consent.js` — no network fetch required. `CMP_ID` updated to `28` (registered IAB CMP), `tcfPolicyVersion` bumped to `4`.

### Fixes

- Fixed a crash (`TypeError: Cannot read properties of undefined (reading 'add')`) at page load caused by vendor 755 (Google Advertising Products) declaring `specialPurposes: [1,2]` and `features: [1,2]` while those entries were absent from the top-level GVL maps. TC strings were never generated, leaving AdSense without valid consent data. The inline `GVL_DATA` now includes the required `specialPurposes` and `features` definitions.
- Set `displayStatus` to `'hidden'` unconditionally in `ping`, `getTCData`, and `addEventListener` responses to prevent Google Funding Choices from loading its own consent banner alongside the custom one.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.15...v2.0.16

## v2.0.15 - 2026-05-26

## What's changed

### Refactoring

- Replaced hardcoded TC strings in the IAB TCF 2.2 stub with spec-compliant strings generated at build time using `@iabtcf/core`. An inline minimal GVL is bundled directly in `cookie-consent.js` — no network fetch required. This ensures vendor 755 (Google Advertising Products) is properly encoded in the `VendorConsents` bitfield, fixing silent AdSense rejection (`FJPve: false`) that persisted even when purpose consents were correctly set. `CMP_ID` updated to `28` (registered IAB CMP), `tcfPolicyVersion` bumped to `4`.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.14...v2.0.15

## v2.0.14 - 2026-05-26

### What's changed

#### Fixes

- Fixed Google AdSense ads not displaying after user consent when using a custom cookie banner. The IAB TCF 2.0 stub in `cookie-consent.js` has been overhauled: it now uses properly encoded TCFv2 strings (`granted`/`denied`) built from the IAB TCFv2 bit layout spec, with `gdprApplies: true`, so AdSense correctly withholds ads until consent is granted and serves them once the user accepts.
- `window.__tcfapi._notify()` is now called on `accept()` and `refuse()` to propagate the updated TC string to active TCF listeners in real time, without requiring a page reload.
- Google Funding Choices no longer loads its own consent banner alongside the custom one — the `__tcfapi` presence with `displayStatus: 'visible'` (while no decision is made) signals to AdSense that a CMP is already active.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.13...v2.0.14

## v2.0.13 - 2026-05-26

### What's changed

#### Fixes

- Fixed Google AdSense ads not displaying after user consent when using a custom cookie consent banner. The root cause was the IAB TCF 2.0 stub in `cookie-consent.js`: an empty `tcString` caused AdSense to treat consent as cryptographically invalid, while `gdprApplies: false` caused AdSense to bypass Consent Mode entirely and serve ads unconditionally. The stub now uses pre-encoded TCFv2 strings for both granted and denied states (`gdprApplies: true`), so AdSense correctly withholds ads until the user accepts and serves them once consent is granted.
- `window.__tcfapi._notify()` is now called on `accept()` and `refuse()` to propagate consent state changes to active TCF listeners (including AdSense) in real time without requiring a page reload.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.12...v2.0.13

## v2.0.12 - 2026-05-26

### What's changed

#### Fixes

- Replaced the full IAB TCF 2.0 stub with a minimal one that declares `gdprApplies: false` — the previous stub returned an empty `tcString` which AdSense validates cryptographically in production, causing it to withhold ads even after user consent. The minimal stub preserves `window.__tcfapi` and the `__tcfapiLocator` iframe (preventing Google Funding Choices from loading its own consent banner) without triggering TC string validation.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.10...v2.0.11

## v2.0.11 - 2026-05-26

### What's changed

#### Fixes

- Removed the IAB TCF 2.0 stub (`window.__tcfapi`) from `cookie-consent.js` — it was causing Google AdSense to withhold ads even after user consent, because the stub returned an empty `tcString` which AdSense validates cryptographically in production. The existing `google-adsense-account` meta tag combined with Consent Mode v2 (`gtag('consent', 'update', {...})`) is sufficient to suppress Google's own consent banner and correctly signal consent state.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.11...v2.0.12

## v2.0.10 - 2026-05-26

### What's changed

#### Fixes

- **AdSense ads no longer served after consent due to empty `vendor.consents` in TCF stub.** Google AdSense (IAB vendor ID `755`) checks its own entry in `vendor.consents` before serving ads. The stub was always returning `vendor: { consents: {} }`, causing AdSense to withhold ads even when all purposes were granted — including after a full page reload with a valid consent cookie. The stub now populates `vendor.consents` with the relevant ad vendor IDs (`755`, `56`, `21`, `91`, `128`, `253`, `256`, `410`) when consent is granted.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.9...v2.0.10

## v2.0.9 - 2026-05-26

### What's changed

#### Fixes

- **TCF stub `vendor.consents` was always empty, blocking AdSense from serving ads.** Google AdSense (IAB vendor ID `755`) checks its own vendor ID in the `vendor.consents` map before serving ads. The stub was returning `vendor: { consents: {} }`, causing AdSense to withhold ads even when all purposes were granted and even after a page reload with a valid consent cookie. The stub now populates `vendor.consents` with the relevant ad vendor IDs (`755`, `56`, `21`, `91`, `128`, `253`, `256`, `410`) when consent is granted.
  
- **AdSense slots not re-filled after consent is given without a page reload.** `<ins class="adsbygoogle">` slots that were pushed before consent was given are held in a CMP-wait state by AdSense. After the user accepts, the consent signal was sent but AdSense never retried the held slots. After `_notify('useractioncomplete')`, the consent handler now re-pushes all `ins.adsbygoogle` elements that have not yet received a `data-ad-status` attribute.
  

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.8...v2.0.9

## v2.0.8 - 2026-05-24

### Fixed

- **TCF stub now correctly signals consent state to Google AdSense.** Two issues in the IAB TCF 2.0 stub were causing AdSense to never serve ads after consent was accepted — even after a page reload.
  - `displayStatus` in the `ping` response is now dynamic: `'visible'` while no decision has been made yet (our banner is on screen), `'hidden'` once the user has accepted or refused. Previously it was always `'hidden'`, which led AdSense to assume the user had already dismissed the CMP with a denied state.
  - `eventStatus` in the `addEventListener` initial callback is now `'useractioncomplete'` when the consent cookie already exists (e.g. on a page reload after acceptance), and `'tcloaded'` when no decision has been made yet. Previously it was always `'tcloaded'`, so AdSense never received the final-state signal it requires before serving ads.
  

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.7...v2.0.8

## v2.0.7 - 2026-05-24

### What's changed

- **PostHog initialization moved from JS bundle to inline Blade script.** The PostHog stub and `posthog.init()` call are now rendered directly inside `<x-cookie-consent::scripts>` by PHP, only when `POSTHOG_PROJECT_TOKEN` is configured. This removes PostHog code from the compiled `cookie-consent.js` bundle entirely, reducing bundle size for installs that don't use PostHog.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.6...v2.0.7

## v2.0.6 - 2026-05-24

### What's changed

- **JS bundle now loads conditionally.** `cookie-consent.js` is only injected when at least one integration is configured (AdSense, GTM, or PostHog), reducing unnecessary asset loads on unconfigured installs.
- **JS bundle load point moved to `<x-cookie-consent::scripts>`.** The `<script>` tag for `cookie-consent.js` is now emitted from the scripts component instead of the banner component, ensuring it is always loaded synchronously before any async ad scripts — even on pages where the banner has already been dismissed.
- **IAB TCF 2.0 stub and PostHog init moved into the JS bundle.** Both were previously inlined in Blade templates. They now live in `cookie-consent.js` and are compiled into the distributed build. The Blade scripts component retains only PHP-rendered config values (`window.CookieConsent`, `window.__posthogConfig`).
- **TCF stub notifies listeners on user action.** `window.__tcfapi._notify()` is now called when the user accepts or refuses consent, dispatching a `useractioncomplete` event to any registered TCF listeners (e.g. Google AdSense).

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.5...v2.0.6

## v2.0.5 - 2026-05-24

### Added

- **Google AdSense integration with Consent Mode v2.** Set `GOOGLE_ADSENSE_CLIENT_ID=pub-XXXXXXXXXXXXXXXX` in `.env` to inject the AdSense `gtag.js` snippet with correct consent signals. When GTM is also configured, AdSense is skipped — GTM handles consent signaling instead.
- **Auto-generated `/ads.txt` endpoint.** When `GOOGLE_ADSENSE_CLIENT_ID` is set, the package serves a plain-text `/ads.txt` file containing your publisher ID alongside common authorized ad network declarations. Disable it with `ENABLE_AUTOGENERATED_ADS_FILE=false`.
- **`google-adsense-account` meta tag.** Automatically injected when AdSense is configured to prevent Google from displaying a duplicate consent banner.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.4...v2.0.5

## v2.0.4 - 2026-05-20

### Added

- **PostHog integration.** Pass your `POSTHOG_PROJECT_TOKEN` (and optionally `POSTHOG_HOST`, `POSTHOG_UI_HOST`) in `.env` to enable PostHog analytics. The script is initialized on every page load and respects cookie persistence settings.
- **`cookie-consent:upgrade` artisan command.** Detects missing config keys and appends them to your published `config/cookie-consent.php` without overwriting existing values. Run it after updating the package to pull in new options automatically.

### Fixed

- Added an inline `window.__cookieConsent` fallback so pages that block the tracking script via CSP or ad-blockers no longer throw a `__cookieConsent is undefined` runtime error.

### Changed

- PostHog is now initialized unconditionally (previously gated on `cookieConsentStatus === 'full'`), allowing it to load its stub and manage its own persistence.
- `UpgradeCommand::stubs()` now parses the package config file dynamically — adding a new key to `config/cookie-consent.php` is sufficient, no code change required.

**Full Changelog**: https://github.com/leobsst/laravel-cookie-consent/compare/v2.0.3...v2.0.4

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
