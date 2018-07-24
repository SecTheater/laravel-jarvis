<?php

namespace SecTheater\Jarvis\Tests\Unit\Repositories;

use \App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Exceptions\ConfigException;
use SecTheater\Jarvis\Tests\TestCase;

class BasicRepositoryTest extends TestCase
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
             $u->comments()->saveMany(factory(\App\Comment::class,3)->make());
            $role = \RoleRepository::findRoleBySlug('user');
            $u->roles()->attach($role);
        });
    }

    /**
     * A basic test example.
     *@test
     * @return void
     */
    public function fetch_all_users()
    {
        $this->assertInstanceOf(Collection::class,\UserRepository::all());
        $this->assertInstanceOf(Collection::class,\UserRepository::all('id'));
    }
    /**
     * A basic test example.
     *@test
     * @return void
     */

    public function find_specific_user()
    {
        $user = \UserRepository::first();
        $this->assertInstanceOf(User::class,\UserRepository::find($user->id));
        $this->assertInstanceOf(User::class,\UserRepository::find($user->id,['id']));
    }
    /**
     * A basic test example.
     *@test
     * @return void
     */

    public function find_by_columns()
    {
        $user = \UserRepository::first();
        $this->assertInstanceOf(Collection::class,\UserRepository::findBy(['username' => $user->username]));
        $this->assertInstanceOf(Collection::class,\UserRepository::findBy('username',$user->username));
        $this->assertInstanceOf(Collection::class,\UserRepository::findBy('created_at','=',$user->created_at));
    }
    /**
     * A basic test example.
     *@test
     * @return void
     */

    public function create_user()
    {
        $this->assertInstanceOf(User::class,\UserRepository::create(['first_name' => 'Mohammed',
            'last_name' => 'Osama',
            'email' => 'mohammedosama@sectheater.org',
            'password' => bcrypt(123456789),
            'username' => 'mohammed',
            'sec_answer' => 'none',
            'sec_question' => 'none',
            'location' => 'Egypt',
            'dob' => '2017-6-6']));
    }
    /**
     * A basic test example.
     *@test
     * @return void
     */

    public function user_exists()
    {
        $user = \UserRepository::first();
        $this->assertTrue(\UserRepository::exists(['username' => $user->username]));
        $this->assertTrue(\UserRepository::exists('username',$user->username));
        $this->assertTrue(\UserRepository::exists('username','=',$user->username));
    }
    /**
     * A basic test example.
     *@test
     * @return void
     */

    public function update_user(){
        $user = \UserRepository::first();
        $this->assertInstanceOf(User::class,\UserRepository::update($user,['username' => 'Ahmed']));
        $this->assertInstanceOf(User::class,\UserRepository::update($user->id,['username' => 'Ahmed']));
    }
    /**
     * A basic test example.
     *@test
     * @return void
     */

    public function delete_user()
    {
        $user = \UserRepository::first();
        $this->assertTrue(\UserRepository::delete($user));
        // $this->assertTrue(\UserRepository::delete($user->id));
    }

    /**
     * A basic test example.
     *@test
     * @return void
     */

    public function order_users()
    {
        $this->assertInstanceOf(Collection::class,\UserRepository::ordered('created_at','desc'));
    }
    /**
     * A basic test example.
     *@test
     * @return void
     */

    public function recent_users()
    {
        $this->assertInstanceOf(Collection::class,\PostRepository::recent(['approved' => true],'approved_at'));
        try {
            $this->assertInstanceOf(Collection::class,\UserRepository::recent(null,'approved_at'));
        } catch (ConfigException $e) {
            $this->assertEquals("users Doesn't have approved_at", $e->getMessage());
        }
    }
    /**
     * A basic test example.
     *@test
     * @return void
     */
    public function fetch_users_that_created_something_related_to_relationship()
    {
        $this->assertInstanceOf(Collection::class,\UserRepository::getUsersHave('activation'));
        $this->assertInstanceOf(Collection::class,\UserRepository::getUsersHave('posts','>',2));
        $this->assertInstanceOf(Collection::class,\UserRepository::getUsersHave('posts',2));
        $this->assertInstanceOf(Collection::class,\UserRepository::getUsersHave('posts',['approved' => true]));
    }
    /**
     * A basic test example.
     *@test
     * @return void
     */
    public function fetch_users_where_have_relation()
    {
        $this->assertInstanceOf(Collection::class,\UserRepository::getUsersWhereHave('posts',['approved' => true]));
    }
    /**
     * A basic test example.
     *@test
     * @return void
     */
    public function fetch_users_where_does_not_have_relation()
    {
        $this->assertInstanceOf(Collection::class,\UserRepository::getUsersWhereDoesntHave('posts',['approved' => true]));
    }
     /**
     * A basic test example.
     *@test
     * @return void
     */
    public function fetch_users_with_relationship()
    {
        $this->assertInstanceOf(Collection::class,\UserRepository::getUsersWith('posts'));
        $this->assertInstanceOf(Collection::class,\UserRepository::getUsersWith('posts','comments'));
        $this->assertInstanceOf(Collection::class,\UserRepository::getUsersWith('posts','comments',['approved' => true]));
        $this->assertInstanceOf(Collection::class,\UserRepository::getUsersWith('posts',['comments' => function($query){
            $query->whereApproved(true);
        }],['approved' => true]));
    }
}
