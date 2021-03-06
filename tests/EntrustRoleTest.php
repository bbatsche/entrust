<?php

use Illuminate\Database\Eloquent\Collection;
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
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $perm1 = Mockery::mock('Bbatsche\Entrust\EntrustPermission')->makePartial();
        $perm2 = Mockery::mock('Bbatsche\Entrust\EntrustPermission')->makePartial();

        $perm1->name = 'user-perm1';
        $perm2->name = 'user-perm2';

        $this->role->perms = new Collection([$perm1, $perm2]);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertTrue($this->role->can('user-perm1'));
        $this->assertFalse($this->role->can('nonuser-perm'));
    }

    public function testCanAny()
    {
        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $this->role->shouldReceive('can')->with('user-perm1')->andReturn(true)->twice();
        $this->role->shouldReceive('can')->with('user-perm2')->andReturn(true)->once();
        $this->role->shouldReceive('can')->with('nonuser-perm1')->andReturn(false)->twice();
        $this->role->shouldReceive('can')->with('nonuser-perm2')->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        // Role has all perms
        $failedPerms = array();
        $this->assertTrue($this->role->canAny(['user-perm1', 'user-perm2'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertEmpty($failedPerms);

        // Role has mixed perms
        $failedPerms = array();
        $this->assertTrue($this->role->canAny(['user-perm1', 'nonuser-perm1'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertContains('nonuser-perm1', $failedPerms);
        $this->assertNotContains('user-perm1', $failedPerms);

        // Role has no perms
        $failedPerms = array();
        $this->assertFalse($this->role->canAny(['nonuser-perm1', 'nonuser-perm2'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertContains('nonuser-perm1', $failedPerms);
        $this->assertContains('nonuser-perm2', $failedPerms);
    }

    public function testCanAll()
    {
        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $this->role->shouldReceive('can')->with('user-perm1')->andReturn(true)->twice();
        $this->role->shouldReceive('can')->with('user-perm2')->andReturn(true)->once();
        $this->role->shouldReceive('can')->with('nonuser-perm1')->andReturn(false)->twice();
        $this->role->shouldReceive('can')->with('nonuser-perm2')->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        // Role has all perms
        $failedPerms = array();
        $this->assertTrue($this->role->canAll(['user-perm1', 'user-perm2'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertEmpty($failedPerms);

        // Role has mixed perms
        $failedPerms = array();
        $this->assertFalse($this->role->canAll(['user-perm1', 'nonuser-perm1'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertContains('nonuser-perm1', $failedPerms);
        $this->assertNotContains('user-perm1', $failedPerms);

        // Role has no perms
        $failedPerms = array();
        $this->assertFalse($this->role->canAll(['nonuser-perm1', 'nonuser-perm2'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertContains('nonuser-perm1', $failedPerms);
        $this->assertContains('nonuser-perm2', $failedPerms);
    }

    public function testSavePermissions()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $newPerms = array('new-perm');

        $relation = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $this->role->shouldReceive('perms')->andReturn($relation)->twice();

        $relation->shouldReceive('sync')->with($newPerms)->once()->ordered();
        $relation->shouldReceive('detach')->once()->ordered();

        /*
        |------------------------------------------------------------
        | Execute
        |------------------------------------------------------------
        */

        $this->role->savePermissions($newPerms);
        $this->role->savePermissions([]);
    }

    public function testAttachPermission()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $relation = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');

        $permArr = array('id' => '2');
        $permObj = Mockery::mock('Bbatsche\Entrust\EntrustPermission')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $this->role->shouldReceive('perms')->andReturn($relation)->times(3);

        $permObj->shouldReceive('getKey')->andReturn('3')->once();

        $relation->shouldReceive('attach')->with('1')->once()->ordered();
        $relation->shouldReceive('attach')->with('2')->once()->ordered();
        $relation->shouldReceive('attach')->with('3')->once()->ordered();

        /*
        |------------------------------------------------------------
        | Execute
        |------------------------------------------------------------
        */

        $this->role->attachPermission('1');
        $this->role->attachPermission($permArr);
        $this->role->attachPermission($permObj);
    }

    public function testDetachPermission()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $relation = Mockery::mock('Illuminate\Database\Eloquent\Relations\BelongsToMany');

        $permArr = array('id' => '2');
        $permObj = Mockery::mock('Bbatsche\Entrust\EntrustPermission')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $this->role->shouldReceive('perms')->andReturn($relation)->times(3);

        $permObj->shouldReceive('getKey')->andReturn('3')->once();

        $relation->shouldReceive('detach')->with('1')->once()->ordered();
        $relation->shouldReceive('detach')->with('2')->once()->ordered();
        $relation->shouldReceive('detach')->with('3')->once()->ordered();

        /*
        |------------------------------------------------------------
        | Execute
        |------------------------------------------------------------
        */

        $this->role->detachPermission('1');
        $this->role->detachPermission($permArr);
        $this->role->detachPermission($permObj);
    }

    public function testAttachPermissions()
    {
        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $this->role->shouldReceive('attachPermission')->with('perm1')->once();
        $this->role->shouldReceive('attachPermission')->with('perm2')->once();

        /*
        |------------------------------------------------------------
        | Execute
        |------------------------------------------------------------
        */

        $this->role->attachPermissions(['perm1', 'perm2']);
    }

    public function testDetachPermissions()
    {
        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $this->role->shouldReceive('detachPermission')->with('perm1')->once();
        $this->role->shouldReceive('detachPermission')->with('perm2')->once();

        /*
        |------------------------------------------------------------
        | Execute
        |------------------------------------------------------------
        */

        $this->role->detachPermissions(['perm1', 'perm2']);
    }
}
