Orchestra Platform Auth Component
==============

Orchestra\Auth extends the functionality of Illuminate\Auth with the extra functionality to retrieve users' role. This is important when we want to use Orchestra\Acl to manage application Access Control List (ACL).

[![Latest Stable Version](https://poser.pugx.org/orchestra/auth/v/stable.png)](https://packagist.org/packages/orchestra/auth) 
[![Total Downloads](https://poser.pugx.org/orchestra/auth/downloads.png)](https://packagist.org/packages/orchestra/auth) 
[![Build Status](https://travis-ci.org/orchestral/auth.png?branch=master)](https://travis-ci.org/orchestral/auth) 
[![Coverage Status](https://coveralls.io/repos/orchestral/auth/badge.png?branch=master)](https://coveralls.io/r/orchestral/auth?branch=master)

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
* [Change Log](http://orchestraplatform.com/docs/2.0/components/auth/changes#v2.1)
