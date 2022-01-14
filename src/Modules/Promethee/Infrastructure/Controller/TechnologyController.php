<?php

namespace App\Modules\Promethee\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TechnologyController extends AbstractController
{
	public function __invoke(): Response
	{
		return new Response();
	}
}
