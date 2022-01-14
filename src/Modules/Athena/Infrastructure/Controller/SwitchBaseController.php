<?php

namespace App\Modules\Athena\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SwitchBaseController extends AbstractController
{
	public function __invoke(int $baseId, string $page): Response
	{
		return new Response();
	}
}
