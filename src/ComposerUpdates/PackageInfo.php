<?php

namespace ComposerUpdates;

class PackageInfo
{
	private $name;

	/** @var Version */
	private $installedVersion;
	
	/** @var Version[] */
	private $newVersions;

	public function __construct($name, Version $installedVersion, array $newVersions)
	{
		$this->name = $name;
		$this->installedVersion = $installedVersion;
		$this->newVersions = $newVersions;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getInstalledVersion()
	{
		return $this->installedVersion;
	}

	public function isUpdateAvailable()
	{
		return count($this->newVersions) > 0;
	}

	public function getAvailableVersion()
	{
		$max = new NullVersion();
		foreach ($this->newVersions as $version) {
			if ($version->isGreaterThan($max)) {
				$max = $version;
			}
		}
		return $max;
	}
}
