<?php

namespace Asylamba\Classes\Library\Http;

class Response
{
	/** @var string **/
	protected $title;
	/** @var string **/
	protected $page;
	
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
}