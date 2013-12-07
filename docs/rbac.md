---
title: Using Resources Based Access Control
---

Orchestra Platform Resources Based Access Control gives you the ability to create custom ACL metrics which is unique to each of your extensions.

In most other solutions, you are either restrict to file based configuration for ACL or only allow to define a single metric for your entire application. This simplicity would later become an issue depends on how many extensions do you have within your application.

* [Concept of RBAC](#concept-of-rbac)
* [Creating a New ACL Instance](#creating-a-new-acl-instance)
* [Verifying the ACL](#verifying-the-acl)

## Concept of RBAC {#concept-of-rbac}

Name     | Description
:--------|:-----------------------
actions  | Actions is either route or activity that we as a user can do (or not do).
roles    | Roles are user group that a user can belong to.
acl      | Is a boolean mapping between actions and roles, which determine whether a role is allow to do an action.

## Creating a New ACL Instance {#creating-a-new-acl-instance}

	<?php

	Orchestra\Acl::make('acme');

Imagine we have a **acme** extension, above configuration is all you need in your extension/application start file.

## Verifying the ACL {#verifying-the-acl}

To verify the created ACL, you can use the following code.

	$acl = Orchestra\Acl::make('acme');

	if ( ! $acl->can('manage acme'))
	{
		return Redirect::to(
			handles('orchestra/foundation::login')
		);
	}

Or you can create a route filter.

	Route::filter('foo.manage', function ()
	{
		if ( ! Orchestra\Acl::make('acme')->can('manage acme'))
		{
			return Redirect::to(
				handles('orchestra/foundation::login')
			);
		}
	});
