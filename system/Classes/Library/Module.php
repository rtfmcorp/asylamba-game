<?php

namespace Asylamba\Classes\Library;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

abstract class Module
{
	public function configure(ContainerBuilder $containerBuilder, string $projectDir): void
	{
		$loader = new YamlFileLoader(
			$containerBuilder,
			new FileLocator("{$projectDir}/system/Modules/{$this->getName()}/Resource/config/")
		);
		$loader->load('config.yml');
	}
	
	/**
	 * This method must return the name of the module and be the same as the module folder name
	 */
	abstract public function getName(): string;
}
