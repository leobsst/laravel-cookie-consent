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
            fn (string $key) => ! \array_key_exists($key, $published)
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
                $additions .= "\n\n" . $stubs[$key];
            }
        }

        // Insert before the closing ];
        $content = preg_replace('/\n];[\s]*$/', $additions . "\n];", $content);

        file_put_contents($publishedPath, $content);
    }

    /**
     * Extracts stubs directly from the package config file.
     * Each block (comment + key-value line) is parsed dynamically, so adding
     * a new key to config/cookie-consent.php is sufficient — no update needed here.
     *
     * @return array<string, string>
     */
    private function stubs(): array
    {
        $source = file_get_contents(__DIR__ . '/../../config/cookie-consent.php');
        $stubs = [];

        preg_match_all(
            '/( {4}\/\*.*?\*\/\n\n {4}\'([^\']+)\' => [^\n]+)/s',
            $source,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $stubs[$match[2]] = $match[1];
        }

        return $stubs;
    }
}
