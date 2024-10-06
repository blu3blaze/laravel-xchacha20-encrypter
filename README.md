# Secure your Laravel application with XChaCha20-Poly1305 encryption

[![Latest Version on Packagist](https://img.shields.io/packagist/v/blu3blaze/laravel-xchacha20-encrypter.svg?style=flat-square)](https://packagist.org/packages/blu3blaze/laravel-xchacha20-encrypter)

This package seamlessly integrates the robust XChaCha20-Poly1305 encryption algorithm into Laravel application by extending the default Encryption facade.

## Prerequisites

- PHP 8.3
- Laravel 11

## Installation

1. Install package via composer:

```bash
composer require blu3blaze/laravel-xchacha20-encrypter
```

2. Add service provider into your bootstrap/providers.php:

```php
\Blu3blaze\Encrypter\EncrypterServiceProvider::class,
```