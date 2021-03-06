<?php

use Bbatsche\Entrust\Entrust;
use Illuminate\Support\Facades\Facade;
use Mockery as m;

class EntrustTest extends PHPUnit_Framework_TestCase
{
    protected $nullFilterTest;
    protected $abortFilterTest;
    protected $customResponseFilterTest;

    protected $expectedResponse;

    public function setUp()
    {
        $this->nullFilterTest = function($filterClosure) {
            if (!($filterClosure instanceof Closure)) {
                return false;
            }

            $this->assertNull($filterClosure());

            return true;
        };

        $this->abortFilterTest = function($filterClosure) {
            if (!($filterClosure instanceof Closure)) {
                return false;
            }

            try {
                $filterClosure();
            } catch (Exception $e) {
                $this->assertSame('abort', $e->getMessage());

                return true;
            }

            // If we've made it this far, no exception was thrown and something went wrong
            return false;
        };

        $this->customResponseFilterTest = function($filterClosure) {
            if (!($filterClosure instanceof Closure)) {
                return false;
            }

            $result = $filterClosure();

            $this->assertSame($this->expectedResponse, $result);

            return true;
        };
    }

    public function tearDown()
    {
        m::close();
    }

    public function testHasRole()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = new stdClass();
        $entrust = m::mock('Bbatsche\Entrust\Entrust[user]', [$app]);
        $user = m::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $entrust->shouldReceive('user')
            ->andReturn($user)
            ->twice()->ordered();

        $entrust->shouldReceive('user')
            ->andReturn(false)
            ->once()->ordered();

        $user->shouldReceive('hasRole')
            ->with('UserRole', false)
            ->andReturn(true)
            ->once();

