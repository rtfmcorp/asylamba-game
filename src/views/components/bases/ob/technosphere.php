<?php
# refinery component
# in athena.bases package

# affichage de la raffinerie

# require
	# {orbitalBase}		ob_tech

use App\Classes\Library\Game;
use App\Classes\Library\Utils;
use App\Classes\Library\Chronos;
use App\Modules\Promethee\Model\Technology;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Zeus\Model\PlayerBonus;
use App\Modules\Demeter\Resource\ColorResource;
use App\Classes\Library\Format;

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');
$orbitalBaseHelper = $this->getContainer()->get(\App\Modules\Athena\Helper\OrbitalBaseHelper::class);
$technologyManager = $this->getContainer()->get(\App\Modules\Promethee\Manager\TechnologyManager::class);
$technologyQueueManager = $this->getContainer()->get(\App\Modules\Promethee\Manager\TechnologyQueueManager::class);
$technologyHelper = $this->getContainer()->get(\App\Modules\Promethee\Helper\TechnologyHelper::class);
$researchManager = $this->getContainer()->get(\App\Modules\Promethee\Manager\ResearchManager::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$sessionToken = $session->get('token');
$technologyResourceRefund = $this->getContainer()->getParameter('promethee.technology_queue.resource_refund');

$technology = $technologyManager->getPlayerTechnology($session->get('playerId'));

# session avec les technos de cette base
$baseTechnologyQueues = $technologyQueueManager->getPlaceQueues($ob_tech->getId());
$playerTechnologyQueues = $technologyQueueManager->getPlayerQueues($session->get('playerId'));

$S_RSM1 = $researchManager->getCurrentSession();
$researchManager->newSession();
$researchManager->load(array('rPlayer' => $session->get('playerId')));

# déblocage
$c1 = array(); $c2 = array(); $c3 = array();
$c4 = array(); $c5 = array();
$c6 = array(); $c7 = array();

echo '<div class="component techno">';
	echo '<div class="head skin-1">';
		echo '<img src="' . $mediaPath . 'orbitalbase/technosphere.png" alt="" />';
		echo '<h2>' . $orbitalBaseHelper->getBuildingInfo(5, 'frenchName') . '</h2>';
		echo '<em>niveau ' . $ob_tech->getLevelTechnosphere() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';

			$coef = $ob_tech->planetHistory;
			$coefBonus = Game::getImprovementFromScientificCoef($coef);
			$techBonus = $session->get('playerBonus')->get(PlayerBonus::TECHNOSPHERE_SPEED);
			$factionBonus = 0;
			if ($session->get('playerInfo')->get('color') == ColorResource::APHERA) {
				# bonus if the player is from Aphera
				$factionBonus += ColorResource::BONUS_APHERA_TECHNO;
			}			
			$totalBonus = $coefBonus + $techBonus + $factionBonus;
			
			echo '<div class="number-box ' . (($totalBonus == 0) ? 'grey' : '') . '">';
				echo '<span class="label">bonus total de vitesse de recherche</span>';
				echo '<span class="value">';
					echo $totalBonus . ' %';
				echo '</span>';
			echo '</div>';

			echo '<h4>File de construction</h4>';
			echo '<div class="queue">';
				$realSizeQueue = 0;
				$remainingTotalTime = 0;
				$totalTimeTechno = 0;

				for ($i = 0; $i < $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::TECHNOSPHERE, 'level', $ob_tech->levelTechnosphere, 'nbQueues'); $i++) {
					if (isset($baseTechnologyQueues[$i])) {
						$queue = $baseTechnologyQueues[$i];
						$realSizeQueue++;
						$totalTimeTechno += $technologyHelper->getInfo($queue->technology, 'time', $queue->targetLevel);
						$remainingTotalTime = Utils::interval(Utils::now(), $queue->dEnd, 's');

						echo '<div class="item active progress" data-progress-output="lite" data-progress-current-time="' . $remainingTotalTime . '" data-progress-total-time="' . $totalTimeTechno . '">';
							echo '<a href="' . Format::actionBuilder('dequeuetechno', $sessionToken, ['baseid' => $ob_tech->getId(), 'techno' => $queue->technology]) . '"' . 
								'class="button hb lt" title="annuler la recherche (attention, vous ne récupérerez que ' . $technologyResourceRefund * 100 . '% du montant investi)">×</a>';
							echo  '<img class="picto" src="' . $mediaPath . 'technology/picto/' . $technologyHelper->getInfo($queue->technology, 'imageLink') . '.png" alt="" />';
							echo '<strong>' . $technologyHelper->getInfo($queue->technology, 'name');
							if (!$technologyHelper->isAnUnblockingTechnology($queue->technology)) {
								echo ' <span class="level">niv. ' . $queue->targetLevel . '</span>';
							}
							echo '</strong>';
							
							if ($realSizeQueue > 1) {
								echo '<em><span class="progress-text">' . Chronos::secondToFormat($remainingTotalTime, 'lite') . '</span></em>';
								echo '<span class="progress-container"></span>';
							} else {
								echo '<em><span class="progress-text">' . Chronos::secondToFormat($remainingTotalTime, 'lite') . '</span></em>';
								echo '<span class="progress-container">';
									echo '<span style="width: ' . Format::percent($totalTimeTechno - $remainingTotalTime, $totalTimeTechno) . '%;" class="progress-bar">';
									echo '</span>';
								echo '</span>';
							}
						echo '</div>';
					} else {
						echo '<div class="item empty">';
							echo '<span class="picto"></span>';
							echo '<strong>Emplacement libre</strong>';
							echo '<span class="progress-container"></span>';
						echo '</div>';
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

for ($i = 0; $i < Technology::QUANTITY; $i++) {
	if (!$technologyHelper->isATechnologyNotDisplayed($i)) {
		$but = ''; $sup = ''; $ctn = array(); $ctn[0] = ''; $ctn[1] = TRUE;
		$disability = 'disable'; $closed = '';
		$inQueue = FALSE;
		$inALocalQueue = FALSE;

		foreach ($playerTechnologyQueues as $playerQueue) {
			if ($playerQueue->getTechnology() === $i) {
				$inQueue = TRUE;
				foreach ($baseTechnologyQueues as $baseQueue) {
					if ($baseQueue->getTechnology() === $i) {
						$inALocalQueue = TRUE;
						break;
					}
				}
				break;
			}
		}
		
		$title = $technologyHelper->getInfo($i, 'name');
		if (!$technologyHelper->isAnUnblockingTechnology($i) && $technology->getTechnology($i) != 0) {
			$title .= ' [' . $technology->getTechnology($i) . ']';
		}

		if ($technologyHelper->isAnUnblockingTechnology($i)) {
			$answer = $technologyHelper->haveRights($i, 'research', 1, $researchManager->getResearchList($researchManager->get()));
		} else {
			$answer = $technologyHelper->haveRights($i, 'research', $technology->getTechnology($i) + 1, $researchManager->getResearchList($researchManager->get()));
		}

		if (!$technologyHelper->haveRights($i, 'technosphereLevel', $ob_tech->getLevelTechnosphere())) {
			# building
			$but  .= '<span class="button disable">';
				$but .= 'il vous faut augmenter votre technosphère au niveau ' . $technologyHelper->getInfo($i, 'requiredTechnosphere');
			$but .= '</span>';
		} elseif ($answer !== TRUE) {
			# recherche
			$but  .= '<span class="button disable">';
				$but .= 'nécessite ';
				for ($j = 0; $j < $answer->size(); $j++) { 
					$but .= $answer->get($j)->get('techno') . '&nbsp;' . $answer->get($j)->get('level');
					if ($j < ($answer->size() - 1)) { $but .= ', '; }
				}
			$but .= '</span>';
		} else {
			# usable techno
			$disability = '';

			# compute time to build with the bonuses
			$timeToBuild = $technologyHelper->getInfo($i, 'time', $technology->getTechnology($i) + 1);
			$timeToBuild -= round($timeToBuild * $totalBonus / 100);
												# warning : $totalBonus est défini plus haut (ne pas inverser les blocs de code !)

			if ($inQueue) {
				$but .= '<span class="button disable">';
					$but .= 'technologie en cours<br />de recherche';
					if (!$technologyHelper->isAnUnblockingTechnology($i)) {
						$but .= ' vers le niveau ' . ($technology->getTechnology($i) + 1);
					}
					if (!$inALocalQueue) {
						$but .= ' sur une autre de vos bases';
					}
				$but .= '</span>';

			} elseif ($technologyHelper->isAnUnblockingTechnology($i) && $technology->getTechnology($i)) {
				$ctn[1] = FALSE;
				$sup .= '<em>développement terminé</em>';
				$closed = 'closed';
			} elseif (!$technologyHelper->isAnUnblockingTechnology($i) && !$technologyHelper->haveRights($i, 'maxLevel', $technology->getTechnology($i) + 1)) {
				# max level reached
				$but .= '<span class="button disable">';
					$but .= 'niveau maximum atteint<br />';
				$but .= '</span>';
			} elseif (!$technologyHelper->haveRights($i, 'queue', $ob_tech, count($baseTechnologyQueues))) {
				# queue size
				$but .= '<span class="button disable">';
					$but .= 'file de recherche pleine<br />';
					$but .= '<span class="final-cost">' . Format::numberFormat($technologyHelper->getInfo($i, 'resource', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="ressources" src="' . $mediaPath . 'resources/resource.png">, ';
						$but .= '<span class="final-cost">' . Format::numberFormat($technologyHelper->getInfo($i, 'credit', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="crédits" src="' . $mediaPath . 'resources/credit.png"> et ';
					$but .= '<span class="final-time">' . Chronos::secondToFormat($timeToBuild, 'lite') . '</span> ';
					$but .= '<img class="icon-color" alt="relèves" src="' . $mediaPath . 'resources/time.png">';
				$but .= '</span>';
			} elseif (!$technologyHelper->haveRights($i, 'credit', $technology->getTechnology($i) + 1, $session->get('playerInfo')->get('credit'))) {
				# crédit
				$but .= '<span class="button disable">';
					$but .= 'pas assez de crédits<br />';
					$but .= '<span class="final-cost">' . Format::numberFormat($technologyHelper->getInfo($i, 'resource', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="ressources" src="' . $mediaPath . 'resources/resource.png">, ';
						$but .= '<span class="final-cost">' . Format::numberFormat($technologyHelper->getInfo($i, 'credit', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="crédits" src="' . $mediaPath . 'resources/credit.png"> et ';
					$but .= '<span class="final-time">' . Chronos::secondToFormat($timeToBuild, 'lite') . '</span> ';
					$but .= '<img class="icon-color" alt="relèves" src="' . $mediaPath . 'resources/time.png">';
				$but .= '</span>';
			} elseif (!$technologyHelper->haveRights($i, 'resource', $technology->getTechnology($i) + 1, $ob_tech->getResourcesStorage())) {
				# ressources
				$but .= '<span class="button disable">';
					$but .= 'pas assez de ressources<br />';
					$but .= '<span class="final-cost">' . Format::numberFormat($technologyHelper->getInfo($i, 'resource', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="ressources" src="' . $mediaPath . 'resources/resource.png">, ';
						$but .= '<span class="final-cost">' . Format::numberFormat($technologyHelper->getInfo($i, 'credit', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="crédits" src="' . $mediaPath . 'resources/credit.png"> et ';
					$but .= '<span class="final-time">' . Chronos::secondToFormat($timeToBuild, 'lite') . '</span> ';
					$but .= '<img class="icon-color" alt="relèves" src="' . $mediaPath . 'resources/time.png">';
				$but .= '</span>';
			} else {
				$but .= '<a class="button" href="' . Format::actionBuilder('buildtechno', $sessionToken, ['baseid' => $ob_tech->getId(), 'techno' => $i]) . '">';
					if ($technologyHelper->isAnUnblockingTechnology($i)) {
						$but .= 'rechercher la technologie<br />';
					} else {
						$but .= 'rechercher le niveau ' . ($technology->getTechnology($i) + 1) . '<br />';
					}
					$but .= '<span class="final-cost">' . Format::numberFormat($technologyHelper->getInfo($i, 'resource', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="ressources" src="' . $mediaPath . 'resources/resource.png"> | ';
						$but .= '<span class="final-cost">' . Format::numberFormat($technologyHelper->getInfo($i, 'credit', $technology->getTechnology($i) + 1)) . '</span> ';
					$but .= '<img class="icon-color" alt="crédits" src="' . $mediaPath . 'resources/credit.png"> | ';
					$but .= '<span class="final-time">' . Chronos::secondToFormat($timeToBuild, 'lite') . '</span> ';
					$but .= '<img class="icon-color" alt="relèves" src="' . $mediaPath . 'resources/time.png">';
				$but .= '</a>';
			}
		}

		$ctn[0] .= '<div class="build-item ' . $disability . ' ' . $closed . '">';
			$ctn[0] .= '<div class="name">';
				$ctn[0] .= '<a href="#" class="addInfoPanel hb lt info" title="plus d\'informations" data-techno-id="' . $i . '" data-info-type="techno">+</a>';
				$ctn[0] .= '<img src="' . $mediaPath . 'technology/picto/' . $technologyHelper->getInfo($i, 'imageLink') . '.png" alt="" />';
				$ctn[0] .= '<strong>' . $title . '</strong>';
				$ctn[0] .= $sup;
			$ctn[0] .= '</div>';
			$ctn[0] .= '<div class="ship-illu"><img class="illu" src="' . $mediaPath . 'technology/img/' . $technologyHelper->getInfo($i, 'imageLink') . '.png" /></div>';
			$ctn[0] .= $but;
		$ctn[0] .= '</div>';

		switch ($technologyHelper->getInfo($i, 'column')) {
			case 1 : $c1[] = $ctn; break;
			case 2 : $c2[] = $ctn; break;
			case 3 : $c3[] = $ctn; break;
			case 4 : $c4[] = $ctn; break;
			case 5 : $c5[] = $ctn; break;
			case 6 : $c6[] = $ctn; break;
			case 7 : $c7[] = $ctn; break;
			default: $c1[] = $ctn; break;
		}
	}
}

# financial
if (in_array($ob_tech->typeOfBase, array(OrbitalBase::TYP_COMMERCIAL, OrbitalBase::TYP_CAPITAL))) {
	echo '<div class="component techno">';
		echo '<div class="head skin-5">';
			echo '<h2>Améliorations industrielles I</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				foreach ($c4 as $key => $value) {
					echo $value[0];
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="component techno">';
		echo '<div class="head skin-5">';
			echo '<h2>Améliorations industrielles II</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				foreach ($c5 as $key => $value) {
					echo $value[0];
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

# military
if (in_array($ob_tech->typeOfBase, array(OrbitalBase::TYP_MILITARY, OrbitalBase::TYP_CAPITAL))) {
	echo '<div class="component techno">';
		echo '<div class="head skin-5">';
			echo '<h2>Améliorations militaires I</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				foreach ($c6 as $key => $value) {
					echo $value[0];
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="component techno">';
		echo '<div class="head skin-5">';
			echo '<h2>Améliorations militaires II</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				foreach ($c7 as $key => $value) {
					echo $value[0];
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

# unblock
echo '<div class="component techno">';
	echo '<div class="head skin-5">';
		echo '<h2>Nouvelles technologies</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($c1 as $key => $value) {
				if ($value[1] == TRUE) {
					echo $value[0];
				}
			}
			foreach ($c1 as $key => $value) {
				if ($value[1] == FALSE) {
					echo $value[0];
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component techno">';
	echo '<div class="head skin-5">';
		echo '<h2>Châssis I</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($c2 as $key => $value) {
				if ($value[1] == TRUE) {
					echo $value[0];
				}
			}
			foreach ($c2 as $key => $value) {
				if ($value[1] == FALSE) {
					echo $value[0];
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component techno">';
	echo '<div class="head skin-5">';
		echo '<h2>Châssis II</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($c3 as $key => $value) {
				if ($value[1] == TRUE) {
					echo $value[0];
				}
			}
			foreach ($c3 as $key => $value) {
				if ($value[1] == FALSE) {
					echo $value[0];
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::TECHNOSPHERE, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$researchManager->changeSession($S_RSM1);
