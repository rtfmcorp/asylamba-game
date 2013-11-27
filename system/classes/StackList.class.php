<?php
class StackList {
	private $elements = array();

	public function size() {
		return count($this->elements);
	}

	public function get($index = 0) {
		if (isset($this->elements[$index])) {
			return $this->elements[$index];
		} else {
			return NULL;
		}
	}

	public function exist($index) {
		if (isset($this->elements[$index])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function add($key, $value) {
		$this->elements[$key] = $value;
	}

	public function insert($key, $value) {
		if (count($this->elements) < $key) {
			$this->elements[$key] = $value;
		} else {
			// décalage des tous les index qui suivent
			$begin = array_slice($this->elements, 0, $key);
			$begin[] = $value;
			$end = array_slice($this->elements, $key);
			$this->elements = array_merge($begin, $end);
		}
	}

	public function append($value) {
		$this->elements[] = $value;
	}

	public function prepend($value) {
		array_unshift($this->elements, $value);
	}

	public function remove($index) {
		if ($index < 0) {
			$index = count($this->elements) + $index;
		}
		if (isset($this->elements[$index])) {
			$begin = array_slice($this->elements, 0, $index);
			$end = array_slice($this->elements, $index+1);
			$this->elements = array_merge($begin, $end);
		} else {
			return FALSE;
		}
	}

	public function clear() {
		$this->elements = array();
	}
}
?>