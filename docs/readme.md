---
title: Auth Component
---

* [Installation](#installation)
* [Configuration](#configuration)

`Orchestra\Auth` extends the functionality of `Illuminate\Auth` with the extra functionality to retrieve users' role. This is important when we want to use `Orchestra\Acl` to manage application Access Control List (ACL).

## Installation {#installation}

To install through composer, simply put the following in your `composer.json` file:

	{
		"require": {
			"orchestra/auth": "2.1.*@dev"
		}
	}

## Configuration {#configuration}

Next add the service provider in `app/config/app.php`.

	'providers' => array(

		// ...
		# Remove 'Illuminate\Auth\AuthServiceProvider'
		# and add 'Orchestra\Auth\AuthServiceProvider'

		'Orchestra\Auth\AuthServiceProvider',
		'Orchestra\Memory\MemoryServiceProvider',

		'Orchestra\Auth\CommandServiceProvider',
	),

> `Orchestra\Auth\AuthServiceProvider` should replace `Illuminate\Auth\AuthServiceProvider`.

### Aliases

To make development easier, you could add `Orchestra\Support\Facades\Acl` alias for easier reference:

	'aliases' => array(

		'Orchestra\Acl' => 'Orchestra\Support\Facades\Acl',

	),

### Migrations

Before we can start using `Orchestra\Auth`, please run the following:

	php artisan auth:migrate

> The command utility is enabled via `Orchestra\Auth\CommandServiceProvider`.

Optionally you can enable `Orchestra\Memory` to use it with `Orchestra\Acl`, please run the following:

	php artisan memory:migrate

> The command utility is enabled via `Orchestra\Memory\CommandServiceProvider`.
