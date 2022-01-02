<?php

namespace Asylamba\Classes\Templating;

use Asylamba\Classes\Library\Http\Response;
use Symfony\Component\DependencyInjection\Container;

class Renderer
{
	protected Container $container;
	
	public function __construct(Container $container)
	{
		$this->container = $container;
	}
	
	public function render(Response $response)
	{
		\ob_start();
		foreach($response->getTemplates() as $template)
		{
			include $template;
		}
	}
	
	public function getContainer(): Container
	{
		return $this->container;
	}
}
