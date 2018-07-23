<?php

if (!function_exists('package_path')) {
    function package_path($path = null)
    {
        return base_path('vendor/sectheater/laravel-jarvis/src').DIRECTORY_SEPARATOR.ltrim($path, '/');
    }
}
if (!function_exists('user')) {
    function user($guard = null)
    {
        return auth()->guard($guard)->check() ? auth()->guard($guard)->user() : null;
    }
}
if (!function_exists('getUser')) {
    function getUser(array $condition)
    {
        if ((count($condition) === 1) && array_key_exists('id', $condition)) {
            return \UserRepository::find($condition['id']);
        }

        return \UserRepository::findBy($condition);
    }
}

if (!function_exists('model_exists')) {
    function model_exists($name)
    {
        return File::exists(str_replace('\\', DIRECTORY_SEPARATOR, base_path(lcfirst(ltrim(config('jarvis.models.namespace'), '\\'))).ucfirst($name).'.php'));
    }
}
if (!function_exists('package_version')) {
    function package_version($packageName)
    {
        $file = base_path('composer.lock');
        $packages = json_decode(file_get_contents($file), true)['packages'];
        foreach ($packages as $package) {
            if (explode('/', $package['name'])[1] == $packageName) {
                return $package['version'];
            }
        }

        throw new \Exception('Package Does not exist', 500);
    }
}
if (!function_exists('repository')) {
    function repository($name)
    {
        $models = config('jarvis.models.package');
        $name = str_replace('Repository', '', $name);
        $model = model($name);
        if (!count(config('jarvis.repositories.user')) || !in_array($name, config('jarvis.repositories.user'))) {
            if (!array_key_exists($name, $models)) {
                throw new \SecTheater\Jarvis\Helpers\HelperException("Repository $name Does not exist", 500);
            }
            $repository = '\SecTheater\Jarvis\\'.ucfirst($name).'\\'.ucfirst($name).'Repository';
        } else {
            $repository = '\\'.trim(config('jarvis.repositories.namespace'), '\\').'\\'.ucfirst($name).'Repository';
        }

        return new $repository($model);
    }
}
if (!function_exists('jarvis_model_exists')) {
    function jarvis_model_exists($name)
    {
        if (str_contains($name, 'Eloquent')) {
            return File::exists(__DIR__.'/../'.ucfirst(str_replace('Eloquent', '', $name)).'/'.ucfirst($name).'.php');
        }

        return File::exists(__DIR__.'/../'.ucfirst($name).'/'.'Eloquent'.ucfirst($name).'.php');
    }
}
if (!function_exists('model')) {
    function model(string $name, array $attributes = [])
    {
        $name = ucfirst(str_replace('Eloquent', '', $name));
        if (File::exists(str_replace('\\', DIRECTORY_SEPARATOR, base_path(lcfirst(ltrim(config('jarvis.models.namespace'), '\\'))).$name.'.php'))) {
            if (array_key_exists(lcfirst($name), config('jarvis.models.user'))) {
                $model = config('jarvis.models.user')[lcfirst($name)];
            } elseif (model_exists($name)) {
                $model = config('jarvis.models.namespace').$name;
            } else {
                $model = config('jarvis.models.package')[lcfirst($name)];
            }

            return new $model($attributes);
        } elseif (File::exists(__DIR__.'/../'.$name.'/'.'Eloquent'.$name.'.php')) {
            $model = '\\SecTheater\\Jarvis\\'.$name.'\\'.'Eloquent'.$name;

            return new $model($attributes);
        }

        throw new \SecTheater\Jarvis\Helpers\HelperException("Model $name Does not exist", 500);
    }
}
if (!function_exists('Jarvis')) {
    function Jarvis()
    {
        if (count(config('jarvis.models.user'))) {
            foreach (config('jarvis.models.user') as $key => $value) {
                $models[$key] = $value;
            }
        }
        foreach (config('jarvis.models.package') as $key => $value) {
            if ($key == 'user' || $key == 'role' || $key == 'reminder' || config('jarvis.'.str_plural($key).'.register')) {
                if (model_exists($key)) {
                    $models[$key] = config('jarvis.models.namespace').ucfirst($key);
                } else {
                    $models[$key] = $value;
                }
            }
        }
        foreach ($models as $key => $value) {
            $class = ucfirst($key).'Repository';
            $$key = new $class(new $value());
        }

        return new \SecTheater\Jarvis\Jarvis(
            $user,
            $activation ?? null,
            $role,
            $post ?? null,
            $comment ?? null,
            $reply ?? null,
            $reminder,
            $like ?? null,
            $tag ?? null
        );
    }
}
