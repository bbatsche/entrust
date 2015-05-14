<?php

namespace Bbatsche\Entrust\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

trait EntrustRoleTrait
{
    /**
     * Boot the role model.
     *
     * Attach event listener to remove the many-to-many records when trying to delete,
     * but will **not** delete any records if the role model uses soft deletes.
     *
     * @return void|bool
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function($role) {
            if (!method_exists(Config::get('entrust::role'), 'bootSoftDeletingTrait')) {
                $role->users()->detach();
                $role->perms()->detach();
            }

            return true;
        });
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustRoleInterface::users()
     */
    public function users()
    {
        return $this->belongsToMany(Config::get('auth.model'), Config::get('entrust::role_user_table'));
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustRoleInterface::perms()
     */
    public function perms()
    {
        return $this->belongsToMany(Config::get('entrust::permission'), Config::get('entrust::permission_role_table'));
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustRoleInterface::can()
     */
    public function can($name)
    {
        return $this->perms->filter(function($perm) use ($name) {
            return $perm->name === $name;
        })->count() === 1;
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\Entrust\RoleInterface::canAny()
     */
    public function canAny($perms, array &$failedPerms = array())
    {
        $passed = false;

        foreach ($perms as $permName) {
            if ($this->can($permName)) {
                $passed = true;
            } else {
                $failedPerms[] = $permName;
            }
        }

        return $passed;
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\Entrust\RoleInterface::canAll()
     */
    public function canAll($perms, array &$failedPerms = array())
    {
        $passed = true;

        foreach ($perms as $permName) {
            if (!$this->can($permName)) {
                $passed = false;
                $failedPerms[] = $permName;
            }
        }

        return $passed;
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\Entrust\RoleInterface::savePermissions()
     */
    public function savePermissions($inputPermissions)
    {
        if (!empty($inputPermissions)) {
            $this->perms()->sync($inputPermissions);
        } else {
            $this->perms()->detach();
        }
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\Entrust\RoleInterface::attachPermission()
     */
    public function attachPermission($permission)
    {
        if (is_object($permission)) $permission = $permission->getKey();

        if (is_array($permission))  $permission = $permission['id'];

        $this->perms()->attach($permission);
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\Entrust\RoleInterface::detachPermission()
     */
    public function detachPermission($permission)
    {
        if (is_object($permission)) $permission = $permission->getKey();

        if (is_array($permission))  $permission = $permission['id'];

        $this->perms()->detach($permission);
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\Entrust\RoleInterface::attachPermissions()
     */
    public function attachPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            $this->attachPermission($permission);
        }
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\Entrust\RoleInterface::detachPermissions()
     */
    public function detachPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            $this->detachPermission($permission);
        }
    }
}
