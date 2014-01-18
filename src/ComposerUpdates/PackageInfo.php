<?php

namespace ComposerUpdates;

class PackageInfo
{
	const STATUS_NO_UPDATE = 0;
	const STATUS_INCOMPATIBLE_UPDATE = 1;
	const STATUS_COMPATIBLE_UPDATE = 2;

	/** @var string*/
	private $name;

	/** @var Version */
	private $installedVersion;

	/** @var Version[] */
	private $compatibleUpdates;

	/** @var Version[] */
	private $incompatibleUpdates;

	/** @var bool */
	private $devOnly;

	/**
	 * @param string $name
	 * @param Version $installedVersion
	 * @param Version[] $compatibleUpdates
	 * @param Version[] $incompatibleUpdates
	 * @param bool $devOnly	 
	 */
	public function __construct($name, Version $installedVersion, array $compatibleUpdates, array $incompatibleUpdates, $devOnly)
	{
		$this->name = $name;
		$this->installedVersion = $installedVersion;
		$this->compatibleUpdates = $compatibleUpdates;
		$this->incompatibleUpdates = $incompatibleUpdates;
		$this->devOnly = $devOnly;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return Version
	 */
	public function getInstalledVersion()
	{
		return $this->installedVersion;
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		if ($this->compatibleUpdates) {
			return self::STATUS_COMPATIBLE_UPDATE;
		} elseif ($this->incompatibleUpdates) {
			return self::STATUS_INCOMPATIBLE_UPDATE;
		} else {
			return self::STATUS_NO_UPDATE;
		}
	}

	/**
	 * @return bool
	 */
	public function isDevOnly()
	{
		return $this->devOnly;
	}

	/**
	 * @return bool
	 */
	public function isCompatibleUpdateAvailable()
	{
		return (bool) $this->compatibleUpdates;
	}

	/**
	 * @return bool
	 */
	public function isIncompatibleUpdateAvailable()
	{
		return (bool) $this->incompatibleUpdates;
	}

	/**
	 * @return Version
	 */
	public function getCompatibleUpdate()
	{
		$max = new NullVersion();
		foreach ($this->compatibleUpdates as $version) {
			if ($version->isGreaterThan($max)) {
				$max = $version;
			}
		}
		return $max;
	}

	/**
	 * @return Version
	 */
	public function getIncompatibleUpdate()
	{
		$max = new NullVersion();
		foreach ($this->incompatibleUpdates as $version) {
			if ($version->isGreaterThan($max)) {
				$max = $version;
			}
		}
		return $max;
	}
}
