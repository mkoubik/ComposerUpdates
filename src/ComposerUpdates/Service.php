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
			$provides = $pool->whatProvides($name, $link->getConstraint());
			$versions = array_map(function ($package) {
				return new Version($package);
			}, $provides);
			$currentVersion = isset($installedVersions[$name]) ? $installedVersions[$name] : new NullVersion();
			$newVersions = array_filter($versions, function($version) use ($currentVersion) {
				return $version->isGreaterThan($currentVersion);
			});
			$packages[] = new PackageInfo($name, $installedVersions[$name], $newVersions);
		}

		return $packages;
	}
}
