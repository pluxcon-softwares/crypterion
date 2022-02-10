<?php

namespace App\Http\Middleware;

use Closure;

class CheckInstallation
{

	public function __construct()
	{

	}

	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @param null $method
	 * @return mixed
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function handle($request, Closure $next)
	{
		return $next($request);
	}
}
