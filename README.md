Auth Component for Orchestra Platform
==============

Auth Component extends the functionality of `Illuminate\Auth` with the extra functionality to retrieve users' role. This is important when we want to manage application Access Control List (ACL).

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/auth.svg?style=flat-square)](https://packagist.org/packages/orchestra/auth)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/auth.svg?style=flat-square)](https://packagist.org/packages/orchestra/auth)
[![MIT License](https://img.shields.io/packagist/l/orchestra/auth.svg?style=flat-square)](https://packagist.org/packages/orchestra/auth)
[![Build Status](https://img.shields.io/travis/orchestral/auth/3.4.svg?style=flat-square)](https://travis-ci.org/orchestral/auth)
[![Coverage Status](https://img.shields.io/coveralls/orchestral/auth/3.4.svg?style=flat-square)](https://coveralls.io/r/orchestral/auth?branch=3.4)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/orchestral/auth/3.4.svg?style=flat-square)](https://scrutinizer-ci.com/g/orchestral/auth/)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Change Log](http://orchestraplatform.com/docs/latest/components/auth/changes#v3-4)

## Version Compatibility

Laravel    | Auth
:----------|:----------
 4.0.x     | 2.0.x
 4.1.x     | 2.1.x
 4.2.x     | 2.2.x
 5.0.x     | 3.0.x
 5.1.x     | 3.1.x
 5.2.x     | 3.2.x
 5.3.x     | 3.3.x
 5.4.x     | 3.4.x@dev

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

## Resources

* [Documentation](http://orchestraplatform.com/docs/latest/components/auth)
