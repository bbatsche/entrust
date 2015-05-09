<?php

namespace Bbatsche\Entrust\Contracts;

interface EntrustRoleInterface
{
    /**
     * Many-to-Many relations with the user model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();
    /**
     * Many-to-Many relations with the permission model.
     * Named "perms" for backwards compatibility. Also because "perms" is short and sweet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function perms();

    /**
     * Check if role is associated with a permission by name
     *
     * @param string $name Permission name
     *
     * @return bool
     */
    public function can($name);
    /**
     * Check if role is associated with **any** permission in a set.
     *
     * @param mixed  $perms       Set of permission names.
     * @param array &$failedPerms The names of what permissions were missing (if any).
     * @return void
     */
    public function canAny($perms, array &$failedPerms = array());
    /**
     * Check if role is associated with **all** permissions in a set.
     *
     * @param mixed  $perms       Set of permission names.
     * @param array &$failedPerms The names of what permissions were missing (if any).
     * @return void
     */
    public function canAll($perms, array &$failedPerms = array());

    /**
     * Save the inputted permissions.
     *
     * @param mixed $inputPermissions
     *
     * @return void
     */
    public function savePermissions($inputPermissions);
    /**
     * Attach permission to current role.
     *
     * @param object|array $permission
     *
     * @return void
     */
    public function attachPermission($permission);
    /**
     * Detach permission form current role.
     *
     * @param object|array $permission
     *
     * @return void
     */
    public function detachPermission($permission);
    /**
     * Attach multiple permissions to current role.
     *
     * @param mixed $permissions
     *
     * @return void
     */
    public function attachPermissions($permissions);
    /**
     * Detach multiple permissions from current role
     *
     * @param mixed $permissions
     *
     * @return void
     */
    public function detachPermissions($permissions);
}
