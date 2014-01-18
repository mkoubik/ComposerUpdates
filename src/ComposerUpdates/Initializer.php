<?php

namespace ComposerUpdates;

use Composer;

class Initializer
{
	private $cacheDir;
	private $localConfigFile;

	/** @var Composer\IO\IOInterface */
	private $io;

	/** @var Composer\Config */
	private $config;

	private $localConfig;

	/** @var Composer\Repository\RepositoryManager */
	private $repositoryManager;

	/** @var Composer\Repository\RepositoryInterface */
	private $installedRepository;

	/** @var Composer\Package\RootPackageInterface */
	private $rootPackage;

	/** @var Composer\DependencyResolver\Pool */
	private $packagePool;

	public function __construct($cacheDir, $localConfigFile)
	{
		$this->cacheDir = $cacheDir . '/_ComposerUpdates';
		$this->localConfigFile = $localConfigFile;
		$this->io = new Composer\IO\NullIO();
	}

	public function getInstalledRepository()
	{
		if ($this->installedRepository === NULL) {
			$package = $this->getRootPackage();

			$installedRootPackage = clone $package;
			$installedRootPackage->setRequires(array());
			$installedRootPackage->setDevRequires(array());

			$localRepo = $this->getRepositoryManager()->getLocalRepository();
			$platformRepo = new Composer\Repository\PlatformRepository();
			$repos = array(
				$localRepo,
				new Composer\Repository\InstalledArrayRepository(array($installedRootPackage)),
				$platformRepo,
			);
			$this->installedRepository = new Composer\Repository\CompositeRepository($repos);
		}
		return $this->installedRepository;
	}

	public function getPackagePool()
	{
		if ($this->packagePool === NULL) {
			$factory = new Composer\Factory;
			$repos = $factory->createDefaultRepositories($this->io, $this->getConfig(), $this->getRepositoryManager());
			$repository = new Composer\Repository\CompositeRepository($repos);
			$this->packagePool = new Composer\DependencyResolver\Pool();
			$this->packagePool->addRepository($repository);
		}
		return $this->packagePool;
	}

	public function getRequires()
	{
		return $this->getRootPackage()->getRequires();
	}

	public function getDevRequires()
	{
		return $this->getRootPackage()->getDevRequires();
	}

	private function getConfig()
	{
		if ($this->config === NULL || $this->localConfig === NULL) {
			$json = new Composer\Json\JsonFile($this->localConfigFile);
			$this->localConfig = $json->read();

			$this->config = new Composer\Config;
			$this->config->merge(array(
				'config' => array(
					'cache-dir' => $this->cacheDir,
				),
			));
			$this->config->merge($this->localConfig);
		}
		return $this->config;
	}

	private function getVendorDir()
	{
		return dirname($this->localConfigFile) . '/' . $this->getConfig()->get('vendor-dir');
	}

	private function getRepositoryManager()
	{
		if ($this->repositoryManager === NULL) {
			$this->repositoryManager = new Composer\Repository\RepositoryManager($this->io, $this->getConfig());
			$this->repositoryManager->setRepositoryClass('composer', 'Composer\Repository\ComposerRepository');
			$this->repositoryManager->setRepositoryClass('vcs', 'Composer\Repository\VcsRepository');
			$this->repositoryManager->setRepositoryClass('package', 'Composer\Repository\PackageRepository');
			$this->repositoryManager->setRepositoryClass('pear', 'Composer\Repository\PearRepository');
			$this->repositoryManager->setRepositoryClass('git', 'Composer\Repository\VcsRepository');
			$this->repositoryManager->setRepositoryClass('svn', 'Composer\Repository\VcsRepository');
			$this->repositoryManager->setRepositoryClass('hg', 'Composer\Repository\VcsRepository');
			$this->repositoryManager->setRepositoryClass('artifact', 'Composer\Repository\ArtifactRepository');

			$json = new Composer\Json\JsonFile($this->getVendorDir() . '/composer/installed.json');
			$localRepo = new Composer\Repository\InstalledFilesystemRepository($json);
			$this->repositoryManager->setLocalRepository($localRepo);
		}
		return $this->repositoryManager;
	}

	private function getRootPackage()
	{
		if ($this->rootPackage === NULL) {
			$loader = new Composer\Package\Loader\RootPackageLoader($this->getRepositoryManager(), $this->getConfig());
			$this->getConfig();
			$this->rootPackage = $loader->load($this->localConfig);
		}
		return $this->rootPackage;
	}
}
