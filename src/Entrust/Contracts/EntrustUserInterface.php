<?php

namespace Bbatsche\Entrust\Contracts;

interface EntrustUserInterface
{
    public function roles();

    public function hasRole($name, $requireAll = false);

    public function is($name);
    public function isAny(array $roles, array &$failedRoles = array());
    public function isAll(array $roles, array &$failedRoles = array());

    public function can($permission, $requireAll = false);

    public function canAny($perms, array &$failedPerms = array());
    public function canAll($perms, array &$failedPerms = array());

    public function ability($roles, $permissions, $options = array());

    public function attachRole($role);
    public function detachRole($role);
    public function attachRoles($roles);
    public function detachRoles($roles);
}
