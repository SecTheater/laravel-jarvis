<?php

namespace SecTheater\Jarvis\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class AuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sectheater:auth';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setting Jarvis Authentication !';
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
        $this->filesystem->copyDirectory('vendor/sectheater/laravel-jarvis/src/Http/Controllers/Auth', 'app/Http/Controllers/Auth');
        if (!\File::exists(resource_path('views/layouts/app.blade.php'))) {
            if (!\File::exists(resource_path('views/layouts'))) {
                \File::makeDirectory(resource_path('views/layouts'));
            }
            copy(base_path('vendor/laravel/framework/src/Illuminate/Auth/Console/stubs/make/views/layouts/app.stub'), resource_path('views/layouts/app.blade.php'));
        }
        $this->filesystem->copyDirectory('vendor/sectheater/laravel-jarvis/src/publishable/Views/Auth', 'resources/views/auth');
        $bar = $this->output->createProgressBar(8);
        $bar->setFormat('[<fg=magenta>%bar%</>]');

        $this->info('Publishing Controllers');
        $bar->advance();
        $files = $this->filesystem->files(base_path('app/Http/Controllers/Auth/'));
        foreach ($files as $file) {
            $file_content = $this->filesystem->get($file);
            if (strpos($file_content, 'namespace SecTheater\\Jarvis\\Http\\Controllers')) {
                $namespace = 'App\\Http\\Controllers';
                $file_content = str_replace('namespace SecTheater\\Jarvis\\Http\\Controllers', 'namespace '.$namespace, $file_content);
                $this->filesystem->put($file->getRealPath(), $file_content);
            }
            if (strpos($file_content, 'use SecTheater\\Jarvis\\Http\\Requests')) {
                $namespace = 'App\\Http\\Requests';
                $file_content = str_replace('use SecTheater\\Jarvis\\Http\\Requests', 'use '.$namespace, $file_content);
                $this->filesystem->put($file->getRealPath(), $file_content);
            }
        }
        $this->info('Controllers namespace is set !');
        $bar->advance();

        $this->info('Publishing Custom Validation Rules !');
        $this->call('sectheater:custom-validation');
        $bar->advance();
        $files = $this->filesystem->files(base_path('app/Rules'));
        foreach ($files as $file) {
            $file_content = $this->filesystem->get($file);
            if (strpos($file_content, 'namespace SecTheater\\Jarvis\\Http\\Rules')) {
                $namespace = '\\App\\Rules';
                $file_content = str_replace('namespace SecTheater\\Jarvis\\Http\\Rules', 'namespace '.$namespace, $file_content);
                $this->filesystem->put($file->getRealPath(), $file_content);
            }
        }
        $bar->advance();

        $this->info('Custom Rules namesapce is set !');
        $bar->advance();

        $this->info('Publishing the Request Validation.');
        $this->call('sectheater:requests');
        $files = $this->filesystem->files(base_path('app/Http/Requests'));
        foreach ($files as $file) {
            $file_content = $this->filesystem->get($file);
            if (strpos($file_content, 'namespace SecTheater\\Jarvis\\Http\\Requests')) {
                $namespace = 'App\\Http\\Requests';
                $file_content = str_replace('namespace SecTheater\\Jarvis\\Http\\Requests', 'namespace '.$namespace, $file_content);
                $this->filesystem->put($file->getRealPath(), $file_content);
            }
            if (strpos($file_content, 'use SecTheater\\Jarvis\\Http\\Rules')) {
                $namespace = 'App\\Rules';
                $file_content = str_replace('use SecTheater\\Jarvis\\Http\\Rules', 'use '.$namespace, $file_content);
                $this->filesystem->put($file->getRealPath(), $file_content);
            }
        }
        $bar->advance();

        $this->info('Request Validation namespaces are set !');
        $bar->advance();

        $this->info('Adding Jarvis Authentication Routes');

        $content = $this->filesystem->get(app_path('Providers/RouteServiceProvider.php'));
        if (false === strpos($content, 'jarvis()->routes()')) {
            $content = str_replace('parent::boot();', "jarvis()->routes();\n\t\t\t\tparent::boot();", $content);
            $this->filesystem->put(app_path('Providers/RouteServiceProvider.php'), $content);
            $this->info('Jarvis has set its routes in RouteServiceProvider');
        } else {
            $this->info('Jarvis Seems that has set its routes earlier.');
        }
        $bar->finish();
        $this->info('Jarvis Authentication is set !');
    }
}
