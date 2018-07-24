<?php

namespace SecTheater\Jarvis\Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
<<<<<<< HEAD
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use SecTheater\Jarvis\Tests\TestCase;
=======
use Tests\TestCase;
>>>>>>> 719d652836f3ca31db799309eb28ae4c52608ad2

class LoginTest extends TestCase
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
        $this->user = jarvis()->registerWithRole(['first_name' => 'Mohammed',
            'last_name'                                        => 'Osama',
            'email'                                            => 'mohammedosama@sectheater.org',
            'password'                                         => bcrypt(123456789),
            'username'                                         => 'mohammed',
            'sec_answer'                                       => 'none',
            'sec_question'                                     => 'none',
            'location'                                         => 'Egypt',
            'dob'                                              => '2017-6-6', ], 'user', true);
    }

    public function test_that_user_is_authenticated()
    {
        $this->actingAs($this->user)->assertAuthenticated();
    }

    public function testLoginById()
    {
        $this->assertInstanceOf(User::class, jarvis()->loginById($this->user->id));
    }
}
