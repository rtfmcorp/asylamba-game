<?php

# create school class action

# int baseid 		id de la base orbitale
# int school 		not used anymore
# string name 		name of the officer

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Zeus\Helper\CheckName;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Modules\Athena\Resource\SchoolClassResource;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');
$entityManager = $this->getContainer()->get('entity_manager');

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$school = $request->query->get('school');
$name   = $request->request->get('name');

$cn = new CheckName();
$cn->maxLenght = 20;

if ($baseId !== FALSE AND $school !== FALSE AND $name !== FALSE AND in_array($baseId, $verif)) {
	if (($orbitalBase = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
		$schoolCommanders = $commanderManager->getBaseCommanders($baseId, [Commander::INSCHOOL]);

		if (count($schoolCommanders) < PlaceResource::get($orbitalBase->typeOfBase, 'school-size')) {
			$reserveCommanders = $commanderManager->getBaseCommanders($baseId, [Commander::RESERVE]);

			if (count($reserveCommanders) < OrbitalBase::MAXCOMMANDERINMESS) {
				$school = intval($school);
				$nbrCommandersToCreate = rand(SchoolClassResource::getInfo($school, 'minSize'), SchoolClassResource::getInfo($school, 'maxSize'));

				if ($cn->checkLength($name) && $cn->checkChar($name)) {
					if (SchoolClassResource::getInfo($school, 'credit') <= $session->get('playerInfo')->get('credit')) {
						# tutorial
						if ($session->get('playerInfo')->get('stepDone') == FALSE &&
							$session->get('playerInfo')->get('stepTutorial') === TutorialResource::CREATE_COMMANDER) {
							$tutorialHelper->setStepDone();
						}

						# débit des crédits au joueur
						$playerManager->decreaseCredit($playerManager->get($session->get('playerId')), SchoolClassResource::getInfo($school, 'credit'));

						for ($i = 0; $i < $nbrCommandersToCreate; $i++) {
							$newCommander = new Commander();
							$commanderManager->upExperience($newCommander, rand(SchoolClassResource::getInfo($school, 'minExp'), SchoolClassResource::getInfo($school, 'maxExp')));
							$newCommander->rPlayer = $session->get('playerId');
							$newCommander->rBase = $baseId;
							$newCommander->palmares = 0;
							$newCommander->statement = 0;
							$newCommander->name = $name;
							$newCommander->avatar = 't' . rand(1, 21) . '-c' . $session->get('playerInfo')->get('color');
							$newCommander->dCreation = Utils::now();
							$newCommander->uCommander = Utils::now();
							$newCommander->setSexe(1);
							$newCommander->setAge(rand(40, 70));
							$entityManager->persist($newCommander);
							$entityManager->flush($newCommander);
						}
						$session->addFlashbag($nbrCommandersToCreate . ' commandant' . Format::addPlural($nbrCommandersToCreate) . ' inscrit' . Format::addPlural($nbrCommandersToCreate) . ' au programme d\'entraînement.', Flashbag::TYPE_SUCCESS);
					} else {
						throw new FormException('vous n\'avez pas assez de crédit.');
					}
				} else {
					throw new FormException('le nom contient des caractères non autorisé ou trop de caractères.');
				}
			} else {
				throw new ErrorException('Vous ne pouvez pas créer de nouveaux officiers si vous en avez déjà ' . Orbitalbase::MAXCOMMANDERINMESS . ' ou plus.');
			}
		} else {
			throw new ErrorException('Trop d\'officiers en formation. Déplacez des officiers dans le mess pour libérer de la place.');
		}
	} else {
		throw new ErrorException('cette base ne vous appartient pas');
	}
}