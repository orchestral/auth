Auth Component for Orchestra Platform
==============

Auth Component extends the functionality of `Illuminate\Auth` with the extra functionality to retrieve users' role. This is important when we want to manage application Access Control List (ACL).

[![tests](https://github.com/orchestral/auth/workflows/tests/badge.svg?branch=6.x)](https://github.com/orchestral/auth/actions?query=workflow%3Atests+branch%3A6.x)
[![Latest Stable Version](https://poser.pugx.org/orchestra/auth/version)](https://packagist.org/packages/orchestra/auth)
[![Total Downloads](https://poser.pugx.org/orchestra/auth/downloads)](https://packagist.org/packages/orchestra/auth)
[![Latest Unstable Version](https://poser.pugx.org/orchestra/auth/v/unstable)](//packagist.org/packages/orchestra/auth)
[![License](https://poser.pugx.org/orchestra/auth/license)](https://packagist.org/packages/orchestra/auth)
[![Coverage Status](https://coveralls.io/repos/github/orchestral/auth/badge.svg?branch=6.x)](https://coveralls.io/github/orchestral/auth?branch=6.x)

## Version Compatibility

Laravel    | Auth
:----------|:----------
 5.5.x     | 3.5.x
 5.6.x     | 3.6.x
 5.7.x     | 3.7.x
 5.8.x     | 3.8.x
 6.x       | 4.x
 7.x       | 5.x
 8.x       | 6.x

## Installation

To install through composer, run the following command from terminal:

```bash
composer require "orchestra/auth"
```

## Configuration

Next replace `Illuminate\Auth\AuthServiceProvider` with the following service provider in `config/app.php`.

```php
'providers' => [

    // ...

    Orchestra\Auth\AuthServiceProvider::class,
    Orchestra\Authorization\AuthorizationServiceProvider::class,
    Orchestra\Memory\MemoryServiceProvider::class,
],
```

> `CommandServiceProvider` are optional, useful for interaction using the Artisan command line tool.

### Aliases

To make development easier, you could add `Orchestra\Support\Facades\ACL` alias for easier reference:

```php
'aliases' => [

    'ACL' => Orchestra\Support\Facades\ACL::class,

],
```

