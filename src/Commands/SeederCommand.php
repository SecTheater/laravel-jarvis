<?php

namespace SecTheater\Jarvis\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class SeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sectheater:seed-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed your database with Jarvis Seeders';
    private $filesystem;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $content = $this->filesystem->get(database_path('seeds/DatabaseSeeder.php'));
        if (!str_contains($content, 'RolesSeeder::class')) {
            $needle = '    public function run()
    {';
            $content = (str_replace($needle, $needle."\n\t\t\t\t\$this->call(\RolesSeeder::class); \n", $content));
            $this->filesystem->put(database_path('seeds/DatabaseSeeder.php'), $content);
            $this->info('DatabaseSeeder is updated with our Roles Seeder. Database is seeded successfully !');
        } else {
            $this->info('It Seems that the seeder exist in your DatabseSeeder');
        }
        $this->call('db:seed');
        if ($this->confirm('Do You wish to convert Permissions into Gates & Policies ? ')) {
            $this->call('sectheater:register-authorization');
        }
    }
}
