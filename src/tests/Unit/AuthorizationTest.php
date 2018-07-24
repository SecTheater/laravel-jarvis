<?php

namespace SecTheater\Jarvis\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Exceptions\InsufficientPermissionsException;
use SecTheater\Jarvis\Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    protected $user;
    public function setUp()
    {
        parent::setUp();
        $this->seed('RolesSeeder');
        factory(\App\User::class)->create()->each(function($u){
            $u->activation()->save(factory(EloquentActivation::class)->make());
            $role = \RoleRepository::findRoleBySlug('user');
            $u->roles()->attach($role);
        });
        $this->user = \UserRepository::first();
    }

    /**
     * A basic test example.
     *@test
     * @return void
     */
    public function user_is_in_role()
    {
        $this->assertTrue($this->user->inRole('user'));
    }
    /**
     * @test
     */
    public function user_has_role()
    {
        $this->assertTrue($this->user->hasRole('view-comment'));
    }
    /**
     * @test
     */
    public function user_has_any_role()
    {
        $this->assertTrue($this->user->hasAnyRole(['view-comment','view-post']));
        $this->assertTrue($this->user->hasAnyRole('create-comment'));
        $this->assertNotTrue($this->user->hasAnyRole('create-profile'));
    }
    /**
     *@test
     */
    public function user_has_all_role()
    {
        $this->assertTrue($this->user->hasAllRole(['view-comment','view-post']));
        $this->assertNotTrue($this->user->hasAllRole(['view-comment','view-profile']));
    }
    /**
     * @test
     */
    public function add_permission_to_user()
    {
        $this->assertTrue($this->user->addPermission('create-profile'));
        $this->assertTrue($this->user->addPermission('create-project',false));
        $this->assertTrue($this->user->addPermission(['create-user' => true,'edit-other-users' => false]));
    }
    /**
     * @test
     */
    public function update_permission_to_user()
    {
        $this->user->addPermission('create-profile');
        $this->assertTrue($this->user->updatePermission('create-profile',false));
        $this->assertTrue($this->user->updatePermission('create-tasks',false,true));
        $this->assertNotTrue($this->user->updatePermission('create-something-doesnot-exist'));
    }
    /**
     *@test
     */
    public function remove_permission_from_user()
    {
        $this->user->addPermission(['create-user' => true,'edit-other-users' => false]);
        $this->assertTrue($this->user->removePermission('create-user','edit-other-users'));
    }
    /**
     * @test
     */
    public function upgrade_user()
    {
        $this->assertTrue(jarvis()->upgradeUser(['create-profile' => true,'create-post' => false],$this->user));
        $this->assertTrue(jarvis()->upgradeUser('create-profile',$this->user));

    }
    /**
     *@test
     */
    public function downgrade_user()
    {
        $this->assertTrue(jarvis()->downgradeUser(['create-profile' => true,'create-post' => false],$this->user));
        $this->assertTrue(jarvis()->downgradeUser('create-profile',$this->user));
    }
}
