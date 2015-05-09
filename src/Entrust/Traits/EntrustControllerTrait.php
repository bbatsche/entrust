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
        return $this->handleEntrustFilter('entrustRoles', 'entrustRoleCallback', 'is', $route, $request);
    }

    public function handleEntrustFilter($collectionName, $callbackName, $entrustMethod, $route, $request)
    {
        $filterPassed = $this->entrustAllowMissing;
        $entrustNames = null;
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
                    $entrustFailed = $entrustNames;
                }
            } else {
                $filterPassed = Entrust::$entrustMethod($entrustNames);
            }
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

            return call_user_func($callback, $method, $entrustFailed, $entrustNames, $route, $request);
        }
    }
}
