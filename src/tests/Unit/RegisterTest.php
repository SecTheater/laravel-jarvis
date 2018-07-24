<?php

namespace SecTheater\Jarvis\Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Role\EloquentRole;
use SecTheater\Jarvis\User\EloquentUser;
use Tests\TestCase;

class RegisterTest extends TestCase
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
    }
    public function testRegister()
    {
            $this->assertInstanceOf(EloquentUser::class,jarvis()->register(['first_name' => 'Mohammed',
            'last_name' => 'Osama',
            'email' => 'mohammedosama@sectheater.org',
            'password' => bcrypt(123456789),
            'username' => 'mohammed',
            'sec_answer' => 'none',
            'sec_question' => 'none',
            'location' => 'Egypt',
            'dob' => '2017-6-6']));
    }
    public function testRegisterAndActivate()
    {
        $user = jarvis()->registerAndActivate(['first_name' => 'Mohammed',
            'last_name' => 'Osama',
            'email' => 'mohammedosama@sectheater.org',
            'password' => bcrypt(123456789),
            'username' => 'mohammed',
            'sec_answer' => 'none',
            'sec_question' => 'none',
            'location' => 'Egypt',
            'dob' => '2017-6-6']);
        $this->assertInstanceOf(User::class,$user);
    }
    public function testRegisterWithRole()
    {
        $this->assertInstanceOf(User::class,jarvis()->registerWithRole(['first_name' => 'Mohammed',
            'last_name' => 'Osama',
            'email' => 'mohammedosama@sectheater.org',
            'password' => bcrypt(123456789),
            'username' => 'mohammed',
            'sec_answer' => 'none',
            'sec_question' => 'none',
            'location' => 'Egypt',
            'dob' => '2017-6-6']));
    }
}
