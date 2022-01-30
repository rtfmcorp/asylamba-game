<?php

namespace App\Modules\Athena\Infrastructure\Controller\Base;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\Parser;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Zeus\Helper\CheckName;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RenameBase extends AbstractController
{
	public function __invoke(
		Request $request,
		Player $currentPlayer,
		OrbitalBase $currentBase,
		Parser $parser,
		OrbitalBaseManager $orbitalBaseManager,
		EntityManager $entityManager,
	): Response {
		// protection du nouveau nom de la base
		$name = $parser->protect($request->request->get('name'));

		if (empty($name)) {
			throw new BadRequestHttpException('Nom invalide');
		}
		$check = new CheckName();
		$check->setMaxLength(20);

		if (!$check->checkLength($name)) {
			throw new BadRequestHttpException('modification du nom de la base orbitale impossible - nom trop long ou trop court');
		}
		if (!$check->checkChar($name)) {
			throw new BadRequestHttpException('modification du nom de la base orbitale impossible - le nom contient des caractères non-autorisés');
		}
		$currentBase->setName($name);

		$session = $request->getSession();
		for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
			if ($session->get('playerBase')->get('ob')->get($i)->get('id') === $currentBase->getId()) {
				$session->get('playerBase')->get('ob')->get($i)->add('name', $name);
			}
		}

		$entityManager->flush($currentBase);

		$this->addFlash('success', 'Le nom a été changé en ' . $name . ' avec succès');

		return $this->redirectToRoute('base_overview');
	}
}
