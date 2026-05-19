<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent\Commands;

use Illuminate\Console\Command;

final class UpgradeCommand extends Command
{
    protected $signature = 'cookie-consent:upgrade {--path= : Path to the published config file (defaults to config_path)}';

    protected $description = 'Add missing configuration keys to the published cookie-consent config file.';

    public function handle(): int
    {
        $publishedPath = $this->option('path') ?? config_path('cookie-consent.php');

        if (! file_exists($publishedPath)) {
            $this->error('Config file not found. Run `php artisan vendor:publish --tag=cookie-consent-config` first.');

            return self::FAILURE;
        }

        $missing = $this->missingKeys($publishedPath);

        if (empty($missing)) {
            $this->info('Config is already up to date.');

            return self::SUCCESS;
        }

        $this->appendMissingKeys($publishedPath, $missing);

        $this->info('Added ' . count($missing) . ' missing key(s): ' . implode(', ', $missing));

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function missingKeys(string $publishedPath): array
    {
        $published = require $publishedPath;
        $packageKeys = array_keys(require __DIR__ . '/../../config/cookie-consent.php');

        return array_values(array_filter(
            $packageKeys,
            fn (string $key) => ! array_key_exists($key, $published)
        ));
    }

    /**
     * @param  list<string>  $keys
     */
    private function appendMissingKeys(string $publishedPath, array $keys): void
    {
        $stubs = $this->stubs();
        $content = file_get_contents($publishedPath);

        $additions = '';
        foreach ($keys as $key) {
            if (isset($stubs[$key])) {
                $additions .= "\n" . $stubs[$key];
            }
        }

        // Insert before the closing ];
        $content = preg_replace('/\n];[\s]*$/', $additions . "\n];", $content);

        file_put_contents($publishedPath, $content);
    }

    /**
     * Stubs for each config key that can be added by this command.
     *
     * @return array<string, string>
     */
    private function stubs(): array
    {
        return [
            'POSTHOG_PROJECT_TOKEN' => <<<'PHP'
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
PHP,
            'POSTHOG_HOST' => <<<'PHP'
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
PHP,
            'POSTHOG_UI_HOST' => <<<'PHP'
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
PHP,
        ];
    }
}
