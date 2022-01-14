<?php

namespace App\Modules\Zeus\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SponsorController extends AbstractController
{
	public function __invoke(): Response
	{
		return new Response();
	}
}
