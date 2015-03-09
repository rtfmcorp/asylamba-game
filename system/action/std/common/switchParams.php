<?php
$params = Utils::getHTTPData('params');

if ($params !== FALSE) {
	if (in_array($params, Params::getParams())) {
		if (Params::check($params)) {
			Params::update($params, FALSE);
		} else {
			Params::update($params, TRUE);
		}
	}
}
?>