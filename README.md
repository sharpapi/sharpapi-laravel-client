# SharpAPI - AI-Powered Swiss Army Knife API. 
[![Latest Version on Packagist](https://img.shields.io/packagist/v/sharpapi/laravel-client.svg?style=flat-square)](https://packagist.org/packages/sharpapi/laravel-client)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sharpapi/laravel-client/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/sharpapi/laravel-client/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sharpapi/laravel-client/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/sharpapi/laravel-client/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/sharpapi/laravel-client.svg?style=flat-square)](https://packagist.org/packages/sharpapi/laravel-client)

Assisting coders with the most repetitive content analysis and content generation processing needs of any app or platform.
SharpAPI is an easy-to-use REST API endpoints to help automate your app AI content processing whether it's: E-commerce, HR Tech, Travel, Tourism & Hospitality, Content or SEO.

## Installation

You can install the package via composer:

```bash
composer require sharpapi/laravel-client
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-client-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-client-config"
```

This is the contents of the published config file:

```php
return [
];
```


## Usage

```php
$sharpApiService = new SharpAPI\SharpApiService();
echo $sharpApiService->echoPhrase('Hello, SharpAPI!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [A2Z WEB LTD](https://github.com/a2zwebltd)
- [Dawid Makowski](https://github.com/makowskid)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
