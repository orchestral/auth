Auth Change Log
==============

## Version 2.0

### v2.0.6

* Replace `DateTime` with `Carbon` on basic roles seeding migration to avoid exception to be thrown when using `php artisan debug` (Laravel v4.1).
* Move commands to it's own service provider.
* Add indexs to `OrchestraAuthCreatePasswordRemindersTable` migration.
* Internal refactor to reduce code complexity.
* Implement [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standard.

### v2.0.5

* Change `Orchestra\Auth\AuthManager::createDatabaseDriver()` visibility based on upstream changes.
* Directly inject `session.store` instance instead of `session` (Session Manager) instance
based on upstream changes.
* Suggest orchestra/model.

### v2.0.4

* `Orchestra\Auth\Acl\Container` should extend `Orchestra\Memory\Abstractable\Container`.

### v2.0.3

* Fixed a problem accessing `Auth::is()` when user is actually not logged in, and also append the default "Guest" role when accessing `Auth::roles()`.

### v2.0.2

* Add `Orchestra\Auth\Acl\Fluent::attach()` and `Orchestra\Auth\Acl\Fluent::detach()` method.
* Call `Illuminate\Auth\AuthServiceProvider::boot()` during booting.

### v2.0.1

* Code improvements.

### v2.0.0

* Migrate `Orchestra\Auth` from Orchestra Platform 1.2.
* Move event `orchestra.auth: roles` to `Orchestra\Auth`. *Note that this would make it incompatible with any auth driver which is not based on `Eloquent`.*
* Deprecate `orchestra.auth: login` and `orchestra.auth: logout`, use `auth.login` and `auth.logout` event instead.
* Add command line utility via `Orchestra\Auth\Console\AuthCommand`.
* Add soft deletes to migration schema.
* Add password reminders migration from Laravel 4.
* Fixed `Orchestra\Auth\Guard::is()` should accept array.
* Rename `Orchestra\Auth\Acl\Environment::shutdown()` to `Orchestra\Auth\Acl\Environment::finish()`.
