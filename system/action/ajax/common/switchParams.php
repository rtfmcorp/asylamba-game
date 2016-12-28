<?php

use Asylamba\Classes\Container\Params;

$params = $this->getContainer()->get('app.request')->request->get('params');

if ($params !== FALSE) {
	if (in_array($params, Params::getParams())) {
		$request->cookies->set('p' . $params, !$request->cookies->get('p' . $params, $params));
	}
}