<?php

use Illuminate\Support\Facades\Config;

class EntrustRoletest extends PHPUnit_Framework_TestCase
{
    protected $role;

    public function setUp()
    {
        $this->role = Mockery::mock('Bbatsche\Entrust\EntrustRole')->makePartial();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function testUsers()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
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

        $this->role->shouldReceive('belongsToMany')->with('UserModelName', 'role_user_table_name')
            ->andReturn($belongsToMany)->once();

        Config::shouldReceive('get')->with('auth.model')->andReturn('UserModelName')->once();
        Config::shouldReceive('get')->with('entrust::role_user_table')->andReturn('role_user_table_name')->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($belongsToMany, $this->role->users());
    }

    public function testPerms()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
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

        $this->role->shouldReceive('belongsToMany')->with('PermissionModelName', 'permission_role_table_name')
            ->andReturn($belongsToMany)->once();

        Config::shouldReceive('get')->with('entrust::permission')
            ->andReturn('PermissionModelName')->once();
        Config::shouldReceive('get')->with('entrust::permission_role_table')
            ->andReturn('permission_role_table_name')->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertSame($belongsToMany, $this->role->perms());
    }

    public function testCan()
    {
        $this->markTestIncomplete();
    }

    public function testCanAny()
    {
        $this->markTestIncomplete();
    }

    public function testCanAll()
    {
        $this->markTestIncomplete();
    }

    public function testSavePermissions()
    {
        $this->markTestIncomplete();
    }

    public function testAttachPermission()
    {
        $this->markTestIncomplete();
    }

    public function testDetachPermission()
    {
        $this->markTestIncomplete();
    }

    public function testAttachPermissions()
    {
        $this->markTestIncomplete();
    }

    public function testDetachPermissions()
    {
        $this->markTestIncomplete();
    }
}
