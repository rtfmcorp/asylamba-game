<?php
class DataAnalysis {
	// AFFICHE $DATA
	public static function creditToStdUnit($credit) {
		return round($credit / 10);
	}

	public static function resourceToStdUnit($resource) {
		return $resource;
	}
}