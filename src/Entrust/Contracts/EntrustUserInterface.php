<?php

namespace Bbatsche\Entrust\Contracts;

interface EntrustUserInterface
{
    /**
     * Many-to-Many relations with role model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();

    /**
     * Checks if the user has a role by its name.
     *
     * @param string|array $name       Role name or array of role names.
     * @param bool         $requireAll All roles in the array are required.
     *
     * @deprecated Replaced by is*() methods.
     *
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::is()
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::isAny()
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::isAll()
     *
     * @return bool
     */
    public function hasRole($name, $requireAll = false);

    /**
     * Check if user has a role by its name.
     *
     * @param string $name Role name.
     *
     * @return bool
     */
    public function is($name);
    /**
     * Check if user has **any** named roles.
     *
     * @param mixed  $roles       Set of role names.
     * @param array &$failedRoles The names of what roles were missing (if any).
     *
     * @return bool
     */
    public function isAny($roles, array &$failedRoles = array());
    /**
     * Check if user has **all** named roles.
     *
     * @param mixed  $roles       Set of role names.
     * @param array &$failedRoles The names of what roles were missing (if any).
     *
     * @return bool
     */
    public function isAll($roles, array &$failedRoles = array());

    /**
     * Check if user has a permission by its name.
     * Using this function with an array of roles is considered @deprecated.
     *
     * @param string|array $permission Permission string or array of permissions.
     * @param bool         $requireAll All permissions in the array are required, @deprecated.
     *
     * @return bool
     */
    public function can($permission, $requireAll = false);
    /**
     * Check if user has **any** named permissions.
     *
     * @param mixed  $perms       Set of permission names.
     * @param array &$failedPerms The names of what permissions were missing (if any).
     *
     * @return bool
     */
    public function canAny($perms, array &$failedPerms = array());
    /**
     * Check if user has **all** named permissions.
     *
     * @param mixed  $perms       Set of permission names.
     * @param array &$failedPerms The names of what permissions were missing (if any).
     *
     * @return bool
     */
    public function canAll($perms, array &$failedPerms = array());

    /**
     * Checks role(s) and permission(s).
     *
     * @param string|array $roles       Array of roles or comma separated string
     * @param string|array $permissions Array of permissions or comma separated string.
     * @param array        $options     validate_all (true|false) or return_type (boolean|array|both)
     *
     * @throws \InvalidArgumentException
     *
     * @return array|bool
     */
    public function ability($roles, $permissions, $options = array());

    /**
     * Alias to eloquent many-to-many relation's attach() method.
     *
     * @param mixed $role
     *
     * @return void
     */
    public function attachRole($role);
    /**
     * Alias to eloquent many-to-many relation's detach() method.
     *
     * @param mixed $role
     *
     * @return void
     */
    public function detachRole($role);
    /**
     * Attach multiple roles to a user
     *
     * @param mixed $roles
     *
     * @return void
     */
    public function attachRoles($roles);
    /**
     * Detach multiple roles from a user
     *
     * @param mixed $roles
     *
     * @return void
     */
    public function detachRoles($roles);
}
