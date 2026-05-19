<?php

declare(strict_types=1);

use Leobsst\LaravelCookieConsent\Commands\UpgradeCommand;

beforeEach(function () {
    $this->tmpPath = sys_get_temp_dir() . '/cookie-consent-' . uniqid() . '.php';
});

afterEach(function () {
    if (file_exists($this->tmpPath)) {
        unlink($this->tmpPath);
    }
});

it('is registered as an artisan command', function () {
    expect(resolve(UpgradeCommand::class))->toBeInstanceOf(UpgradeCommand::class);
});

it('returns failure when config file does not exist', function () {
    $this->artisan('cookie-consent:upgrade', ['--path' => '/nonexistent/cookie-consent.php'])
        ->assertFailed();
});

it('reports up to date when no keys are missing', function () {
    $fullConfig = require __DIR__ . '/../../../config/cookie-consent.php';
    file_put_contents($this->tmpPath, "<?php\n\nreturn " . var_export($fullConfig, true) . ";\n");

    $this->artisan('cookie-consent:upgrade', ['--path' => $this->tmpPath])
        ->expectsOutputToContain('up to date')
        ->assertSuccessful();
});

it('adds all missing posthog keys to the config file', function () {
    $configWithoutPosthog = <<<'PHP'
<?php

return [

    'GOOGLE_TAG_MANAGER_ID' => env('GOOGLE_TAG_MANAGER_ID'),

    'LEARN_MORE_LINK' => env('COOKIE_LEARN_MORE_LINK', '/privacy-policy'),

    'CONSENT_BANNER_VIEW' => env('COOKIE_CONSENT_BANNER_VIEW', 'cookie-consent::components.cookie-banner'),

    'ACCENT_COLOR' => env('COOKIE_CONSENT_ACCENT_COLOR', '#3490dc'),

    'duration' => (60 * 24 * 365),

    'same_site' => 'Lax',
];
PHP;

    file_put_contents($this->tmpPath, $configWithoutPosthog);

    $this->artisan('cookie-consent:upgrade', ['--path' => $this->tmpPath])
        ->expectsOutputToContain('POSTHOG_PROJECT_TOKEN')
        ->assertSuccessful();

    $result = require $this->tmpPath;

    expect($result)->toHaveKeys(['POSTHOG_PROJECT_TOKEN', 'POSTHOG_HOST', 'POSTHOG_UI_HOST']);
});

it('only adds truly missing keys', function () {
    $configWithPartialPosthog = <<<'PHP'
<?php

return [

    'GOOGLE_TAG_MANAGER_ID' => env('GOOGLE_TAG_MANAGER_ID'),

    'LEARN_MORE_LINK' => env('COOKIE_LEARN_MORE_LINK', '/privacy-policy'),

    'CONSENT_BANNER_VIEW' => env('COOKIE_CONSENT_BANNER_VIEW', 'cookie-consent::components.cookie-banner'),

    'ACCENT_COLOR' => env('COOKIE_CONSENT_ACCENT_COLOR', '#3490dc'),

    'duration' => (60 * 24 * 365),

    'same_site' => 'Lax',

    'POSTHOG_PROJECT_TOKEN' => env('POSTHOG_PROJECT_TOKEN'),

    'POSTHOG_HOST' => env('POSTHOG_HOST', 'https://eu.i.posthog.com'),
];
PHP;

    file_put_contents($this->tmpPath, $configWithPartialPosthog);

    $this->artisan('cookie-consent:upgrade', ['--path' => $this->tmpPath])
        ->expectsOutputToContain('POSTHOG_UI_HOST')
        ->assertSuccessful();

    // Require the updated file to verify it is valid PHP and has exactly the right keys
    $result = require $this->tmpPath;

    expect($result)->toHaveKey('POSTHOG_UI_HOST');
    expect($result)->toHaveKey('POSTHOG_PROJECT_TOKEN');
    expect($result)->toHaveKey('POSTHOG_HOST');
    expect(array_keys($result))->toContain('POSTHOG_HOST');
    // Ensure POSTHOG_HOST and POSTHOG_UI_HOST are distinct entries
    expect(array_count_values(array_keys($result))['POSTHOG_HOST'])->toBe(1);
    expect(array_count_values(array_keys($result))['POSTHOG_UI_HOST'])->toBe(1);
});

it('produces a valid php file after upgrade', function () {
    $configWithoutPosthog = <<<'PHP'
<?php

return [
    'GOOGLE_TAG_MANAGER_ID' => env('GOOGLE_TAG_MANAGER_ID'),
    'LEARN_MORE_LINK' => env('COOKIE_LEARN_MORE_LINK', '/privacy-policy'),
    'CONSENT_BANNER_VIEW' => env('COOKIE_CONSENT_BANNER_VIEW', 'cookie-consent::components.cookie-banner'),
    'ACCENT_COLOR' => env('COOKIE_CONSENT_ACCENT_COLOR', '#3490dc'),
    'duration' => (60 * 24 * 365),
    'same_site' => 'Lax',
];
PHP;

    file_put_contents($this->tmpPath, $configWithoutPosthog);

    $this->artisan('cookie-consent:upgrade', ['--path' => $this->tmpPath])->assertSuccessful();

    $result = require $this->tmpPath;

    expect($result)->toBeArray();
    expect($result)->toHaveKeys(['POSTHOG_PROJECT_TOKEN', 'POSTHOG_HOST', 'POSTHOG_UI_HOST']);
});
