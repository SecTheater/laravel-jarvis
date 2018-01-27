<?php

namespace SecTheater\Jarvis\Commands;

use Illuminate\Console\Command;

class CreatePermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sectheater:create-permission
                {name : Permission name.}
                {privilege : setup the forbidden privileges only.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup a fresh permission.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $user = \UserRepository::whereUsername($this->argument('username'))->first();

        $role = \RoleRepository::whereSlug($this->argument('rank'))->first();
        if ($user && $role) {
            $user->update(['permissions' => null]);
            $user->roles()->sync($role);

            return $this->info("$user->first_name  Role is set to  `{$role->name}`");
        }

        return $this->error('User Or Role Does not exist');
    }
}
