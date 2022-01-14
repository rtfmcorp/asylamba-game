<?php

namespace App\Shared\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ParamsController extends AbstractController
{
	public function __invoke(): Response
	{
		return new Response();
	}
}
