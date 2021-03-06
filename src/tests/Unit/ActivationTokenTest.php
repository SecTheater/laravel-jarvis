<?php

namespace SecTheater\Jarvis\Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Tests\TestCase;

class ActivationTokenTest extends TestCase
{
    use DatabaseMigrations,DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->seed('RolesSeeder');
        factory(\App\User::class)->create()->each(function ($u) {
            $this->actingAs(\UserRepository::first());
            $u->activation()->save(factory(EloquentActivation::class)->make());
            $role = \RoleRepository::findRoleBySlug('user');
            $u->roles()->attach($role);
        });
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testActivationTokenEquals()
    {
        $this->assertEquals(auth()->user()->activation->first()->token, \ActivationRepository::first()->token);
    }
}
