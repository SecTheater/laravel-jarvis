<?php

namespace SecTheater\Jarvis\Http\Middleware;

use Cache;
use Closure;
use SecTheater\Jarvis\Exceptions\InsufficientPermissionsException;

class JarvisMiddleware {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null) {
		if (auth()->guard($guard)->user() === null) {
			throw new InsufficientPermissionsException('You are not logged in to access here', 401);

		}
		if (config('jarvis.user.check-if-online.check')) {
			$expiration = \Carbon\Carbon::now()->addMinutes(config('jarvis.user.check-if-online.expiration'));
			\Cache::put('user-is-online-'.\Jarvis::user()->id, true, $expiration);
		}
		$actions = request()->route()->getAction();
		$roles   = isset($actions['roles'])?$actions['roles']:null;
		if (auth()->guard($guard)->user()->hasAnyRole($roles) && $roles !== NULL) {
			return $next($request);
		}
		throw new InsufficientPermissionsException('Insufficient Permissions.', 401);

	}
}
