<?php

namespace Bbatsche\Entrust\Traits;

use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Exception\InvalidArgumentException;

trait EntrustUserTrait
{
    /**
     * Boot the user model.
     *
     * Attach event listener to remove the many-to-many records when trying to delete,
     * but will **not** delete any records if the user model uses soft deletes.
     *
     * @return void|bool
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function($user) {
            if (!method_exists(Config::get('auth.model'), 'bootSoftDeletingTrait')) {
                $user->roles()->sync([]);
            }

            return true;
        });
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::roles()
     */
    public function roles()
    {
        return $this->belongsToMany(Config::get('entrust::role'), Config::get('entrust::role_user_table'), 'user_id', 'role_id');
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::hasRole()
     */
    public function hasRole($name, $requireAll = false)
    {
        if (is_array($name)) {
            if ($requireAll) {
                return $this->isAll($name);
            } else {
                return $this->isAny($name);
            }
        } else {
            return $this->is($name);
        }
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::is()
     */
    public function is($name)
    {
        return $this->roles->filter(function($role) use ($name) {
            return $role->name === $name;
        })->count() === 1;
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::isAny()
     */
    public function isAny($roles, array &$failedRoles = array())
    {
        $passed = false;

        foreach ($roles as $roleName) {
            if ($this->is($roleName)) {
                $passed = true;
            } else {
                $failedRoles[] = $roleName;
            }
        }

        return $passed;
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::isAll()
     */
    public function isAll($roles, array &$failedRoles = array())
    {
        $passed = true;

        foreach ($roles as $roleName) {
            if (!$this->is($roleName)) {
                $passed = false;
                $failedRoles[] = $roleName;
            }
        }

        return $passed;
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::can()
     */
    public function can($permission, $requireAll = false)
    {
        // Attempt to eager load permissions if the roles haven't been loaded already
        // No trivial way to detect if roles *and* permissions have been loaded, so just assume roles for now
        if (!array_key_exists('roles', $this->getRelations())) {
            $this->load('roles.perms');
        }

        if (is_array($permission)) {
            if ($requireAll) {
                return $this->canAll($permission);
            } else {
                return $this->canAny($permission);
            }
        } else {
            return $this->roles->filter(function($role) use ($permission) {
                return $role->can($permission);
            })->count() > 0;
        }
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::canAny()
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
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::canAll()
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
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::ability()
     */
    public function ability($roles, $permissions, $options = array())
    {
        // Convert string to array if that's what is passed in.
        if (!is_array($roles)) {
            $roles = explode(',', $roles);
        }
        if (!is_array($permissions)) {
            $permissions = explode(',', $permissions);
        }

        // Set up default values and validate options.
        if (!isset($options['validate_all'])) {
            $options['validate_all'] = false;
        } else {
            if ($options['validate_all'] != true && $options['validate_all'] != false) {
                throw new InvalidArgumentException();
            }
        }
        if (!isset($options['return_type'])) {
            $options['return_type'] = 'boolean';
        } else {
            if ($options['return_type'] != 'boolean' &&
                $options['return_type'] != 'array' &&
                $options['return_type'] != 'both') {
                throw new InvalidArgumentException();
            }
        }

        // Loop through roles and permissions and check each.
        $checkedRoles = array();
        $checkedPermissions = array();
        foreach ($roles as $role) {
            $checkedRoles[$role] = $this->hasRole($role);
        }
        foreach ($permissions as $permission) {
            $checkedPermissions[$permission] = $this->can($permission);
        }

        // If validate all and there is a false in either
        // Check that if validate all, then there should not be any false.
        // Check that if not validate all, there must be at least one true.
        if(($options['validate_all'] && !(in_array(false,$checkedRoles) || in_array(false,$checkedPermissions))) ||
            (!$options['validate_all'] && (in_array(true,$checkedRoles) || in_array(true,$checkedPermissions)))) {
            $validateAll = true;
        } else {
            $validateAll = false;
        }

        // Return based on option
        if ($options['return_type'] == 'boolean') {
            return $validateAll;
        } elseif ($options['return_type'] == 'array') {
            return array('roles' => $checkedRoles, 'permissions' => $checkedPermissions);
        } else {
            return array($validateAll, array('roles' => $checkedRoles, 'permissions' => $checkedPermissions));
        }

    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::attachRole()
     */
    public function attachRole($role)
    {
        if(is_object($role)) {
            $role = $role->getKey();
        }

        if(is_array($role)) {
            $role = $role['id'];
        }

        $this->roles()->attach($role);
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::detachRole()
     */
    public function detachRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }

        $this->roles()->detach($role);
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::attachRoles()
     */
    public function attachRoles($roles)
    {
        foreach ($roles as $role) {
            $this->attachRole($role);
        }
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustUserInterface::detachRoles()
     */
    public function detachRoles($roles)
    {
        foreach ($roles as $role) {
            $this->detachRole($role);
        }
    }
}
