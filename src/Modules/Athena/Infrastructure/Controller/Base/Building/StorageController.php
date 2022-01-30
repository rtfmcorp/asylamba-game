<?php

namespace App\Modules\Athena\Infrastructure\Controller\Base\Building;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class StorageController extends AbstractController
{
	public function __invoke(): Response
	{
		return $this->render('pages/athena/storage.html.twig');
	}
}
