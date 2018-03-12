<?php

namespace SecTheater\Jarvis\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ControllersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sectheater:controllers {namespace? : Append a different namespace rather than default one.}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish The Controllers To Specified Folder.';
    private $filesystem;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = $this->filesystem->allFiles(base_path('vendor/sectheater/laravel-jarvis/src/Http/Controllers/'));
        $destination_namespace = trim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $this->argument('namespace') ?? '\\app\\Http\\Controllers'), '/');
        $destination_namespace = starts_with($destination_namespace, 'App') ? lcfirst($destination_namespace) : $destination_namespace;
        $bar = $this->output->createProgressBar(8);
        $bar->setFormat('[<fg=magenta>%bar%</>]');
        if (str_contains($destination_namespace, 'app')) {
            if (!$this->filesystem->isDirectory($destination_namespace)) {
                $this->filesystem->makeDirectory($destination_namespace, 0755, false, true);
            }
        } else {
            if (!$this->filesystem->isDirectory(app_path($destination_namespace))) {
                $this->filesystem->makeDirectory(app_path($destination_namespace), 0755, false, true);
            }
        }
        $bar->advance();
        $this->info('Publishing the Controllers !');
        foreach ($files as $file) {
            $parts = explode(DIRECTORY_SEPARATOR, $file);
            $filename = end($parts);
            $subfolder = ($parts[count($parts) - 2] !== 'Controllers') ? $parts[count($parts) - 2] : null;
            if ($filename == 'Controller.php') {
                continue;
            }
            $path = $file->getPath().'/'.$filename;
            if ($file->isDir()) {
                if ($subfolder) {
                    if (!$this->filesystem->isDirectory($destination_namespace.DIRECTORY_SEPARATOR.$subfolder)) {
                        $this->filesystem->makeDirectory($destination_namespace.DIRECTORY_SEPARATOR.$subfolder, 0755, false, true);
                    }
                    $this->filesystem->copyDirectory($path, $destination_namespace.DIRECTORY_SEPARATOR.$subfolder, 0755, false, true);
                } else {
                    $this->filesystem->copyDirectory($path, $destination_namespace);
                }
            }
            if ($file->isFile()) {
                if ($subfolder) {
                    if (!$this->filesystem->isDirectory($destination_namespace.DIRECTORY_SEPARATOR.$subfolder)) {
                        $this->filesystem->makeDirectory($destination_namespace.DIRECTORY_SEPARATOR.$subfolder, 0755, false, true);
                    }

                    $this->filesystem->copy($path, $destination_namespace.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.$file->getFileName());
                    $file_content = $this->filesystem->get($destination_namespace.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.$file->getFileName());
                    if (strpos($file_content, 'namespace SecTheater\\Jarvis\\Http\\Controllers')) {
                        $namespace = ucfirst(str_replace('/', '\\', $destination_namespace));
                        $file_content = str_replace('namespace SecTheater\\Jarvis\\Http\\Controllers', 'namespace '.$namespace, $file_content);
                        $this->filesystem->put($destination_namespace.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.$file->getFileName(), $file_content);
                    }
                } else {
                    $this->filesystem->copy($path, $destination_namespace.DIRECTORY_SEPARATOR.$file->getFileName());
                    $file_content = $this->filesystem->get($destination_namespace.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.$file->getFileName());
                    if (strpos($file_content, 'namespace SecTheater\\Jarvis\\Http\\Controllers')) {
                        $namespace = ucfirst(str_replace('/', '\\', $destination_namespace));
                        $file_content = str_replace('namespace SecTheater\\Jarvis\\Http\\Controllers', 'namespace '.$namespace, $file_content);
                        $this->filesystem->put($destination_namespace.DIRECTORY_SEPARATOR.$subfolder.DIRECTORY_SEPARATOR.$file->getFileName(), $file_content);
                    }
                }
            }
        }
        $bar->advance();
        $this->info('Published The Contollers and namespaces are set');
        if ($this->confirm('publish the requests validation ? ')) {
            $this->call('sectheater:requests');
            $bar->advance();
            $this->info('Request Validation is published and namespaces are set.');
        }
        if ($this->confirm('publish the custom rules valdation ? ')) {
            $this->call('sectheater:custom-validation');
            $bar->advance();
            $this->info('Custom Rules Validation is published and namespaces are set.');
        }
        $bar->finish();
        $this->info('Everything is set !');
    }
}
