<?php

namespace Asylamba\Classes\Library\Http;

class Response
{
	/** @var string **/
	protected $title;
	/** @var string **/
	protected $page;
	/** @var Request **/
	protected $request;
	/** @var string **/
	protected $redirect;
	/** @var array **/
	protected $templates = [];
	
	/**
	 * @param \Asylamba\Classes\Library\Http\Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param string $page
	 */
	public function setPage($page)
	{
		$this->page = $page;
	}
	
	/**
	 * @return string
	 */
	public function getPage()
	{
		return $this->page;
	}
	
	/**
	 * @param int $path
	 * @param boolean $externalDomain
	 */
	public function redirect($path, $externalDomain = FALSE) {
		$this->redirect = $path;
		$this->request->setCrossDomain($externalDomain);
	}
	
	/**
	 * @return string
	 */
	public function getRedirect()
	{
		return $this->redirect;
	}
	
	public function rewriteCookies()
	{
		
	}
	
	/**
	 * @param string $template
	 */
	public function addTemplate($template)
	{
		$this->templates[] = $template;
	}
	
	/**
	 * @return array
	 */
	public function getTemplates()
	{
		return $this->templates;
	}
}