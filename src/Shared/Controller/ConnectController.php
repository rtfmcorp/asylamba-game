<?php

namespace App\Shared\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ConnectController extends AbstractController
{
	public function __invoke(): Response
	{
		return new Response();
	}
}
