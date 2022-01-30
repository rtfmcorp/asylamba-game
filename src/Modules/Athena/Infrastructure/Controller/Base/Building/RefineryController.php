<?php

namespace App\Modules\Athena\Infrastructure\Controller\Base\Building;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class RefineryController extends AbstractController
{
	public function __invoke(): Response
	{
		return $this->render('pages/athena/refinery.html.twig');
	}
}
