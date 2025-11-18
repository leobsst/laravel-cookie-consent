<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Enums;

enum CookieConsentEnum: string
{
    case FULL = 'full';
    case PARTIAL = 'partial';
    case NONE = 'none';
}
