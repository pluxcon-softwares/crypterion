<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;


class RequirementController extends Controller
{

	public function __construct()
	{
	}

	/**
	 * Get requirement data
	 *
	 * @return array
	 */
	public function data()
	{
		return [
			'core'        => $this->getCoreRequirements(),
			'permissions' => $this->getPermissionRequirements(),
			'php'         => $this->getPhpRequirements(),
		];
	}

	/**
	 * Get permission requirements
	 *
	 * @return \Illuminate\Support\Collection
	 */
	protected function getPermissionRequirements()
	{
		$paths = config('installer.permissions');
		return collect($paths)->map(function ($data, $path) {
			$fullPath = base_path($path);
			$status = $this->checkPermission($fullPath, $data['recursive']);
			return [
				'name'      => $path,
				'recursive' => $data['recursive'],
				'status'    => $status,
			];
		})->values()->toArray();
	}

	/**
	 * Recursively check path writable permission
	 *
	 * @param $path
	 * @param bool $recursive
	 * @return bool
	 */
	protected function checkPermission($path, $recursive = true)
	{
		if ($recursive && is_dir($path)) {
			if (is_writable($path)) {
				$objects = scandir($path);
				foreach ($objects as $object) {
					if ($object != "." && $object != "..") {
						$nextPath = $path . "/" . $object;

						if (!$this->checkPermission($nextPath)) {
							return false;
						} else {
							continue;
						}
					}
				}
				return true;
			} else {
				return false;
			}
		} else {
			return is_writable($path);
		}
	}

	/**
	 * Get php requirements
	 *
	 * @return \Illuminate\Support\Collection
	 */
	protected function getPhpRequirements()
	{
		$requirements = config('installer.requirements.php');
		return collect($requirements)->map(function ($extension) {
			return [
				'status' => extension_loaded($extension),
				'name'   => $extension,
			];
		})->values()->toArray();
	}

	/**
	 * Get core requirements body
	 *
	 * @return array
	 */
	protected function getCoreRequirements()
	{
		$requirements = collect();
		$requirements->push($this->getCorePhpRequirement());
		return $requirements->toArray();
	}

	/**
	 * Get core php requirements
	 *
	 * @return array
	 */
	protected function getCorePhpRequirement()
	{
		$required = config('installer.core.minPhpVersion');
		$current = $this->getCurrentPhpVersion();
		return [
			'name'     => 'PHP Version',
			'required' => $required,
			'status'   => version_compare($current, $required, '>='),
			'current'  => $current,
		];
	}

	/**
	 * Get current php version
	 *
	 * @return mixed
	 */
	protected function getCurrentPhpVersion()
	{
		preg_match("#^\d+(\.\d+)*#", PHP_VERSION, $match);
		return $match[0];
	}
}
