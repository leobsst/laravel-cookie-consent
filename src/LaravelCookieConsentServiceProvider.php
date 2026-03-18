<?php

declare(strict_types=1);

namespace Leobsst\LaravelCookieConsent;

use Illuminate\Support\Facades\Blade;
use Leobsst\LaravelCookieConsent\Components\CookieBanner;
use Leobsst\LaravelCookieConsent\Components\Scripts;
use Leobsst\LaravelCookieConsent\Http\Middleware\HandleCookieConsent;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCookieConsentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-cookie-consent')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishAssets()
                    ->askToStarRepoOnGitHub('leobsst/laravel-cookie-consent');
            })
            ->hasViewComponents('cookie-consent', Scripts::class, CookieBanner::class)
            ->hasConfigFile()
            ->hasAssets()
            ->hasTranslations()
            ->hasViews();

        $this->getBladeDirectives();
    }

    public function packageRegistered(): void
    {
        $this->loadTranslations();
    }

    public function packageBooted(): void
    {
        $this->registerMiddleware();

        Blade::componentNamespace('Leobsst\\LaravelCookieConsent\\Components', 'cookie-consent');
    }

    /**
     * Get the Blade directives for the package.
     */
    private function getBladeDirectives(): void
    {
        Blade::directive(
            'cookieConsentScripts',
            fn () => "<?php echo view('cookie-consent::components.scripts', [
                'googleTagManagerId' => config('cookie-consent.GOOGLE_TAG_MANAGER_ID')
            ])->render(); ?>"
        );
    }

    /**
     * Register middleware for the package
     */
    private function registerMiddleware(): void
    {
        // Register the HandleCookieConsent middleware in the web middleware group
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', HandleCookieConsent::class);
    }

    /**
     * Load translations for the package
     */
    private function loadTranslations(): void
    {
        $langPath = __DIR__ . '/../resources/lang';

        // Load JSON translations
        $this->loadJsonTranslationsFrom($langPath);

        // Publish translations
        $this->publishes([
            $langPath => $this->app->langPath('vendor/cookie-consent'),
        ], 'cookie-consent-lang');
    }
}
