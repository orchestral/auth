Orchestra Platform Auth Component
==============

Orchestra\Auth extends the functionality of Illuminate\Auth with the extra functionality to retrieve users' role. This is important when we want to use Orchestra\Acl to manage application Access Control List (ACL).

[![Build Status](https://travis-ci.org/orchestral/auth.png?branch=master)](https://travis-ci.org/orchestral/auth)

## Quick Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/auth": "2.0.*"
	},
	"minimum-stability": "dev"
}
```

Next replace `Illuminate\Auth\AuthServiceProvider` with the following service provider in `app/config/app.php`.

```php
'providers' => array(
	
	// ...
	
	'Orchestra\Auth\AuthServiceProvider',
	'Orchestra\Auth\PackageServiceProvider',
),
```

You might want to add `Orchestra\Acl` to class aliases in `app/config/app.php`:

```php
'aliases' => array(

	// ...

	'Orchestra\Acl' => 'Orchestra\Support\Facades\Acl',
),
```

## Resources

* [Documentation](http://docs.orchestraplatform.com/pages/components/auth)
* [Change Logs](https://github.com/orchestral/auth/wiki/Change-Logs)
