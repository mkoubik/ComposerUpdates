<?php

namespace ComposerUpdates\Diagnostics;

use Nette;
use ComposerUpdates;

class Panel extends Nette\Object implements Nette\Diagnostics\IBarPanel
{
	/** @var ComposerUpdates\PackageInfo[] */
	private $packages;

	private static $green = '<span style="display:inline-block;width:16px;height:16px;margin-top:3px;margin-bottom:-4px;border-radius:8px;background:#599B4A"></span>';
	private static $yellow = '<span style="display:inline-block;width:16px;height:16px;margin-top:3px;margin-bottom:-4px;border-radius:8px;background:#ffc029"></span>';

	private static $greenTab = '<span style="display:inline-block;width:16px;height:16px;border-radius:8px;background:#599B4A"></span>';
	private static $yellowTab = '<span style="display:inline-block;width:16px;height:16px;border-radius:8px;background:#ffc029"></span>';

	public function __construct(ComposerUpdates\Service $service)
	{
		$this->packages = $service->getPackages();
	}

	public function getTab()
	{
		if (empty($this->packages)) {
			return NULL;
		}

		$updates = array_filter($this->packages, function(ComposerUpdates\PackageInfo $package) {
			return $package->isUpdateAvailable();
		});

		if (count($updates) === 0) {
			return self::$green.'&nbsp;up to date';
		}

		return self::$yellow.'&nbsp;'
			.count($updates).' '.(count($updates) === 1 ? 'update' : 'updates');
	}

	public function getPanel()
	{
		if (empty($this->packages)) {
			return NULL;
		}

		$s = '<table>';

		foreach ($this->packages as $package) {
			$s .= '<tr><td>';
			if ($package->isUpdateAvailable()) {
				$s .= self::$yellowTab.' '.$package->getName().': '.$package->getInstalledVersion();
				$s .= ' ('.$package->getAvailableVersion().' available)';
			} else {
				$s .= self::$greenTab.' '.$package->getName().': '.$package->getInstalledVersion();
			}
			$s .= '</td></tr>';
		}

		$s .= '</table>';
		return $s;
	}

	public function register()
	{
		Nette\Diagnostics\Debugger::$bar->addPanel($this);
	}
}
