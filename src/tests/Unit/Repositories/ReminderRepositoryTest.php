<?php

namespace SecTheater\Jarvis\Tests\Unit\Repositories;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use SecTheater\Jarvis\Reminder\EloquentReminder;
use SecTheater\Jarvis\Reminder\ReminderException;
use SecTheater\Jarvis\Tests\TestCase;

class ReminderRepositoryTest extends TestCase
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
            $u->reminder()->save(factory(EloquentReminder::class)->make());
            $u->activation()->save(factory(EloquentReminder::class)->make());
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
        $this->user->reminder()->create([
            'token'=> str_random(32),
        ]);
        \ReminderRepository::complete($this->user, $this->user->reminder->first()->token);
    }

    /**
     *@test
     */
    public function user_has_token()
    {
        $this->assertInstanceOf(EloquentReminder::class, \ReminderRepository::hasToken(auth()->user()));
        $this->assertNotTrue(EloquentReminder::class, \ReminderRepository::hasToken($this->user));
    }

    /**
     * @test
     */
    public function has_or_create_token()
    {
        $this->assertInstanceOf(EloquentReminder::class, \ReminderRepository::hasOrCreateToken(auth()->user()));
        $this->assertNotTrue(EloquentReminder::class, \ReminderRepository::hasOrCreateToken($this->user));
    }

    /**
     *@test
     */
    public function user_has_completed_his_reminder()
    {
        $user = jarvis()->registerAndActivate(['first_name' => 'Mohammed',
            'last_name'                                     => 'Osama',
            'email'                                         => 'mohammedosama@sectdheater.org',
            'password'                                      => bcrypt(123456789),
            'username'                                      => 'mohammaed',
            'sec_answer'                                    => 'none',
            'sec_question'                                  => 'none',
            'location'                                      => 'Egypt',
            'dob'                                           => '2017-6-6', ]);
        $this->assertNotTrue(\ReminderRepository::completed(auth()->user()));
        $this->assertTrue(\ReminderRepository::completed($user));
    }

    /**
     * @test
     */
    public function complete_user_reminder()
    {
        $this->assertTrue(\ReminderRepository::complete(auth()->user(), auth()->user()->reminder->first()->token));

        try {
            $this->assertTrue(\ReminderRepository::complete(auth()->user(), auth()->user()->reminder->first()->token));
        } catch (ReminderException $e) {
            $this->assertEquals('User Does not have this token', $e->getMessage());
        }

        try {
            $this->assertNotTrue(\ReminderRepository::complete(auth()->user(), '199dqwdqno123nxpqdaskdnqw'));
        } catch (ReminderException $e) {
            $this->assertEquals('User Does not have this token', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function delete_reminder_record_for_user()
    {
        $this->assertTrue(\ReminderRepository::clearFor(auth()->user()));
        $this->assertTrue(\ReminderRepository::clearFor($this->user, true));
        $this->assertNotTrue(\ReminderRepository::clearFor($this->user, true, true));
    }

    /**
     * @test
     */
    public function clear_records_for_users()
    {
        $this->assertTrue(\ReminderRepository::clear());
        $this->assertTrue(\ReminderRepository::clear(true));
    }

    /**
     * @test
     */
    public function generate_new_token_for_user()
    {
        $this->assertNotTrue(EloquentReminder::class, \ReminderRepository::generateToken(auth()->user()));
        $this->assertInstanceOf(EloquentReminder::class, \ReminderRepository::generateToken(auth()->user(), true));
    }

    /**
     * @test
     */
    public function force_generate()
    {
        $this->assertInstanceOf(EloquentReminder::class, \ReminderRepository::forceGenerateToken(auth()->user()));
    }

    /**
     * @test
     */
    public function regenerate_token()
    {
        $this->assertInstanceOf(EloquentReminder::class, \ReminderRepository::regenerateToken(auth()->user()));
        \ReminderRepository::clearFor(auth()->user(), true, true);
        $this->assertNotTrue(EloquentReminder::class, \ReminderRepository::regenerateToken(auth()->user()));
        $this->assertInstanceOf(EloquentReminder::class, \ReminderRepository::regenerateToken(auth()->user(), true));
    }

    /**
     * @test
     */
    public function remove_expired()
    {
        $this->assertTrue(\ReminderRepository::removeExpired(21));
        /*
         * Removing Expired can't be done twice, therefore It's going to return false next shot.
         * You can pass optionally days.
         */
        $this->assertNotTrue(\ReminderRepository::removeExpired());
    }
}
