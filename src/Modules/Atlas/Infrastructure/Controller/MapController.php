<?php

namespace App\Modules\Atlas\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MapController extends AbstractController
{
	public function __invoke(): Response
	{
		return new Response();
	}
}
