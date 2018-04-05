<?php

namespace SecTheater\Jarvis\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use SecTheater\Jarvis\Activation\ActivationRepository;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Comment\CommentRepository;
use SecTheater\Jarvis\Comment\EloquentComment;
use SecTheater\Jarvis\Facades\Jarvis;
use SecTheater\Jarvis\Like\EloquentLike;
use SecTheater\Jarvis\Like\LikeRepository;
use SecTheater\Jarvis\Post\EloquentPost;
use SecTheater\Jarvis\Post\PostRepository;
use SecTheater\Jarvis\Reminder\EloquentReminder;
use SecTheater\Jarvis\Reminder\ReminderRepository;
use SecTheater\Jarvis\Reply\EloquentReply;
use SecTheater\Jarvis\Reply\ReplyRepository;
use SecTheater\Jarvis\Role\EloquentRole;
use SecTheater\Jarvis\Role\RoleRepository;
use SecTheater\Jarvis\Tag\EloquentTag;
use SecTheater\Jarvis\Tag\TagRepository;
use SecTheater\Jarvis\User\EloquentUser;
use SecTheater\Jarvis\User\UserRepository;

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
            'migrations'                              => [
                "{$publishablePath}/Database/Migrations/"=> database_path('migrations'),
            ],
            'seeds'                                => [
                "{$publishablePath}/Database/Seeders/"=> database_path('seeds'),
            ],
            'config'                               => [
                "{$publishablePath}/config/jarvis.php"=> config_path('jarvis.php'),
            ],
            'Markdown'                         => [
                "{$publishablePath}/Views/Emails/"=> resource_path('emails'),
            ],
            'Emails'                  => [
                "{$publishablePath}/Mail"=> app_path('Mail'),
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
        $this->commands(\SecTheater\Jarvis\Commands\AssignUserCommand::class);
        $this->commands(\SecTheater\Jarvis\Commands\CustomValidationCommand::class);
        $this->commands(\SecTheater\Jarvis\Commands\RequestsCommand::class);
        $this->commands(\SecTheater\Jarvis\Commands\ObserversCommand::class);
        //  $this->commands(Commands\CreatePermissionCommand::class);
    }

    private function registerObservers()
    {
        EloquentActivation::observe(\SecTheater\Jarvis\Observers\ActivationObserver::class);
        EloquentComment::observe(\SecTheater\Jarvis\Observers\CommentObserver::class);
        EloquentPost::observe(\SecTheater\Jarvis\Observers\PostObserver::class);
        EloquentReminder::observe(\SecTheater\Jarvis\Observers\ReminderObserver::class);
        EloquentReply::observe(\SecTheater\Jarvis\Observers\ReplyObserver::class);
        EloquentRole::observe(\SecTheater\Jarvis\Observers\RoleObserver::class);
        EloquentUser::observe(\SecTheater\Jarvis\Observers\UserObserver::class);
        if (model_exists('Post')) {
            \App\Post::observe(\SecTheater\Jarvis\Observers\PostObserver::class);
        }
        if (model_exists('User')) {
            \App\User::observe(\SecTheater\Jarvis\Observers\UserObserver::class);
        }
        if (model_exists('Comment')) {
            \App\Comment::observe(\SecTheater\Jarvis\Observers\CommentObserver::class);
        }
        if (model_exists('Activation')) {
            \App\Activation::observe(\SecTheater\Jarvis\Observers\ActivationObserver::class);
        }
        if (model_exists('Reminder')) {
            \App\Reminder::observe(\SecTheater\Jarvis\Observers\ReminderObserver::class);
        }
        if (model_exists('Reply')) {
            \App\Reply::observe(\SecTheater\Jarvis\Observers\ReplyObserver::class);
        }
    }

    protected function registerBindings()
    {
        $this->app->singleton('Jarvis', function () {
            $user = new UserRepository(new EloquentUser());
            $activation = new ActivationRepository(new EloquentActivation());
            $role = new RoleRepository(new EloquentRole());
            $post = new PostRepository(new EloquentPost());
            $comment = new CommentRepository(new EloquentComment());
            $reply = new ReplyRepository(new EloquentReply());
            $reminder = new ReminderRepository(new EloquentReminder());
            $like = new LikeRepository(new EloquentLike());
            $tag = new TagRepository(new EloquentTag());

            return  new \SecTheater\Jarvis\Jarvis(
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
        if (config('jarvis.posts.register')) {
            $this->app->singleton('PostRepository', function () {
                return new PostRepository(new EloquentPost());
            });
        }
        if (config('jarvis.comments.register')) {
            $this->app->singleton('CommentRepository', function () {
                return new CommentRepository(new EloquentComment());
            });
        }

        if (config('jarvis.activation.register')) {
            $this->app->singleton('ActivationRepository', function () {
                return new ActivationRepository(new EloquentActivation());
            });
        }
        if (config('jarvis.replies.register')) {
            $this->app->singleton('ReplyRepository', function () {
                return new ReplyRepository(new EloquentReply());
            });
        }
        $this->app->singleton('ReminderRepository', function () {
            return new ReminderRepository(new EloquentReminder());
        });
        if (config('jarvis.like.register')) {
            $this->app->singleton('LikeRepository', function () {
                return new LikeRepository(new EloquentLike());
            });
        }
        $this->app->singleton('RoleRepository', function () {
            return new RoleRepository(new EloquentRole());
        });
        if (config('jarvis.tag.register')) {
            $this->app->singleton('TagRepository', function () {
                return new TagRepository(new TagRepository());
            });
        }
        $this->app->singleton('UserRepository', function () {
            return new UserRepository(new EloquentUser());
        });
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
    }
}
