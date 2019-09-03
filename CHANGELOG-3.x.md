# Changelog for 3.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/auth`.

## 3.8.2

Released: 2019-08-26

### Added

* Added `Orchestra\Auth\Concerns\HasRoles`.
* Added `Orchestra\Auth\Concerns\ProvidesRoles`.

### Changes

* Override `Orchestra\Auth\AuthManager::viaRequest()` to use `Orchestra\Auth\RequestGuard`.
* `Orchestra\Authorization` should accepts `Illuminate\Contracts\Auth\Guard` and only check for `Orchestra\Contracts\Auth\Guard` if it's available.

## 3.8.1

Released: 2019-08-09

### Changes

* Use `static function` rather than `function` whenever possible, the PHP engine does not need to instantiate and later GC a `$this` variable for said closure.

## 3.8.0

Released: 2019-03-12

### Changes

* Update support to Laravel Framework 5.8.

### Remove

* Remove deprecated `Orchestra\Auth\CommandServiceProvider`.

## 3.7.1

Released: 2019-02-21

### Changes

* Improve performance by prefixing all global functions calls with `\` to skip the look up and resolve process and go straight to the global function.
* Use `Illuminate\Events\Dispatcher::dispatch()` instead deprecated `Illuminate\Events\Dispatcher::fire()`.

## 3.7.0

Released: 2018-11-08

### Changes

* Update support to Laravel Framework 5.7.

## 3.6.1

Released: 2018-05-24

### Fixes

* Fixes `Orchestra\Auth\Passwords\PasswordBrokerManager` contract.

## 3.6.0

Released: 2017-10-03

### Added

* Added `Orchestra\Authorization\Authorization::canAs()` and `Orchestra\Authorization\Authorization::canIfAs()` which you can send an instance of `Orchestra\Contracts\Authorization\Authorizable` to check authorization explicitly for the `$user`.

### Changes

* Update support to Laravel Framework 5.6.

### Deprecated

* Deprecate `Orchestra\Auth\CommandServiceProvider`.

## 3.5.2

Released: 2018-04-25

### Added

* Added `Orchestra\Authorization\Authorization::canAs()` and `Orchestra\Authorization\Authorization::canIfAs()` which you can send an instance of `Orchestra\Contracts\Authorization\Authorizable` to check authorization explicitly for the `$user`.

## 3.5.1

Released: 2017-11-29

### Changes

* Remove `orchestra/notifier` dependencies.
* Split `Orchestra\Authorization\Fluent` to `Orchestra\Authorization\Action` and `Orchestra\Authorization\Role`.

## 3.5.0

Released: 2017-10-03

### Changes

* Update support to Laravel Framework 5.5.
