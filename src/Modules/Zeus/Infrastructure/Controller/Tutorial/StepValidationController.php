<?php

namespace App\Modules\Zeus\Infrastructure\Controller\Tutorial;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class StepValidationController extends AbstractController
{
	public function __invoke(): Response
	{
		return new Response();
	}
}
