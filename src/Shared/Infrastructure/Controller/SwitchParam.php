<?php

namespace App\Shared\Infrastructure\Controller;

use App\Classes\Container\Params;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SwitchParam extends AbstractController
{
	public function __invoke(Request $request): Response
	{
		$params = $request->query->get('params');

		if ($params !== FALSE && in_array($params, Params::getParams())) {
			$request->cookies->set('p' . $params, $request->cookies->get('p' . $params) ?? Params::$params[$params]);
		}

		return $this->redirect($request->headers->get('Referer'));
	}
}
