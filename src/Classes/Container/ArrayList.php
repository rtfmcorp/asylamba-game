<?php

namespace App\Classes\Container;

class ArrayList {
    /** @var array **/
    protected $elements = array();

    /**
     * @return int
     */
    public function size() {
        return count($this->elements);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        if (isset($this->elements[$key])) {
            return $this->elements[$key];
        }
        return null;
    }

	/**
	 * @param string $key
	 * @return boolean
	 */
	public function exist($key) {
		return isset($this->elements[$key]);
	}

	public function has(string $key): bool
	{
		return isset($this->elements[$key]);
	}

    /**
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function equal($key, $value) {
        return (($this->exist($key) && $this->get($key) == $value));
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function add($key, $value) {
        $this->elements[$key] = $value;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function remove($key) {
        if (isset($this->elements[$key])) {
            unset($this->elements[$key]);
        }
        return false;
    }

    public function clear() {
        $this->elements = array();
    }
}
