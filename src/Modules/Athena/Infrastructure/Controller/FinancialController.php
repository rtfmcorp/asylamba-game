<?php

namespace App\Modules\Athena\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FinancialController extends AbstractController
{
	public function __invoke(): Response
	{
		return new Response();
	}
}
