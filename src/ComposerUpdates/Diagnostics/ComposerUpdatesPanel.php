<?php

namespace ComposerUpdates\Diagnostics;

use Nette;
use ComposerUpdates;

class ComposerUpdatesPanel extends Nette\Object implements Nette\Diagnostics\IBarPanel
{
	/** @var ComposerUpdates\PackageInfo[] */
	private $packages;

	public function __construct($rootDir, ComposerUpdates\Service $service, Nette\Caching\IStorage $storage)
	{
		$cache = new Nette\Caching\Cache($storage, 'ComposerUpdates.panel');
		$this->packages = $cache->load($rootDir, function (& $dependencies) use ($rootDir, $service) {
			$dependencies = array(
				Nette\Caching\Cache::EXPIRATION => '1 hour',
				Nette\Caching\Cache::FILES => array(
					$rootDir . '/composer.json',
					$rootDir . '/composer.lock',
				),
			);
			return array_merge($service->getPackages(), $service->getDevPackages());
		});
	}

	/**
	 * Html code for DebuggerBar Tab.
	 * @return string
	 */
	public function getTab()
	{
		if (!$this->packages) {
			return;
		}

		$status = max(array_map(function(ComposerUpdates\PackageInfo $package) {
			return $package->getStatus();
		}, $this->packages));

		$updates = count(array_filter($this->packages, function(ComposerUpdates\PackageInfo $package) {
			return $package->getStatus();
		}));

		return self::render(__DIR__ . '/templates/tab.phtml', array(
			'status' => $status,
			'updates' => $updates,
		));
	}

	/**
	 * Html code for DebuggerBar Panel.
	 * @return string
	 */
	public function getPanel()
	{
		if (!$this->packages) {
			return;
		}

		$packages = $this->packages;
		uksort($packages, function ($key1, $key2) use ($packages) {
			$status1 = $packages[$key1]->getStatus();
			$status2 = $packages[$key2]->getStatus();
			return $status1 !== $status2 ? $status1 < $status2 : $key1 > $key2;
		});

		return self::render(__DIR__ . '/templates/panel.phtml', array(
			'packages' => $packages,
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
