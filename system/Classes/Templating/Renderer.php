<?php

namespace Asylamba\Classes\Templating;

use Asylamba\Classes\Library\Http\Response;

use Asylamba\Classes\DependencyInjection\Container;

class Renderer
{
	/** @var Container **/
	protected $container;
	
	/**
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}
	
	/**
	 * @param Response $response
	 */
	public function render(Response $response)
	{
		ob_start();
		foreach($response->getTemplates() as $template)
		{
			include $template;
		}
		if ($redirect = $response->getRedirect()) {
			ob_end_clean();
			header('Location: /' . $redirect);
			exit();
		}
	}
	
	/**
	 * @return Container
	 */
	public function getContainer()
	{
		return $this->container;
	}
}