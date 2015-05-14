<?php

use Bbatsche\Entrust\Contracts\EntrustUserInterface;
use Bbatsche\Entrust\Traits\EntrustUserTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Mockery as m;

class EntrustUserTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRoles()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $belongsToMany = new stdClass();
        $user = m::mock('HasRoleUser')->makePartial();

        $app = m::mock('app')->shouldReceive('instance')->getMock();
        $config = m::mock('config');
        Config::setFacadeApplication($app);
        Config::swap($config);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('belongsToMany')
            ->with('role_table_name', 'assigned_roles_table_name', 'user_id', 'role_id')
            ->andReturn($belongsToMany)
            ->once();

        Config::shouldReceive('get')->once()->with('entrust::role')
            ->andReturn('role_table_name');
        Config::shouldReceive('get')->once()->with('entrust::role_user_table')
            ->andReturn('assigned_roles_table_name');

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertSame($belongsToMany, $user->roles());
    }

    public function testHasRole()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user->shouldReceive('isAll')->with(['AllRole1', 'AllRole2'])->andReturn(true)->once();
        $user->shouldReceive('isAll')->with(['AllRole3', 'AllRole4'])->andReturn(false)->once();
        $user->shouldReceive('isAny')->with(['AnyRole1', 'AnyRole2'])->andReturn(true)->once();
        $user->shouldReceive('isAny')->with(['AnyRole3', 'AnyRole4'])->andReturn(false)->once();
        $user->shouldReceive('is')->with('SingleRole1')->andReturn(true)->once();
        $user->shouldReceive('is')->with('SingleRole2')->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertTrue($user->hasRole(['AllRole1', 'AllRole2'], true));
        $this->assertFalse($user->hasRole(['AllRole3', 'AllRole4'], true));
        $this->assertTrue($user->hasRole(['AnyRole1', 'AnyRole2']));
        $this->assertFalse($user->hasRole(['AnyRole3', 'AnyRole4']));
        $this->assertTrue($user->hasRole('SingleRole1'));
        $this->assertFalse($user->hasRole('SingleRole2'));
    }

    public function testIs()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $roleA = $this->mockRole('RoleA');
        $roleB = $this->mockRole('RoleB');

        $user = new HasRoleUser();
        $user->roles = new Collection([$roleA, $roleB]);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertTrue($user->is('RoleB'));
        $this->assertFalse($user->is('RoleC'));
    }

    public function testIsAny()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user->shouldReceive('is')->with('RoleA')->andReturn(true)->twice();
        $user->shouldReceive('is')->with('RoleB')->andReturn(true)->once();
        $user->shouldReceive('is')->with('RoleC')->andReturn(false)->twice();
        $user->shouldReceive('is')->with('RoleD')->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $failedRoles = array();
        $this->assertTrue($user->isAny(['RoleA', 'RoleB'], $failedRoles));
        $this->assertInternalType('array', $failedRoles);
        $this->assertEmpty($failedRoles);

        $failedRoles = array();
        $this->assertTrue($user->isAny(['RoleA', 'RoleC'], $failedRoles));
        $this->assertInternalType('array', $failedRoles);
        $this->assertContains('RoleC', $failedRoles);
        $this->assertNotContains('RoleA', $failedRoles);

        $failedRoles = array();
        $this->assertFalse($user->isAny(['RoleC', 'RoleD'], $failedRoles));
        $this->assertInternalType('array', $failedRoles);
        $this->assertContains('RoleC', $failedRoles);
        $this->assertContains('RoleD', $failedRoles);
    }

    public function testIsAll()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user->shouldReceive('is')->with('RoleA')->andReturn(true)->twice();
        $user->shouldReceive('is')->with('RoleB')->andReturn(true)->once();
        $user->shouldReceive('is')->with('RoleC')->andReturn(false)->twice();
        $user->shouldReceive('is')->with('RoleD')->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $failedRoles = array();
        $this->assertTrue($user->isAll(['RoleA', 'RoleB'], $failedRoles));
        $this->assertInternalType('array', $failedRoles);
        $this->assertEmpty($failedRoles);

        $failedRoles = array();
        $this->assertFalse($user->isAll(['RoleA', 'RoleC'], $failedRoles));
        $this->assertInternalType('array', $failedRoles);
        $this->assertContains('RoleC', $failedRoles);
        $this->assertNotContains('RoleA', $failedRoles);

        $failedRoles = array();
        $this->assertFalse($user->isAll(['RoleC', 'RoleD'], $failedRoles));
        $this->assertInternalType('array', $failedRoles);
        $this->assertContains('RoleC', $failedRoles);
        $this->assertContains('RoleD', $failedRoles);
    }

    public function testCan()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $roleA = $this->mockRole('RoleA');
        $roleB = $this->mockRole('RoleB');

        $user = m::mock('HasRoleUser')->makePartial();
        $user->roles = new Collection([$roleA, $roleB]);

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user->shouldReceive('canAny')->with(['AnyPerm1', 'AnyPerm2'])->andReturn(true)->once();
        $user->shouldReceive('canAny')->with(['AnyPerm3', 'AnyPerm4'])->andReturn(false)->once();
        $user->shouldReceive('canAll')->with(['AllPerm1', 'AllPerm2'])->andReturn(true)->once();
        $user->shouldReceive('canAll')->with(['AllPerm3', 'AllPerm4'])->andReturn(false)->once();

        $roleA->shouldReceive('can')->with('perm1')->andReturn(true)->once();
        $roleB->shouldReceive('can')->with('perm1')->andReturn(true)->once();
        $roleB->shouldReceive('can')->with('perm2')->andReturn(true)->once();
        $roleA->shouldReceive('can')->with('perm2')->andReturn(false)->once();
        $roleA->shouldReceive('can')->with('perm3')->andReturn(false)->once();
        $roleB->shouldReceive('can')->with('perm3')->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertTrue($user->can(['AnyPerm1', 'AnyPerm2']));
        $this->assertFalse($user->can(['AnyPerm3', 'AnyPerm4']));
        $this->assertTrue($user->can(['AllPerm1', 'AllPerm2'], true));
        $this->assertFalse($user->can(['AllPerm3', 'AllPerm4'], true));

        $this->assertTrue($user->can('perm1'));
        $this->assertTrue($user->can('perm2'));
        $this->assertFalse($user->can('perm3'));
    }

    public function testCanAny()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user->shouldReceive('can')->with('perm1')->andReturn(true)->twice();
        $user->shouldReceive('can')->with('perm2')->andReturn(true)->once();
        $user->shouldReceive('can')->with('perm3')->andReturn(false)->twice();
        $user->shouldReceive('can')->with('perm4')->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $failedPerms = array();
        $this->assertTrue($user->canAny(['perm1', 'perm2'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertEmpty($failedPerms);

        $failedPerms = array();
        $this->assertTrue($user->canAny(['perm1', 'perm3'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertContains('perm3', $failedPerms);
        $this->assertNotContains('perm1', $failedPerms);

        $failedPerms = array();
        $this->assertFalse($user->canAny(['perm3', 'perm4'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertContains('perm3', $failedPerms);
        $this->assertContains('perm4', $failedPerms);
    }

    public function testCanAll()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $user->shouldReceive('can')->with('perm1')->andReturn(true)->twice();
        $user->shouldReceive('can')->with('perm2')->andReturn(true)->once();
        $user->shouldReceive('can')->with('perm3')->andReturn(false)->twice();
        $user->shouldReceive('can')->with('perm4')->andReturn(false)->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $failedPerms = array();
        $this->assertTrue($user->canAll(['perm1', 'perm2'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertEmpty($failedPerms);

        $failedPerms = array();
        $this->assertFalse($user->canAll(['perm1', 'perm3'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertContains('perm3', $failedPerms);
        $this->assertNotContains('perm1', $failedPerms);

        $failedPerms = array();
        $this->assertFalse($user->canAll(['perm3', 'perm4'], $failedPerms));
        $this->assertInternalType('array', $failedPerms);
        $this->assertContains('perm3', $failedPerms);
        $this->assertContains('perm4', $failedPerms);
    }

    public function testAbilityShouldReturnBoolean()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $userPermNameA = 'user_can_a';
        $userPermNameB = 'user_can_b';
        $userPermNameC = 'user_can_c';
        $nonUserPermNameA = 'user_cannot_a';
        $nonUserPermNameB = 'user_cannot_b';
        $userRoleNameA = 'UserRoleA';
        $userRoleNameB = 'UserRoleB';
        $nonUserRoleNameA = 'NonUserRoleA';
        $nonUserRoleNameB = 'NonUserRoleB';

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('hasRole')
            ->with(m::anyOf($userRoleNameA, $userRoleNameB))
            ->andReturn(true);
        $user->shouldReceive('hasRole')
            ->with(m::anyOf($nonUserRoleNameA, $nonUserRoleNameB))
            ->andReturn(false);
        $user->shouldReceive('can')
            ->with(m::anyOf($userPermNameA, $userPermNameB, $userPermNameC))
            ->andReturn(true);
        $user->shouldReceive('can')
            ->with(m::anyOf($nonUserPermNameA, $nonUserPermNameB))
            ->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        // Case: User has everything.
        $this->assertTrue(
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB]
            )
        );
        $this->assertTrue(
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['validate_all' => true]
            )
        );

        // Case: User lacks a role.
        $this->assertTrue(
            $user->ability(
                [$nonUserRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB]
            )
        );
        $this->assertFalse(
            $user->ability(
                [$nonUserRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['validate_all' => true]
            )
        );

        // Case: User lacks a permission.
        $this->assertTrue(
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$nonUserPermNameA, $userPermNameB]
            )
        );
        $this->assertFalse(
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$nonUserPermNameA, $userPermNameB],
                ['validate_all' => true]
            )
        );

        // Case: User lacks everything.
        $this->assertFalse(
            $user->ability(
                [$nonUserRoleNameA, $nonUserRoleNameB],
                [$nonUserPermNameA, $nonUserPermNameB]
            )
        );
        $this->assertFalse(
            $user->ability(
                [$nonUserRoleNameA, $nonUserRoleNameB],
                [$nonUserPermNameA, $nonUserPermNameB],
                ['validate_all' => true]
            )
        );
    }

    public function testAbilityShouldReturnArray()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $userPermNameA = 'user_can_a';
        $userPermNameB = 'user_can_b';
        $userPermNameC = 'user_can_c';
        $nonUserPermNameA = 'user_cannot_a';
        $nonUserPermNameB = 'user_cannot_b';
        $userRoleNameA = 'UserRoleA';
        $userRoleNameB = 'UserRoleB';
        $nonUserRoleNameA = 'NonUserRoleA';
        $nonUserRoleNameB = 'NonUserRoleB';

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('hasRole')
            ->with(m::anyOf($userRoleNameA, $userRoleNameB))
            ->andReturn(true);
        $user->shouldReceive('hasRole')
            ->with(m::anyOf($nonUserRoleNameA, $nonUserRoleNameB))
            ->andReturn(false);
        $user->shouldReceive('can')
            ->with(m::anyOf($userPermNameA, $userPermNameB, $userPermNameC))
            ->andReturn(true);
        $user->shouldReceive('can')
            ->with(m::anyOf($nonUserPermNameA, $nonUserPermNameB))
            ->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        // Case: User has everything.
        $this->assertSame(
            [
                'roles'       => [$userRoleNameA => true, $userRoleNameB => true],
                'permissions' => [$userPermNameA => true, $userPermNameB => true]
            ],
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['return_type' => 'array']
            )
        );
        $this->assertSame(
            [
                'roles'       => [$userRoleNameA => true, $userRoleNameB => true],
                'permissions' => [$userPermNameA => true, $userPermNameB => true]
            ],
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['validate_all' => true, 'return_type' => 'array']
            )
        );


        // Case: User lacks a role.
        $this->assertSame(
            [
                'roles'       => [$nonUserRoleNameA => false, $userRoleNameB => true],
                'permissions' => [$userPermNameA    => true, $userPermNameB  => true]
            ],
            $user->ability(
                [$nonUserRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['return_type' => 'array']
            )
        );
        $this->assertSame(
            [
                'roles'       => [$nonUserRoleNameA => false, $userRoleNameB => true],
                'permissions' => [$userPermNameA    => true, $userPermNameB  => true]
            ],
            $user->ability(
                [$nonUserRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['validate_all' => true, 'return_type' => 'array']
            )
        );


        // Case: User lacks a permission.
        $this->assertSame(
            [
                'roles'       => [$userRoleNameA    => true, $userRoleNameB  => true],
                'permissions' => [$nonUserPermNameA => false, $userPermNameB => true]
            ],
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$nonUserPermNameA, $userPermNameB],
                ['return_type' => 'array']
            )
        );
        $this->assertSame(
            [
                'roles'       => [$userRoleNameA    => true, $userRoleNameB  => true],
                'permissions' => [$nonUserPermNameA => false, $userPermNameB => true]
            ],
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$nonUserPermNameA, $userPermNameB],
                ['validate_all' => true, 'return_type' => 'array']
            )
        );


        // Case: User lacks everything.
        $this->assertSame(
            [
                'roles'       => [$nonUserRoleNameA => false, $nonUserRoleNameB => false],
                'permissions' => [$nonUserPermNameA => false, $nonUserPermNameB => false]
            ],
            $user->ability(
                [$nonUserRoleNameA, $nonUserRoleNameB],
                [$nonUserPermNameA, $nonUserPermNameB],
                ['return_type' => 'array']
            )
        );
        $this->assertSame(
            [
                'roles'       => [$nonUserRoleNameA => false, $nonUserRoleNameB => false],
                'permissions' => [$nonUserPermNameA => false, $nonUserPermNameB => false]
            ],
            $user->ability(
                [$nonUserRoleNameA, $nonUserRoleNameB],
                [$nonUserPermNameA, $nonUserPermNameB],
                ['validate_all' => true, 'return_type' => 'array']
            )
        );
    }

    public function testAbilityShouldReturnBoth()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $userPermNameA = 'user_can_a';
        $userPermNameB = 'user_can_b';
        $userPermNameC = 'user_can_c';
        $nonUserPermNameA = 'user_cannot_a';
        $nonUserPermNameB = 'user_cannot_b';
        $userRoleNameA = 'UserRoleA';
        $userRoleNameB = 'UserRoleB';
        $nonUserRoleNameA = 'NonUserRoleA';
        $nonUserRoleNameB = 'NonUserRoleB';

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('hasRole')
            ->with(m::anyOf($userRoleNameA, $userRoleNameB))
            ->andReturn(true);
        $user->shouldReceive('hasRole')
            ->with(m::anyOf($nonUserRoleNameA, $nonUserRoleNameB))
            ->andReturn(false);
        $user->shouldReceive('can')
            ->with(m::anyOf($userPermNameA, $userPermNameB, $userPermNameC))
            ->andReturn(true);
        $user->shouldReceive('can')
            ->with(m::anyOf($nonUserPermNameA, $nonUserPermNameB))
            ->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        // Case: User has everything.
        $this->assertSame(
            [
                true,
                [
                    'roles'       => [$userRoleNameA => true, $userRoleNameB => true],
                    'permissions' => [$userPermNameA => true, $userPermNameB => true]
                ]
            ],
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['return_type' => 'both']
            )
        );
        $this->assertSame(
            [
                true,
                [
                    'roles'       => [$userRoleNameA => true, $userRoleNameB => true],
                    'permissions' => [$userPermNameA => true, $userPermNameB => true]
                ]
            ],
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['validate_all' => true, 'return_type' => 'both']
            )
        );


        // Case: User lacks a role.
        $this->assertSame(
            [
                true,
                [
                    'roles'       => [$nonUserRoleNameA => false, $userRoleNameB => true],
                    'permissions' => [$userPermNameA    => true, $userPermNameB  => true]
                ]
            ],
            $user->ability(
                [$nonUserRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['return_type' => 'both']
            )
        );
        $this->assertSame(
            [
                false,
                [
                    'roles'       => [$nonUserRoleNameA => false, $userRoleNameB => true],
                    'permissions' => [$userPermNameA    => true, $userPermNameB  => true]
                ]
            ],
            $user->ability(
                [$nonUserRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['validate_all' => true, 'return_type' => 'both']
            )
        );


        // Case: User lacks a permission.
        $this->assertSame(
            [
                true,
                [
                    'roles'       => [$userRoleNameA    => true, $userRoleNameB  => true],
                    'permissions' => [$nonUserPermNameA => false, $userPermNameB => true]
                ]
            ],
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$nonUserPermNameA, $userPermNameB],
                ['return_type' => 'both']
            )
        );
        $this->assertSame(
            [
                false,
                [
                    'roles'       => [$userRoleNameA    => true, $userRoleNameB  => true],
                    'permissions' => [$nonUserPermNameA => false, $userPermNameB => true]
                ]
            ],
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$nonUserPermNameA, $userPermNameB],
                ['validate_all' => true, 'return_type' => 'both']
            )
        );


        // Case: User lacks everything.
        $this->assertSame(
            [
                false,
                [
                    'roles'       => [$nonUserRoleNameA => false, $nonUserRoleNameB => false],
                    'permissions' => [$nonUserPermNameA => false, $nonUserPermNameB => false]
                ]
            ],
            $user->ability(
                [$nonUserRoleNameA, $nonUserRoleNameB],
                [$nonUserPermNameA, $nonUserPermNameB],
                ['return_type' => 'both']
            )
        );
        $this->assertSame(
            [
                false,
                [
                    'roles'       => [$nonUserRoleNameA => false, $nonUserRoleNameB => false],
                    'permissions' => [$nonUserPermNameA => false, $nonUserPermNameB => false]
                ]
            ],
            $user->ability(
                [$nonUserRoleNameA, $nonUserRoleNameB],
                [$nonUserPermNameA, $nonUserPermNameB],
                ['validate_all' => true, 'return_type' => 'both']
            )
        );
    }

    public function testAbilityShouldAcceptStrings()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('hasRole')
            ->with(m::anyOf('UserRoleA', 'UserRoleB'))
            ->andReturn(true);
        $user->shouldReceive('hasRole')
            ->with('NonUserRoleB')
            ->andReturn(false);
        $user->shouldReceive('can')
            ->with(m::anyOf('user_can_a', 'user_can_b', 'user_can_c'))
            ->andReturn(true);
        $user->shouldReceive('can')
            ->with('user_cannot_b')
            ->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertSame(
            $user->ability(
                ['UserRoleA', 'NonUserRoleB'],
                ['user_can_a', 'user_cannot_b'],
                ['return_type' => 'both']
            ),
            $user->ability(
                'UserRoleA,NonUserRoleB',
                'user_can_a,user_cannot_b',
                ['return_type' => 'both']
            )
        );
    }

    public function testAbilityDefaultOptions()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $userPermNameA = 'user_can_a';
        $userPermNameB = 'user_can_b';
        $userPermNameC = 'user_can_c';
        $nonUserPermNameA = 'user_cannot_a';
        $nonUserPermNameB = 'user_cannot_b';
        $userRoleNameA = 'UserRoleA';
        $userRoleNameB = 'UserRoleB';
        $nonUserRoleNameA = 'NonUserRoleA';
        $nonUserRoleNameB = 'NonUserRoleB';

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('hasRole')
            ->with(m::anyOf($userRoleNameA, $userRoleNameB))
            ->andReturn(true);
        $user->shouldReceive('hasRole')
            ->with(m::anyOf($nonUserRoleNameA, $nonUserRoleNameB))
            ->andReturn(false);
        $user->shouldReceive('can')
            ->with(m::anyOf($userPermNameA, $userPermNameB, $userPermNameC))
            ->andReturn(true);
        $user->shouldReceive('can')
            ->with(m::anyOf($nonUserPermNameA, $nonUserPermNameB))
            ->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        // Case: User has everything.
        $this->assertSame(
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB]
            ),
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['validate_all' => false, 'return_type' => 'boolean']
            )
        );


        // Case: User lacks a role.
        $this->assertSame(
            $user->ability(
                [$nonUserRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB]
            ),
            $user->ability(
                [$nonUserRoleNameA, $userRoleNameB],
                [$userPermNameA, $userPermNameB],
                ['validate_all' => false, 'return_type' => 'boolean']
            )
        );


        // Case: User lacks a permission.
        $this->assertSame(
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$nonUserPermNameA, $userPermNameB]
            ),
            $user->ability(
                [$userRoleNameA, $userRoleNameB],
                [$nonUserPermNameA, $userPermNameB],
                ['validate_all' => false, 'return_type' => 'boolean']
            )
        );


        // Case: User lacks everything.
        $this->assertSame(
            $user->ability(
                [$nonUserRoleNameA, $nonUserRoleNameB],
                [$nonUserPermNameA, $nonUserPermNameB]
            ),
            $user->ability(
                [$nonUserRoleNameA, $nonUserRoleNameB],
                [$nonUserPermNameA, $nonUserPermNameB],
                ['validate_all' => false, 'return_type' => 'boolean']
            )
        );
    }

    public function testAbilityShouldThrowInvalidArgumentException()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $user = m::mock('HasRoleUser')->makePartial();

        function isExceptionThrown(
            HasRoleUser $user,
            array $roles,
            array $perms,
            array $options
        ) {
            $isExceptionThrown = false;

            try {
                $user->ability($roles, $perms, $options);
            } catch (InvalidArgumentException $e) {
                $isExceptionThrown = true;
            }

            return $isExceptionThrown;
        }

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('hasRole')
            ->times(3);
        $user->shouldReceive('can')
            ->times(3);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $this->assertFalse(isExceptionThrown($user, ['RoleA'], ['manage_a'], ['return_type' => 'boolean']));
        $this->assertFalse(isExceptionThrown($user, ['RoleA'], ['manage_a'], ['return_type' => 'array']));
        $this->assertFalse(isExceptionThrown($user, ['RoleA'], ['manage_a'], ['return_type' => 'both']));
        $this->assertTrue(isExceptionThrown($user, ['RoleA'], ['manage_a'], ['return_type' => 'potato']));
    }

    public function testAttachRole()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $roleObject = m::mock('Role');
        $roleArray = ['id' => 2];

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $roleObject->shouldReceive('getKey')
            ->andReturn(1);

        $user->shouldReceive('roles')
            ->andReturn($user);
        $user->shouldReceive('attach')
            ->with(1)
            ->once()->ordered();
        $user->shouldReceive('attach')
            ->with(2)
            ->once()->ordered();
        $user->shouldReceive('attach')
            ->with(3)
            ->once()->ordered();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $user->attachRole($roleObject);
        $user->attachRole($roleArray);
        $user->attachRole(3);
    }

    public function testDetachRole()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $roleObject = m::mock('Role');
        $roleArray = ['id' => 2];

        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $roleObject->shouldReceive('getKey')
            ->andReturn(1);

        $user->shouldReceive('roles')
            ->andReturn($user);
        $user->shouldReceive('detach')
            ->with(1)
            ->once()->ordered();
        $user->shouldReceive('detach')
            ->with(2)
            ->once()->ordered();
        $user->shouldReceive('detach')
            ->with(3)
            ->once()->ordered();


        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $user->detachRole($roleObject);
        $user->detachRole($roleArray);
        $user->detachRole(3);
    }

    public function testAttachRoles()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('attachRole')
            ->with(1)
            ->once()->ordered();
        $user->shouldReceive('attachRole')
            ->with(2)
            ->once()->ordered();
        $user->shouldReceive('attachRole')
            ->with(3)
            ->once()->ordered();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $user->attachRoles([1, 2, 3]);
    }

    public function testDetachRoles()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */
        $user = m::mock('HasRoleUser')->makePartial();

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */
        $user->shouldReceive('detachRole')
            ->with(1)
            ->once()->ordered();
        $user->shouldReceive('detachRole')
            ->with(2)
            ->once()->ordered();
        $user->shouldReceive('detachRole')
            ->with(3)
            ->once()->ordered();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $user->detachRoles([1, 2, 3]);
    }

    protected function mockPermission($permName)
    {
        $permMock = m::mock('Permission');
        $permMock->name = $permName;
        $permMock->display_name = ucwords(str_replace('_', ' ', $permName));

        return $permMock;
    }

    protected function mockRole($roleName)
    {
        $roleMock = m::mock('Role');
        $roleMock->name = $roleName;
        $roleMock->perms = [];
        $roleMock->permissions = [];

        return $roleMock;
    }
}

class HasRoleUser implements EntrustUserInterface
{
    use EntrustUserTrait;

    public $roles;

    public function belongsToMany($role, $assignedRolesTable)
    {

    }
}
