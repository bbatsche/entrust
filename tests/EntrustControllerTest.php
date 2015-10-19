<?php

use Bbatsche\Entrust\Traits\EntrustControllerTrait;
use Bbatsche\Entrust\EntrustFacade;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntrustControllerTest extends PHPUnit_Framework_TestCase
{
    protected $entrust;
    protected $app;
    protected $controller;
    protected $route;
    protected $request;

    public function setUp()
    {
        $this->entrust    = Mockery::mock('Bbatsche\Entrust\Entrust');
        $this->app        = Mockery::mock('Illuminate\Foundation\Application');
        $this->controller = Mockery::mock('EntrustTestController')->makePartial();

        $this->route      = Mockery::mock('Illuminate\Routing\Route');
        $this->request    = Mockery::mock('Illuminate\Http\Request');

        $this->app->shouldReceive('instance');

        $this->route->shouldReceive('getActionName')->andReturn('ControllerName@actionName')->twice();

        Facade::setFacadeApplication($this->app);
        App::swap($this->app);
        EntrustFacade::swap($this->entrust);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function failedFilterDataProvider()
    {
        return array(
            array('filterThrowsException'),
            array('callbackString'),
            array('callbackClosure'),
            array('callbackFunction')
        );
    }

    public function testNoCollectionAllowMissing()
    {
        return $this->fireTests();
    }

    /**
     * @dataProvider failedFilterDataProvider
     */
    public function testNoCollectionDisallowMissing($testCallback)
    {
        $this->controller->entrustAllowMissing = false;

        return $this->$testCallback(array(), array(), null, null);
    }

    public function testFilterSingularPass()
    {
        $this->controller->entrustRoles = array('actionName' => 'RoleName');
        $this->controller->entrustPerms = array('actionName' => 'perm-name');

        $this->entrust->shouldReceive('is')->with('RoleName')->andReturn(true)->once();
        $this->entrust->shouldReceive('can')->with('perm-name')->andReturn(true)->once();

        return $this->fireTests();
    }

    /**
     * @dataProvider failedFilterDataProvider
     */
    public function testFilterSingularFail($testCallback)
    {
        $this->controller->entrustRoles = array('actionName' => 'RoleName');
        $this->controller->entrustPerms = array('actionName' => 'perm-name');

        $this->entrust->shouldReceive('is')->with('RoleName')->andReturn(false)->once();
        $this->entrust->shouldReceive('can')->with('perm-name')->andReturn(false)->once();

        return $this->$testCallback('RoleName', 'perm-name', 'RoleName', 'perm-name');
    }

    public function testFilterMultipleAnyAllowedPass()
    {
        $this->controller->entrustRoles = array('actionName' => array('RoleName1', 'RoleName2'));
        $this->controller->entrustPerms = array('actionName' => array('perm-name-a', 'perm-name-b'));

        $entrustUser = Mockery::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        $entrustUser->shouldReceive('isAny')->with(array('RoleName1', 'RoleName2'), array())
            ->andReturn(true)->once();
        $entrustUser->shouldReceive('canAny')->with(array('perm-name-a', 'perm-name-b'), array())
            ->andReturn(true)->once();

        $this->entrust->shouldReceive('user')->andReturn($entrustUser)->twice();

        return $this->fireTests();
    }

    /**
     * @dataProvider failedFilterDataProvider
     */
    public function testFilterMultipleAnyAllowedFail($testCallback)
    {
        $allRoles = array('RoleName1', 'RoleName2');
        $allPerms = array('perm-name-a', 'perm-name-b');

        $this->controller->entrustRoles = array('actionName' => $allRoles);
        $this->controller->entrustPerms = array('actionName' => $allPerms);

        $entrustUser = Mockery::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        $entrustUser->shouldReceive('isAny')
            ->with($allRoles, Mockery::on(function(&$roles) {
                if (!is_array($roles)) return false;

                $roles[] = 'FailedRole';

                return true;
            }))->andReturn(false)->once();
        $entrustUser->shouldReceive('canAny')
            ->with($allPerms, Mockery::on(function(&$perms) {
                if (!is_array($perms)) return false;

                $perms[] = 'failed-perm';

                return true;
            }))->andReturn(false)->once();

        $this->entrust->shouldReceive('user')->andReturn($entrustUser)->twice();

        return $this->$testCallback(array('FailedRole'), array('failed-perm'), $allRoles, $allPerms);
    }

    public function testFilterMultipleAllRequiredPass()
    {
        $this->controller->entrustRequireAll = true;

        $this->controller->entrustRoles = array('actionName' => array('RoleName1', 'RoleName2'));
        $this->controller->entrustPerms = array('actionName' => array('perm-name-a', 'perm-name-b'));

        $entrustUser = Mockery::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        $entrustUser->shouldReceive('isAll')->with(array('RoleName1', 'RoleName2'), array())
            ->andReturn(true)->once();
        $entrustUser->shouldReceive('canAll')->with(array('perm-name-a', 'perm-name-b'), array())
            ->andReturn(true)->once();

        $this->entrust->shouldReceive('user')->andReturn($entrustUser)->twice();

        return $this->fireTests();
    }

    /**
     * @dataProvider failedFilterDataProvider
     */
    public function testFilterMultipleAllRequiredFail($testCallback)
    {
        $allRoles = array('RoleName1', 'RoleName2');
        $allPerms = array('perm-name-a', 'perm-name-b');

        $this->controller->entrustRoles = array('actionName' => $allRoles);
        $this->controller->entrustPerms = array('actionName' => $allPerms);

        $this->controller->entrustRequireAll = true;

        $entrustUser = Mockery::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        $entrustUser->shouldReceive('isAll')
            ->with($allRoles, Mockery::on(function(&$roles) {
                if (!is_array($roles)) return false;

                $roles[] = 'FailedRole';

                return true;
            }))->andReturn(false)->once();

        $entrustUser->shouldReceive('canAll')
            ->with($allPerms, Mockery::on(function(&$perms) {
                if (!is_array($perms)) return false;

                $perms[] = 'failed-perm';

                return true;
            }))->andReturn(false)->once();

        $this->entrust->shouldReceive('user')->andReturn($entrustUser)->twice();

        return $this->$testCallback(array('FailedRole'), array('failed-perm'), $allRoles, $allPerms);
    }

    /**
     * @dataProvider failedFilterDataProvider
     */
    public function testFilterMultipleNoUser()
    {
        $allRoles = array('RoleName1', 'RoleName2');
        $allPerms = array('perm-name-a', 'perm-name-b');

        $this->controller->entrustRoles = array('actionName' => $allRoles);
        $this->controller->entrustPerms = array('actionName' => $allPerms);

        $this->entrust->shouldReceive('user')->andReturn(false)->twice();

        return $this->filterThrowsException(array(), array(), $allRoles, $allPerms);
    }

    protected function filterThrowsException()
    {
        $exception = Mockery::mock('Symfony\Component\HttpKernel\Exception\HttpException');

        $this->app->shouldReceive('abort')->with(403)->andThrow($exception)->twice();

        try {
            $this->controller->entrustRoleFilter($this->route, $this->request);

            $this->fail('Expected exception was not thrown');
        } catch (HttpException $e) {
            $this->assertSame($exception, $e);
        }

        try {
            $this->controller->entrustPermissionFilter($this->route, $this->request);

            $this->fail('Expected exception was not thrown');
        } catch (HttpException $e) {
            $this->assertSame($exception, $e);
        }
    }

    protected function callbackString($failedRoles, $failedPerms, $allRoles, $allPerms)
    {
        $this->controller->entrustRoleCallback       = 'functionName';
        $this->controller->entrustPermissionCallback = 'functionName';

        $this->controller->shouldReceive('functionName')
            ->with('actionName', $failedRoles, $allRoles, $this->route, $this->request)
            ->andReturn('Callback Role Result')->once();
        $this->controller->shouldReceive('functionName')
            ->with('actionName', $failedPerms, $allPerms, $this->route, $this->request)
            ->andReturn('Callback Permission Result')->once();

        $this->fireTests('Callback Role Result', 'Callback Permission Result');
    }

    protected function callbackClosure($failedRoles, $failedPerms, $allRoles, $allPerms)
    {
        $this->controller->entrustRoleCallback = function(
            $actionName, $passedFailedRoles, $passedAllRoles, $route, $request
        ) use (
            $failedRoles, $allRoles
        ) {
            $this->assertEquals('actionName', $actionName);
            $this->assertEquals($failedRoles, $passedFailedRoles);
            $this->assertEquals($allRoles,    $passedAllRoles);
            $this->assertSame($this->route,   $route);
            $this->assertSame($this->request, $request);

            return 'Callback Role Result';
        };

        $this->controller->entrustPermissionCallback = function(
            $actionName, $passedFailedPerms, $passedAllPerms, $route, $request
        ) use (
            $failedPerms, $allPerms
        ) {
            $this->assertEquals('actionName', $actionName);
            $this->assertEquals($failedPerms, $passedFailedPerms);
            $this->assertEquals($allPerms,    $passedAllPerms);
            $this->assertSame($this->route,   $route);
            $this->assertSame($this->request, $request);

            return 'Callback Permission Result';
        };

        $this->fireTests('Callback Role Result', 'Callback Permission Result');
    }

    protected function callbackFunction($failedRoles, $failedPerms, $allRoles, $allPerms)
    {
        $controller = Mockery::mock('TestControllerWithCallbacks')->makePartial();

        $controller->entrustPerms        = $this->controller->entrustPerms;
        $controller->entrustRoles        = $this->controller->entrustRoles;
        $controller->entrustAllowMissing = $this->controller->entrustAllowMissing;
        $controller->entrustRequireAll   = $this->controller->entrustRequireAll;

        $this->controller = $controller;

        $this->controller->expectedAction      = 'actionName';
        $this->controller->expectedFailedRoles = $failedRoles;
        $this->controller->expectedFailedPerms = $failedPerms;
        $this->controller->expectedAllRoles    = $allRoles;
        $this->controller->expectedAllPerms    = $allPerms;
        $this->controller->expectedRoute       = $this->route;
        $this->controller->expectedRequest     = $this->request;

        $this->fireTests('Callback Role Result', 'Callback Permission Result');
    }

    protected function fireTests($expectedRoleResult = null, $expectedPermResult = null)
    {
        $result = $this->controller->entrustRoleFilter($this->route, $this->request);
        $this->assertEquals($expectedRoleResult, $result);

        $result = $this->controller->entrustPermissionFilter($this->route, $this->request);
        $this->assertEquals($expectedPermResult, $result);
    }
}

class EntrustTestController extends Controller
{
    use EntrustControllerTrait;

    public function __set($key, $value)
    {
        $this->$key = $value;
    }

    public function __get($key)
    {
        return $this->$key;
    }
}

class TestControllerWithCallbacks extends EntrustTestController
{
    public $expectedAction;
    public $expectedFailedRoles;
    public $expectedFailedPerms;
    public $expectedAllRoles;
    public $expectedAllPerms;
    public $expectedRoute;
    public $expectedRequest;

    public function entrustRoleCallback($action, $failedRoles, $allRoles, $route, $request)
    {
        PHPUnit_Framework_Assert::assertEquals($this->expectedAction,      $action);
        PHPUnit_Framework_Assert::assertEquals($this->expectedFailedRoles, $failedRoles);
        PHPUnit_Framework_Assert::assertEquals($this->expectedAllRoles,    $allRoles);
        PHPUnit_Framework_Assert::assertSame($this->expectedRoute,   $route);
        PHPUnit_Framework_Assert::assertSame($this->expectedRequest, $request);

        return 'Callback Role Result';
    }

    public function entrustPermissionCallback($action, $failedPerms, $allPerms, $route, $request)
    {
        PHPUnit_Framework_Assert::assertEquals($this->expectedAction,      $action);
        PHPUnit_Framework_Assert::assertEquals($this->expectedFailedPerms, $failedPerms);
        PHPUnit_Framework_Assert::assertEquals($this->expectedAllPerms,    $allPerms);
        PHPUnit_Framework_Assert::assertSame($this->expectedRoute,   $route);
        PHPUnit_Framework_Assert::assertSame($this->expectedRequest, $request);

        return 'Callback Permission Result';
    }
}
