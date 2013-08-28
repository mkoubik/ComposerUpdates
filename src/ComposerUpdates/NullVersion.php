<?php

namespace ComposerUpdates;

use composer\Package\PackageInterface;

class NullVersion extends Version
{
	protected $version;

	protected $prettyVersion;

	public function __construct()
	{
		$this->version = -1;
	}
}
