Auth Component for Orchestra Platform
==============

Auth Component extends the functionality of `Illuminate\Auth` with the extra functionality to retrieve users' role. This is important when we want to manage application Access Control List (ACL).

[![Build Status](https://travis-ci.org/orchestral/auth.svg?branch=3.6)](https://travis-ci.org/orchestral/auth)
[![Latest Stable Version](https://poser.pugx.org/orchestra/auth/version)](https://packagist.org/packages/orchestra/auth)
[![Total Downloads](https://poser.pugx.org/orchestra/auth/downloads)](https://packagist.org/packages/orchestra/auth)
[![Latest Unstable Version](https://poser.pugx.org/orchestra/auth/v/unstable)](//packagist.org/packages/orchestra/auth)
[![License](https://poser.pugx.org/orchestra/auth/license)](https://packagist.org/packages/orchestra/auth)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Changelog](https://github.com/orchestral/auth/releases)

## Version Compatibility

Laravel    | Auth
:----------|:----------
 4.x.x     | 2.x.x
 5.0.x     | 3.0.x
 5.1.x     | 3.1.x
 5.2.x     | 3.2.x
 5.3.x     | 3.3.x
 5.4.x     | 3.4.x
 5.5.x     | 3.5.x
 5.6.x     | 3.6.x@dev

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
    "require": {
        "orchestra/auth": "~3.0"
    }
}
```

And then run `composer install` from the terminal.

### Quick Installation

Above installation can also be simplify by using the following command:

    composer require "orchestra/auth=~3.0"

## Configuration

Next replace `Illuminate\Auth\AuthServiceProvider` with the following service provider in `config/app.php`.

```php
'providers' => [

    // ...

    Orchestra\Auth\AuthServiceProvider::class,
    Orchestra\Authorization\AuthorizationServiceProvider::class,
    Orchestra\Memory\MemoryServiceProvider::class,

    Orchestra\Auth\CommandServiceProvider::class,
    Orchestra\Memory\CommandServiceProvider::class,
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

