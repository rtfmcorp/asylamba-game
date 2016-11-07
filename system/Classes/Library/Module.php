<?php

namespace Asylamba\Classes\Library;

use Asylamba\Classes\Worker\Application;
use Asylamba\Classes\Worker\Container;
use Asylamba\Classes\Configuration\Configuration;
use Symfony\Component\Config\FileLocator;

abstract class Module
{
	/** @var Container **/
	protected $container;
	
	/**
	 * @param Application $application
	 */
	public function __construct(Application $application)
	{
		$this->container = $application->getContainer();
		$this->configure();
	}
	
	public function configure()
	{
		$configurationFiles = [
			"{$this->container->getParameter('root_path')}/system/Modules/{$this->getName()}/Resource/config/config.yml"
		];
		$configuration = new Configuration(new FileLocator($configurationFiles));
		$configuration->buildContainer($this->container, $configurationFiles);
	}
	
	/**
	 * This method must return the name of the module and be the same as the module folder name
	 * 
	 * @return string
	 */
	abstract public function getName();
}