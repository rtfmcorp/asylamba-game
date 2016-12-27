<?php

# create school class action

# int baseid 		id de la base orbitale
# int school 		not used anymore
# string name 		name of the officer

use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Zeus\Helper\CheckName;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Modules\Athena\Resource\SchoolClassResource;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Zeus\Helper\TutorialHelper;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$school = $request->query->get('school');
$name   = $request->request->get('name');

$cn = new CheckName();
$cn->maxLenght = 20;

if ($baseId !== FALSE AND $school !== FALSE AND $name !== FALSE AND in_array($baseId, $verif)) {
	$S_OBM1 = $orbitalBaseManager->getCurrentSession();
	$orbitalBaseManager->newSession();
	$orbitalBaseManager->load(array('rPlace' => $baseId, 'rPlayer' => $session->get('playerId')));

	if ($orbitalBaseManager->size() > 0) {
		$S_COM1 = $commanderManager->getCurrentSession();
		$commanderManager->newSession();
		$commanderManager->load(array('c.statement' => Commander::INSCHOOL, 'c.rBase' => $baseId));

		if ($commanderManager->size() < PlaceResource::get($orbitalBaseManager->get()->typeOfBase, 'school-size')) {
			$commanderManager->load(array('c.statement' => Commander::RESERVE, 'c.rBase' => $baseId));

			if ($commanderManager->size() < OrbitalBase::MAXCOMMANDERINMESS) {
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
						$S_PAM1 = $playerManager->getCurrentSession();
						$playerManager->newSession(ASM_UMODE);
						$playerManager->load(array('id' => $session->get('playerId')));
						$playerManager->decreaseCredit($playerManager->get(), SchoolClassResource::getInfo($school, 'credit'));
						$playerManager->changeSession($S_PAM1);

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
							$commanderManager->add($newCommander);
						}
						$this->getContainer()->get('app.response')->flashbag->add($nbrCommandersToCreate . ' commandant' . Format::addPlural($nbrCommandersToCreate) . ' inscrit' . Format::addPlural($nbrCommandersToCreate) . ' au programme d\'entraînement.', Response::FLASHBAG_SUCCESS);
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
	$orbitalBaseManager->changeSession($S_OBM1);
}