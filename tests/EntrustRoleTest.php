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

        $this->role->shouldReceive('belongsToMany')->with('users_table_name', 'roles_table_name')
            ->andReturn($belongsToMany)->once();

        Config::shouldReceive('get')->once()->with('auth.model')->andReturn('users_table_name');
        Config::shouldReceive('get')->once()->with('entrust::role_user_table')->andReturn('roles_table_name');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertSame($belongsToMany, $this->role->users());
    }

    public function testPerms()
    {
        $this->markTestIncomplete();
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
