<?php

namespace SecTheater\Jarvis\Commands;

use Illuminate\Console\Command;

class AssignUserCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sectheater:assign
                {username : The username of the user}
                {rank : The rank of the user (slug within the roles table.)}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Setup a fresh privilege to a user ( deletes all of old permissions and assign new one )';
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$user = \UserRepository::whereUsername($this->argument('username'))->first();

		$role = \RoleRepository::whereSlug($this->argument('rank'))->first();

		if ($user && $role) {
			$user->update(['permissions' => NULL]);
			$user->roles()->sync($role);
			return $this->info("$user->first_name  Role is set to  `{$role->name}`");
		}
		return $this->error("User Or Role Does not exist");
	}
}
