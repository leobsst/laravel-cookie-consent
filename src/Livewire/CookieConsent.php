<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class CookieConsent extends Component
{
    public ?string $consent = null;

    public bool $loadScript = false;

    public ?string $learnMoreLink = null;

    public function mount(Request $request): void
    {
        $this->consent = $request->cookie('cookie_consent');
        $this->loadScript = config('cookie-consent.GOOGLE_TAG_MANAGER_ID') !== null;
        if ($linkConfig = config('cookie-consent.LEARN_MORE_LINK')) {
            $this->learnMoreLink = Route::has($linkConfig)
                ? route($linkConfig)
                : $linkConfig;
        }
    }

    public function updatedConsent(): void
    {
        Cookie::queue('cookie_consent', $this->consent, 525600);
        $this->dispatch('cookie-consent-updated', consent: $this->consent === '1');
    }

    public function render(): Factory | View
    {
        /** @var view-string */
        $view = config('cookie-consent.CONSENT_BANNER_VIEW', 'cookie-consent::livewire.cookie-consent');

        return view($view);
    }
}
