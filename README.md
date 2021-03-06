# Entrust (Laravel4 Package)

Entrust is a succinct and flexible way to add Role-based Permissions to **Laravel 4**.

First and foremost I must give credit to the original developers of this package.
Andrew Elkins (@andrewelkins) and Leroy Merlin (@zizaco) did excellent work on the fundamental design and functionality.
My fork is intended to:
- Remove extra components not really relevant to role & permission management (in particular, Ardent).
- Add extra functionality I felt was useful and particularly suited to this package.
- Make integrating the package more flexible and dynamic (eventually).

Were my changes ever to be integrated back into the Zizaco version of this plugin, I think that would be lovely.
Either way though, I hope to demonstrate some genuinely helpful features and options.

## Contents

- [Quick start](#quick-start)
    - [Required setup](#required-setup)
- [Configuration](#configuration)
    - [User Relation to Roles](#user-relation-to-roles)
    - [Models](#models)
        - [Role](#role)
        - [Permission](#permission)
        - [User](#user)
        - [Non-Standard Table Names](#non-standard-table-names)
            - [Role Constructor](#role-constructor)
            - [Permission Constructor](#permission-constructor)
        - [EntrustRole and EntrustPermission Classes](#entrustrole-and-entrustpermission-classes)
        - [Soft Deleting](#soft-deleting)
- [Usage](#usage)
    - [Concepts](#concepts)
        - [Checking for Roles & Permissions](#checking-for-roles--permissions)
        - [User Ability](#user-ability)
    - [Controller Trait (and Filters)](#controller-trait)
    - [Short Syntax Route Filter](#short-syntax-route-filter)
    - [Route Filter](#route-filter)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Additional Information](#additional-information)

## Quick start

**PS:** Even though it's not needed, Entrust works very well with [Confide](https://github.com/Zizaco/confide) in order to eliminate repetitive tasks involving the management of users: account creation, login, logout, confirmation by e-mail, password reset, etc.

[Take a look at Confide](https://github.com/Zizaco/confide).

### Required setup

In the `require` key of `composer.json` file add the following:

```json
"bbatsche/entrust": "~2.0"
```

Run the Composer update command:

```bash
composer update
```

In your `config/app.php` add `'Bbatsche\Entrust\EntrustServiceProvider'` to the end of the `$providers` array:

```php
'providers' => array(
    'Illuminate\Foundation\Providers\ArtisanServiceProvider',
    'Illuminate\Auth\AuthServiceProvider',
    // ...
    'Bbatsche\Entrust\EntrustServiceProvider',
),
```

At the end of `config/app.php` add `'Entrust' => 'Bbatsche\Entrust\EntrustFacade'` to the `$aliases` array:

```php
'aliases' => array(
    'App'        => 'Illuminate\Support\Facades\App',
    'Artisan'    => 'Illuminate\Support\Facades\Artisan',
    // ...
    'Entrust'    => 'Bbatsche\Entrust\EntrustFacade',
),
```

## Configuration

Set the property values in the `config/auth.php` (typically, these values are configured correctly out of the box but it is worth double checking). These values will be used by Entrust to refer to the correct user table and model.

You can also publish the configuration for this package to further customize table names and model namespaces:

```bash
php artisan config:publish bbatsche/entrust
```

### User relation to roles

Now generate the Entrust migration:

```bash
php artisan entrust:migration
```

It will generate the `<timestamp>_entrust_setup_tables.php` migration.
You may now run it with the artisan migrate command:

```bash
php artisan migrate
```

After the migration, four new tables will be present:
- `roles` &mdash; stores role records
- `permissions` &mdash; stores permission records
- `role_user` &mdash; stores [many-to-many](http://laravel.com/docs/4.2/eloquent#many-to-many) relations between roles and users
- `permission_role` &mdash; stores [many-to-many](http://laravel.com/docs/4.2/eloquent#many-to-many) relations between roles and permissions

### Models

#### Role

Create a Role model inside `app/models/Role.php` using the following example:

```php
<?php

use Bbatsche\Entrust\Contracts\EntrustRoleInterface;
use Bbatsche\Entrust\Traits\EntrustRoleTrait;

class Role extends Eloquent implements EntrustRoleInterface
{
    use EntrustRoleTrait;
}
```

The `Role` model has three main attributes:

- `name` &mdash; Unique name for the Role, used for looking up role information in the application layer. For example: "admin", "owner", "employee".
- `display_name` &mdash; Human readable name for the Role. Not necessarily unique and optional. For example: "User Administrator", "Project Owner", "Widget  Co. Employee".
- `description` &mdash; A more detailed explanation of what the Role does. Also optional.

Both `display_name` and `description` are optional; their fields are nullable in the database.

#### Permission

Create a Permission model inside `app/models/Permission.php` using the following example:

```php
<?php

use Bbatsche\Entrust\Contracts\EntrustPermissionInterface;
use Bbatsche\Entrust\Traits\EntrustPermissionTrait;

class Permission extends Eloquent implements EntrustPermissionInterface
{
    use EntrustPermissionTrait;
}
```

The `Permission` model has the same three attributes as the `Role`:

- `name` &mdash; Unique name for the permission, used for looking up permission information in the application layer. For example: "create-post", "edit-user", "post-payment", "mailing-list-subscribe".
- `display_name` &mdash; Human readable name for the permission. Not necessarily unique and optional. For example "Create Posts", "Edit Users", "Post Payments", "Subscribe to mailing list".
- `description` &mdash; A more detailed explanation of the Permission.

In general, it may be helpful to think of the last two attributes in the form of a sentence: "The permission `display_name` allows a user to `description`."

#### User

Finally, add the same same traits & role pattern to your user model. For example:

```php
<?php

use Bbatsche\Entrust\Contracts\EntrustUserInterface;
use Bbatsche\Entrust\Traits\EntrustUserTrait;

class User extends Eloquent implements EntrustUserInterface
{
    use EntrustUserTrait;

    ...
}
```

This will enable the relation with `Role` and add several methods to check for roles or permissions within your `User` model.

Don't forget to dump composer autoload

```bash
composer dump-autoload
```

**And you are ready to go.**

#### Non-Standard Table Names

Entrust is configured by default to follow Laravel's naming conventions for table names, so data for your `Role` model is stored in a table called `roles`. If you change these defaults in Entrust's configuration, you need to reflect these changes in your models as well. This can be done simply by adding a constructor like the following:

##### Role Constructor
```php
public function __construct($attr = array())
{
    $this->table = Config::get('entrust::roles_table');

    parent::__construct($attr);
}
```

##### Permission Constructor
```php
public function __construct($attr = array())
{
    $this->table = Config::get('entrust::permissions_table');

    parent::__construct($attr);
}
```

#### EntrustRole and EntrustPermission Classes

For easy of use (and backwards compatibility) Entrust includes abstract classes `Bbatsche\Entrust\EntrustRole` and `Bbatsche\Entrust\EntrustPermission`. Your `Role` and `Permission` models may simply extend these classes which in turn implement their respective interfaces and traits. They also include the aforementioned constructors. Depending on your needs, these may be simpler to implement in your models.

#### Soft Deleting

The default migration takes advantage of `onDelete('cascade')` clauses within the pivot tables to remove relations when a parent record is deleted. If for some reason you cannot use cascading deletes in your database, `EntrustRoleTrait`, `EntrustPermissionTrait`, and `EntrustUserTrait` include event listeners to manually delete records in relevant pivot tables. In the interest of not accidentally deleting data, the event listeners will **not** delete pivot data if the model uses soft deleting. However, due to limitations in Laravel's event listeners, there is no way to distinguish between a call to `delete()` versus a call to `forceDelete()`. For this reason, **before you force delete a model, you must manually delete any of the relationship data** (unless your pivot tables uses cascading deletes). For example:

```php
$role = Role::findOrFail(1); // Pull back a given role

// Regular Delete
$role->delete(); // This will work no matter what

// Force Delete
$role->users()->sync([]); // Delete relationship data
$role->perms()->sync([]); // Delete relationship data

$role->forceDelete(); // Now force delete will work regardless of whether the pivot table has cascading delete
```

## Usage

### Concepts
Let's start by creating the following `Role`s and `Permission`s:

```php
$owner = new Role();
$owner->name         = 'owner';
$owner->display_name = 'Project Owner'; // optional
$owner->description  = 'User is the owner of a given project'; // optional
$owner->save();

$admin = new Role();
$admin->name         = 'admin';
$admin->display_name = 'User Administrator'; // optional
$admin->description  = 'User is allowed to manage and edit other users'; // optional
$admin->save();
```

Next, with both roles created let's assign them to the users. Thanks to `EntrustUserTrait` this is as easy as:

```php
$user = User::where('username', '=', 'bbatsche')->first();

// role attach alias
$user->attachRole($admin); // parameter can be an Role object, array, or id

// or eloquent's original technique
$user->roles()->attach($admin->id); // id only
```

Now we just need to add permissions to those Roles:

```php
$createPost = new Permission();
$createPost->name         = 'create-post';
$createPost->display_name = 'Create Posts'; // optional
// Allow a user to...
$createPost->description  = 'create new blog posts'; // optional
$createPost->save();

$editUser = new Permission();
$editUser->name         = 'edit-user';
$editUser->display_name = 'Edit Users'; // optional
// Allow a user to...
$editUser->description  = 'edit existing users'; // optional
$editUser->save();

$admin->attachPermission($createPost);
// equivalent to $admin->perms()->attach($createPost->id);

$owner->attachPermissions(array($createPost, $editUser));
// equivalent to $owner->perms()->attach(array($createPost->id, $editUser->id));
```

#### Checking for Roles & Permissions

Now we can check for a role with the `is()` method simply by doing:

```php
$user->is('owner'); // false
$user->is('admin'); // true
```

If you would like to check *multiple* roles, you can use either `isAny()` or `isAll()` depending on whether you require just one of the roles, or all of them:

```php
$user->isAny(['owner', 'admin']); // true
$user->isAll(['owner', 'admin']); // false, $user does not have the 'owner' role
```

If you need more details about which roles failed, you can pass a variable to the second argument. After calling `isAny()` or `isAll()` the variable will be an array of roles that failed.

```php
$failedRoles = array();

$user->isAny(['owner', 'admin'], $failedRoles); // true
// or
$user->isAll(['owner', 'admin'], $failedRoles); // false

print_r($failedRoles);
// Array
// (
//      [0] => owner
// )
```

Similarly, if we want to check for a user's permissions we use the method `can()`:

```php
$user->can('create-post'); // true
$user->can('edit-user');   // false
```

Just like with roles, there are methods for checking multiple permissions; `canAny()` and `canAll()`:

```php
$user->canAny(['create-post', 'edit-user']); // true
$user->canAll(['create-post', 'edit-user']); // false, $user doesn't have 'edit-user' permission
```

These methods can also include a variable for tracking which specific permissions failed:

```php
$failedPerms = array();

$user->canAny(['create-post', 'edit-user'], $failedPerms); // true
// or
$user->canAll(['create-post', 'edit-user'], $failedPerms); // false

print_r($failedPerms);
// Array
// (
//      [0] => edit-user
// )
```

You can have as many `Role`s as you want for each `User`, and each `Role` can have as many `Permissions` as necessary.

The `Entrust` class has shortcuts to both `is*()` and `can*()` methods for the currently logged in user:

```php
Entrust::is('role-name');
Entrust::can('permission-name');

// are identical to

Auth::user()->is('role-name');
Auth::user()->can('permission-name);
```

```php
Entrust::isAny(['role-name-1', 'role-name-2', /* ... */]);
Entrust::isAll(['role-name-1', 'role-name-2', /* ... */]);

Entrust::canAny(['permission-name-1', 'permission-name-2', /* ... */]);
Entrust::canAll(['permission-name-1', 'permission-name-2', /* ... */]);

// are identical to

Auth::user()->isAny(['role-name-1', 'role-name-2', /* ... */]);
Auth::user()->isAll(['role-name-1', 'role-name-2', /* ... */]);

Auth::user()->canAny(['permission-name-1', 'permission-name-2', /* ... */]);
Auth::user()->canAll(['permission-name-1', 'permission-name-2', /* ... */]);
```

_**Note:** Laravel Facades do not support pass by reference, meaning you **cannot** pass a second argument to the `*Any()` and `*All()` methods to find out exactly which permissions or roles failed. To do this, you must get an instance of the `User` object._

#### User ability

More advanced checking can be done using the `ability()` function.
It takes in three parameters (roles, permissions, options):
- `roles` is a set of roles to check.
- `permissions` is a set of permissions to check.

Either of the roles or permissions variable can be a comma separated string or array:

```php
$user->ability(array('admin', 'owner'), array('create-post', 'edit-user'));

// or

$user->ability('admin,owner', 'create-post,edit-user');
```

This will check whether the user has any of the provided roles and permissions.
In this case it will return true since the user is an `admin` and has the `create-post` permission.

The third parameter is an options array:

```php
$options = array(
    'validate_all' => true | false (Default: false),
    'return_type'  => boolean | array | both (Default: boolean)
);
```

- `validate_all` is a boolean flag to set whether to check all the values for true, or to return true if at least one role or permission is matched.
- `return_type` specifies whether to return a boolean, array of checked values, or both in an array.

Here is an example output:

```php
$options = array(
    'validate_all' => true,
    'return_type' => 'both'
);

list($validate, $allValidations) = $user->ability(
    array('admin', 'owner'),
    array('create-post', 'edit-user'),
    $options
);

var_dump($validate);
// bool(false)

var_dump($allValidations);
// array(4) {
//     ['role'] => bool(true)
//     ['role_2'] => bool(false)
//     ['create-post'] => bool(true)
//     ['edit-user'] => bool(false)
// }
```

### Controller Trait

Typically when you are creating a controller you will find that there are a set of roles or permissions you want to enforce on a per-action basis. Entrust includes a trait for your controllers that makes enforcing these rules quite simple. First, you must use the trait in your controller. For example, we could include it in a `BaseController` so that the feature is available in all the controllers for our application:

```php
<?php

use Bbatsche\Entrust\Traits\EntrustControllerTrait;

class BaseController extends Controller
{
    use EntrustControllerTrait;
}
```

This trait includes two methods that can be used to filter requests to your controller, they are `entrustPermissionFilter()` and `entrustRoleFilter()`. To enable one or both of these filters in your controller, use the `beforeFilter()` method in the controller's constructor:

```php
public function __construct()
{
    $this->beforeFilter('@entrustRoleFilter');

    // and / or

    $this->beforeFilter('@entrustPermissionFilter');
}
```

The required roles or permissions are specified in properties of your controller; either `$entrustPerms` or `$entrustRoles`. These properties should be associative arrays, with the key being the name of the controller action (or method), and the value being the roles or permissions required for that action. The roles and permissions can be either a string or an array of multiple values. For example:

```php
protected $entrustPerms = array(
    'create'  => 'post-create',
    'edit'    => ['post-edit-own',    'post-edit'],
    'destroy' => ['post-destroy-own', 'post-destroy']
);

protected $entrustRoles = array(
    'index'  => ['owner', 'admin'],
    'store'  => 'owner',
    'update' => 'owner'
);
```

Entrust will now automatically check against these roles or permissions for each method in the controller. If the current user does not have the required role or permissions, a [403](http://httpstatusdogs.com/403-forbidden) response will be returned.

The controller trait includes a couple of optional flags that control how it interprets the array of perms and roles. When the specified value is an array, Entrust assumes than any one of the entities is sufficient. To force Entrust to require *all* the values, set the property `$entrustRequireAll` to `true` in your controller. In addition, if an method is not specified in your array, the filters treats this as a "pass". To make Entrust require a role or permission to be specified for *all* actions, set the property `$entrustAllowMissing` to `false`. This setting may be useful as a security sanity check, meaning that if the role or permission for an action is not specified, then no user should be able to access it.

If you would like to do some additional handling after one of the filters fails you can create callback functions. Callback functions may be specified in one of three ways. First, you can create methods called `entrustPermissionCallback()` or `entrustRoleCallback()`. Second, you can create a closure and assign it to either `$entrustPermisionCallback` or `$entrustRoleCallback` properties of your controller. Or lastly, you can create the method separately and assign its name as a string to either of those properties. In any case, Entrust will call your callback function whenever one of the filters fails. It will pass the following to your function:

1. The controller method name
1. The permissions or roles that failed
1. The entire set of permissions or roles that were required
1. The `Route` object
1. The `Request` object

A sample callback function could look like the following:

```php
public function entrustPermissionCallback($method, $failedPerms, $allPerms)
{
    if (empty($allPerms)) {
        // No perms defined but filter still failed, meaning user was not authenticated
        App::abort(401, 'You do not have permission to view this page, please log in.');
    }

    // Empty failed perms means user was not authenticated
    // Act as if *all* perms failed instead
    $failedPerms = $failedPerms ?: $allPerms;

    $join = $this->entrustRequireAll ? 'and' : 'or';

    $desc = Permission::whereIn('name', (array)$failedPerms)->lists('description');

    switch (count($desc)) {
        case 1:
            $message = "You do not have permission to {$desc[0]}!";
            break;
        case 2:
            $message = "You do not have permission to {$desc[0]} $join {$desc[1]}!";
            break;
        default:
            $last = array_pop($desc);

            $message = 'You do not have permission to ' . implode(', ', $desc) . ", $join $last!";
            break;
    }

    App::abort(403, $message);
}
```

### Short syntax route filter

To filter a route by permission or role you can call the following in your `app/filters.php`:

```php
// only users with roles that have the 'manage_posts' permission will be able to access any route within admin/post
Entrust::routeNeedsPermission('admin/post*', 'create-post');

// only owners will have access to routes within admin/advanced
Entrust::routeNeedsRole('admin/advanced*', 'owner');

// optionally the second parameter can be an array of permissions or roles
// user would need to match all roles or permissions for that route
Entrust::routeNeedsPermission('admin/post*', array('create-post', 'edit-comment'));
Entrust::routeNeedsRole('admin/advanced*', array('owner','writer'));
```

Both of these methods accept a third parameter.
If the third parameter is null then the return of a prohibited access will be `App::abort(403)`, otherwise the third parameter will be returned.
So you can use it like:

```php
Entrust::routeNeedsRole('admin/advanced*', 'owner', Redirect::to('/home'));
```

Furthermore both of these methods accept a fourth parameter.
It defaults to true and checks all roles/permissions given.
If you set it to false, the function will only fail if all roles/permissions fail for that user.
Useful for admin applications where you want to allow access for multiple groups.

```php
// if a user has 'create-post', 'edit-comment', or both they will have access
Entrust::routeNeedsPermission('admin/post*', array('create-post', 'edit-comment'), null, false);

// if a user is a member of 'owner', 'writer', or both they will have access
Entrust::routeNeedsRole('admin/advanced*', array('owner','writer'), null, false);

// if a user is a member of 'owner', 'writer', or both, or user has 'create-post', 'edit-comment' they will have access
// if the 4th parameter is true then the user must be a member of Role and must have Permission
Entrust::routeNeedsRoleOrPermission(
    'admin/advanced*',
    array('owner', 'writer'),
    array('create-post', 'edit-comment'),
    null,
    false
);
```

### Route filter

Entrust roles/permissions can be used in filters by simply using the `can` and `hasRole` methods from within the Facade:

```php
Route::filter('manage_posts', function()
{
    // check the current user
    if (!Entrust::can('create-post')) {
        return Redirect::to('admin');
    }
});

// only users with roles that have the 'manage_posts' permission will be able to access any admin/post route
Route::when('admin/post*', 'manage_posts');
```

Using a filter to check for a role:

```php
Route::filter('owner_role', function()
{
    // check the current user
    if (!Entrust::hasRole('Owner')) {
        App::abort(403);
    }
});

// only owners will have access to routes within admin/advanced
Route::when('admin/advanced*', 'owner_role');
```

As you can see `Entrust::hasRole()` and `Entrust::can()` checks if the user is logged in, and then if he or she has the role or permission.
If the user is not logged the return will also be `false`.

## Troubleshooting

If you encounter an error when doing the migration that looks like:

```
SQLSTATE[HY000]: General error: 1005 Can't create table 'laravelbootstrapstarter.#sql-42c_f8' (errno: 150)
    (SQL: alter table `role_user` add constraint role_user_user_id_foreign foreign key (`user_id`)
    references `users` (`id`)) (Bindings: array ())
```

Then it's likely that the `id` column in your user table does not match the `user_id` column in `role_user`.
Match sure both are `INT(10)`.

## License

Entrust is free software distributed under the terms of the MIT license.

## Additional information

Current library documentation can be found on [GitHub Pages](http://bbatsche.github.io/entrust/).

Any questions, feel free to contact me or ask [here](http://laravel.io/forum/09-23-2014-package-zizaco-entrust).

Any issues, please [report here](https://github.com/bbatsche/entrust/issues).
