<?php

namespace App\Http\Controllers;

use App\Helpers\RouteGenerator\RouteGenerator;
use Illuminate\Http\Request;

class InstallerController extends Controller
{
	/**
	 * @var RouteGenerator
	 */
	protected $routeManager;

	/**
	 * IndexController constructor.
	 *
	 * @param RouteGenerator $routeGenerator
	 */
	public function __construct(RouteGenerator $routeGenerator)
	{
		$this->routeManager = $routeGenerator;
	}

	/**
	 * Show installer index
	 *
	 * @param Request $request
	 * @return \Illuminate\View\View
	 */
	public function view()
	{
		$data = [
			'name'      => config('app.name'),
			'settings'  => [],
			'routeData' => $this->routeManager->generate('installer'),
			'csrfToken' => csrf_token(),
		];

		return view('installer', compact('data'));
	}
}
