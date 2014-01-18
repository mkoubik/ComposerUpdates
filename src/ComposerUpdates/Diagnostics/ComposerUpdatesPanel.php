<?php

namespace ComposerUpdates\Diagnostics;

use Nette;
use ComposerUpdates;

class ComposerUpdatesPanel extends Nette\Object implements Nette\Diagnostics\IBarPanel
{
	/** @var ComposerUpdates\PackageInfo[] */
	private $packages;

	public function __construct(ComposerUpdates\Service $service)
	{
		$this->packages = $service->getPackages();
	}

	/**
	 * Html code for DebuggerBar Tab.
	 * @return string
	 */
	public function getTab()
	{
		if (empty($this->packages)) {
			return;
		}
		
		$updates = array_filter($this->packages, function(ComposerUpdates\PackageInfo $package) {
			return $package->isUpdateAvailable();
		});

		return self::render(__DIR__ . '/templates/tab.phtml', array(
			'updates' => $updates,
		));
	}

	/**
	 * Html code for DebuggerBar Panel.
	 * @return string
	 */
	public function getPanel()
	{
		if (empty($this->packages)) {
			return;
		}
		
		uasort($this->packages, function (ComposerUpdates\PackageInfo $package1, ComposerUpdates\PackageInfo $package2) {
			$update1 = $package1->isUpdateAvailable();
			$update2 = $package2->isUpdateAvailable();
			return $update1 !== $update2 ? $update1 < $update2 : $package1->getName() > $package2->getName();
		});
		
		return self::render(__DIR__ . '/templates/panel.phtml', array(
			'packages' => $this->packages,
		));
	}

	/**
	 * @param string $file
	 * @param array $vars
	 * @return string
	 */
	public static function render($file, $vars)
	{
		ob_start();
		Nette\Utils\LimitedScope::load(str_replace('/', DIRECTORY_SEPARATOR, $file), $vars);
		return ob_get_clean();
	}
}
