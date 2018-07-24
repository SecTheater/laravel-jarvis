<?php

namespace SecTheater\Jarvis\Tests\Unit\Repositories;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Tag\EloquentTag;
use SecTheater\Jarvis\Tests\TestCase;

class TagRepositoryTest extends TestCase
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
            $u->posts()->saveMany(factory(\App\Post::class, 3)->make());
            $u->comments()->saveMany(factory(\App\Comment::class, 30)->make());
            $u->replies()->saveMany(factory(\App\Reply::class, 30)->make());
            $u->tags()->saveMany(factory(\App\Tag::class, 20)->make());
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
    public function fetch_tag_posts()
    {
        $this->assertInstanceOf(EloquentCollection::class, \TagRepository::getTagPosts(\TagRepository::first()->name));
    }

    /**
     *@test
     */
    public function fetch_user_tags()
    {
        $this->assertInstanceOf(Collection::class, \TagRepository::userTags(\UserRepository::first()));
    }
}
