<?php
class Alert {
	private $alerts = array();

	public function add($message, $type = ALERT_STD_INFO) {
		$this->alerts[] = array($message, $type);
		return $this->size() - 1;
	}

	public function clear() {
		$this->alerts = array();
	}

	public function size() {
		return count($this->alerts);
	}

	public function get($position) {
		if (isset($this->alerts[$position])) {
			return $this->alerts[$position];
		} else {
			return FALSE;
		}
	}

	public function getAlerts($tag, $type = ALERT_DEFAULT) {
		$format = '';
		foreach ($this->alerts as $k) {
			if ($type != ALERT_DEFAULT AND $type = $k[1]) {
				$format .= '<' . $tag . ' class="alert_' . $k[1] . '">' . $k[0] . '</' . $tag . '>';
			}
		}
		return $format;
	}

	public function logAlerts($path, $date = TRUE, $supp = array()) {
		# TODO
	}

	public function readUrl() {
		if (isset($_GET['say'])) {
			$this->alerts[] = array($_GET['say'], ALERT_URL_INFO);
		}
	}
}
?>