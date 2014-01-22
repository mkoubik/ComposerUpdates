<?php

namespace ComposerUpdates;

class Service
{
	/** @var Initializer */
	private $initializer;

	public function __construct(Initializer $initializer)
	{
		$this->initializer = $initializer;
	}

	/**
	 * @return PackageInfo[]
	 */
	public function getPackages()
	{
		$requires = $this->initializer->getRequires();
		return $this->getPackagesFromRequires($requires);
	}

	/**
	 * @return PackageInfo[]
	 */
	public function getDevPackages()
	{
		$requires = $this->initializer->getDevRequires();
		return $this->getPackagesFromRequires($requires, TRUE);
	}

	private function getPackagesFromRequires(array $requires, $devOnly = FALSE)
	{
		$installedRepo = $this->initializer->getInstalledRepository();

		$installedVersions = array();
		foreach ($installedRepo->getPackages() as $package) {
			$installedVersions[$package->getName()] = new Version($package);
		}

		$pool = $this->initializer->getPackagePool();
		$packages = array();

		foreach ($requires as $link) {
			$name = $link->getTarget();
			if (strpos($name, '/') === FALSE) {
				continue;
			}
			$currentVersion = isset($installedVersions[$name]) ? $installedVersions[$name] : new NullVersion();

			$provides = $pool->whatProvides($name, $link->getConstraint());
			$versions = array_map(function ($package) {
				return new Version($package);
			}, $provides);
			$compatibleUpdates = array_filter($versions, function($version) use ($currentVersion) {
				return $version->isGreaterThan($currentVersion);
			});

			$provides = $pool->whatProvides($name);
			$versions = array_map(function ($package) {
				return new Version($package);
			}, $provides);
			$incompatibleUpdates = array_filter($versions, function($version) use ($currentVersion) {
				return $version->isGreaterThan($currentVersion);
			});

			$packages[] = new PackageInfo($name, $currentVersion, $compatibleUpdates, $incompatibleUpdates, $devOnly);
		}

		return $packages;
	}
}
