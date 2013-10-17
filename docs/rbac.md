Using Role Based Access Control
==============

Orchestra Platform Role Based Access Control gives you the ability to create custom ACL metrics which is unique to each of your extensions. 

In most other solutions, you are either restrict to file based configuration for ACL or only allow to define a single metric for your entire application. This simplicity would later become an issue depends on how many extensions do you have within your application.

* [Concept of RBAC](#concept-of-rbac)
* [Creating a New ACL Instance](#creating-a-new-acl-instance)
* [Verifying the ACL](#verifying-the-acl)

## Concept of RBAC

Name     | Description
:--------|:-----------------------
actions  | Actions is either route or activity that we as a user can do (or not do).
roles    | Roles are user group that a user can belong to.
acl      | Is a boolean mapping between actions and roles, which determine whether a role is allow to do an action.

## Creating a New ACL Instance

```php
<?php

Orchestra\Acl::make('acme')->attach(Orchestra\Memory::make());
```

Imagine we have a **acme** extension, above configuration is all you need in your extension start file.

> Using `attach()` allow the ACL to utilize `Orchestra\Memory` to store the metric so we don't have to define the ACL in every request.

## Verifying the ACL

To verify the created ACL, you can use the following code.

```php
$acl = Orchestra\Acl::make('acme');

if ( ! $acl->can('manage acme')) 
{
	return Redirect::to(
		handles('orchestra/foundation::login')
	);
}
```

Or you can create a route filter.

```php
Route::filter('foo.manage', function ()
{
	if ( ! Orchestra\Acl::make('acme')->can('manage acme'))
	{
		return Redirect::to(
			handles('orchestra/foundation::login')
		);
	}
});
```

## Migration Example

Since an ACL metric is defined for each extension, it is best to define ACL actions using a migration file.

```php
<?php

use Illuminate\Database\Migrations\Migration;

class FooDefineAcl extends Migration {
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$role = Orchestra\Model\Role::admin();
		$acl  = Orchestra\Acl::make('acme');
		
		$actions = array(
			'manage acme',
			'view acme',
		);

		$acl->actions()->fill($actions);
		$acl->roles()->add($role->name);
		
		$acl->allow($role->name, $actions);
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// nothing to do here.
	}
}
```

