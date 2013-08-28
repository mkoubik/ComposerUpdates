ComposerUpdates
===============

Installation
------------

```sh
$ composer require mkoubik/composer-updates:@dev
```

```php
# bootstrap.php:

$configurator->onCompile[] = function ($config, Nette\Config\Compiler $compiler) {
	$compiler->addExtension('composerUpdates', new ComposerUpdates\DI\ComposerUpdatesExtension());
};

```

```yaml
# config.neon

composerUpdates:
	cacheDir: %tempDir%/cache
	localConfigFile: %wwwDir%/../composer.json
```

Screenshots
-----------
![before](http://i.nahraj.to/f/sNr.png)


![after](http://i.nahraj.to/f/sNq.png)
