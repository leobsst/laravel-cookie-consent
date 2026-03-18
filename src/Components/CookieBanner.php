<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;

final class CookieBanner extends Component
{
    public bool $loadScript;

    public ?string $learnMoreLink;

    public ?string $accentColor;

    public int $cookieDuration;

    public string $sameSite;

    public function __construct()
    {
        $this->loadScript = config('cookie-consent.GOOGLE_TAG_MANAGER_ID') !== null;

        $this->learnMoreLink = null;
        if ($linkConfig = config('cookie-consent.LEARN_MORE_LINK')) {
            $this->learnMoreLink = Route::has($linkConfig)
                ? route($linkConfig)
                : $linkConfig;
        }

        $this->accentColor = config('cookie-consent.ACCENT_COLOR');
        $this->cookieDuration = config('cookie-consent.duration', 60 * 24 * 365) * 60; // minutes → seconds
        $this->sameSite = config('cookie-consent.same_site', 'Lax');
    }

    public function render(): View
    {
        /** @var view-string */
        $view = config('cookie-consent.CONSENT_BANNER_VIEW', 'cookie-consent::components.cookie-banner');

        return view($view, [
            'loadScript' => $this->loadScript,
            'learnMoreLink' => $this->learnMoreLink,
            'accentColor' => $this->accentColor,
            'cookieDuration' => $this->cookieDuration,
            'sameSite' => $this->sameSite,
        ]);
    }
}
