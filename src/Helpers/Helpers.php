<?php

use SecTheater\Jarvis\Activation\ActivationRepository;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Comment\CommentRepository;
use SecTheater\Jarvis\Comment\EloquentComment;
use SecTheater\Jarvis\Exceptions\HelperException;
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

if (!function_exists('user')) {
	function user($guard = null) {
		return auth()->guard($guard)->check() ? auth()->user() : null;
	}
}
if (!function_exists('getUser')) {
	function getUser(array $condition) {
		if ((count($condition) === 1) && array_key_exists('id', $condition)) {
			return \UserRepository::find($condition['id']);
		}

		return \UserRepository::findBy($condition);
	}
}

if (!function_exists('model_exists')) {
	function model_exists($name) {
		return File::exists(str_replace('\\', DIRECTORY_SEPARATOR, base_path(lcfirst(ltrim(config('jarvis.models.namespace'),'\\'))) . ucfirst($name) . '.php'));
	}
}
if (!function_exists('package_version')) {
	function package_version($packageName) {
		$file = base_path('composer.lock');
		$packages = json_decode(file_get_contents($file), true)['packages'];
		foreach ($packages as $package) {
			if ($package['name'] == $packageName) {
				return $package['version'];
			}
		}

		throw new \Exception('Package Does not exist', 500);
	}
}
if (!function_exists('repository')) {
	function repository($name){
		$models = config('jarvis.models.package');
		$model = model($name);
		if(!count(config('jarvis.repositories.user')) || !in_array($name, config('jarvis.repositories.user'))){
			if(!array_key_exists($name, $models)){
				throw new HelperException('Call To Undefined Repository');
			}
			$repository = '\SecTheater\Jarvis\\' . ucfirst($name) . '\\' . ucfirst($name) . 'Repository';
		}else{
			$repository = '\\' . trim(config('jarvis.repositories.namespace'),'\\') . '\\' . ucfirst($name) . 'Repository';
		}
		return new $repository($model);
	}
}
if (!function_exists('jarvis_model_exists')) {
	function jarvis_model_exists($name) {
		if (str_contains($name,'Eloquent')) {
			return File::exists(__DIR__ . '/../' . ucfirst(str_replace('Eloquent', '', $name)) . '/' . ucfirst($name) . '.php');
		}
		return File::exists(__DIR__ . '/../' . ucfirst($name) . '/' . 'Eloquent' . ucfirst($name) . '.php');
	}
}
if (!function_exists('model')) {
	function model(string $name, array $attributes = null) {
		if (File::exists(str_replace('\\', DIRECTORY_SEPARATOR, base_path(lcfirst(ltrim(config('jarvis.models.namespace'),'\\'))) . ucfirst($name) . '.php'))) {
			if (array_key_exists(lcfirst($name), config('jarvis.models.user'))) {
				$model = config('jarvis.models.namespace') . config('jarvis.models.user')[lcfirst($name)];
			}else if(model_exists($name)){
				$model = config('jarvis.models.namespace') . ucfirst($name);
			}else {
				$model = config('jarvis.models.package')[lcfirst($name)];
			}
			return new $model($attributes);
		} elseif (File::exists(__DIR__ . '/../' . str_replace('Eloquent', '', $name) . '/' . ucfirst($name) . '.php')) {
			$model = '\\SecTheater\\Jarvis\\' . ucfirst(str_replace('Eloquent', '', $name)) . '\\' . ucfirst($name);
			return new $model($attributes);
		}

		throw new HelperException("Model $name Does not exist", 500);
	}
}
if (!function_exists('Jarvis')) {
	function Jarvis() {
		$user = new UserRepository(new EloquentUser());
		$activation = new ActivationRepository(new EloquentActivation());
		$role = new RoleRepository(new EloquentRole());
		$post = new PostRepository(new EloquentPost());
		$comment = new CommentRepository(new EloquentComment());
		$reply = new ReplyRepository(new EloquentReply());
		$reminder = new ReminderRepository(new EloquentReminder());
		$like = new LikeRepository(new EloquentLike());
		$tag = new TagRepository(new EloquentTag());

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
	}
}
