Using Auth
==============

* [Retrieving Roles](#retrieving-roles)
* [Check Roles](#check-roles)

Essentially, the Auth class offered by Laravel 4 is already good enough for normal usage. Orchestra Platform only extends the default operation and allow a user to be link with one or many roles.

## Retrieving Roles {#retrieving-roles}

Retrieve user's roles is as simple as:

	$roles = Auth::roles();

## Check Roles {#check-roles}

To check if user has a role.

	if (Auth::is(['admin'])) {
		echo "Is an admin";
	}
