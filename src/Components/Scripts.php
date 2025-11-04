<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Components;

use Illuminate\View\Component;
use Illuminate\View\Factory;
use Illuminate\View\View;

class Scripts extends Component
{
    public function __construct(
        private ?string $googleTagManagerId = null
    ) {}

    public function render(): View | Factory
    {
        /** @var view-string */
        $view = 'cookie-consent::components.scripts';

        return view($view, [
            'googleTagManagerId' => $this->googleTagManagerId,
        ]);
    }
}
