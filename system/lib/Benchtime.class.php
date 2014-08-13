<?php
# ############### #
# Benchmark class #
# ############### #
#
# Use this class to know script's time execution with precision
#
# @author Gil Clavien
# @version 0.2

class Benchtime {
	# time storage
	protected $name;
	protected $time = array();

	# params variables
	const TXT  = 1;
	const HTML = 2;
	const PHP  = 3;

	public function __construct($name = 'Default') {
		$this->name = $name;
		$this->makePoint('Start');
	}

	# démarre un nouveau temps
	public function makePoint($name = FALSE) {
		$this->time[] = array(
			'name' => ucfirst($name ? $name : 'Breakpoint'),
			'time' => $this->getMicrotime()
		);
	}

	public function getResult($mode = FALSE) {
		$interval = $this->transform();
		$mode	  = $mode ? $mode : self::HTML;
		$ret 	  = '';

		if ($mode == self::TXT) {
			$ret .= $this->name . "\r\n";
			$ret .= '-------------------' . "\r\n";
			$ret .= '   | time  | name  ' . "\r\n";
			$ret .= '---|-------|-------' . "\r\n";

			foreach ($interval as $k => $v) {
				$ret .= '#' . ($k + 1) . ' | ' . number_format($v['time'], 3, '.', ' ') . ' | ' . $v['name'] . "\r\n";
			}
		} elseif ($mode == self::HTML) {
			$ret .= '<table>';
				$ret .= '<tr>';
					$ret .= '<th></th>';
					$ret .= '<th>time</th>';
					$ret .= '<th>name</th>';
				$ret .= '</tr>';

				foreach ($interval as $k => $v) {
					$ret .= '<tr>';
						$ret .= '<td>#' . ($k + 1) . '</td>';
						$ret .= '<td>' . number_format($v['time'], 3, '.', ' ') . '</td>';
						$ret .= '<td>' . $v['name'] . '</td>';
					$ret .= '</tr>';
				}
			$ret .= '</table>';
		} elseif ($mode == self::PHP) {
			$ret = $interval;
		}
		
		return $ret;
	}

	protected function transform() {
		$interval = array();

		for ($i = 1; $i < count($this->time); $i++) { 
			$interval[] = array(
				'name' => $this->time[$i]['name'], 
				'time' => $this->time[$i]['time'] - $this->time[$i - 1]['time']
			);
		}

		return $interval;
	}

	protected function getMicrotime() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
}
?>