<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
  <a href="https://github.com/leobsst/laravel-cookie-consent"><img src="https://img.shields.io/badge/version-1.0.0-blue.svg" alt="Version"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-green.svg" alt="License"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?logo=laravel" alt="Laravel"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php" alt="PHP"></a>
</p>

<h1 style="text-align: center;">Laravel Cookie Consent</h1>

A modern Laravel package for handling cookie consent with Google Tag Manager integration. This package provides a Livewire-based cookie consent banner with built-in support for Google Consent Mode v2, making GDPR compliance simple and straightforward.

## Features

- Livewire-based interactive cookie consent banner
- Simple binary consent system (Accept/Refuse)
- Automatic Google Tag Manager integration
- Consent Mode v2 support for Google Analytics
- Persistent user preferences stored in session
- Tailwind CSS styling with customizable accent color
- Easy integration with existing Laravel projects
- Multi-language support (EN, FR, IT, ES, DE)

## Requirements

- PHP 8.2 or higher
- Laravel 12.0 or higher
- Livewire 3.0 or higher
- Tailwind CSS

## Installation

### Step 1: Install the Package

```bash
composer require leobsst/laravel-cookie-consent
```

### Step 2: Publish Assets (Required)

```bash
php artisan vendor:publish --tag=cookie-consent-assets
```

This publishes:
- `public/vendor/cookie-consent/laravel-cookie-consent.js` - JavaScript file that handles Google Tag Manager consent updates

### Step 3: Publish config file (highly recommended)

```bash
php artisan vendor:publish --tag=cookie-consent-config
```

This will create a `config/cookie-consent.php` file where you can customize:

- **GOOGLE_TAG_MANAGER_ID**: Your Google Tag Manager ID (GTM-XXXXXXX)
- **LEARN_MORE_LINK**: URL or route name for the "Learn more" link (default: `/privacy-policy`)
- **CONSENT_BANNER_VIEW**: Custom view for the consent banner
- **ACCENT_COLOR**: Accent color for buttons and links (default: `#3490dc`)

### Alternative: Use Install Command

You can also use the built-in install command which will guide you through the setup:

```bash
php artisan cookie-consent:install
```

This command will:
- Publish the configuration file
- Publish the package assets
- Ask you to star the repository on GitHub

## Configuration

### Tailwind CSS Integration

> [!IMPORTANT]
>
> To ensure the package's views are properly styled with Tailwind CSS, you need to add the following directive to your project's `app.css` file

```css
/* COMPILE TAILWINDCSS DIRECTIVES IN VIEWS */
@source '../../../../vendor/leobsst/laravel-cookie-consent/resources/views/**/*.blade.php';
```

This ensures that Tailwind CSS processes the utility classes used in the cookie consent banner.

## Usage

### Basic Usage

1. **Add the Google Tag Manager ID to your `.env` file:**

```env
GOOGLE_TAG_MANAGER_ID=GTM-XXXXXXX
```

2. **Include the cookie consent scripts and component in your layout file:**

```blade
<!DOCTYPE html>
<html>
<head>
    <!-- Your head content -->
    @cookieConsentScripts
</head>
<body>
    <!-- Your content -->

    <livewire:laravel-cookie-consent::cookie-consent />
</body>
</html>
```

The `@cookieConsentScripts` directive loads Google Tag Manager with Consent Mode v2 enabled. The banner will automatically appear for users who haven't set their cookie preferences yet.

### How It Works

The package implements a simple binary consent system:

- **User accepts**: Google Tag Manager consent is updated to grant `ad_storage`, `ad_user_data`, and `ad_personalization` (while `analytics_storage` remains denied)
- **User refuses**: All non-essential consents remain denied
- **Consent status**: Stored in the session as `cookie_consent` (true/false/null)

The middleware automatically shares the consent status with all views, ensuring tracking scripts only load when consent is granted.

### Complete Example

Here's a complete example of a layout file with the cookie consent banner:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Tag Manager with Consent Mode --}}
    @cookieConsentScripts
</head>
<body>
    <div id="app">
        {{ $slot }}
    </div>

    {{-- Cookie Consent Banner --}}
    <livewire:laravel-cookie-consent::cookie-consent />
</body>
</html>
```

### Checking User Consent

You can check the user's consent status in your application using the session:

```php
// Check if user has given consent
$consent = session('cookie_consent');

