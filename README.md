Redoubt
=========

A resource-level ACL for Laravel 4. Based on and inspired by [lukaszb/django-guardian](https://github.com/lukaszb/django-guardian), an excellent Django library.

*Note*: this in active development. The interfaces won't change, but there will be more functionality added in within the next few months.

## Installation

Add the following line to the `require` section of `composer.json`:

```json
{
    "require": {
        "greggilbert/redoubt": "dev-master"
    }
}
```

## Setup

1. Add `Greggilbert\Redoubt\RedoubtServiceProvider` to the service provider list in `app/config/app.php`.
2. Add `'Redoubt' => 'Greggilbert\Redoubt\Facades\Redoubt',` to the list of aliases in `app/config/app.php`.
3. If you're using Eloquent, run `php artisan migrate --package=greggilbert/redoubt`.
4. OPTIONAL: If you plan to override any of the base classes (e.g. User), run `php artisan config:publish greggilbert/redoubt`.

## Usage

Redoubt offers two levels of permissions: users and groups. Users and groups can be given access to resources, and users can be associated to groups. Each resouce must have permission defined on it.

Redoubt uses Laravel's built-in polymorphic relations to handle its associations, so all you have to do is pass in the actual model.

### On resources

Resources need to implement `Greggilbert\Redoubt\Permission\PermissibleInterface`, which defines one method, `getPermissions()`. The method needs to return an array where the key is the permission, and the value is the description:

```php
class Article implements Greggilbert\Redoubt\Permission\PermissibleInterface
{
    public function getPermissions()
    {
        return array(
            'edit' => 'Edit an article',
            'view' => 'View an article',
        );
    }
}
```

This MUST be defined for each method; trying to associate a permission on a resource where the permission is not already defined will throw an error.

### To create a group:

```php
$group = Redoubt::group()->create(array(
    'name' => 'My Group',
));
```

### To associate a user to a resource:

```php
$resource = Article::find(1);

Redoubt::allowUser('edit', $resource);
```

`allowUser()` has a third parameter for a user; if it's not defined, it will default to the current one used by Laravel's `Auth`.

### To deassociate a user to a resource:

```php
Redoubt::disallowUser('edit', $resource);
```

### To associate a group to a resource:

```php
$group = // your definition here...

Redoubt::allowGroup('edit', $resource, $group);
```

### To deassociate a group to a resource:

```php
Redoubt::disallowGroup('edit', $resource, $group);
```

### To associate a user to a group:

If you're using the default configuration, Users and Groups are Eloquent models, so you would do:

```php
$user->groups()->attach($group);
```

### To check if a user has access:

```php
Redoubt::userCan('edit', $resource); // returns a boolean
```

`Redoubt::userCan()` checks if the user has access or if they're in any groups that have that access.

### To get all permissions that a user has:

```php
Redoubt::getPermissions();
```

`getPermissions()` can take three parameters: a user, an object, and a permission. All of these parameters are optional. If the first parameter is left as null, it will use the current user.

The following would get all the permissions the current user has for Articles.

```php
$permissions = Redoubt::getPermissions(null, 'Article');
```

Similarly, this would get all the permissions the current user has for editing Articles.

```php
$permissions = Redoubt::getPermissions(null, 'Article', 'edit');
```

You can pass in an Article object for the second parameter as well.


### To get users who have permissions to an object:

```php
Redoubt::getUsers('edit', $resource);
```

Note that this will return UserObjectPermission models; you'll need to then call `->getUser()` to get the user.

### To get groups who have permissions to an object:

```php
Redoubt::getGroups('edit', $resource);
```

Note that this will return GroupObjectPermission models; you'll need to then call `->getGroup()` to get the group.

## Extension

Redoubt has a built-in User class, but if you want to extend it to use on your own, either extend `Greggilbert\Redoubt\User\EloquentUser` or implement the `Greggilbert\Redoubt\User\UserInterface` interface. You'll also need to publish the config for the package and change the user model listed there.
