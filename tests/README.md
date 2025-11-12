# Laravel Cookie Consent Package Tests

This directory contains all tests for the `laravel-cookie-consent` package, written with **Pest PHP**.

## Test Structure

```
tests/
├── Arch/                        # Architecture tests
│   └── ArchTest.php            # Code architecture rules
├── Feature/                     # Integration tests
│   ├── Components/
│   │   └── ScriptsTest.php     # Scripts component tests
│   ├── Livewire/
│   │   └── CookieConsentTest.php  # Livewire component tests
│   ├── Middleware/
│   │   └── HandleCookieConsentTest.php  # Middleware tests
│   └── ServiceProviderTest.php  # Service Provider tests
├── Unit/                        # Unit tests
│   └── ExampleTest.php         # Configuration tests
├── Pest.php                     # Pest configuration
└── TestCase.php                 # Base test class
```

## Running Tests

### All tests
```bash
composer test
```

### Tests with coverage
```bash
composer test-coverage
```

### Specific tests

#### Architecture tests only
```bash
vendor/bin/pest tests/Arch
```

#### Unit tests only
```bash
vendor/bin/pest tests/Unit
```

#### Integration tests only
```bash
vendor/bin/pest tests/Feature
```

#### A specific test file
```bash
vendor/bin/pest tests/Feature/Livewire/CookieConsentTest.php
```

## Test Descriptions

### Architecture Tests (Arch/)

Architecture tests verify that the code follows certain rules and conventions:

- ✅ No debugging functions (`dd`, `dump`, `var_dump`, etc.)
- ✅ Strict types declared (`declare(strict_types=1)`)
- ✅ Livewire components extend `Livewire\Component`
- ✅ Blade components extend `Illuminate\View\Component`
- ✅ Middlewares are in the `Http\Middleware` namespace
- ✅ No use of `die` or `exit`
- ✅ No direct use of the `env()` function (must be in configs)

### Middleware Tests (Feature/Middleware/)

Tests for `HandleCookieConsent`:

- Sharing consent status with views
- Correctly passing the request to the next middleware
- Handling different consent states (null, true, false)

### Livewire Component Tests (Feature/Livewire/)

Tests for `CookieConsent`:

- Initialization with session values
- Google Tag Manager ID configuration
- "Learn more" link configuration
- Session update on consent change
- Handling different configuration cases

### Scripts Component Tests (Feature/Components/)

Tests for the Blade `Scripts` component:

- Component instantiation
- Passing the Google Tag Manager ID
- View rendering
- Container resolution

### Service Provider Tests (Feature/)

Tests for `LaravelCookieConsentServiceProvider`:

- Livewire component registration
- Middleware registration
- `@cookieConsentScripts` Blade directive registration
- Configuration loading
- Views loading
- Translations loading

### Unit Tests (Unit/)

Basic tests for:

- Package configuration loading
- Presence of all required configuration keys

## Writing New Tests

### Example Feature Test with Pest

```php
<?php

declare(strict_types=1);

it('can do something', function () {
    $result = doSomething();

    expect($result)->toBeTrue();
});
```

### Example Test with Configuration

```php
it('uses custom configuration', function () {
    config(['cookie-consent.GOOGLE_TAG_MANAGER_ID' => 'GTM-TEST']);

    $component = new CookieConsent;
    $component->mount();

    expect($component->loadScript)->toBeTrue();
});
```

### Example Test with Session

```php
it('reads from session', function () {
    session(['cookie_consent' => true]);

    $component = new CookieConsent;
    $component->mount();

    expect($component->consent)->toBeTrue();
});
```

## Conventions

1. **Use `declare(strict_types=1)`** at the beginning of each test file
2. **Name your tests descriptively**: use `it()` or `test()` with clear descriptions
3. **One test = one main assertion**: each test should verify a specific behavior
4. **Use `beforeEach()`** for setup common to multiple tests
5. **Prefer `expect()` over `$this->assert*()`** for assertions

## Resources

- [Pest PHP Documentation](https://pestphp.com/docs)
- [Pest Architecture Plugin](https://pestphp.com/docs/arch-testing)
- [Pest Laravel Plugin](https://pestphp.com/docs/plugins#laravel)