if ($consent === true) {
    // User has accepted cookies
} elseif ($consent === false) {
    // User has refused cookies
} else {
    // User hasn't made a choice yet (null)
}
```

In Blade views, you can use the shared variable:

```blade
@if($loadTrackingScripts)
    <!-- This will only render if user has accepted cookies -->
    <script>
        // Your custom tracking code
    </script>
@endif
```

### Customizing the Banner

You can customize the banner's appearance by publishing and modifying the views:

```bash
php artisan vendor:publish --tag=cookie-consent-views
```

This will publish the views to `resources/views/vendor/cookie-consent/` where you can customize them to match your design.

### Translations

The package includes translations for the following languages:
- English (en)
- French (fr)
- Italian (it)
- Spanish (es)
- German (de)

The banner automatically uses the language configured in your Laravel application (`config('app.locale')`).

**Available translation keys:**
- `cookie-consent::translations.description`: Description of cookie usage
- `cookie-consent::translations.question`: Question asking for consent
- `cookie-consent::translations.learn_more`: "Learn more" link text
- `cookie-consent::translations.accept`: "Accept" button text
- `cookie-consent::translations.refuse`: "Refuse" button text

#### Publishing Translations

To customize the translations, publish the language files:

```bash
php artisan vendor:publish --tag=cookie-consent-lang
```

This will publish the translation files to `resources/lang/vendor/cookie-consent/` where you can modify them.

## Google Tag Manager Consent Mode

The package implements Google Consent Mode v2 with the following default settings:

**When user hasn't decided (default state):**
```javascript
{
  'functional_storage': 'granted',
  'security_storage': 'granted',
  'analytics_storage': 'denied',
  'ad_storage': 'denied',
  'ad_user_data': 'denied',
  'ad_personalization': 'denied'
}
```

**When user accepts:**
```javascript
{
  'functional_storage': 'granted',
  'security_storage': 'granted',
  'analytics_storage': 'denied',
  'ad_storage': 'granted',
  'ad_user_data': 'granted',
  'ad_personalization': 'granted'
}
```

**When user refuses:**
All non-essential consents remain denied.

The JavaScript file ([resources/js/index.js](resources/js/index.js)) listens for Livewire events and updates the Google Tag Manager consent accordingly.

## Advanced Configuration

### Environment Variables

Add these variables to your `.env` file:

```env
# Required for Google Tag Manager integration
GOOGLE_TAG_MANAGER_ID=GTM-XXXXXXX

# Optional customizations
COOKIE_LEARN_MORE_LINK=/privacy-policy
COOKIE_CONSENT_BANNER_VIEW=cookie-consent::livewire.cookie-consent
COOKIE_CONSENT_ACCENT_COLOR=#3490dc
```

### Custom View

To use a completely custom view for the consent banner:

1. Create your custom view file
2. Set the `CONSENT_BANNER_VIEW` in your config file or `.env`
3. Ensure your view uses the same Livewire component properties (`$consent`, `$loadScript`, `$learnMoreLink`)

### Middleware

The `HandleCookieConsent` middleware is automatically registered in the `web` middleware group. It:
- Retrieves the consent status from the session
- Shares `$loadTrackingScripts` with all views (true only if consent is accepted)
- Shares `$cookieConsentStatus` with all views (true/false/null)

## FAQ

### Why is analytics_storage always denied?

The package currently sets `analytics_storage` to denied by default to provide a conservative approach to GDPR compliance. You can modify this behavior in the JavaScript file if your use case requires it.

### How do I reset a user's consent?

You can clear the session:

```php
session()->forget('cookie_consent');
```

### Can I use this without Google Tag Manager?

Yes! Simply don't set the `GOOGLE_TAG_MANAGER_ID` environment variable. The banner will still work and store the user's consent preference, which you can use to conditionally load your own tracking scripts.

### How do I style the banner to match my design?

You have several options:
1. Use the `ACCENT_COLOR` config to change the button/link color
2. Publish the views and modify the Tailwind classes
3. Create a completely custom view and set `CONSENT_BANNER_VIEW`

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Security

If you've found a bug regarding security please mail [contact@leobsst.fr](mailto:contact@leobsst.fr) instead of using the issue tracker.

## Credits

- [LEOBSST](https://github.com/LEOBSST)
- [B.L.A.M. PRODUCTION](https://linksly.fr/BLAM-PRODUCTION)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
