Auth Component for Orchestra Platform
==============

Auth Component extends the functionality of `Illuminate\Auth` with the extra functionality to retrieve users' role. This is important when we want to use Orchestra\Acl to manage application Access Control List (ACL).

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/auth.svg?style=flat)](https://packagist.org/packages/orchestra/auth)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/auth.svg?style=flat)](https://packagist.org/packages/orchestra/auth)
[![MIT License](https://img.shields.io/packagist/l/orchestra/auth.svg?style=flat)](https://packagist.org/packages/orchestra/auth)
[![Build Status](https://img.shields.io/travis/orchestral/auth/3.0.svg?style=flat)](https://travis-ci.org/orchestral/auth)
[![Coverage Status](https://img.shields.io/coveralls/orchestral/auth/3.0.svg?style=flat)](https://coveralls.io/r/orchestral/auth?branch=3.0)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/orchestral/auth/3.0.svg?style=flat)](https://scrutinizer-ci.com/g/orchestral/auth/)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Resources](#resources)

## Version Compatibility

Laravel    | Auth
:----------|:----------
 4.0.x     | 2.0.x
 4.1.x     | 2.1.x
 4.2.x     | 2.2.x
 5.0.x     | 3.0.x

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/auth": "3.0.*"
	}
}
```

And then run `composer install` from the terminal.

### Quick Installation

Above installation can also be simplify by using the following command:

    composer require "orchestra/auth=3.0.*"

## Configuration

Next replace `Illuminate\Auth\AuthServiceProvider` with the following service provider in `config/app.php`.

```php
'providers' => [

	// ...

	'Orchestra\Auth\AuthServiceProvider',
	'Orchestra\Authorization\AuthorizationServiceProvider',
	'Orchestra\Memory\MemoryServiceProvider',

	'Orchestra\Auth\CommandServiceProvider',
	'Orchestra\Memory\CommandServiceProvider',
],
```

> `CommandServiceProvider` are optional, useful for interaction using the Artisan command line tool.

### Aliases

To make development easier, you could add `Orchestra\Support\Facades\ACL` alias for easier reference:

```php
'aliases' => [

	'ACL' => 'Orchestra\Support\Facades\ACL',

],
```

## Resources

* [Documentation](http://orchestraplatform.com/docs/latest/components/auth)
* [Change Log](http://orchestraplatform.com/docs/latest/components/auth/changes#v3-0)
