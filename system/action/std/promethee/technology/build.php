<?php
# building a technology action

# int baseid 		id de la base orbitale
# int techno 	 	id de la technologie

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Promethee\Model\TechnologyQueue;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$database = $this->getContainer()->get('database');
$session = $this->getContainer()->get('session_wrapper');
$request = $this->getContainer()->get('app.request');
$technologyQueueManager = $this->getContainer()->get('promethee.technology_queue_manager');
$technologyHelper = $this->getContainer()->get('promethee.technology_helper');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$researchManager = $this->getContainer()->get('promethee.research_manager');
$technologyManager = $this->getContainer()->get('promethee.technology_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
    $verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$techno = $request->query->get('techno');


if ($baseId !== false and $techno !== false and in_array($baseId, $verif)) {
    if ($technologyHelper->isATechnology($techno) && !$technologyHelper->isATechnologyNotDisplayed($techno)) {
        if (($technologyQueueManager->getPlayerTechnologyQueue($session->get('playerId'), $techno)) === null) {
            $technos = $technologyManager->getPlayerTechnology($session->get('playerId'));
            $targetLevel = $technos->getTechnology($techno) + 1;
            // @TODO I think this piece of code is dead
            $technologyQueues = $technologyQueueManager->getPlaceQueues($baseId);
            $nbTechnologyQueues = count($technologyQueues);
            foreach ($technologyQueues as $technologyQueue) {
                if ($technologyQueue->technology == $techno) {
                    $targetLevel++;
                }
            }
            if (($ob = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
                $S_RSM1 = $researchManager->getCurrentSession();
                $researchManager->newSession(ASM_UMODE);
                $researchManager->load(array('rPlayer' => $session->get('playerId')));

                if ($technologyHelper->haveRights($techno, 'resource', $targetLevel, $ob->getResourcesStorage())
                    and $technologyHelper->haveRights($techno, 'credit', $targetLevel, $session->get('playerInfo')->get('credit'))
                    and $technologyHelper->haveRights($techno, 'queue', $ob, $nbTechnologyQueues)
                    and $technologyHelper->haveRights($techno, 'levelPermit', $targetLevel)
                    and $technologyHelper->haveRights($techno, 'technosphereLevel', $ob->getLevelTechnosphere())
                    and ($technologyHelper->haveRights($techno, 'research', $targetLevel, $researchManager->getResearchList($researchManager->get())) === true)
                    and $technologyHelper->haveRights($techno, 'maxLevel', $targetLevel)
                    and $technologyHelper->haveRights($techno, 'baseType', $ob->typeOfBase)) {

                    # tutorial
                    if ($session->get('playerInfo')->get('stepDone') == false) {
                        switch ($session->get('playerInfo')->get('stepTutorial')) {
                            case TutorialResource::SHIP0_UNBLOCK:
                                if ($techno == Technology::SHIP0_UNBLOCK) {
                                    $tutorialHelper->setStepDone();
                                }
                                break;
                            case TutorialResource::SHIP1_UNBLOCK:
                                if ($techno == Technology::SHIP1_UNBLOCK) {
                                    $tutorialHelper->setStepDone();
                                }
                                break;
                        }
                    }

                    // construit la nouvelle techno
                    $time = $technologyHelper->getInfo($techno, 'time', $targetLevel);
                    $bonusPercent = $session->get('playerBonus')->get(PlayerBonus::TECHNOSPHERE_SPEED);
                    if ($session->get('playerInfo')->get('color') == ColorResource::APHERA) {
                        # bonus if the player is from Aphera
                        $bonusPercent += ColorResource::BONUS_APHERA_TECHNO;
                    }

                    # ajout du bonus du lieu
                    $bonusPercent += Game::getImprovementFromScientificCoef($ob->planetHistory);
                    $bonus = round($time * $bonusPercent / 100);
                    
                    $createdAt =
                        ($nbTechnologyQueues === 0)
                        ? Utils::now()
                        : $technologyQueues[$nbTechnologyQueues - 1]->getEndedAt()
                    ;
                    $tq =
                        (new TechnologyQueue())
                        ->setPlayerId($session->get('playerId'))
                        ->setPlaceId($baseId)
                        ->setTechnology($techno)
                        ->setTargetLevel($targetLevel)
                        ->setCreatedAt($createdAt)
                        ->setEndedAt(Utils::addSecondsToDate($createdAt, round($time - $bonus)))
                    ;
                    $technologyQueueManager->add($tq);

                    // débit resources
                    $orbitalBaseManager->decreaseResources($ob, $technologyHelper->getInfo($techno, 'resource', $targetLevel));
                    
                    // débit des crédits
                    $playerManager->decreaseCredit($playerManager->get($session->get('playerId')), $technologyHelper->getInfo($techno, 'credit', $targetLevel));

                    if (DATA_ANALYSIS) {
                        $qr = $database->prepare(
                            'INSERT INTO 
							DA_BaseAction(`from`, type, opt1, opt2, weight, dAction)
							VALUES(?, ?, ?, ?, ?, ?)'
                        );
                        $qr->execute([$session->get('playerId'), 2, $techno, $targetLevel, (DataAnalysis::resourceToStdUnit($technologyHelper->getInfo($techno, 'resource', $targetLevel)) + DataAnalysis::creditToStdUnit($technologyHelper->getInfo($techno, 'credit', $targetLevel))), Utils::now()]);
                    }

                    // alerte
                    $session->addFlashbag('Développement de la technologie programmée', Flashbag::TYPE_SUCCESS);
                } else {
                    throw new ErrorException('les conditions ne sont pas remplies pour développer une technologie');
                }
                $researchManager->changeSession($S_RSM1);
            } else {
                throw new ErrorException('cette base ne vous appartient pas');
            }
        } else {
            throw new ErrorException('Cette technologie est déjà en construction');
        }
    } else {
        throw new ErrorException('la technologie indiquée n\'est pas valide');
    }
} else {
    throw new FormException('pas assez d\'informations pour développer une technologie');
}
