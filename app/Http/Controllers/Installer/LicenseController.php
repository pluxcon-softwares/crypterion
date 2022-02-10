<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\Providers\License;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LicenseController extends Controller
{

	public function __construct()
	{

	}



	/**
	 * Verify license
	 *
	 * @param Request $request
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function verify(Request $request)
	{
		$this->validate($request, [
			'code'  => 'required|string',
			'email' => 'required|email',
		]);
	}
}
