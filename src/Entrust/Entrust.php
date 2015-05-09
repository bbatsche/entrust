<?php namespace Bbatsche\Entrust;

/**
 * This class is the main entry point of entrust. Usually this the interaction
 * with this class will be done through the Entrust Facade
 *
 * @license MIT
 * @package Bbatsche\Entrust
 */
class Entrust
{
    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new Entrust instance.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Checks if the current user has one or more roles by their name
     *
     * @param string|array $role       Role name or array of role names.
     * @param bool         $requireAll All roles in the array are required.
     *
     * @deprecated Replaced by is*() methods.
     *
     * @see \Bbatsche\Entrust\Entrust::is()
     * @see \Bbatsche\Entrust\Entrust::isAny()
     * @see \Bbatsche\Entrust\Entrust::isAll()
     *
     * @return bool
     */
    public function hasRole($role, $requireAll = false)
    {
        if ($user = $this->user()) {
            return $user->hasRole($role, $requireAll);
        }

        return false;
    }

    /**
     * Check if current user has a role by its name
     *
     * @param string $role Role name.
     *
     * @return bool
     */
    public function is($role)
    {
        if ($user = $this->user()) {
            return $user->is($role);
        }

        return false;
    }

    /**
     * Check if current user has **any** named roles.
     *
     * @param mixed  $roles       Set of role names.
     * @param array &$failedRoles The names of what roles were missing (if any).
     *
     * @return bool
     */
    public function isAny($roles, array &$failedRoles = array())
    {
        if ($user = $this->user()) {
            return $user->isAny($roles, $failedRoles);
        }

        return false;
    }

    /**
     * Check if the current user has **all** named roles.
     *
     * @param mixed  $roles       Set of role names.
     * @param array &$failedRoles The names of what roles were missing (if any).
     *
     * @return bool
     */
    public function isAll($roles, array &$failedRoles = array())
    {
        if ($user = $this->user()) {
            return $user->isAll($roles, $failedRoles);
        }

        return false;
    }

    /**
     * Check if the current user has a permission by its name.
     * Using this function with an array of roles is considered @deprecated.
     *
     * @param string|array $permission Permission name or array of permission names.
     * @param bool         $requireAll All permissions in the array are required, @deprecated.
     *
     * @see \Bbatsche\Entrust\Entrust::canAny()
     * @see \Bbatsche\Entrust\Entrust::canAll()
     *
     * @return bool
     */
    public function can($permission, $requireAll = false)
    {
        if ($user = $this->user()) {
            return $user->can($permission, $requireAll);
        }

        return false;
    }

    /**
     * Check if the current user has **any** named permissions.
     *
     * @param mixed  $perms       Set of permission names.
     * @param array &$failedPerms The names of what permissions were missing (if any).
     *
     * @return bool
     */
    public function canAny($perms, array &$failedPerms = array())
    {
        if ($user = $this->user()) {
            return $user->canAny($perms, $failedPerms);
        }

        return false;
    }

    /**
     * Check if the current user has **all** named permissions.
     *
     * @param mixed  $perms       Set of permission names.
     * @param array &$failedPerms The names of what permissions were missing (if any).
     *
     * @return bool
     */
    public function canAll($perms, array &$failedPerms = array())
    {
        if ($user = $this->user()) {
            return $user->canAll($perms, $failedPerms);
        }

        return false;
    }

    /**
     * Get the currently authenticated user or null.
     *
     * @return Illuminate\Auth\UserInterface|null
     */
    public function user()
    {
        return $this->app->auth->user();
    }

    /**
     * Filters a route for a role or set of roles.
     *
     * If the third parameter is null then abort with status code 403.
     * Otherwise the $result is returned.
     *
     * @param string       $route      Route pattern. i.e: "admin/*"
     * @param array|string $roles      The role(s) needed
     * @param mixed        $result     i.e: Redirect::to('/')
     * @param bool         $requireAll User must have all roles
     *
     * @return mixed
     */
    public function routeNeedsRole($route, $roles, $result = null, $requireAll = true)
    {
        $filterName  = is_array($roles) ? implode('_', $roles) : $roles;
        $filterName .= '_'.substr(md5($route), 0, 6);

        $closure = function () use ($roles, $result, $requireAll) {
            $hasRole = $this->hasRole($roles, $requireAll);

            if (!$hasRole) {
                return empty($result) ? $this->app->abort(403) : $result;
            }
        };

        // Same as Route::filter, registers a new filter
        $this->app->router->filter($filterName, $closure);

        // Same as Route::when, assigns a route pattern to the
        // previously created filter.
        $this->app->router->when($route, $filterName);
    }

    /**
     * Filters a route for a permission or set of permissions.
     *
     * If the third parameter is null then abort with status code 403.
     * Otherwise the $result is returned.
     *
     * @param string       $route       Route pattern. i.e: "admin/*"
     * @param array|string $permissions The permission(s) needed
     * @param mixed        $result      i.e: Redirect::to('/')
     * @param bool         $requireAll  User must have all permissions
     *
     * @return mixed
     */
    public function routeNeedsPermission($route, $permissions, $result = null, $requireAll = true)
    {
        $filterName  = is_array($permissions) ? implode('_', $permissions) : $permissions;
        $filterName .= '_'.substr(md5($route), 0, 6);

        $closure = function () use ($permissions, $result, $requireAll) {
            $hasPerm = $this->can($permissions, $requireAll);

            if (!$hasPerm) {
                return empty($result) ? $this->app->abort(403) : $result;
            }
        };

        // Same as Route::filter, registers a new filter
        $this->app->router->filter($filterName, $closure);

        // Same as Route::when, assigns a route pattern to the
        // previously created filter.
        $this->app->router->when($route, $filterName);
    }

    /**
     * Filters a route for role(s) and/or permission(s).
     *
     * If the third parameter is null then abort with status code 403.
     * Otherwise the $result is returned.
     *
     * @param string       $route       Route pattern. i.e: "admin/*"
     * @param array|string $roles       The role(s) needed
     * @param array|string $permissions The permission(s) needed
     * @param mixed        $result      i.e: Redirect::to('/')
     * @param bool         $requireAll  User must have all roles and permissions
     *
     * @return void
     */
    public function routeNeedsRoleOrPermission($route, $roles, $permissions, $result = null, $requireAll = false)
    {
        $filterName  =      is_array($roles)       ? implode('_', $roles)       : $roles;
        $filterName .= '_'.(is_array($permissions) ? implode('_', $permissions) : $permissions);
        $filterName .= '_'.substr(md5($route), 0, 6);

        $closure = function () use ($roles, $permissions, $result, $requireAll) {
            $hasRole  = $this->hasRole($roles, $requireAll);
            $hasPerms = $this->can($permissions, $requireAll);

            if ($requireAll) {
                $hasRolePerm = $hasRole && $hasPerms;
            } else {
                $hasRolePerm = $hasRole || $hasPerms;
            }

            if (!$hasRolePerm) {
                return empty($result) ? $this->app->abort(403) : $result;
            }
        };

        // Same as Route::filter, registers a new filter
        $this->app->router->filter($filterName, $closure);

        // Same as Route::when, assigns a route pattern to the
        // previously created filter.
        $this->app->router->when($route, $filterName);
    }
}
