<?php

namespace Asylamba\Classes\Library\Http;

use Asylamba\Classes\Library\ParameterBag;

class Request {
	/** @var string **/
	protected $url;
	/** @var boolean **/
	protected $isCrossDomain = false;
	/** @var string **/
	protected $externalDomain;
	/** @var string **/
	protected $redirect;
	/** @var ParameterBag **/
	public $headers;
	/** @var ParameterBag **/
	public $request;
	/** @var ParameterBag **/
	public $query;
	
	public function __construct()
	{
		$this->headers = new ParameterBag();
		$this->request = new ParameterBag();
		$this->query = new ParameterBag();
	}
	
	public function initialize()
	{
		foreach ($_GET as $key => $value) {
			$this->query[$key] = $value;
		}
		foreach ($_POST as $key => $value) {
			$this->request[$key] = $value;
		}
		foreach ($_SERVER as $key => $value) {
			if (!strpos($key, 'HTTP_')) {
				continue;
			}
			$this->headers[$key] = $value;
		}
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}
	
	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}
	
	/**
	 * @param string $externalDomain
	 * @return \Asylamba\Classes\Library\Http\Request
	 */
	public function setCrossDomain($externalDomain)
	{
		$this->externalDomain = $externalDomain;
		$this->isCrossDomain = true;
		
		return $this;
	}
	
	public function getCrossDomain()
	{
		return $this->isCrossDomain;
	}
}