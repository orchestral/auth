# Changelog

This changelog references the relevant changes (bug and security fixes) done to `orchestra/auth`.

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

## 3.4.1

Released: 2017-09-25

### Fixes

* Fixes `remember_me` timing attack vector.

## 3.4.0

Released: 2017-05-03

### Changes

* Update support to Laravel Framework 5.4.
