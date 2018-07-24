<?php

namespace SecTheater\Jarvis\Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Tests\TestCase;

class ChangePasswordTest extends TestCase
{
     use DatabaseMigrations,DatabaseTransactions;
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
    public function user_can_change_password()
    {
        $this->assertTrue(jarvis()->changePassword(123456789,12345678910,$this->user));
    }
}
