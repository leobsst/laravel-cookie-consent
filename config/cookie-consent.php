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

    'CONSENT_BANNER_VIEW' => env('COOKIE_CONSENT_BANNER_VIEW', 'cookie-consent::livewire.cookie-consent'),

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
];
