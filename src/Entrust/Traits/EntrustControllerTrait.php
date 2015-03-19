<?php

namespace Bbatsche\Entrust\Traits;

use Bbatsche\Entrust\EntrustFacade as Entrust;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;

trait EntrustControllerTrait
{
    protected $entrustPerms = [];
    protected $entrustRoles = [];

    protected $entrustAllowMissing = true;
    protected $entrustRequireAll   = false;

    protected $entrustPermissionCallback = '';
    protected $entrustRoleCallback = '';

    public function entrustPermissionFilter(Route $route, Request $request)
    {
        return $this->handleEntrustFilter('entrustPerms', 'entrustPermissionCallback', 'can', $route, $request);
    }

    public function entrustRoleFilter(Route $route, Request $request)
    {
        return $this->handleEntrustFilter('entrustRoles', 'entrustRoleCallback', 'hasRole', $route, $request);
    }

    public function handleEntrustFilter($collectionName, $callbackName, $entrustMethod, $route, $request)
    {
        $filterPassed = $this->entrustAllowMissing;
        $keyName = null;

        list($class, $method) = explode('@', $route->getActionName());

        $collection = $this->$collectionName;

        if (!empty($collection[$method])) {
            $keyName = $collection[$method];

            $filterPassed = Entrust::$entrustMethod($keyName, $this->entrustRequireAll);
        }

        if (!$filterPassed) {
            if (!empty($this->$callbackName) && is_string($this->$callbackName)) {
                // callbackName is the name of a function

                $callback = array($this, $this->$callbackName);
            } elseif (is_callable($this->$callbackName)) {
                // callbackName is a closure (or I guess some other callable type?)

                $callback = $this->$callbackName;
            } else {
                App::abort(403);
            }

            return call_user_func($callback, $method, $keyName, $route, $request);
        }
    }
}