        $user->shouldReceive('hasRole')
            ->with('NonUserRole', false)
            ->andReturn(false)
            ->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($entrust->hasRole('UserRole'));
        $this->assertFalse($entrust->hasRole('NonUserRole'));
        $this->assertFalse($entrust->hasRole('AnyRole'));
    }

    public function testIs()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = new stdClass();
        $entrust = m::mock('Bbatsche\Entrust\Entrust[user]', [$app]);
        $user = m::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $entrust->shouldReceive('user')->andReturn($user)->twice()->ordered();
        $entrust->shouldReceive('user')->andReturn(false)->once()->ordered();

        $user->shouldReceive('is')->with('UserRole')->andReturn(true)->once();
        $user->shouldReceive('is')->with('NonUserRole')->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($entrust->is('UserRole'));
        $this->assertFalse($entrust->is('NonUserRole'));
        $this->assertFalse($entrust->is('AnyRole'));
    }

    public function testIsAny()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $app = new stdClass();
        $entrust = m::mock('Bbatsche\Entrust\Entrust[user]', [$app]);
        $user = m::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $entrust->shouldReceive('user')->andReturn($user)->twice()->ordered();
        $entrust->shouldReceive('user')->andReturn(false)->once()->ordered();

        $user->shouldReceive('isAny')
            ->with(['UserRoleA', 'UserRoleB'], array())
            ->andReturn(true)
            ->once();
        $user->shouldReceive('isAny')
            ->with(['NonUserRoleA', 'NonUserRoleB'], m::on(function(&$roles) {
                if (!is_array($roles)) return false;

                $roles[] = 'NonUserRoleA';
                $roles[] = 'NonUserRoleB';

                return true;
            }))->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $failedRoles = array();
        $this->assertTrue($entrust->isAny(['UserRoleA', 'UserRoleB'], $failedRoles));
        $this->assertInternalType('array', $failedRoles);
        $this->assertEmpty($failedRoles);

        $failedRoles = array();
        $this->assertFalse($entrust->isAny(['NonUserRoleA', 'NonUserRoleB'], $failedRoles));
        $this->assertInternalType('array', $failedRoles);
        $this->assertContains('NonUserRoleA', $failedRoles);
        $this->assertContains('NonUserRoleB', $failedRoles);

        $this->assertFalse($entrust->isAny(['AnyRole']));
    }

    public function testIsAll()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $app = new stdClass();
        $entrust = m::mock('Bbatsche\Entrust\Entrust[user]', [$app]);
        $user = m::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $entrust->shouldReceive('user')->andReturn($user)->twice()->ordered();
        $entrust->shouldReceive('user')->andReturn(false)->once()->ordered();

        $user->shouldReceive('isAll')
            ->with(['UserRoleA', 'UserRoleB'], array())
            ->andReturn(true)
            ->once();
        $user->shouldReceive('isAll')
            ->with(['NonUserRoleA', 'NonUserRoleB'], m::on(function(&$roles) {
                if (!is_array($roles)) return false;

                $roles[] = 'NonUserRoleA';
                $roles[] = 'NonUserRoleB';

                return true;
            }))->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $failedRoles = array();
        $this->assertTrue($entrust->isAll(['UserRoleA', 'UserRoleB'], $failedRoles));
        $this->assertInternalType('array', $failedRoles);
        $this->assertEmpty($failedRoles);

        $failedRoles = array();
        $this->assertFalse($entrust->isAll(['NonUserRoleA', 'NonUserRoleB'], $failedRoles));
        $this->assertInternalType('array', $failedRoles);
        $this->assertContains('NonUserRoleA', $failedRoles);
        $this->assertContains('NonUserRoleB', $failedRoles);

        $this->assertFalse($entrust->isAll(['AnyRole']));
    }

    public function testCan()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = new stdClass();
        $entrust = m::mock('Bbatsche\Entrust\Entrust[user]', [$app]);
        $user = m::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $entrust->shouldReceive('user')
            ->andReturn($user)
            ->twice()->ordered();

        $entrust->shouldReceive('user')
            ->andReturn(false)
            ->once()->ordered();

        $user->shouldReceive('can')
            ->with('user_can', false)
            ->andReturn(true)
            ->once();

        $user->shouldReceive('can')
            ->with('user_cannot', false)
            ->andReturn(false)
            ->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertTrue($entrust->can('user_can'));
        $this->assertFalse($entrust->can('user_cannot'));
        $this->assertFalse($entrust->can('any_permission'));
    }

    public function testCanAny()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $app = new stdClass();
        $entrust = m::mock('Bbatsche\Entrust\Entrust[user]', [$app]);
        $user = m::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $entrust->shouldReceive('user')->andReturn($user)->twice()->ordered();
        $entrust->shouldReceive('user')->andReturn(false)->once()->ordered();

        $user->shouldReceive('canAny')
            ->with(['user-can1', 'user-can2'], array())
            ->andReturn(true)
            ->once();
        $user->shouldReceive('canAny')
            ->with(['user-cannot1', 'user-cannot2'], m::on(function(&$perms) {
                if (!is_array($perms)) return false;

                $perms[] = 'user-cannot1';
                $perms[] = 'user-cannot2';

                return true;
            }))->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $failedPerms = array();
        $this->assertTrue($entrust->canAny(['user-can1', 'user-can2'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertEmpty($failedPerms);

        $failedPerms = array();
        $this->assertFalse($entrust->canAny(['user-cannot1', 'user-cannot2'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertContains('user-cannot1', $failedPerms);
        $this->assertContains('user-cannot2', $failedPerms);

        $this->assertFalse($entrust->canAny(['any-perm']));
    }

    public function testCanAll()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $app = new stdClass();
        $entrust = m::mock('Bbatsche\Entrust\Entrust[user]', [$app]);
        $user = m::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $entrust->shouldReceive('user')->andReturn($user)->twice()->ordered();
        $entrust->shouldReceive('user')->andReturn(false)->once()->ordered();

        $user->shouldReceive('canAll')
            ->with(['user-can1', 'user-can2'], array())
            ->andReturn(true)
            ->once();
        $user->shouldReceive('canAll')
            ->with(['user-cannot1', 'user-cannot2'], m::on(function(&$perms) {
                if (!is_array($perms)) return false;

                $perms[] = 'user-cannot1';
                $perms[] = 'user-cannot2';

                return true;
            }))->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $failedPerms = array();
        $this->assertTrue($entrust->canAll(['user-can1', 'user-can2'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertEmpty($failedPerms);

        $failedPerms = array();
        $this->assertFalse($entrust->canAll(['user-cannot1', 'user-cannot2'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertContains('user-cannot1', $failedPerms);
        $this->assertContains('user-cannot2', $failedPerms);

        $this->assertFalse($entrust->canAll(['any-perm']));
    }

    public function testUser()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = new stdClass();
        $app->auth = m::mock('Auth');
        $entrust = new Entrust($app);
        $user = m::mock('Bbatsche\Entrust\Contracts\EntrustUserInterface');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $app->auth->shouldReceive('user')
            ->andReturn($user)
            ->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertSame($user, $entrust->user());
    }

    public function testRouteNeedsRole()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = new stdClass();
        $app->router = m::mock('Route');
        $entrust = new Entrust($app);

        $route = 'route';
        $oneRole = 'RoleA';
        $manyRole = ['RoleA', 'RoleB', 'RoleC'];

        $oneRoleFilterName  = $this->makeFilterName($route, [$oneRole]);
        $manyRoleFilterName = $this->makeFilterName($route, $manyRole);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $app->router->shouldReceive('filter')
            ->with(m::anyOf($oneRoleFilterName, $manyRoleFilterName), m::type('Closure'))
            ->twice()->ordered();

        $app->router->shouldReceive('when')
            ->with($route, m::anyOf($oneRoleFilterName, $manyRoleFilterName))
            ->twice();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $entrust->routeNeedsRole($route, $oneRole);
        $entrust->routeNeedsRole($route, $manyRole);
    }

    public function testRouteNeedsPermission()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = new stdClass();
        $app->router = m::mock('Route');
        $entrust = new Entrust($app);

        $route = 'route';
        $onePerm = 'can_a';
        $manyPerm = ['can_a', 'can_b', 'can_c'];

        $onePermFilterName = $this->makeFilterName($route, [$onePerm]);
        $manyPermFilterName = $this->makeFilterName($route, $manyPerm);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $app->router->shouldReceive('filter')
            ->with(m::anyOf($onePermFilterName, $manyPermFilterName), m::type('Closure'))
            ->twice()->ordered();

        $app->router->shouldReceive('when')
            ->with($route, m::anyOf($onePermFilterName, $manyPermFilterName))
            ->twice();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $entrust->routeNeedsPermission($route, $onePerm);
        $entrust->routeNeedsPermission($route, $manyPerm);
    }

    public function testRouteNeedsRoleOrPermission()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $app = new stdClass();
        $app->router = m::mock('Route');
        $entrust = new Entrust($app);

        $route = 'route';
        $oneRole = 'RoleA';
        $manyRole = ['RoleA', 'RoleB', 'RoleC'];
        $onePerm = 'can_a';
        $manyPerm = ['can_a', 'can_b', 'can_c'];

        $oneRoleOnePermFilterName = $this->makeFilterName($route, [$oneRole], [$onePerm]);
        $oneRoleManyPermFilterName = $this->makeFilterName($route, [$oneRole], $manyPerm);
        $manyRoleOnePermFilterName = $this->makeFilterName($route, $manyRole, [$onePerm]);
        $manyRoleManyPermFilterName = $this->makeFilterName($route, $manyRole, $manyPerm);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $app->router->shouldReceive('filter')
            ->with(
                m::anyOf(
                    $oneRoleOnePermFilterName,
                    $oneRoleManyPermFilterName,
                    $manyRoleOnePermFilterName,
                    $manyRoleManyPermFilterName
                ),
                m::type('Closure')
            )
            ->times(4)->ordered();

        $app->router->shouldReceive('when')
            ->with(
                $route,
                m::anyOf(
                    $oneRoleOnePermFilterName,
                    $oneRoleManyPermFilterName,
                    $manyRoleOnePermFilterName,
                    $manyRoleManyPermFilterName
                )
            )
            ->times(4);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $entrust->routeNeedsRoleOrPermission($route, $oneRole, $onePerm);
        $entrust->routeNeedsRoleOrPermission($route, $oneRole, $manyPerm);
        $entrust->routeNeedsRoleOrPermission($route, $manyRole, $onePerm);
        $entrust->routeNeedsRoleOrPermission($route, $manyRole, $manyPerm);
    }

    public function simpleFilterDataProvider()
    {
        return array(
            // Filter passes, null is returned
            array(true, 'nullFilterTest'),
            // Filter fails, App::abort() is called
            array(false, 'abortFilterTest', true),
            // Filter fails, custom response is returned
            array(false, 'customResponseFilterTest', false, new stdClass())
        );
    }

    /**
     * @dataProvider simpleFilterDataProvider
     */
    public function testFilterGeneratedByRouteNeedsRole($returnValue, $filterTest, $abort = false, $expectedResponse = null)
    {
        $this->filterTestExecution('routeNeedsRole', 'hasRole', $returnValue, $filterTest, $abort, $expectedResponse);
    }

    /**
     * @dataProvider simpleFilterDataProvider
     */
    public function testFilterGeneratedByRouteNeedsPermission($returnValue, $filterTest, $abort = false, $expectedResponse = null)
    {
        $this->filterTestExecution('routeNeedsPermission', 'can', $returnValue, $filterTest, $abort, $expectedResponse);
    }

    protected function filterTestExecution($methodTested, $mockedMethod, $returnValue, $filterTest, $abort, $expectedResponse)
    {
        // Mock Objects
        $app         = m::mock('Illuminate\Foundation\Application');
        $app->router = m::mock('Route');
        $entrust     = m::mock("Bbatsche\Entrust\Entrust[$mockedMethod]", [$app]);

        // Static values
        $route       = 'route';
        $methodValue = 'role-or-permission';
        $filterName  = $this->makeFilterName($route, [$methodValue]);

        $app->router->shouldReceive('when')->with($route, $filterName)->once();
        $app->router->shouldReceive('filter')->with($filterName, m::on($this->$filterTest))->once();

        if ($abort) {
            $app->shouldReceive('abort')->with(403)->andThrow('Exception', 'abort')->once();
        }

        $this->expectedResponse = $expectedResponse;

        $entrust->shouldReceive($mockedMethod)->with($methodValue, m::any(true, false))->andReturn($returnValue)->once();
        $entrust->$methodTested($route, $methodValue, $expectedResponse);
    }

    public function routeNeedsRoleOrPermissionFilterDataProvider()
    {
        return array(
            // Both role and permission pass, null is returned
            array(true,  true,  'nullFilterTest'),
            array(true,  true,  'nullFilterTest', true),
            // Role OR permission fail, require all is false, null is returned
            array(false, true,  'nullFilterTest'),
            array(true,  false, 'nullFilterTest'),
            // Role and/or permission fail, App::abort() is called
            array(false, true,  'abortFilterTest', true,  true),
            array(true,  false, 'abortFilterTest', true,  true),
            array(false, false, 'abortFilterTest', false, true),
            array(false, false, 'abortFilterTest', true,  true),
            // Role and/or permission fail, custom response is returned
            array(false, true,  'customResponseFilterTest', true,  false, new stdClass()),
            array(true,  false, 'customResponseFilterTest', true,  false, new stdClass()),
            array(false, false, 'customResponseFilterTest', false, false, new stdClass()),
            array(false, false, 'customResponseFilterTest', true,  false, new stdClass())
        );
    }

    /**
     * @dataProvider routeNeedsRoleOrPermissionFilterDataProvider
     */
    public function testFilterGeneratedByRouteNeedsRoleOrPermission(
        $roleIsValid, $permIsValid, $filterTest, $requireAll = false, $abort = false, $expectedResponse = null
    ) {
        $app         = m::mock('Illuminate\Foundation\Application');
        $app->router = m::mock('Route');
        $entrust     = m::mock('Bbatsche\Entrust\Entrust[hasRole, can]', [$app]);

        // Static values
        $route      = 'route';
        $roleName   = 'UserRole';
        $permName   = 'user-permission';
        $filterName = $this->makeFilterName($route, [$roleName], [$permName]);

        $app->router->shouldReceive('when')->with($route, $filterName)->once();
        $app->router->shouldReceive('filter')->with($filterName, m::on($this->$filterTest))->once();

        $entrust->shouldReceive('hasRole')->with($roleName, $requireAll)->andReturn($roleIsValid)->once();
        $entrust->shouldReceive('can')->with($permName, $requireAll)->andReturn($permIsValid)->once();

        if ($abort) {
            $app->shouldReceive('abort')->with(403)->andThrow('Exception', 'abort')->once();
        }

        $this->expectedResponse = $expectedResponse;

        $entrust->routeNeedsRoleOrPermission($route, $roleName, $permName, $expectedResponse, $requireAll);
    }

    protected function makeFilterName($route, array $roles, array $permissions = null)
    {
        if (is_null($permissions)) {
            return implode('_', $roles) . '_' . substr(md5($route), 0, 6);
        } else {
            return implode('_', $roles) . '_' . implode('_', $permissions) . '_' . substr(md5($route), 0, 6);
        }
    }
}
