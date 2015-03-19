<?php

namespace Bbatsche\Entrust\Contracts;

interface EntrustRoleInterface
{
    public function users();
    public function perms();

    public function can($name);
    public function canAny($perms, array &$failedPerms = array());
    public function canAll($perms, array &$failedPerms = array());

    public function savePermissions($inputPermissions);
    public function attachPermission($permission);
    public function detachPermission($permission);
    public function attachPermissions($permissions);
    public function detachPermissions($permissions);
}
