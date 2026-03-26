# Send Ray payloads to the cloud via ourray.app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/our-ray.svg?style=flat-square)](https://packagist.org/packages/spatie/our-ray)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/spatie/our-ray/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/spatie/our-ray/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/our-ray.svg?style=flat-square)](https://packagist.org/packages/spatie/our-ray)

This package allows you to send [Ray](https://myray.app) payloads to the cloud via [ourray.app](https://ourray.app).

## Installation

You can install the package via composer:

```bash
composer require spatie/our-ray
```

## Usage

You can send any Ray call to the cloud by chaining `->cloud()`:

```php
ray('my debug data')->cloud();
```

Or use the `our()` helper to automatically send to the cloud:

```php
our()->ray('my debug data');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Spatie](https://github.com/spatie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
