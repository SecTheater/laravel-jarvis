<?php

namespace SecTheater\Jarvis\Tests\Unit\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Role\EloquentRole;
use Tests\TestCase;

class RoleRepositoryTest extends TestCase
{
    use DatabaseMigrations,DatabaseTransactions;
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
        factory(\App\User::class, 3)->create()->each(function ($u) {
            $this->actingAs(\UserRepository::first());
            $u->activation()->save(factory(EloquentActivation::class)->make());
            $role = \RoleRepository::findRoleBySlug('user');
            $u->roles()->attach($role);
        });
        $this->user = jarvis()->registerAndActivate(['first_name' => 'Mohammed',
            'last_name'                                           => 'Osama',
            'email'                                               => 'mohammedosama@sectheater.org',
            'password'                                            => bcrypt(123456789),
            'username'                                            => 'mohammed',
            'sec_answer'                                          => 'none',
            'sec_question'                                        => 'none',
            'location'                                            => 'Egypt',
            'dob'                                                 => '2017-6-6', ]);
    }

    /**
     * @test
     */
    public function find_role_by_slug()
    {
        $this->assertInstanceOf(EloquentRole::class, \RoleRepository::findRoleBySlug('user'));
    }

    /**
     *@test
     */
    public function find_role_by_id()
    {
        $this->assertInstanceOf(EloquentRole::class, \RoleRepository::findRoleById(1));
    }

    /**
     * @test
     */
    public function find_role_by_name()
    {
        $this->assertInstanceOf(EloquentRole::class, \RoleRepository::findRoleByName('Normal User'));
    }
}
