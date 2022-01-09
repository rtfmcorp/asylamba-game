<?php

use App\Classes\Container\Params;

$request = $this->getContainer()->get('app.request');
$params = $request->query->get('params');

if ($params !== FALSE) {
	if (in_array($params, Params::getParams())) {
		if ($request->cookies->get('p' . $params, $params)) {
			$request->cookies->add('p' . $params, false, true);
		} else {
			$request->cookies->add('p' . $params, true, true);
		}
	}
}
