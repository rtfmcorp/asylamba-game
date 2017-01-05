<?php

namespace Asylamba\Classes\Library\Http;

use Asylamba\Classes\Container\History;

class Response
{
	/** @var string **/
	protected $title;
	/** @var string **/
	protected $page;
	/** @var Request **/
	protected $request;
	/** @var History **/
	protected $history;
	/** @var string **/
	protected $redirect;
	
	/**
	 * @param \Asylamba\Classes\Library\Http\Request $request
	 * @param History $history
	 */
	public function __construct(Request $request, History $history) {
		$this->request = $request;
		$this->history = $history;
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
	 * @param int $v
	 * @param boolean $externalDomain
	 */
	public function redirect($v = 0, $externalDomain = FALSE) {
		$this->request->setCrossDomain($externalDomain);
		$this->redirect = 
			($v === 0)
			? $this->history->getPastPath()
			: $v
		;
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
}