<?php

use Illuminate\Support\Facades\Config;

class EntrustPermissionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testRoles()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $permission = Mockery::mock('Bbatsche\Entrust\EntrustPermission')->makePartial();

        $belongsToMany = new stdClass();

        $app    = Mockery::mock('app')->shouldReceive('instance')->getMock();
        $config = Mockery::mock('config');

        Config::setFacadeApplication($app);
        Config::swap($config);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $permission->shouldReceive('belongsToMany')->with('RoleModelName', 'permission_role_table_name')
            ->andReturn($belongsToMany)->once();

        Config::shouldReceive('get')->with('entrust::role')
            ->andReturn('RoleModelName')->once();
        Config::shouldReceive('get')->with('entrust::permission_role_table')
            ->andReturn('permission_role_table_name')->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($belongsToMany, $permission->roles());
    }
}
