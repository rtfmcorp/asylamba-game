<?php

namespace Asylamba\Classes\Library;

class ParameterBag {
	/** @var array **/
	protected $parameters = [];
	
	/**
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function set($key, $value)
	{
		$this->parameters[$key] = $value;
		
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return boolean
	 */
	public function has($key)
	{
		return isset($this->parameters[$key]);
	}
	
	/**
	 * @param string $key
	 * @return $this
	 */
	public function remove($key)
	{
		unset($this->parameters[$key]);
		
		return $this;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return $this->parameters[$key];
	}
	
	/**
	 * @return array
	 */
	public function all()
	{
		return $this->parameters;
	}
}