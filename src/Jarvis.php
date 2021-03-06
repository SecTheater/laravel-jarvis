<?php

namespace SecTheater\Jarvis;

use ActivationRepository;
use App\User;
use Artify\Artify\Traits\Roles\Roles;
use CommentRepository;
use Hash;
use Illuminate\Support\Facades\Route;
use LikeRepository;
use PostRepository;
use ReminderRepository;
use ReplyRepository;
use RoleRepository;
use Schema;
use SecTheater\Jarvis\Activation\ActivationException;
use SecTheater\Jarvis\Exceptions\ConfigException;
use SecTheater\Jarvis\Interfaces\UserInterface;
use SecTheater\Jarvis\Routes\RouteRegistrar;
use SecTheater\Jarvis\User\EloquentUser;
use TagRepository;
use UserRepository;

class Jarvis
{
    use Roles;
    protected $user;
    protected $activation;
    protected $role;
    protected $post;
    protected $comment;
    protected $reply;
    protected $reminder;
    protected $like;
    protected $tag;

    public function __construct(UserRepository $user, ActivationRepository $activation = null, RoleRepository $role, PostRepository $post = null, CommentRepository $comment = null, ReplyRepository $reply = null, ReminderRepository $reminder, LikeRepository $like = null, TagRepository $tag = null)
    {
        $this->user = $user;
        $this->activation = $activation;
        $this->role = $role;
        $this->post = $post;
        $this->comment = $comment;
        $this->reply = $reply;
        $this->reminder = $reminder;
        $this->like = $like;
        $this->tag = $tag;
    }

    /**
     * [changePassword Changes Password Of The user provided.].
     *
     * @param UserInterface $user
     * @param string        $old_password
     * @param string        $new_pasword
     *
     * @return [bool]
     */
    public function changePassword(string $old_password, string $new_pasword, UserInterface $user = null):bool
    {
        if (!isset($user)) {
            $user = auth()->user();
        }
        if (Hash::check($old_password, $user->password)) {
            $user->password = bcrypt($new_pasword);

            return $user->save();
        }

        return false;
    }

    public function registerWithRole(array $data, $slug = 'user', $activation = false)
    {
        if ($user = $this->register($data, $activation)) {
            $role = \RoleRepository::findRoleBySlug($slug);
            $role->users()->attach($user);

            return $user;
        }

        return false;
    }

    public function register(array $data, bool $activation = false)
    {
        if (!config('jarvis.activations.register') && $activation) {
            throw new ActivationException('Activation process is not enabled within your project.');
        }
        if ($this->checkColumns($data)) {
            $data['password'] = bcrypt($data['password']);
            $user = \UserRepository::create($data);
            if (config('jarvis.activations.register')) {
                \ActivationRepository::generateToken($user);
                if ($activation && ($EloquentActivation = \ActivationRepository::hasToken($user))) {
                    $EloquentActivation->update([
                            'token'        => null,
                            'completed'    => true,
                            'completed_at' => date('Y-m-d H:i:s'),
                        ]);
                }
            }

            return $user;
        }

        return false;
    }

    public function registerAndActivate(array $data)
    {
        return $this->register($data, true);
    }

    public function filterLoginName($data)
    {
        $loginNames = EloquentUser::$loginNames;
        foreach ($data as $key => $value) {
            if (in_array($key, $loginNames)) {
                $loginName = filter_var($value, FILTER_VALIDATE_EMAIL) ? $loginNames['email'] : $loginNames['string'];
                array_forget($data, [$loginNames['email'], $loginNames['string']]);
                $data[$loginName] = $value;
            }
        }

        return $data;
    }

    public function loginById($id, $remember = false)
    {
        if ($id instanceof UserInterface) {
            $user = $id;
        }

        if (!is_int($id) && !is_object($id)) {
            return false;
        }
        $user = \Auth::loginUsingId($user->id ?? $id, $remember);
        if (config('jarvis.activations.register')) {
            if (\ActivationRepository::completed($user)) {
                return $this->user();
            } else {
                $this->logout();

                throw new ActivationException('User is not activated yet.', 404);
            }
        }

        return $user;
    }

    public function login(array $data, $remember = false, $check = true)
    {
        if ($data = $this->filterLoginName($data)) {
            if (auth()->attempt($data, $remember)) {
                if ($check && config('jarvis.activations.register')) {
                    if (auth()->user()->activation->count() && auth()->user()->activation->first()->completed === true) {
                        return auth()->user();
                    }

                    $this->logout();

                    throw new ActivationException('User is not activated yet.', 401);
                }

                return auth()->user();
            }
        }
        if (auth()->check()) {
            auth()->logout();
        }

        return false;
    }

    public function loginAndRemember(array $data, $check = true):bool
    {
        return $this->login($data, true, $check);
    }

    public function hasRole($role)
    {
        return $this->user()->hasRole($role);
    }

    public function inRole($slug)
    {
        return $this->user()->inRole($slug);
    }

    public function hasAnyRole($roles)
    {
        return $this->user()->hasAnyRole($roles);
    }

    public function hasAllRole($roles)
    {
        return $this->user()->hasAllRole($roles);
    }

    public function check()
    {
        return $this->user();
    }

    public function logout($everywhere = false):bool
    {
        auth()->logout();
        if ($everywhere) {
            session()->flush();
        }

        return true;
    }

    public function forceLogin($data, $remember):bool
    {
        return $this->login($data, $remember, false);
    }

    public function checkColumns($columns)
    {
        return array_walk($columns, function ($column, $value) {
            if (!Schema::hasColumn('users', $value)) {
                throw new ConfigException('Invalid Column for table');
            }
        });
    }

    public function user()
    {
        return auth()->check() ? auth()->user() : null;
    }

    public function getUserById(int $id)
    {
        return \UserRepository::find($id);
    }

    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        $defaultOptions = [
            'namespace' => '\SecTheater\Jarvis\Http\Controllers',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function ($router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }

    public function approve($class)
    {
        if (config('jarvis.'.strtolower(str_plural(str_replace('Eloquent', '', class_basename($class)))).'.approve')) {
            return ($class->update(['approved' => true, 'approved_by' => auth()->user()->id, 'approved_at' => date('Y-m-d H:i:s')])) ?: false;
        }

        throw new ConfigException('Approval Process for '.str_plural(class_basename($class)).' Is not set !');
    }

    public function upgradeUser($permissions, UserInterface $user)
    {
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

    public function downgradeUser($permissions, UserInterface $user)
    {
        if (is_array($permissions)) {
            foreach ($permissions as $permission => $value) {
                $user->updatePermission($permission, $value, true);
            }

            return true;
        } elseif (is_string($permissions)) {
            return $user->updatePermission($permissions, false, true);
        }

        return false;
    }

    public function listUsers($activated = false, array $prevent = null, $type = 'any')
    {
        if (!config('jarvis.activations.register') && $activated == true) {
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

        return $users ?? null;
    }

    public function getUsers(bool $activated = false)
    {
        if (config('jarvis.activations.register')) {
            return User::whereHas(['activation' => function ($query) use ($activated) {
                $query->whereCompleted($activated);
            }])->get();
        }

        return User::all();
    }
}
