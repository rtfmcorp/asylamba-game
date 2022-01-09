<?php
# rename the orbital base action

# int baseid 		id (rPlayer) de la base orbitale
# string name 		new name for the orbital base

use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;
use App\Modules\Zeus\Helper\CheckName;

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$name = $request->request->get('name');

// protection du nouveau nom de la base
$p = $this->getContainer()->get(\Asylamba\Classes\Library\Parser::class);
$name = $p->protect($name);

if ($baseId !== FALSE AND $name !== FALSE AND in_array($baseId, $verif)) {
	if (($orbitalBase = $orbitalBaseManager->get($baseId, $session->get('playerId'))) !== null) {
		$check = new CheckName();
		$check->setMaxLength(20); 

		if ($check->checkLength($name)) {
			if ($check->checkChar($name)) {
				$orbitalBase->setName($name);

				for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
					if ($session->get('playerBase')->get('ob')->get($i)->get('id') == $baseId) {
						$session->get('playerBase')->get('ob')->get($i)->add('name', $name);
					}
				}
				
				$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush($orbitalBase);

				$session->addFlashbag('Le nom a été changé en ' . $name . ' avec succès', Flashbag::TYPE_SUCCESS);
			} else {
				throw new ErrorException('modification du nom de la base orbitale impossible - le nom contient des caractères non-autorisés');
			}
		} else {
			throw new ErrorException('modification du nom de la base orbitale impossible - nom trop long ou trop court');
		}
	} else {
		throw new ErrorException('cette base ne vous appartient pas');
	}
} else {
	throw new FormException('pas assez d\'informations pour changer le nom de la base orbitale');
}