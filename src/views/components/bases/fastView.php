<?php
# obFastView componant
# in athena package

# affiche les infos importantes d'une orbital base, dispose de lien rapide vers la main page

# require
	# {orbitalBase}		ob_fastView
	# (int)				ob_index
	# (bool)			fastView_profil

# calcul

use App\Classes\Library\Utils;
use App\Classes\Library\Chronos;
use App\Modules\Athena\Resource\ShipResource;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Classes\Library\Game;
use App\Modules\Gaia\Resource\PlaceResource;
use App\Classes\Library\Format;
use App\Modules\Zeus\Model\PlayerBonus;
use App\Modules\Athena\Model\CommercialRoute;

$container = $this->getContainer();
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$commercialRouteManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\CommercialRouteManager::class);
$buildingQueueManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\BuildingQueueManager::class);
$shipQueueManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\ShipQueueManager::class);
$orbitalBaseHelper = $this->getContainer()->get(\Asylamba\Modules\Athena\Helper\OrbitalBaseHelper::class);
$buildingResourceRefund = $this->getContainer()->getParameter('athena.building.building_queue_resource_refund');
$sessionToken = $session->get('token');
$technologyHelper = $this->getContainer()->get(\Asylamba\Modules\Promethee\Helper\TechnologyHelper::class);
$entityManager = $this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class);
$mediaPath = $container->getParameter('media');

// @TODO: move it to the using part of the code and remove useless data
if ($ob_fastView->getLevelSpatioport() > 0) {
	$nMaxCR = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $ob_fastView->getLevelSpatioport(), 'nbRoutesMax');
	$nCRWaitingForOther = 0; $nCRWaitingForMe = 0;
	$nCROperational = 0; $nCRInStandBy = 0;
	$nCRInDock = 0;

	$routes = array_merge(
		$commercialRouteManager->getByBase($ob_fastView->getId()),
		$commercialRouteManager->getByDistantBase($ob_fastView->getId())
	);
	if (count($routes) > 0) {
		foreach ($routes as $route) {
			if ($route->getStatement() == CommercialRoute::PROPOSED AND $route->getPlayerId1() == $session->get('playerId')) {
				$nCRWaitingForOther++;
			} elseif ($route->getStatement() == CommercialRoute::PROPOSED AND $route->getPlayerId1() != $session->get('playerId')) {
				$nCRWaitingForMe++;
			} elseif ($route->getStatement() == CommercialRoute::ACTIVE) {
				$nCROperational++;
			} elseif ($route->getStatement() == CommercialRoute::STANDBY) {
				$nCRInStandBy++;
			}
		}
		$entityManager->clear(CommercialRoute::class);
		$nCRInDock = $nCROperational + $nCRInStandBy + $nCRWaitingForOther;
	}
}

