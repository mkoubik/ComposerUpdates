<?php

namespace ComposerUpdates;

use Composer\Package\PackageInterface;
use Icecave\SemVer;

class Version
{
	protected $version;

	protected $prettyVersion;

	/** @var SemVer\Version|NULL */
	protected $semver;

	public function __construct(PackageInterface $package)
	{
		$this->version = $package->getVersion();
		// v0.9.0-alpha5 => 0.9.0-alpha5
		$this->prettyVersion = preg_replace('/^v/', '', $package->getPrettyVersion());

		try {
			$this->semver = SemVer\Version::parse($this->prettyVersion);
		} catch (\InvalidArgumentException $e) {
			$this->semver = NULL;
		}
	}

	public function isGreaterThan(Version $that)
	{
		if ($that instanceof Version && $that->semver !== NULL && $this->semver !== NULL) {
			return $this->semver->isGreaterThan($that->semver);
		}
		return $this->version > $that->version;
	}

	public function __toString()
	{
		return $this->prettyVersion;
	}
}
