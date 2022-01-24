<?php

namespace App\Modules\Athena\Infrastructure\Controller\Financial;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SendCreditsToPlayer extends AbstractController
{
	public function __invoke(): Response
	{
		return new Response();
	}
}
