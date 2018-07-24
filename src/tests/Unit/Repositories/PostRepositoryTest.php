<?php

namespace SecTheater\Jarvis\Tests\Unit\Repositories;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Tests\TestCase;

class PostRepositoryTest extends TestCase
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
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function fetch_archives()
    {
        // works only on mysql.
        $this->assertInstanceOf(EloquentCollection::class, \PostRepository::archives());
    }
    /**
     *@test
     */
    public function fetch_popular_posts()
    {
        $this->assertInstanceOf(Collection::class, \PostRepository::getPopularPosts());
        $this->assertEquals(0, \PostRepository::getPopularPosts()->count());
    }
    /**
     *@test
     */
    public function fetch_approved_posts()
    {
        $this->assertInstanceOf(EloquentCollection::class,\PostRepository::getApproved());
        $this->assertInstanceOf(EloquentCollection::class,\PostRepository::getApproved('comments',['approved' => false]));
    }
    /**
     *@test
     */
    public function fetch_unapproved_posts()
    {
        $this->assertInstanceOf(EloquentCollection::class,\PostRepository::getUnapproved());
        $this->assertInstanceOf(EloquentCollection::class,\PostRepository::getUnapproved('comments',['approved' => false]));
    }
    /**
     * @test
     */
    public function fetch_user_data_collection_via_relation()
    {
        $this->assertInstanceOf(Collection::class,\PostRepository::userPosts(\UserRepository::first()));
        $this->assertInstanceOf(Collection::class,\PostRepository::userPosts(\UserRepository::first(),['approved' => true]));
    }

}
