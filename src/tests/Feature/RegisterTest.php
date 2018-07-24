<?php

namespace SecTheater\Jarvis\Tests\Feature;

use \Illuminate\Foundation\Testing\DatabaseMigrations;
use \Illuminate\Foundation\Testing\DatabaseTransactions;
use \Tests\TestCase;

class RegisterTest extends TestCase
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
        $this->withoutMiddleware();
    }

    /**
     *@test
     */
    public function user_can_see_register_page()
    {
        $response = $this->get('/register');

        $response->assertSeeText('Register');
    }

    /*
     * @test
     */
    // public function user_can_submit_register_form()
    // {
    //     $response = $this->post('/register',[
    //         'username' => 'Mohammed',
    //         'password' => 'secret',
    //         'password_confirmation' => 'secret',
    //         'email' => 'mohammedosama@sectheater.org',
    //         'first_name' => 'Mohammed',
    //         'last_name' => 'Mohammed Osama',
    //         'location' => 'Egypt',
    //         '_token' => csrf_token(),
    //     ]);
    //     $response->assertStatus(200);
    // }
}
