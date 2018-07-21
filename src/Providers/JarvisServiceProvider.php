<?php

namespace SecTheater\Jarvis\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use SecTheater\Jarvis\Facades\Jarvis;

class JarvisServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Filesystem $filesystem)
    {
        if (config('jarvis.observers.register')) {
            $this->registerObservers();
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishables();
            $this->registerConsoleCommands();
        }
        $this->registerHelpers();
        $this->registerBlades();
        $this->registerBindings();
        Schema::defaultStringLength(191);
    }

    private function registerHelpers()
    {
        require_once __DIR__.'/../Helpers/Helpers.php';
    }

    private function registerPublishables()
    {
        $publishablePath = dirname(__DIR__).'/publishable';
        $publishable = [
            'migrations' => [
                "{$publishablePath}/Database/Migrations/" => database_path('migrations'),
            ],
            'seeds' => [
                "{$publishablePath}/Database/Seeders/" => database_path('seeds'),
            ],
            'config' => [
                "{$publishablePath}/config/jarvis.php" => config_path('jarvis.php'),
            ],
            'Markdown' => [
                "{$publishablePath}/Views/Emails/" => resource_path('views/emails'),
            ],
            'Emails' => [
                "{$publishablePath}/Mail" => app_path('Mail'),
            ],

        ];
        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    private function registerConsoleCommands()
    {
        $this->commands(\SecTheater\Jarvis\Commands\InstallCommand::class);
        $this->commands(\SecTheater\Jarvis\Commands\ControllersCommand::class);
        $this->commands(\SecTheater\Jarvis\Commands\AuthCommand::class);
        $this->commands(\SecTheater\Jarvis\Commands\CustomValidationCommand::class);
        $this->commands(\SecTheater\Jarvis\Commands\RequestsCommand::class);
        $this->commands(\SecTheater\Jarvis\Commands\SeederCommand::class);
    }

    private function registerObservers()
    {
        foreach ($this->models as $key => $value) {
            $value::observe("\SecTheater\Jarvis\Observers\\".ucfirst($key).'Observer');
        }
    }

    protected function registerBindings()
    {
        $this->app->singleton('Jarvis', function () {
            foreach ($this->models as $key => $value) {
                $class = '\SecTheater\Jarvis\\'.ucfirst($key).'\\'.ucfirst($key).'Repository';
                $$key = new $class(new $value());
            }

            return new \SecTheater\Jarvis\Jarvis(
                $user,
                $activation,
                $role,
                $post,
                $comment,
                $reply,
                $reminder,
                $like,
                $tag
            );
        });
        foreach ($this->models as $key => $value) {
            $class = '\SecTheater\Jarvis\\'.ucfirst($key).'\\'.ucfirst($key).'Repository';
            $model = new $class(new $value());
            if ($key == 'user' || $key == 'role' || config('jarvis.'.str_plural($key).'.register')) {
                $this->app->singleton(class_basename($class), function () use ($model) {
                    return $model;
                });
            }
        }
    }

    private function registerBlades()
    {
        Blade::if('hasRole', function (string $role, Model $user = null) {
            if (!isset($user)) {
                $user = auth()->user();
            }

            return auth()->check() && $user->hasRole($role);
        });
        Blade::if('hasAnyRole', function (array $role, Model $user = null) {
            if (!isset($user)) {
                $user = auth()->user();
            }

            return auth()->check() && $user->hasAnyRole($role);
        });
        Blade::if('equals', function (Model $user) {
            return auth()->check() && auth()->user()->id === $user->id;
        });
        Blade::if('role', function (string $slug, Model $user = null) {
            if (!isset($user)) {
                $user = auth()->user();
            }

            return $user->roles->first()->slug === $slug;
        });
        Blade::if('hasAllRole', function (array $role, Model $user = null) {
            if (!isset($user)) {
                $user = auth()->user();
            }

            return auth()->check() && $user->hasAllRole($role);
        });
        Blade::if('inRole', function (string $slug, Model $user = null) {
            if (!isset($user)) {
                $user = auth()->user();
            }

            return auth()->check() && $user->inRole($slug);
        });
    }

    public function __get($key)
    {
        if (count(config('jarvis.models.user'))) {
            foreach (config('jarvis.models.user') as $key => $value) {
                $this->models[$key] = $value;
            }
        }
        foreach (config('jarvis.models.package') as $key => $value) {
            if (model_exists($key)) {
                $this->models[$key] = config('jarvis.models.namespace').ucfirst($key);
            } else {
                $this->models[$key] = $value;
            }
        }
        return $this->models ?? null;
    }
}
