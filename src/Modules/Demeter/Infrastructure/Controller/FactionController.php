<?php

namespace App\Modules\Demeter\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FactionController extends AbstractController
{
	public function __invoke(): Response
	{
		return new Response();
	}
}
