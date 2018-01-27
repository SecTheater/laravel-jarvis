<?php
namespace SecTheater\Jarvis;
use App\User;
use Hash;
use Schema;
use SecTheater\Jarvis\Activation\ActivationException;
use SecTheater\Jarvis\Activation\ActivationRepository;

use SecTheater\Jarvis\Comment\CommentRepository;

use SecTheater\Jarvis\Exceptions\ConfigException;
use SecTheater\Jarvis\Interfaces\RestrictionInterface;

use SecTheater\Jarvis\Like\LikeRepository;

use SecTheater\Jarvis\Post\PostRepository;

use SecTheater\Jarvis\Reminder\ReminderRepository;

use SecTheater\Jarvis\Reply\ReplyRepository;

use SecTheater\Jarvis\Role\RoleRepository;

use SecTheater\Jarvis\Tag\TagRepository;

use SecTheater\Jarvis\Traits\Roles\Roles;
use SecTheater\Jarvis\User\EloquentUser;
use SecTheater\Jarvis\User\UserRepository;

class Jarvis {
	use Roles;
	protected $user, $activation, $role, $post, $comment, $reply, $reminder, $like, $tag;
	public function __construct(UserRepository $user, ActivationRepository $activation = null, RoleRepository $role, PostRepository $post = null, CommentRepository $comment = null, ReplyRepository $reply = null, ReminderRepository $reminder, LikeRepository $like = null, TagRepository $tag = null) {
		$this->user       = $user;
		$this->activation = $activation;
		$this->role       = $role;
		$this->post       = $post;
		$this->comment    = $comment;
		$this->reply      = $reply;
		$this->reminder   = $reminder;
		$this->like       = $like;
		$this->tag        = $tag;
	}
	/**
	 * [changePassword Changes Password Of The user provided.]
	 * @param  RestrictionInterface $user
	 * @param  string        $old_password
	 * @param  string        $new_pasword
	 * @return [bool]
	 */
	function changePassword(string $old_password, string $new_pasword, RestrictionInterface $user = null):bool {
		if (!isset($user)) {
			$user = auth()->user();
		}
		if (Hash::check($old_password, $user->password)) {
			$user->password = bcrypt($new_pasword);
			return $user->save();
		}
		return false;
	}
	function registerWithRole(array $data, $slug = 'user', $activation = false) {
		if ($user = $this->register($data, $activation)) {
			$role = \RoleRepository::findRoleBySlug($slug);
			$role->users()->attach($user);
			return $user;
		}
		return false;
	}
	function register(array $data, $activation = false) {
		if (!config('jarvis.activation.register') && $activation == true) {
			throw new ActivationException('Activation process is not enabled within your project.');
		}
		if ($this->checkColumns($data)) {
			$data['password'] = bcrypt($data['password']);
			$user             = \UserRepository::create($data);
			if (config('jarvis.activation.register')) {
				\ActivationRepository::generateToken($user);
				if ($activation === true && ($EloquentActivation = \ActivationRepository::tokenExists($user))) {
					$EloquentActivation->update([
							'token'        => null,
							'completed'    => true,
							'completed_at' => date('Y-m-d H:i:s')
						]);
				}

			}
			return $user;
		}
		return false;
	}
	function registerAndActivate(array $data) {
		return $this->register($data, true);
	}
	function filterLoginName($data) {
		$loginNames = EloquentUser::$loginNames;
		foreach ($data as $key => $value) {
			if (in_array($key, $loginNames)) {
				$loginName = filter_var($value, FILTER_VALIDATE_EMAIL)?$loginNames['email']:$loginNames['string'];
				array_forget($data, [$loginNames['email'], $loginNames['string']]);
				$data[$loginName] = $value;
			}
		}
		return $data;

	}
	function loginById($id, $remember = false) {
		if ($id instanceof RestrictionInterface) {
			$user = $id;
		}

		if (!is_int($id) && !is_object($id)) {
			return false;
		}
		$user = \Auth::loginUsingId($user->id??$id, $remember);
		if (config('jarvis.activation.register')) {
			if ($user->activation()->exists() && $user->activation->first()->completed === true) {
				return true;
			} else {
				\Auth::logout();
				throw new ActivationException('User is not activated yet.', 404);
			}
		}
		return \Auth::loginUsingId($user->id??$id, $remember);

	}
	function login(array $data, $remember = false, $check = true):bool {
		if ($data = $this->filterLoginName($data)) {
			if (auth()->attempt($data, $remember)) {
				if ($check && config('jarvis.activation.register')) {
					if (auth()->user()->activation->count() && auth()->user()->activation->first()->completed === true) {
						return true;
					}
					return false;
				}
				return true;
			}
		}
		if (auth()->check()) {
			auth()   ->logout();
		}
		return false;
	}
	function loginAndRemember(array $data, $check = true):bool {
		return $this->login($data, true, $check);
	}
	function logout($everywhere = false):bool {
		auth()->logout();
		if ($everywhere) {
			session()->flush();

		}
		return true;
	}
	function forceLogin($data, $remember):bool {
		return $this->login($data, $remember, false);
	}
	function checkColumns($columns) {
		return array_walk($columns, function ($column, $value) {
				if (!Schema::hasColumn('users', $value)) {
					throw new ConfigException('Invalid Column for table');
				}
			});
	}
	function user() {
		return auth()->check()?auth()->user():null;
	}

