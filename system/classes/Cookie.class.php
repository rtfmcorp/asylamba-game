<?php
/**
 * SUJET A MODIF, ALORS ATTENTION A VOUS
 **/

class Cookie extends ArrayList {
	protected $name;

	public function __construct() {
		$this->name = APP_NAME . '_' . SERVER_SESS;
		$this->init();
	}

	public function add($key, $value) {
		$this->elements[$key] = $value;
		$this->rewrite();
	}

	public function remove($key) {
		if (isset($this->elements[$key])) {
			unset($this->elements[$key]);
		} else {
			return FALSE;
		}
		$this->rewrite();
	}

	public function clear() {
		$this->elements = array();
		$this->rewrite();
	}

	public function rewrite() {
		setcookie($this->name, serialize($this->elements), time() + 3000000, '/');
	}

	public function init() {
		if (isset($_COOKIE[$this->name])) {
			$this->elements = unserialize($_COOKIE[$this->name]);
		}
	}
}
?>