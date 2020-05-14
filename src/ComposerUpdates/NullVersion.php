<?php

namespace ComposerUpdates;

class NullVersion extends Version
{
	public function __construct()
	{
		$this->version = -1;
		$this->prettyVersion = 'none';
	}
}
