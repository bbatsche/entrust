<?php

namespace Bbatsche\Entrust\Traits;

use Bbatsche\Entrust\EntrustFacade as Entrust;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;

/**
 * Trait useful for adding simplified before filters to controllers
 *
 * @package Bbatsche\Entrust
 */
trait EntrustControllerTrait
{
    /**
     * Associative array of permissions to check on given actions
     * Format should be:
     *      'methodName' => 'permission-name'
     * or
     *      'methodName' => array('permission-name-1', 'permission-name-2', etc)
     *
     * @var array
     */
    protected $entrustPerms = [];
    /**
     * Associative array of roles to check on given actions
     * Format should be:
     *      'methodName' => 'role-name'
     * or
     *      'methodName' => array('role-name-1', 'role-name-2', etc)
     *
     * @var array
     */
    protected $entrustRoles = [];

    /**
     * When processing filters, treat undefined roles or permissions as a "pass"
     * Setting this to `false` can be useful as a sanity check for more secure controllers
     *
     * @var bool
     */
    protected $entrustAllowMissing = true;
    /**
     * When an action requires an array of permissions or roles, require that *all*
     * the values be present for the user, rather than just one of them
     *
     * @var bool
     */
    protected $entrustRequireAll   = false;

    /**
     * Function to be returned after entrustPermissionFilter() fails, instead of throwing a 403 exception.
     * If the value is a string, a method with that name will run.
     * If the value is a Closure, it will be run directly.
     *
     * @var string|Closure
     */
    protected $entrustPermissionCallback = null;
    /**
     * Function to be returned after entrustRoleFilter() fails, instead of throwing a 403 exception.
     * If the value is a string, a method with that name will be run.
     * If the value is a Closure, it will be run directly.
     *
     * @var string|Closure
     */
    protected $entrustRoleCallback = null;

    /**
     * Before filter to check $entrustPerms when processing a request. The function will:
     *
     * 1. Check for a key in $entrustPerms that matches the requested action name
     * 2. If that key is found, check the currently logged in user for the required permission(s)
     * 3. If the user does not have the permission(s), throw a 403 HttpException
     *
     * If the key is not found, and $allowMissing is `false`, then this filter will fail
     *
     * If the specified permission is an array and $requireAll is `true`,
     * then the user must have *all* the permissions
     *
     * If the controller has a function called entrustPermissionCallback(),
     * then it will be returned instead of throwing an exception
     *
     * If $entrustPermissionCallback is defined and is either a string or closure,
     * then it will be called and returned instead of throwing an exception.
     *
     * @param \Illuminate\Routing\Route $route
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
     */
    public function entrustPermissionFilter(Route $route, Request $request)
    {
        return $this->handleEntrustFilter('entrustPerms', 'entrustPermissionCallback', 'can', $route, $request);
    }

    /**
     * Before filter to check $entrustRoles when processing a request. The function will:
     *
     * 1. Check for a key in $entrustRoles that matches the requested action name
     * 2. If that key is found, check the currently logged in user for the required role(s)
     * 3. If the user does not have the role(s), throw a 403 HttpException
     *
     * If the key is not found, and $allowMissing is `false`, then this filter will fail
     *
     * If the specified role is an array and $requireAll is `true`,
     * then the user must have *all* the roles
     *
     * If the controller has a function called entrustRoleCallback(),
     * then it will be returned instead of throwing an exception
     *
     * If $entrustRoleCallback is defined and is either a string or closure,
     * then it will be called and returned instead of throwing an exception.
     *
     * @param \Illuminate\Routing\Route $route
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return void
     */
    public function entrustRoleFilter(Route $route, Request $request)
    {
        return $this->handleEntrustFilter('entrustRoles', 'entrustRoleCallback', 'is', $route, $request);
    }

    private function handleEntrustFilter($collectionName, $callbackName, $entrustMethod, $route, $request)
    {
        $filterPassed  = $this->entrustAllowMissing;
        $entrustNames  = null;
        $entrustFailed = array();

        list($class, $method) = explode('@', $route->getActionName());

        $collection = $this->$collectionName;

        if (!empty($collection[$method])) {
            $entrustNames = $collection[$method];

            if (is_array($entrustNames)) {
                $entrustMethod .= $this->entrustRequireAll ? 'All' : 'Any';

                if ($user = Entrust::user()) {
                    $filterPassed = $user->$entrustMethod($entrustNames, $entrustFailed);
                } else {
                    $filterPassed = false;
                }
            } elseif(!($filterPassed = Entrust::$entrustMethod($entrustNames))) {
                // Filter failed; reassign failed values with required values since it was singular
                $entrustFailed = $entrustNames;
            }
        }

        if (!$filterPassed) {
            $reflection = new \ReflectionClass($this);

            if (!empty($this->$callbackName) && is_string($this->$callbackName)) {
                // callbackName is the name of a function

                $callback = array($this, $this->$callbackName);
            } elseif (is_callable($this->$callbackName)) {
                // callbackName is a closure

                $callback = $this->$callbackName;
            } elseif ($reflection->hasMethod($callbackName)) {
                // callbackName is a function in the class

                $callback = array($this, $callbackName);
            } else {
                App::abort(403);
            }

            return call_user_func($callback, $method, $entrustFailed, $entrustNames, $route, $request);
        }
    }
}
