<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Scripts extends Component
{
    public function __construct(
        private ?string $googleTagManagerId = null,
        private ?string $posthogProjectToken = null,
        private ?string $posthogHost = null,
        private ?string $posthogUiHost = null,
    ) {}

    public function render(): View
    {
        return view('cookie-consent::components.scripts', [
            'googleTagManagerId' => $this->googleTagManagerId,
            'posthogProjectToken' => $this->posthogProjectToken,
            'posthogHost' => $this->posthogHost,
            'posthogUiHost' => $this->posthogUiHost,
        ]);
    }
}
