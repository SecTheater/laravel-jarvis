<?php

namespace SecTheater\Jarvis\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class RequestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sectheater:requests {namespace? : Append a different namespace rather than default one.}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish The Requests Validation To Specified Folder.';
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
        $files = $this->filesystem->files(base_path('vendor/sectheater/laravel-jarvis/src/Http/Requests'));
        $destination_namespace = trim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $this->argument('namespace') ?? 'app\\Http\\Requests'), '/');
        $destination_namespace = starts_with($destination_namespace, 'App') ? lcfirst($destination_namespace) : $destination_namespace;
        if (str_contains($destination_namespace, 'app')) {
            if (!$this->filesystem->isDirectory($destination_namespace)) {
                $this->filesystem->makeDirectory($destination_namespace, 0755, false, true);
            }
        } else {
            if (!$this->filesystem->isDirectory(app_path($destination_namespace))) {
                $this->filesystem->makeDirectory(app_path($destination_namespace), 0755, false, true);
            }
        }
        foreach ($files as $file) {
            $parts = explode(DIRECTORY_SEPARATOR, $file);
            $filename = end($parts);
            $path = $file->getPath().'/'.$filename;
            if ($file->isFile()) {
                $this->filesystem->copy($path, $destination_namespace.DIRECTORY_SEPARATOR.$file->getFileName());
                $file_content = $this->filesystem->get($destination_namespace.DIRECTORY_SEPARATOR.$file->getFileName());
                if (strpos($file_content, 'namespace SecTheater\\Jarvis\\Http\\Requests')) {
                    $namespace = ucfirst(str_replace('/', '\\', $destination_namespace));
                    $file_content = str_replace('namespace SecTheater\\Jarvis\\Http\\Requests', 'namespace '.$namespace, $file_content);
                    $this->filesystem->put($destination_namespace.DIRECTORY_SEPARATOR.$file->getFileName(), $file_content);
                }
                if (strpos($file_content, 'use SecTheater\\Jarvis\\Http\\Rules')) {
                    $namespace = '\\App\\Rules';
                    $file_content = str_replace('use SecTheater\\Jarvis\\Http\\Rules', 'use '.$namespace, $file_content);
                    $this->filesystem->put($destination_namespace.DIRECTORY_SEPARATOR.$file->getFileName(), $file_content);
                }
            }
        }
        $this->info('Jarvis Requests Validation Are Published !');
    }
}
