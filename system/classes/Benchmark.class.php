<?php

/**
 * Benchmark class
 * ---------------
 * Use this class to know script's time execution with precision
 *
 * @author Gil Clavien
 * @version 0.1
 **/

class Benchmark {
	protected $bTime = 0;
	protected $eTime = 0;
	protected $rTime = 0;

	protected $running = FALSE;

	protected $formatTime = 's';
	protected $precision = 3;

	public function __construct($run = TRUE) {
		if ($run) {
			$this->start();
		}
	}

	public function getTime($mode = 's', $precision = 3) {
		if ($this->running) { $this->end(); }
		return $this->formatTime($mode, $precision);
	}

	public function printTime($mode = 's', $precision = 3) {
		if ($this->running) { $this->end(); }
		echo $this->formatTime($mode, $precision) . '<br />';
	}

	public function start() {
		if (!$this->running) {
			$this->bTime = $this->getMicrotime();
			$this->running = TRUE;
		} else {
			throw new Exception('Benchmark object is currently running', 1);
		}
	}

	public function end() {
		if ($this->running) {
			$this->eTime = $this->getMicrotime();
			$this->rTime = $this->eTime - $this->bTime;
			$this->running = FALSE;
		} else {
			throw new Exception('Benchmark object is not running', 1);
		}
	}

	public function clear() {
		$this->bTime = 0;
		$this->eTime = 0;
		$this->rTime = 0;
		$this->running = FALSE;
		$this->$unity = 's';
		$this->$precision = 3;
	}

	protected function formatTime($mode, $precision) {
		switch ($mode) {
			case 'mcs': $formatTime = $this->rTime * 1000000;
				break;
			case 'mls': $formatTime = $this->rTime * 1000;
				break;
			case 's'  : $formatTime = $this->rTime;
				break;
			case 'm'  : $formatTime = $this->rTime / 60;
				break;
			case 'h'  : $formatTime = $this->rTime / 360;
				break;
			default   : throw new Exception('Unknow return time format', 1);
				break;
		}

		if ($precision < -10 OR $precision > 10) {
			throw new Exception('Out of range time precision', 1);
		}

		$formatTime = round($formatTime, $precision);
		return $formatTime;
	}

	public static function getMicrotime() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
}
?>