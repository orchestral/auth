Orchestra Platform Auth Component
==============

Orchestra\Auth extends the functionality of Illuminate\Auth with the extra functionality to retrieve users' role. This is important when we want to use Orchestra\Acl to manage application Access Control List (ACL).

[![Build Status](https://travis-ci.org/orchestral/auth.png?branch=2.0)](https://travis-ci.org/orchestral/auth) [![Coverage Status](https://coveralls.io/repos/orchestral/auth/badge.png?branch=2.0)](https://coveralls.io/r/orchestral/auth?branch=2.0)

## Quick Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/auth": "2.0.*"
	}
}
```

Next replace `Illuminate\Auth\AuthServiceProvider` with the following service provider in `app/config/app.php`.

```php
'providers' => array(
	
	// ...
	
	'Orchestra\Auth\AuthServiceProvider',
	'Orchestra\Memory\MemoryServiceProvider',
),
```

## Resources

* [Documentation](http://orchestraplatform.com/docs/2.0/components/auth)
* [Change Logs](https://github.com/orchestral/auth/wiki/Change-Logs)
