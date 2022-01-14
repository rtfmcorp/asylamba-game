<?php

namespace App\Modules\Hermes\Infrastructure\Controller\Conversation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ListController extends AbstractController
{
	public function __invoke(): Response
	{
		return new Response();
	}
}
