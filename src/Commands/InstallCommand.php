<?php

namespace SecTheater\Jarvis\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SecTheater\Jarvis\Providers\JarvisServiceProvider;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sectheater:install';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Getting your environment Ready !';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $aliases = [
        'Jarvis'     => '\'Jarvis\' 			  => SecTheater\\Jarvis\\Facades\\Jarvis::class ,',
        'activation' => '\'ActivationRepository\' => SecTheater\\Jarvis\\Facades\\ActivationRepository::class ,',
        'comments'   => '\'CommentRepository\'    => SecTheater\\Jarvis\\Facades\\CommentRepository::class ,',
        'likes'      => '\'LikeRepository\'       => SecTheater\\Jarvis\\Facades\\LikeRepository::class ,',
        'replies'    => '\'ReplyRepository\'      => SecTheater\\Jarvis\\Facades\\ReplyRepository::class ,',
        'posts'      => '\'PostRepository\'       => SecTheater\\Jarvis\\Facades\\PostRepository::class ,',
        'reminders'  => '\'ReminderRepository\'   => SecTheater\\Jarvis\\Facades\\ReminderRepository::class ,',
        'roles'      => '\'RoleRepository\'       => SecTheater\\Jarvis\\Facades\\RoleRepository::class ,',
        'tags'       => '\'TagRepository\'        => SecTheater\\Jarvis\\Facades\\TagRepository::class,',
        'users'      => '\'UserRepository\'       => SecTheater\\Jarvis\\Facades\\UserRepository::class,',

    ];
    private $filesystem;

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
        $middleware_contents = $this->filesystem->get(base_path('app/Http/Kernel.php'));
        if (false === strpos($middleware_contents, '\\SecTheater\\Jarvis\\Http\\Middleware\\JarvisMiddleware::class')) {
            $contents = str_replace('protected $routeMiddleware = [', 'protected $routeMiddleware = [
		\'Jarvis\' => \\SecTheater\\Jarvis\\Http\\Middleware\\JarvisMiddleware::class,', $middleware_contents);
            $this->filesystem->put(base_path('app/Http/Kernel.php'), $contents);
        }
        foreach ($this->aliases as $name => $alias) {
            if ($name == 'Jarvis' || $name == 'roles' || $name == 'users' || $name == 'reminders') {
                $app_content = $this->filesystem->get(base_path('config/app.php'));
                if (false === strpos($app_content, $alias)) {
                    $contents = str_replace('\'aliases\' => [', "'aliases' => [ \n $alias", $app_content);
                    $this->filesystem->put(base_path('config/app.php'), $contents);
                }
                continue;
            }

            if (config('jarvis.'.$name.'.register')) {
                $app_content = $this->filesystem->get(base_path('config/app.php'));

                if (false === strpos($app_content, $alias)) {
                    $contents = str_replace('\'aliases\' => [', "'aliases' => [ \n \t \t $alias", $app_content);
                    $this->filesystem->put(base_path('config/app.php'), $contents);
                }
            }
        }
        $routes_contents = $this->filesystem->get(base_path('routes/web.php'));
        if (false === strpos($routes_contents, 'Jarvis::Routes()')) {
            if (strpos($routes_contents, 'Auth::routes()')) {
                str_replace('Auth::routes()', '', $routes_contents);
            }
            $this->filesystem->append(
                base_path('routes/web.php'),
                "\n\n Jarvis::Routes();"
            );
        }
        $this->call('vendor:publish', ['--provider' => JarvisServiceProvider::class]);
        $name = posix_getpwuid(posix_geteuid())['name'];
        $time = (\Carbon\Carbon::now()->format('A') === 'AM') ? 'Morning' : 'Evening';
        $this->info("Good $time $name , Hopefully you fill up this survey to designate the features within your application");
        $value = $this->choice('Does Your Application contain of Comments,Replies,Posts,Authentication,Tags & Likes ? ', ['Yes', 'No']);
        if ($value === 'No') {
            $bar = $this->output->createProgressBar(count(config('jarvis')));
            $bar->setFormat('[<fg=magenta>%bar%</>]');
            if ($this->confirm('Will You use our default routes ?')) {
                config(['jarvis.routes.register' => true]);
            } else {
                config(['jarvis.routes.register' => false]);
            }
            $bar->advance();

            if ($this->confirm('Does Your Application contain of comments system ?')) {
                config(['jarvis.comments.register' => true]);
            } else {
                config(['jarvis.comments.register' => false]);
            }
            $bar->advance();
            if ($this->confirm('Does Your Application contain of replies system ?')) {
                config(['jarvis.replies.register' => true]);
            } else {
                config(['jarvis.replies.register' => false]);
            }
            $bar->advance();
            if (config('jarvis.comments.register') && config('jarvis.replies.register')) {
                if ($this->confirm('Does Your Application need to approve comments,replies?')) {
                    config(['jarvis.replies.approve' => true, 'jarvis.comments.approve' => true]);
                } else {
                    config(['jarvis.replies.approve' => false, 'jarvis.comments.approve' => false]);
                }
                $bar->advance();
            } else {
                if (config('jarvis.comments.register')) {
                    if ($this->confirm('Does Your Application need to approve comments ?')) {
                        config(['jarvis.comments.approve' => true]);
                    } else {
                        config(['jarvis.comments.approve' => false]);
                    }
                    $bar->advance();
                }
                if (config('jarvis.replies.register')) {
                    if ($this->confirm('Does Your Application need to approve replies ?')) {
                        config(['jarvis.replies.approve' => true]);
                    } else {
                        config(['jarvis.replies.approve' => false]);
                    }
                    $bar->advance();
                }
            }
            if ($this->confirm('Does Your Application Need to Activate the user account  ?')) {
                config(['jarvis.activation.register' => true]);
                $bar->advance();
                $value = (int) $this->ask('How much time does the token need to be expired in days ?');
                if (is_int($value) && $vale >= 1) {
                    config(['jarvis.activation.expiration' => $value]);
                } else {
                    $this->info('The value is not an integer, default one is set instead.');
                }
                $bar->advance();
            } else {
                config(['jarvis.activation.register' => false]);
            }
            $bar->advance();

            $value = (int) $this->ask('How much time does the reminder token need to be expired in days ?');
            if (is_int($value) && $value >= 1) {
                config(['jarvis.reminder.expiration' => $value]);
            } else {
                $this->info('The value is not a positive integer, default one is set instead.');
            }
            $bar->advance();
            if ($this->confirm('Does Your Application Contain of Posting')) {
                config(['jarvis.posts.register' => true]);
                $bar->advance();

                if ($this->confirm('Do posts need to be approved ? ')) {
                    config(['jarvis.posts.approve' => true]);
                } else {
                    config(['jarvis.posts.approve' => false]);
                }
                $bar->advance();
            } else {
                config(['jarivs.posts.register' => false]);
            }
            $bar->finish();
            $this->info('These settings are set, feel free to modify them . Settings are located in config/jarvis.php');
        } else {
            $this->info('The default settings are set, feel free to modify them . Settings are located in config/jarvis.php');
        }
    }
}
