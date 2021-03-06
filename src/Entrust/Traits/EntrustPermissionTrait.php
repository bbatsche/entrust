<?php

namespace Bbatsche\Entrust\Traits;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

trait EntrustPermissionTrait
{
    /**
     * Boot the permission model.
     *
     * Attach event listener to remove the many-to-many records when trying to delete,
     * but will **not** delete any records if the permission model uses soft deletes.
     *
     * @return void|bool
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function($permission) {
            if (!method_exists(Config::get('entrust::permission'), 'bootSoftDeletingTrait')) {
                $permission->roles()->detach();
            }

            return true;
        });
    }

    /**
     * @see \Bbatsche\Entrust\Contracts\EntrustPermissionInterface::roles()
     */
    public function roles()
    {
        return $this->belongsToMany(Config::get('entrust::role'), Config::get('entrust::permission_role_table'));
    }
}
