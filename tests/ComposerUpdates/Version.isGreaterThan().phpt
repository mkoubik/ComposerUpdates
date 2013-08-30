<?php

use Composer\Package\Package;
use ComposerUpdates\Version;
use ComposerUpdates\NullVersion;

require __DIR__ . '/bootstrap.php';

$v09 = new Version(new Package('test', '0.9.0.0', 'v0.9.0'));
$v1 = new Version(new Package('test', '1.0.0.0', 'v1.0.0'));
$null = new NullVersion();

Assert::true($v1->isGreaterThan($v09));
Assert::false($v09->isGreaterThan($v1));
Assert::false($v09->isGreaterThan($v09));

Assert::true($v09->isGreaterThan($null));
Assert::true($v1->isGreaterThan($null));
Assert::false($null->isGreaterThan($v09));
Assert::false($null->isGreaterThan($v1));
