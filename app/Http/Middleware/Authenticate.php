<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory as Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Authenticate extends Middleware
{
	/**
	 * VerifyLicense constructor.
	 *
	 * @param Auth $auth
	 */
	public function __construct(Auth $auth)
	{
		parent::__construct($auth);
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @param string[] ...$guards
	 * @return mixed
	 *
	 * @throws AuthenticationException
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function handle($request, Closure $next, ...$guards)
	{
		$this->authenticate($request, $guards);
		$user = $this->authenticatedUser();

		if (
			$this->auth->guard('api')->check() &&
			!$this->isTwoFactorVerificationRoute($request)
		) {
			if ($user->shouldVerifyTwoFactorForAuthentication()) {
				if (!$user->twoFactorVerifiedViaApi()) {
					$message = trans('auth.two_factor_required');
					throw new HttpException(419, $message);
				} else {
					$user->setTwoFactorApiVerification();
				}
			}
		}

		$deactivation = $user->inactive_until;
		if ($deactivation && $deactivation > now()) {
			$message = trans('auth.deactivated', [
				'date' => $deactivation->toFormattedDateString()
			]);

			throw new AuthenticationException($message, $guards);
		}

		return $next($request);
	}

	/**
	 * Get authenticated user
	 *
	 * @return User|Authenticatable
	 */
	protected function authenticatedUser()
	{
		return $this->auth->user();
	}

	/**
	 * Check if request is on route for two-factor verification
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return bool
	 */
	protected function isTwoFactorVerificationRoute($request)
	{
		$pattern = "*two-factor*";
		return $request->fullUrlIs($pattern) ||
			$request->is($pattern);
	}

	/**
	 * Get the path the user should be redirected to when they are not authenticated.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return string
	 */
	protected function redirectTo($request)
	{
		if (!$request->expectsJson()) {
			return url('/');
		}
	}
}
