<?php
class ArrayList {
	protected $elements = array();

	public function size() {
		return count($this->elements);
	}

	public function get($key) {
		if (isset($this->elements[$key])) {
			return $this->elements[$key];
		} else {
			return NULL;
		}
	}

	public function exist($key) {
		if (isset($this->elements[$key])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function equal($key, $value) {
		if ($this->exist($key) && $this->get($key) == $value) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function add($key, $value) {
		$this->elements[$key] = $value;
	}

	public function remove($key) {
		if (isset($this->elements[$key])) {
			unset($this->elements[$key]);
		} else {
			return FALSE;
		}
	}

	public function clear() {
		$this->elements = array();
	}
}
?>