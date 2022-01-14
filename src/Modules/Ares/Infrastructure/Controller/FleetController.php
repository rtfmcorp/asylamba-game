<?php

namespace App\Modules\Ares\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FleetController extends AbstractController
{
	public function __invoke(): Response
	{
		return new Response();
	}
}
