<?php

namespace SecTheater\Jarvis\Tests\Unit\Repositories;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use SecTheater\Jarvis\Activation\ActivationException;
use SecTheater\Jarvis\Activation\EloquentActivation;
use Tests\TestCase;

class ActivationRepositoryTest extends TestCase
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
        factory(\App\User::class,3)->create()->each(function($u){
            $this->actingAs(\UserRepository::first());
            $u->activation()->save(factory(EloquentActivation::class)->make());
             $u->posts()->saveMany(factory(\App\Post::class,3)->make());
             $u->comments()->saveMany(factory(\App\Comment::class,30)->make());
             $u->replies()->saveMany(factory(\App\Reply::class,30)->make());
            $role = \RoleRepository::findRoleBySlug('user');
            $u->roles()->attach($role);
        });
        $this->user = jarvis()->registerAndActivate(['first_name' => 'Mohammed',
            'last_name' => 'Osama',
            'email' => 'mohammedosama@sectheater.org',
            'password' => bcrypt(123456789),
            'username' => 'mohammed',
            'sec_answer' => 'none',
            'sec_question' => 'none',
            'location' => 'Egypt',
            'dob' => '2017-6-6']);

    }
    /**
     *@test
     */
    public function user_has_token()
    {
       $this->assertInstanceOf(EloquentActivation::class,\ActivationRepository::hasToken(auth()->user()));
       $this->assertNotTrue(EloquentActivation::class,\ActivationRepository::hasToken($this->user));
    }
    /**
     * @test
     */
    public function has_or_create_token()
    {
       $this->assertInstanceOf(EloquentActivation::class,\ActivationRepository::hasOrCreateToken(auth()->user()));
       $this->assertInstanceOf(EloquentActivation::class,\ActivationRepository::hasOrCreateToken($this->user));
    }
    /**
     *@test
     */
    public function user_has_completed_his_activation()
    {
        $user = jarvis()->registerAndActivate(['first_name' => 'Mohammed',
            'last_name' => 'Osama',
            'email' => 'mohammedosama@sectdheater.org',
            'password' => bcrypt(123456789),
            'username' => 'mohammaed',
            'sec_answer' => 'none',
            'sec_question' => 'none',
            'location' => 'Egypt',
            'dob' => '2017-6-6']);
       $this->assertNotTrue(\ActivationRepository::completed(auth()->user()));
       $this->assertTrue(\ActivationRepository::completed($user));
    }
    /**
     * @test
     */
    public function complete_user_activation()
    {
        $this->assertTrue(\ActivationRepository::complete(auth()->user(),auth()->user()->activation->first()->token));
        try {
            $this->assertTrue(\ActivationRepository::complete(auth()->user(),auth()->user()->activation->first()->token));

        } catch (ActivationException $e) {
            $this->assertEquals('User Does not have this token', $e->getMessage());
        }
        try {
            $this->assertNotTrue(\ActivationRepository::complete(auth()->user(),'199dqwdqno123nxpqdaskdnqw'));
        } catch (ActivationException $e) {
            $this->assertEquals('User Does not have this token', $e->getMessage());
        }
    }
     /**
     * @test
     */
    public function delete_activation_record_for_user()
    {
        $this->assertTrue(\ActivationRepository::clearFor(auth()->user()));
        $this->assertTrue(\ActivationRepository::clearFor($this->user,true));
        $this->assertNotTrue(\ActivationRepository::clearFor($this->user,true,true));
    }
    /**
     * @test
     */
    public function clear_records_for_users()
    {
        $this->assertTrue(\ActivationRepository::clear());
        $this->assertTrue(\ActivationRepository::clear(true));
    }
    /**
     * @test
     */
    public function generate_new_token_for_user()
    {
        $this->assertNotTrue(EloquentActivation::class,\ActivationRepository::generateToken(auth()->user()));
        $this->assertInstanceOf(EloquentActivation::class,\ActivationRepository::generateToken(auth()->user(),true));
    }
    /**
     * @test
     */
    public function force_generate()
    {
        $this->assertInstanceOf(EloquentActivation::class,\ActivationRepository::forceGenerateToken(auth()->user()));
    }
    /**
     * @test
     */
    public function regenerate_token()
    {
        $this->assertInstanceOf(EloquentActivation::class,\ActivationRepository::regenerateToken(auth()->user()));
        \ActivationRepository::clearFor(auth()->user(),true,true);
        $this->assertNotTrue(EloquentActivation::class,\ActivationRepository::regenerateToken(auth()->user()));
        $this->assertInstanceOf(EloquentActivation::class,\ActivationRepository::regenerateToken(auth()->user(),true));
    }
    /**
     * @test
     */
    public function remove_expired()
    {
        $this->assertTrue(\ActivationRepository::removeExpired(21));
        /**
         * Removing Expired can't be done twice, therefore It's going to return false next shot.
         * You can pass optionally days.
         */
        $this->assertNotTrue(\ActivationRepository::removeExpired());
    }

}
