Integration with Memory Component
==============

Integration with `Orchestra\Memory` would allow a persistent storage of ACL metric, this would reduce the need to define ACL on every request.

## Creating a New ACL Instance

```php
<?php

Orchestra\Acl::make('acme')->attach(Orchestra\Memory::make());
```

> Using `attach()` allow the ACL to utilize `Orchestra\Memory` to store the metric so we don't have to define the ACL in every request.

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