echo '<div class="component">';
	if ($fastView_profil) {
		echo '<div class="head skin-1">';
			echo '<img src="' . $mediaPath . 'map/place/place1-' . Game::getSizeOfPlanet($ob_fastView->getPlanetPopulation()) . '.png" alt="' . $ob_fastView->getName() . '" />';
			echo '<h2>' . $ob_fastView->getName() . '</h2>';
			echo '<em>';
				echo PlaceResource::get($ob_fastView->typeOfBase, 'name') . ' — ' . $ob_fastView->getPoints() . ' points';
			echo '</em>';
		echo '</div>';
	} else {
		echo '<div class="head skin-2">';
			echo '<h2>Vue de situation</h2>';
		echo '</div>';
	}
	echo '<div class="fix-body">';
		echo '<div class="body">';
			# affichage des routes en attentes
			if ($ob_fastView->getLevelSpatioport() > 0 && $nCRWaitingForMe != 0) {
				echo '<a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $ob_fastView->getId(), 'page' => 'spatioport']) . '" class="alert">Vous avez ' . $nCRWaitingForMe . ' proposition' . Format::plural($nCRWaitingForMe) . ' commerciale' . Format::plural($nCRWaitingForMe) . '</a>';
			}

			# affichage des ressources
			echo '<div class="number-box">';
				echo '<span class="label">Ressources en stock</span>';
				echo '<span class="value">';
					echo Format::numberFormat($ob_fastView->getResourcesStorage());
					echo ' <img alt="ressources" src="' . $mediaPath . 'resources/resource.png" class="icon-color">';
				echo '</span>';

				$storageSpace = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $ob_fastView->getLevelStorage(), 'storageSpace');
				$storageBonus = $session->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE);
				if ($storageBonus > 0) {
					$storageSpace += ($storageSpace * $storageBonus / 100);
				}
				$percent = Format::numberFormat($ob_fastView->getResourcesStorage() / $storageSpace * 100);
				echo '<span class="progress-bar hb bl" title="remplissage : ' . $percent . '%">';
					echo '<span style="width:' . $percent . '%;" class="content"></span>';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $ob_fastView->getId(), 'page' => 'refinery']) . '" class="link hb lt" title="vers la raffinerie">→</a>';
				echo '</span>';
			echo '</div>';

			echo '<h4>Générateur</h4>';

			$nextTime = 0;
			$nextTotalTime = 0;
			$realSizeQueue = 0;

			echo '<div class="queue">';
			for ($j = 0; $j < $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::GENERATOR, 'level', $ob_fastView->levelGenerator, 'nbQueues'); $j++) {
				if (isset($ob_fastView->buildingQueues[$j])) {
					$qe = $ob_fastView->buildingQueues[$j];

					$realSizeQueue++;
					$nextTime = Utils::interval(Utils::now(), $qe->dEnd, 's');
					$nextTotalTime += $orbitalBaseHelper->getBuildingInfo($qe->buildingNumber, 'level', $qe->targetLevel, 'time');

					echo '<div class="item ' . (($realSizeQueue > 1) ? 'active' : '') . ' progress" data-progress-output="lite" data-progress-no-reload="true" data-progress-current-time="' . $nextTime . '" data-progress-total-time="' . $nextTotalTime . '">';
							'class="button hb lt" title="annuler la construction (attention, vous ne récupérerez que ' . $buildingResourceRefund * 100 . '% du montant investi)">×</a>';
					echo '<img class="picto" src="' . $mediaPath . 'orbitalbase/' . $orbitalBaseHelper->getBuildingInfo($qe->buildingNumber, 'imageLink') . '.png" alt="" />';
					echo '<strong>';
						echo $orbitalBaseHelper->getBuildingInfo($qe->buildingNumber, 'frenchName');
						echo ' <span class="level">niv. ' . $qe->targetLevel . '</span>';
					echo '</strong>';
					if ($realSizeQueue > 1) {
						echo '<em><span class="progress-text">' . Chronos::secondToFormat($nextTime, 'lite') . '</span></em>';
						echo '<span class="progress-container"></span>';
					} else {
						echo '<em><span class="progress-text">' . Chronos::secondToFormat($nextTime, 'lite') . '</span></em>';

						echo '<span class="progress-container">';
							echo '<span style="width: ' . Format::percent($nextTotalTime - $nextTime, $nextTotalTime) . '%;" class="progress-bar"></span>';
						echo '</span>';
					}
					echo '</div>';
				} else {
					echo '<a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $ob_fastView->getId(), 'page' => 'generator']) . '" class="item link">';
						echo 'Construire un bâtiment';
					echo '</a>';

					break;
				}
			}
			echo '</div>';

			if ($ob_fastView->getLevelDock1() > 0) {
				echo '<h4>Chantier Alpha</h4>';

				$shipQueues = $shipQueueManager->getByBaseAndDockType($ob_fastView->rPlace, 1);
				$realSizeQueue = 0;

				echo '<div class="queue">';
					for ($j = 0; $j < $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::DOCK1, 'level', $ob_fastView->levelDock1, 'nbQueues'); $j++) {
						if (isset($shipQueues[$j])) {
							$queue = $shipQueues[$j];
							$realSizeQueue++;
							$totalTimeShips = $queue->quantity * ShipResource::getInfo($queue->shipNumber, 'time');
							$remainingTime = Utils::interval(Utils::now(), $queue->dEnd, 's');

							echo $realSizeQueue > 1
								? '<div class="item">'
								: '<div class="item active progress" data-progress-output="lite" data-progress-no-reload="true" data-progress-current-time="' . $remainingTime . '" data-progress-total-time="' . $totalTimeShips . '">';
							echo  '<img class="picto" src="' . $mediaPath . 'ship/picto/' . ShipResource::getInfo($queue->shipNumber, 'imageLink') . '.png" alt="" />';
							echo '<strong>' . $queue->quantity . ' ' . ShipResource::getInfo($queue->shipNumber, 'codeName') . Format::addPlural($queue->quantity) . '</strong>';
							
							if ($realSizeQueue > 1) {
								echo '<span class="progress-container"></span>';
							} else {
								echo '<em><span class="progress-text">' . Chronos::secondToFormat($remainingTime, 'lite') . '</span></em>';
								echo '<span class="progress-container">';
									echo '<span style="width: ' . Format::percent($totalTimeShips - $remainingTime, $totalTimeShips) . '%;" class="progress-bar">';
									echo '</span>';
								echo '</span>';
							}
							echo '</div>';
						} else {
							echo '<a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $ob_fastView->getId(), 'page' => 'dock1']) . '" class="item link">';
								echo 'Lancer la production';
							echo '</a>';

							break;
						}
					}
				echo '</div>';
			}

			if ($ob_fastView->getLevelDock2() > 0) {
				echo '<h4>Chantier de Ligne</h4>';

				$shipQueues = $shipQueueManager->getByBaseAndDockType($ob_fastView->rPlace, 2);
				$realSizeQueue = 0;

				echo '<div class="queue">';
					for ($j = 0; $j < $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::DOCK1, 'level', $ob_fastView->levelDock1, 'nbQueues'); $j++) {
						if (isset($shipQueues[$j])) {
							$queue = $shipQueues[$j];
							$realSizeQueue++;
							$totalTimeShips = $queue->quantity * ShipResource::getInfo($queue->shipNumber, 'time');
							$remainingTime = Utils::interval(Utils::now(), $queue->dEnd, 's');

							echo $realSizeQueue > 1
								? '<div class="item">'
								: '<div class="item active progress" data-progress-output="lite" data-progress-no-reload="true" data-progress-current-time="' . $remainingTime . '" data-progress-total-time="' . $totalTimeShips . '">';
							echo  '<img class="picto" src="' . $mediaPath . 'ship/picto/' . ShipResource::getInfo($queue->shipNumber, 'imageLink') . '.png" alt="" />';
							echo '<strong>' . $queue->quantity . ' ' . ShipResource::getInfo($queue->shipNumber, 'codeName') . Format::addPlural($queue->quantity) . '</strong>';
							
							if ($realSizeQueue > 1) {
								echo '<span class="progress-container"></span>';
							} else {
								echo '<em><span class="progress-text">' . Chronos::secondToFormat($remainingTime, 'lite') . '</span></em>';
								echo '<span class="progress-container">';
									echo '<span style="width: ' . Format::percent($totalTimeShips - $remainingTime, $totalTimeShips) . '%;" class="progress-bar">';
									echo '</span>';
								echo '</span>';
							}
							echo '</div>';
						} else {
							echo '<a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $ob_fastView->getId(), 'page' => 'dock2']) . '" class="item link">';
								echo 'Lancer la production';
							echo '</a>';

							break;
						}
					}
				echo '</div>';
			}

			if ($ob_fastView->getLevelTechnosphere() > 0) {
				echo '<h4>Technosphère</h4>';

				$realSizeQueue = 0;
				$remainingTotalTime = 0;
				$totalTimeTechno = 0;

				echo '<div class="queue">';
				for ($j = 0; $j < $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::TECHNOSPHERE, 'level', $ob_fastView->levelTechnosphere, 'nbQueues'); $j++) {
					if (isset($ob_fastView->technoQueues[$j])) {
						$queue = $ob_fastView->technoQueues[$j];
						$realSizeQueue++;
						$totalTimeTechno += $technologyHelper->getInfo($queue->technology, 'time', $queue->targetLevel);
						$remainingTotalTime = Utils::interval(Utils::now(), $queue->dEnd, 's');

						echo '<div class="item active progress" data-progress-output="lite" data-progress-no-reload="true" data-progress-current-time="' . $remainingTotalTime . '" data-progress-total-time="' . $totalTimeTechno . '">';
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
						echo '<a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $ob_fastView->getId(), 'page' => 'technosphere']) . '" class="item link">';
							echo 'Développer une technologie';
						echo '</a>';

						break;
					}
				}
				echo '</div>';
			}

			if ($ob_fastView->getLevelSpatioport() > 0) {
				echo '<h4>Spatioport</h4>';

				echo '<div class="number-box">';
					echo '<span class="label">Routes commerciales</span>';
					echo '<span class="value">';
						echo $nCROperational . ' / ' . $nMaxCR;
					echo '</span>';

					$percent = Format::numberFormat($nCROperational / $nMaxCR * 100);
					echo '<span class="progress-bar hb bl" title="remplissage : ' . $percent . '%">';
						echo '<span style="width:' . $percent . '%;" class="content"></span>';
					echo '</span>';

					echo '<span class="group-link">';
						echo '<a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $ob_fastView->getId(), 'page' => 'spatioport']) . '" class="link hb lt" title="vers le spatioport">→</a>';
					echo '</span>';
				echo '</div>';

			}
		echo '</div>';
	echo '</div>';
echo '</div>';
