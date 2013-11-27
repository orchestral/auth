Setup Auth
==============

Starting from 2.1, the default event listener `orchestra.auth: roles` is no longer registered in `Orchestra\Auth\AuthServiceProvider`. This would allow better configuration over convertion control for your application.

An example setup code would be:

```php
Auth::setup(function ($user, $roles) {
	// If user is not logged in.
	if (is_null($user)) {
		return $roles;
	}

	if ($user->is_admin) {
		$roles = array('Administrator');
	} else {
		$roles = array('Member');
	}

	return $roles;
});
```

> For Orchestra Platform, the listener are automatically registered in the bootstrap process.
