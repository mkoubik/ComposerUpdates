<?php

namespace ComposerUpdates\DI;

use Nette;

class ComposerUpdatesExtension extends Nette\DI\CompilerExtension
{
	private $defaults = array(
		'cacheDir' => NULL,
		'localConfigFile' => NULL,
	);

	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('initializer'))
			->setClass('ComposerUpdates\Initializer', array($config['cacheDir'], $config['localConfigFile']));

		$builder->addDefinition($this->prefix('service'))
			->setClass('ComposerUpdates\Service');

		$builder->addDefinition($this->prefix('panel'))
			->setClass('ComposerUpdates\Diagnostics\ComposerUpdatesPanel');
	}

	public function afterCompile(Nette\PhpGenerator\ClassType $class)
	{
		$builder = $this->getContainerBuilder();
		if ($builder->parameters['debugMode']) {
			$class->methods['initialize']->addBody($builder->formatPhp(
				'Nette\Diagnostics\Debugger::getBar()->addPanel(?);',
				Nette\DI\Compiler::filterArguments(array(new Nette\DI\Statement($this->prefix('@panel'))))
			));
		}
	}
}
