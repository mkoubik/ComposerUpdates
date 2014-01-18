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
		return $this->getPackagesFromRequires($requires);
	}

	private function getPackagesFromRequires(array $requires)
	{
		$installedRepo = $this->initializer->getInstalledRepository();
		$pool = $this->initializer->getPackagePool();

		$installedVersions = array();
		foreach ($installedRepo->getPackages() as $package) {
			$installedVersions[$package->getName()] = new Version($package);
		}

		$packages = array();

		foreach ($requires as $link) {
			$name = $link->getTarget();
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
			
			$packages[] = new PackageInfo($name, $installedVersions[$name], $compatibleUpdates, $incompatibleUpdates);
		}

		return $packages;
	}
}