	function getUserById(int $id) {
		return \UserRepository::find($id);
	}
	function Routes() {
		require_once __DIR__ .'/Routes/web.php';
	}
	function approve($class) {
		if ('jarvis.'.config(strtolower(str_plural(str_replace('Eloquent', '', class_basename($class))))).'.approve') {
			return ($class->update(['approved' => true, 'approved_by' => auth()->user()->id, 'approved_at' => date('Y-m-d H:i:s')]))?:false;

		}
		throw new ConfigException('Approval Process for '.str_plural(class_basename($class)).' Is not set !');
	}
	function upgradeUser($permissions, $id, array $prevent = null, $type = 'any') {
		if ($id instanceof RestrictionInterface) {
			$user = $id;
		} else {
			$user = $this->getUserById($id);
		}
		if (!$user) {
			return false;
		}
		if ($type !== 'any' && $type !== 'all') {
			throw new \Exception('Invalid Type, Type should be either all or any.', 404);
		}
		if (isset($prevent) && $user->hasAnyRole($prevent) && $type === 'any') {
			return false;
		} elseif (isset($prevent) && $user->hasAllRole($prevent) && $type === 'all') {
			return false;
		}
		if (is_array($permissions)) {
			foreach ($permissions as $permission => $value) {
				$user->updatePermission($permission, $value, true);
			}
			return true;
		} elseif (is_string($permissions)) {
			return $user->updatePermission($permissions, true, true);

		}

		return false;

	}
	function downgradeUser($permissions, $id, array $prevent = null, $type = 'any') {
		if ($id instanceof RestrictionInterface) {
			$user = $id;
		} else {
			$user = $this->getUserById($id);
		}
		if (!$user) {
			return false;
		}
		if ($type !== 'any' && $type !== 'all') {
			throw new \Exception('Invalid Type, Type should be either all or any.', 404);
		}
		if (isset($prevent) && $user->hasAnyRole($prevent) && $type === 'any') {
			return false;
		} elseif (isset($prevent) && $user->hasAllRole($prevent) && $type === 'all') {
			return false;
		}
		if (is_array($permissions)) {

			foreach ($permissions as $permission => $value) {

				$user->updatePermission($user->id, $permission, $value, true);

			}

			return true;

		} elseif (is_string($permissions)) {
			return $user->updatePermission($permissions, false, true);

		}
		return false;
	}
	function listUsers($activated = false, array $prevent = null, $type = 'any') {
		if (!config('jarvis.activation.register') && $activated == true) {
			throw new ActivationException('This Process is not enabled within your project.');
		}
		foreach ($this->getUsers($activated) as $activation) {
			if ($activation instanceof User) {
				$user = $activation;
			} else {
				$user = $activation->user;

			}
			if ($type !== 'any' && $type !== 'all') {
				throw new \Exception('Invalid Type, Type should be either all or any.', 404);
			}
			if (isset($prevent) && $user->hasAnyRole($prevent) && $type === 'any') {
				return false;
			} elseif (isset($prevent) && $user->hasAllRole($prevent) && $type === 'all') {
				return false;
			}

			if (!$prevent) {
				$users[] = $user;

			}
		}
		return $users??NULL;
	}
	function getUsers($activated = false) {
		if (config('jarvis.activation.register')) {
			return \ActivationRepository::with('user')->where('completed', $activated)->get();
		}
		return User::all();
	}

}