<?php

namespace SecTheater\Jarvis\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\DetectsApplicationNamespace;

class RegisterAuthorizationCommand extends Command
{
    use DetectsApplicationNamespace;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sectheater:register-authorization';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setting Jarvis Authorization Policy & Gates.';
    private $filesystem;
    protected $permissions = [];
    protected $views = [
        'stubs/AuthyServiceProvider.stub' => 'Providers/AuthyServiceProvider.php',
    ];
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
        $this->info('Gathering Information of Roles');
        $permissions = \RoleRepository::pluck('permissions')->collapse();
        if(!count($permissions) ){
            return $this->error('Roles are not set yet !');
        }
        foreach ($permissions as $key => $value) {
            $key = explode('-',$key);
            $this->permissions[str_plural($key[1])][] = $key[0];
        }
      $this->exportViews();
   }
    protected function exportViews()
    {
        $this->info('Registering Policies & Gates !');
        foreach ($this->views as $key => $view) {

            $originalContent = $this->filesystem->get(__DIR__ .DIRECTORY_SEPARATOR. $key);
            $content = $this->appendNamespaces($originalContent);
            $content = $this->appendPolicies($content,$this->permissions);
            $content = $this->appendGates($content,array_keys($this->permissions));
            foreach ($this->permissions as $permission => $value) {
                $model   = str_singular(ucfirst($permission));
                $content = $this->replaceNamespaces($model,$content);
                $content = $this->replacePolicies($model,$content);
                $content = $this->replaceArraySegements($model,$content);
                $content = $this->replaceGates($model,$content,$value);
                $this->generatePolicyStub($model,$value);

            }
            $content = str_replace('use App\DummyModel;','',$content);
            $content = str_replace('use App\Policies\DummyPolicy;','',$content);
            $this->filesystem->put(__DIR__ . DIRECTORY_SEPARATOR . $key, $content);
            copy(
                __DIR__.DIRECTORY_SEPARATOR . $key,
                app_path($view)
            );
            $this->filesystem->put(__DIR__ . DIRECTORY_SEPARATOR . $key , $originalContent);
        }
        $content = $this->filesystem->get(config_path('app.php'));
        if (!strpos($content, 'App\Providers\AuthyServiceProvider::class')) {
            $this->info('Registering Authy Service Provider ');
            $content = str_replace('App\Providers\AuthServiceProvider::class,',"App\Providers\AuthServiceProvider::class,\n\t\t\t\tApp\Providers\AuthyServiceProvider::class,",$content);
            $this->filesystem->put(config_path('app.php'),$content);
        }else{
            $this->info('It Seems that Authy Service Provider has been registered earlier.');
        }
    }
    protected function appendNamespaces($content)
    {
        return str_replace("namespace App\Providers;","namespace App\Providers;\n" . str_repeat("use App\DummyModel;\nuse App\Policies\DummyPolicy;", count($this->permissions)),$content);

    }
    protected function appendPolicies($content,$policies)
    {
        return str_replace('App\DummyModel::class => App\Policies\DummyPolicy::class',str_repeat("App\DummyModel::class => App\Policies\DummyPolicy::class\t\t",count($policies)),$content);
    }
    protected function appendGates($content,$policies)
    {
        $permissions =  \RoleRepository::pluck('permissions')->collapse();
        return str_replace('Gate::define(\'dummy-access\',\'\App\Policies\DummyPolicy@DummyAction\');',str_repeat("Gate::define(\'dummy-access\',\'\App\Policies\DummyPolicy@DummyAction\');\n\t\t",$permissions->count()),$content);
    }
    protected function replaceNamespaces($model,$content)
    {
        return str_replace_first("use App\DummyModel;",'use SecTheater\Jarvis\\'. $model .'\Eloquent' . $model . ";",$content);
    }
    protected function replacePolicies($model,$content)
    {
        return str_replace_first("use App\Policies\DummyPolicy;","use App\Policies\\{$model}Policy;\n",$content);
    }
    protected function replaceArraySegements($model,$content)
    {
       return  str_replace_first("App\DummyModel::class => App\Policies\DummyPolicy::class","SecTheater\Jarvis\\$model\Eloquent{$model}::class => App\Policies\\{$model}Policy::class,\n",$content);
    }
    protected function replaceGates($model,$content,$permissions)
    {
        foreach($permissions as $permission){
            $access = $permission . "-". lcfirst($model);
            $content = str_replace_first("Gate::define(\'dummy-access\',\'\App\Policies\DummyPolicy@DummyAction\');\n","Gate::define('$access','\App\Policies\\{$model}Policy@$permission');\n",$content);
        }
        return $content;
    }
    protected function generatePolicyStub($model,$permissions)
    {
        $originalContent = $content = $this->filesystem->get(__DIR__ . DIRECTORY_SEPARATOR . 'stubs/Policy.stub');
        if (str_contains($model, 'User')) {
            str_replace('use NamespacedDummyModel;' ,'',$originalContent);
        }else{
            $content = str_replace('use NamespacedDummyModel;','use SecTheater\Jarvis\\' . $model . '\Eloquent'.$model. ';',$originalContent);
        }
       if(in_array('approve',$permissions)){
            $content = str_replace('use HandlesAuthorization;',"use HandlesAuthorization;\n\tpublic function approve(User \$user, Eloquent". ucfirst($model) . " \${$model}) {\n\t\treturn \$user->hasRole('approve-". lcfirst($model)."') || \$user->id == \$". lcfirst($model)."->user_id;\n\t}",$content);
       }
        $content = str_replace('DummyClass',"{$model}Policy",$content);
        $content = str_replace('DummyModel','Eloquent'.$model,$content);
        $content = str_replace('dummyModel' , lcfirst($model) , $content);
        $content = str_replace('-dummy','-'.lcfirst($model),$content);
        $content = str_replace('$dummy','$' .lcfirst($model),$content);
       if(!\File::exists(app_path('Policies/' . "{$model}Policy.php"))){
            \File::makeDirectory(app_path('Policies'),0755,false,true);
       }
        $this->filesystem->put(__DIR__ . DIRECTORY_SEPARATOR . 'stubs/Policy.stub',$content);
        copy(
                __DIR__.DIRECTORY_SEPARATOR . 'stubs/Policy.stub',
                app_path('Policies/' . "{$model}Policy.php")
        );
        $this->filesystem->put(__DIR__ . DIRECTORY_SEPARATOR . 'stubs/Policy.stub',$originalContent);
    }
}
