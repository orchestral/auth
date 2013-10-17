Auth Component
==============

* [Installation](#installation)
* [Configuration](#configuration)

`Orchestra\Auth` extends the functionality of `Illuminate\Auth` with the extra functionality to retrieve users' role. This is important when we want to use `Orchestra\Acl` to manage application Access Control List (ACL).

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/auth": "2.1.*@dev"
	}
}
```

## Configuration

Next add the service provider in `app/config/app.php`.

```php
'providers' => array(
	
	// ...
	# Remove 'Illuminate\Auth\AuthServiceProvider' 
	# and add 'Orchestra\Auth\AuthServiceProvider'
	
	'Orchestra\Auth\AuthServiceProvider',
	'Orchestra\Memory\MemoryServiceProvider',

	'Orchestra\Auth\CommandServiceProvider',
), 
```

> `Orchestra\Auth\AuthServiceProvider` should replace `Illuminate\Auth\AuthServiceProvider`.

### Migrations

Before we can start using `Orchestra\Auth`, please run the following:

```bash
php artisan orchestra:auth install
```

> The command utility is enabled via `Orchestra\Auth\CommandServiceProvider`.
