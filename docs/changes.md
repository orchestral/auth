---
title: Auth Change Log

---

## Version 2.2 {#v2-2}

### v2.2.0@dev {#v2-2-0}

* Bump minimum version to PHP v5.4.0.
* Rename `Orchestra\Auth\Acl\Environment` to `Orchestra\Auth\Acl\Factory`.

## Version 2.1 {#v2-1}

### v2.1.4 {#v2-1-4}

* Allow custom auth driver to resolve `Orchestra\Auth\Guard`.
* Implement [PSR-4](https://github.com/php-fig/fig-standards/blob/master/proposed/psr-4-autoloader/psr-4-autoloader.md) autoloading structure.

### v2.1.3 {#v2-1-3}

* Add `Orchestra\Auth\Acl\Fluent::getSlugFromName()` helper method.
* Ensure only ACL metric information is synced to `orchestra/memory`.
* Add `Orchestra\Auth\Acl\Fluent::findKey()` helper method.
* Multiple refactor.

### v2.1.2 {#v2-1-2}

* Fixes invalid result when using `Auth::isNot()` and `Auth::isNotAny()` helper method.

### v2.1.1 {#v2-1-1}

* Add ability to append `Eloquent` result directly to assign roles. E.g: `$acl->roles()->add(Orchestra\Model\Role::admin())`.
* Avoid throwing `RuntimeException` when trying to attach the same Memory instance to ACL.
* Add `Auth::isAny()`, `Auth::isNot()` and `Auth::isNotAny()` helper class.

### v2.1.0 {#v2-1-0}

* Add `Orchestra\Auth\Acl\Fluent::attach()` and `Orchestra\Auth\Acl\Fluent::detach()` method.
* Call `Illuminate\Auth\AuthServiceProvider::boot()` during booting.
* `Orchestra\Auth\Acl\Container` should extend `Orchestra\Memory\Abstractable\Container`.
* Predefined package path to avoid additional overhead to guest package path.
* Change `Orchestra\Auth\AuthManager::createDatabaseDriver()` visibility based on upstream changes.
* Directly inject `session.store` instance instead of `session` (Session Manager) instance
based on upstream changes.
* Suggest orchestra/model.
* Rename command to `php artisan auth:migrate`.
* Add `Auth::setup()` method to easily create roles event listener.
* Replace `DateTime` with `Carbon` on basic roles seeding migration to avoid exception to be thrown when using `php artisan debug` (Laravel v4.1).
* Move commands to it's own service provider.
* Internal refactor to reduce code complexity.
* Implement [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standard.

## Version 2.0 {#v2-0}

### v2.0.6 {#v2-0-6}

* Replace `DateTime` with `Carbon` on basic roles seeding migration to avoid exception to be thrown when using `php artisan debug` (Laravel v4.1).
* Move commands to it's own service provider.
* Add indexs to `OrchestraAuthCreatePasswordRemindersTable` migration.
* Internal refactor to reduce code complexity.
* Implement [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standard.

### v2.0.5 {#v2-0-5}

* Change `Orchestra\Auth\AuthManager::createDatabaseDriver()` visibility based on upstream changes.
* Directly inject `session.store` instance instead of `session` (Session Manager) instance
based on upstream changes.
* Suggest orchestra/model.

### v2.0.4 {#v2-0-4}

* `Orchestra\Auth\Acl\Container` should extend `Orchestra\Memory\Abstractable\Container`.

### v2.0.3 {#v2-0-3}

* Fixed a problem accessing `Auth::is()` when user is actually not logged in, and also append the default "Guest" role when accessing `Auth::roles()`.

### v2.0.2 {#v2-0-2}

* Add `Orchestra\Auth\Acl\Fluent::attach()` and `Orchestra\Auth\Acl\Fluent::detach()` method.
* Call `Illuminate\Auth\AuthServiceProvider::boot()` during booting.

### v2.0.1 {#v2-0-1}

* Code improvements.

### v2.0.0 {#v2-0-0}

* Migrate `Orchestra\Auth` from Orchestra Platform 1.2.
* Move event `orchestra.auth: roles` to `Orchestra\Auth`. *Note that this would make it incompatible with any auth driver which is not based on `Eloquent`.*
* Deprecate `orchestra.auth: login` and `orchestra.auth: logout`, use `auth.login` and `auth.logout` event instead.
* Add command line utility via `Orchestra\Auth\Console\AuthCommand`.
* Add soft deletes to migration schema.
* Add password reminders migration from Laravel 4.
* Fixed `Orchestra\Auth\Guard::is()` should accept array.
* Rename `Orchestra\Auth\Acl\Environment::shutdown()` to `Orchestra\Auth\Acl\Environment::finish()`.
