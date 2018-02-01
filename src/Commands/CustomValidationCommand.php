<?php

namespace SecTheater\Jarvis\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CustomValidationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sectheater:custom-validation {namespace? : Append a different namespace rather than default one.}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish The Rules Validation  To Specified Folder.';
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
        $files = $this->filesystem->files(base_path('vendor/sectheater/laravel-jarvis/src/Http/Rules'));
        $destination_namespace = trim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $this->argument('namespace') ?? '\\app\\Rules'), '/');
        $destination_namespace = starts_with($destination_namespace, 'App') ? lcfirst($destination_namespace) : $destination_namespace;
        if (str_contains($destination_namespace, 'app')) {
            if (!$this->filesystem->isDirectory($destination_namespace)) {
                $this->filesystem->makeDirectory($destination_namespace);
            }
        } else {
            if (!$this->filesystem->isDirectory(app_path($destination_namespace))) {
                $this->filesystem->makeDirectory(app_path($destination_namespace));
            }
        }
        foreach ($files as $file) {
            $parts = explode(DIRECTORY_SEPARATOR, $file);
            $filename = end($parts);
            $path = $file->getPath().'/'.$filename;
            if ($file->isFile()) {
                $this->filesystem->copy($path, $destination_namespace.DIRECTORY_SEPARATOR.$file->getFileName());
                $file_content = $this->filesystem->get($destination_namespace.DIRECTORY_SEPARATOR.$file->getFileName());
                if (strpos($file_content, 'namespace SecTheater\\Jarvis\\Http\\Rules')) {
                    $namespace = ucfirst(str_replace('/', '\\', $destination_namespace));
                    $file_content = str_replace('namespace SecTheater\\Jarvis\\Http\\Rules', 'namespace '.$namespace, $file_content);
                    $this->filesystem->put($destination_namespace.DIRECTORY_SEPARATOR.$file->getFileName(), $file_content);
                }
            }
        }
        $this->info('Jarvis Validation Rules Are Published !');
    }
}
