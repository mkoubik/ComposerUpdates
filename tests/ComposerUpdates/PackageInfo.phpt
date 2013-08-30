<?php

use Composer\Package\Package;
use ComposerUpdates\PackageInfo;
use ComposerUpdates\Version;

require __DIR__ . '/bootstrap.php';

test(function() {
	$v2_0_10 = createVersion('nette/nette', '2.0.10.0', 'v2.0.10');
	$v2_0_11 = createVersion('nette/nette', '2.0.11.0', 'v2.0.11');
	$v2_0_12 = createVersion('nette/nette', '2.0.12.0', 'v2.0.12');
	$info = new PackageInfo('nette/nette', $v2_0_10, array($v2_0_11, $v2_0_12));

	Assert::true($info->isUpdateAvailable());
	Assert::equal('v2.0.12', (string) $info->getAvailableVersion());
});

test(function() {
	$v2_0_12 = createVersion('nette/nette', '2.0.12.0', 'v2.0.12');
	$info = new PackageInfo('nette/nette', $v2_0_12, array());

	Assert::false($info->isUpdateAvailable());
});

function createVersion($name, $version, $prettyVersion) {
	return new Version(new Package($name, $version, $prettyVersion));
}
