<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Tag Manager ID
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Google Tag Manager ID to enable integration
    | with Google Tag Manager for managing your cookies and tracking scripts.
    | This ID is typically in the format 'GTM-XXXXXXX'.
    |
    */

    'GOOGLE_TAG_MANAGER_ID' => env('GOOGLE_TAG_MANAGER_ID'),

    /*
    |--------------------------------------------------------------------------
    | PostHog Project Token
    |--------------------------------------------------------------------------
    |
    | Here you may specify your PostHog project token to enable PostHog
    | analytics. Analytics will only be initialized after the user accepts
    | cookies. Leave null to disable PostHog integration.
    |
    */

    'POSTHOG_PROJECT_TOKEN' => env('POSTHOG_PROJECT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | PostHog Host
    |--------------------------------------------------------------------------
    |
    | The PostHog instance host. Use 'https://eu.i.posthog.com' for EU Cloud,
    | 'https://us.i.posthog.com' for US Cloud, or your self-hosted URL.
    |
    */

    'POSTHOG_HOST' => env('POSTHOG_HOST', 'https://eu.i.posthog.com'),

    /*
    |--------------------------------------------------------------------------
    | PostHog UI Host
    |--------------------------------------------------------------------------
    |
    | When POSTHOG_HOST points to a reverse proxy, set this to the actual
    | PostHog UI host so the toolbar and session recordings link correctly.
    | Defaults to the same value as POSTHOG_HOST when not set.
    |
    | Example: 'https://eu.posthog.com'
    |
    */

    'POSTHOG_UI_HOST' => env('POSTHOG_UI_HOST'),

    /*
    |--------------------------------------------------------------------------
    | Learn More Link
    |--------------------------------------------------------------------------
    |
    | This value determines the URL for the "Learn More" link in the cookie
    | consent banner. You can set this to a page on your website that provides
    | more information about your cookie usage and privacy policy.
    |
    | The value can be a route name, a full URL, or a relative path.
    |
    */

    'LEARN_MORE_LINK' => env('COOKIE_LEARN_MORE_LINK', '/privacy-policy'),

    /*
    |--------------------------------------------------------------------------
    | Consent Banner View
    |--------------------------------------------------------------------------
    |
    | This value specifies the view file that will be used to render the
    | cookie consent banner. You can customize this view to match your
    | website's design and branding.
    |
    */

    'CONSENT_BANNER_VIEW' => env('COOKIE_CONSENT_BANNER_VIEW', 'cookie-consent::components.cookie-banner'),

    /*
    |--------------------------------------------------------------------------
    | Accent Color
    |--------------------------------------------------------------------------
    |
    | This value sets the accent color used in the cookie consent banner.
    | You can specify any valid CSS color value (e.g., hex, rgb).
    |
    */

    'ACCENT_COLOR' => env('COOKIE_CONSENT_ACCENT_COLOR', '#3490dc'),

    /*
    |--------------------------------------------------------------------------
    | Cookie duration configuration
    |--------------------------------------------------------------------------
    |
    | This section can be used to define duration for the cookie consent cookie.
    |
    */

    'duration' => (60 * 24 * 365), // 1 year in minutes

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies
    |--------------------------------------------------------------------------
    |
    | This option determines how your cookies behave when cross-site requests
    | take place, and can be used to mitigate CSRF attacks. By default, we
    | will set this value to "None" which will allow cookies to be sent in all
    |
    | See: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie#samesitesamesite-value
    |
    | Supported: "lax", "strict", "none", null
    |
    */

    'same_site' => 'Lax',
];
