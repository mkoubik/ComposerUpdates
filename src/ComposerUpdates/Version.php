<?php

namespace ComposerUpdates;

use Composer\Package\PackageInterface;

class Version
{
	protected $version;

	protected $prettyVersion;

	public function __construct(PackageInterface $package)
	{
		$this->version = $package->getVersion();
		$this->prettyVersion = $package->getPrettyVersion();
	}

	public function isGreaterThan(Version $that)
	{
		return $this->version > $that->version;
	}

	public function __toString()
	{
		return $this->prettyVersion;
	}
}
