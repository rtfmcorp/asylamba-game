<?php
# validate tutorial step action

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Classes\Exception\FormException;

$session = $this->getContainer()->get('session_wrapper');
$response = $this->getContainer()->get('app.response');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$shipQueueManager = $this->getContainer()->get('athena.ship_queue_manager');

$playerId = $session->get('playerId');
$stepTutorial = $session->get('playerInfo')->get('stepTutorial');
$stepDone = $session->get('playerInfo')->get('stepDone');

if ($stepDone == true and TutorialResource::stepExists($stepTutorial)) {
    $player = $playerManager->get($playerId);
    
    if ($player->stepDone == $stepDone and $player->stepTutorial == $stepTutorial) {
        $experience = TutorialResource::getInfo($stepTutorial, 'experienceReward');
        $credit = TutorialResource::getInfo($stepTutorial, 'creditReward');
        $resource = TutorialResource::getInfo($stepTutorial, 'resourceReward');
        $ship = TutorialResource::getInfo($stepTutorial, 'shipReward');
        $playerBases = $orbitalBaseManager->getPlayerBases($player->id);
        $alert = 'Etape validée. ';

        $firstReward = true;
        if ($experience > 0) {
            $firstReward = false;
            $alert .= 'Vous gagnez ' . $experience . ' points d\'expérience';
            $playerManager->increaseExperience($player, $experience);
        }

        if ($credit > 0) {
            if ($firstReward) {
                $firstReward = false;
                $alert .= 'Vous gagnez ' . $credit . 'crédits';
            } else {
                $alert .= ', ainsi que ' . $credit . ' crédits';
            }
            $playerManager->increaseCredit($player, $credit);
        }

        if ($resource > 0 || $ship != array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)) {
            $ob = $playerBases[0];

            if ($resource > 0) {
                if ($firstReward) {
                    $firstReward = false;
                    $alert .= 'Vous gagnez ' . $resource . ' ressources';
                } else {
                    $alert .= ' et ' . $resource . ' ressources';
                }
                $alert .= ' sur votre base orbitale ' . $ob->name . '. ';
                $orbitalBaseManager->increaseResources($ob, $resource, true);
            }

            if ($ship != array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)) {
                $qty = 0;
                $ships = array();
                foreach ($ship as $key => $value) {
                    if ($value != 0) {
                        $ships[$qty] = array();
                        $ships[$qty]['quantity'] = $value;
                        $ships[$qty]['name'] = ShipResource::getInfo($key, 'codeName');
                        $qty++;

                        # add ship to dock
                        $orbitalBaseManager->addShipToDock($ob, $key, $value);
                    }
                }
                if ($firstReward) {
                    $firstReward = false;
                    $alert .= 'Vous gagnez ';
                    $endOfAlert = ' sur votre base orbitale ' . $ob->name . '. ';
                } else {
                    $alert .= '. Vous gagnez également ';
                    $endOfAlert = '. ';
                }

                # complete alert
                foreach ($ships as $key => $value) {
                    if ($key == 0) {
                        $alert .= $value['quantity'] . ' ' . $value['name'] . Format::plural($value['quantity']);
                    } elseif ($qty - 1 == $key) {
                        $alert .= ' et ' . $value['quantity'] . ' ' . $value['name'] . Format::plural($value['quantity']);
                    } else {
                        $alert .= ', ' . $value['quantity'] . ' ' . $value['name'] . Format::plural($value['quantity']);
                    }
                }
                $alert .= $endOfAlert;
            }
        } else {
            $alert .= '. ';
        }

        $alert .= 'La prochaine étape vous attend.';
        $session->addFlashbag($alert, Flashbag::TYPE_SUCCESS);
        
        $nextStep = $stepTutorial;
        if (TutorialResource::isLastStep($stepTutorial)) {
            $nextStep = 0;
            $session->addFlashbag('Bravo, vous avez terminé le tutoriel. Bonne continuation et bon amusement sur Asylamba, vous pouvez maintenant voler de vos propres ailes !', Flashbag::TYPE_SUCCESS);
        } else {
            $nextStep += 1;
        }

        # verify if the next step is already done
        $nextStepAlreadyDone = false;
        $redirectWithoutJeanMi = false;
        switch ($nextStep) {
            case TutorialResource::NAVIGATION:
                $redirectWithoutJeanMi = true;
                $nextStepAlreadyDone = true;
                break;
            case TutorialResource::GENERATOR_LEVEL_2:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::GENERATOR, 2);
                break;
            case TutorialResource::REFINERY_LEVEL_3:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::REFINERY, 3);
                break;
            case TutorialResource::STORAGE_LEVEL_3:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::STORAGE, 3);
                break;
            case TutorialResource::TECHNOSPHERE_LEVEL_1:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::TECHNOSPHERE, 1);
                break;
            case TutorialResource::MODIFY_UNI_INVEST:
                # asdf
                break;
            case TutorialResource::CREATE_COMMANDER:
                # asdf
                break;
            case TutorialResource::DOCK1_LEVEL_1:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::DOCK1, 1);
                break;
            case TutorialResource::SHIP0_UNBLOCK:
                $nextStepAlreadyDone = $tutorialHelper->isNextTechnoStepAlreadyDone($playerId, Technology::SHIP0_UNBLOCK);
                break;
            case TutorialResource::BUILD_SHIP0:
                # verify in the queue
                # load the queues
                foreach ($playerBases as $ob) {
                    $shipQueues = $shipQueueManager->getBaseQueues($ob->rPlace);
                    foreach ($shipQueues as $shipQueue) {
                        if ($shipQueue->shipNumber == ShipResource::PEGASE) {
                            $nextStepAlreadyDone = true;
                            break;
                        }
                    }
                }
                break;
            case TutorialResource::AFFECT_COMMANDER:
                # asdf
                break;
            case TutorialResource::FILL_SQUADRON:
                # asdf
                break;
            case TutorialResource::MOVE_FLEET_LINE:
                # asdf
                break;
            case TutorialResource::SPY_PLANET:
                # asdf
                break;
            case TutorialResource::LOOT_PLANET:
                # asdf
                break;
            case TutorialResource::FACTION_FORUM:
                # asdf
                break;
            case TutorialResource::SHARE_ASYLAMBA:
                $nextStepAlreadyDone = true;
                break;
            case TutorialResource::REFINERY_LEVEL_10:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::REFINERY, 10);
                break;
            case TutorialResource::STORAGE_LEVEL_8:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::STORAGE, 8);
                break;
            case TutorialResource::DOCK1_LEVEL_6:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::DOCK1, 6);
                break;
            case TutorialResource::REFINERY_LEVEL_16:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::REFINERY, 16);
                break;
            case TutorialResource::STORAGE_LEVEL_12:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::STORAGE, 12);
                break;
            case TutorialResource::TECHNOSPHERE_LEVEL_6:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::TECHNOSPHERE, 6);
                break;
            case TutorialResource::SHIP1_UNBLOCK:
                $nextStepAlreadyDone = $tutorialHelper->isNextTechnoStepAlreadyDone($playerId, Technology::SHIP1_UNBLOCK);
                break;
            case TutorialResource::DOCK1_LEVEL_15:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::DOCK1, 15);
                break;
            case TutorialResource::BUILD_SHIP1:
                # asdf
                break;
            case TutorialResource::REFINERY_LEVEL_20:
                $nextStepAlreadyDone = $tutorialHelper->isNextBuildingStepAlreadyDone($playerId, OrbitalBaseResource::REFINERY, 20);
                break;
            case TutorialResource::SPONSORSHIP:
                $nextStepAlreadyDone = true;
                break;
        }
        if (!$nextStepAlreadyDone) {
            $player->stepDone = 0;
            $session->get('playerInfo')->add('stepDone', false);
        }
        $player->stepTutorial = $nextStep;
        $session->get('playerInfo')->add('stepTutorial', $nextStep);
        $this->getContainer()->get('entity_manager')->flush($player);
        if ($redirectWithoutJeanMi) {
            $response->redirect('profil');
        }
    } else {
        $session->get('playerInfo')->add('stepDone', $player->stepDone);
        $session->get('playerInfo')->add('stepTutorial', $player->stepTutorial);

        throw new FormException('Vous ne pouvez pas valider deux fois la même étape.');
    }
} else {
    throw new FormException('Impossible de valider l\'étape avant de l\'avoir effectuée.');
}
