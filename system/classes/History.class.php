<?php
class History {
	const MAX_SIZE = 5;
	private $events = array();

	public function add($path) {
		array_unshift($this->events, $path);
		if (count($this->events) > self::MAX_SIZE) {
			$this->events = array_slice($this->events, 0, self::MAX_SIZE);
		} 
	}

	public function getCurrentPath() {
		return $this->events[0];
	}

	public function getPastPath($larger = 0) {
		if (isset($this->events[$larger])) {
			return $this->events[$larger];
		}
	}

	public function clear() {
		$this->events = array();
	}
}
?>