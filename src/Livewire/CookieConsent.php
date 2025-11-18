<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Leobsst\LaravelCookieConsent\Enums\CookieConsentEnum;
use Livewire\Component;

class CookieConsent extends Component
{
    public bool $loadScript = false;

    public ?string $learnMoreLink = null;

    public function mount(): void
    {
        $this->loadScript = config('cookie-consent.GOOGLE_TAG_MANAGER_ID') !== null;
        if ($linkConfig = config('cookie-consent.LEARN_MORE_LINK')) {
            $this->learnMoreLink = Route::has($linkConfig)
                ? route($linkConfig)
                : $linkConfig;
        }
    }

    public function updateConsent(bool $consent): void
    {
        Cookie::queue(Cookie::make(
            'cookie_consent',
            $consent ? CookieConsentEnum::FULL->value : CookieConsentEnum::NONE->value,
            config('cookie-consent.duration', 60 * 24 * 365),
            config('session.path', '/'),
            config('session.domain'),
            true,
            config('session.http_only', true),
            false,
            config('cookie-consent.same_site', 'Lax')
        ));

        $this->dispatch('cookie-consent-updated', consent: $consent);
    }

    public function render(): Factory | View
    {
        /** @var view-string */
        $view = config('cookie-consent.CONSENT_BANNER_VIEW', 'cookie-consent::livewire.cookie-consent');

        return view($view);
    }
}
