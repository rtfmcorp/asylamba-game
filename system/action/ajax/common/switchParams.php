<?php

use Asylamba\Classes\Container\Params;

$request = $this->getContainer()->get('app.request');

$params = $request->request->get('params');

if ($params !== FALSE) {
	if (in_array($params, Params::getParams())) {
		$request->cookies->set('p' . $params, !$request->cookies->get('p' . $params, $params));
	}
}