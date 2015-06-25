<?php
# obFastView componant
# in athena package

# affiche les infos importantes d'une orbital base, dispose de lien rapide vers la main page

# require
	# {orbitalBase}		ob_fastView
	# (int)				ob_index
	# (bool)			fastView_profil

# calcul
if ($ob_fastView->getLevelSpatioport() > 0) {
	$S_CRM_OFV = ASM::$crm->getCurrentSession();
	ASM::$crm->changeSession($ob_fastView->routeManager);

	$nMaxCR = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $ob_fastView->getLevelSpatioport(), 'nbRoutesMax');
	$nCRWaitingForOther = 0; $nCRWaitingForMe = 0;
	$nCROperational = 0; $nCRInStandBy = 0;
	$nCRInDock = 0;

	if (ASM::$crm->size() > 0) {
		for ($j = 0; $j < ASM::$crm->size(); $j++) {
			if (ASM::$crm->get($j)->getStatement() == CRM_PROPOSED AND ASM::$crm->get($j)->getPlayerId1() == CTR::$data->get('playerId')) {
				$nCRWaitingForOther++;
			} elseif (ASM::$crm->get($j)->getStatement() == CRM_PROPOSED AND ASM::$crm->get($j)->getPlayerId1() != CTR::$data->get('playerId')) {
				$nCRWaitingForMe++;
			} elseif (ASM::$crm->get($j)->getStatement() == CRM_ACTIVE) {
				$nCROperational++;
			} elseif (ASM::$crm->get($j)->getStatement() == CRM_STANDBY) {
				$nCRInStandBy++;
			}
		}

		$nCRInDock = $nCROperational + $nCRInStandBy + $nCRWaitingForOther;
	}
}

echo '<div class="component">';
	if ($fastView_profil) {
		echo '<div class="head skin-1">';
			echo '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($ob_fastView->getPlanetPopulation()) . '.png" alt="' . $ob_fastView->getName() . '" />';
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
				echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $ob_fastView->getId(), 'page' => 'spatioport']) . '" class="alert">Vous avez ' . $nCRWaitingForMe . ' proposition' . Format::plural($nCRWaitingForMe) . ' commerciale' . Format::plural($nCRWaitingForMe) . '</a>';
			}

			# affichage des ressources
			echo '<div class="number-box">';
				echo '<span class="label">Ressources en stock</span>';
				echo '<span class="value">';
					echo Format::numberFormat($ob_fastView->getResourcesStorage());
					echo ' <img alt="ressources" src="' . MEDIA . 'resources/resource.png" class="icon-color">';
				echo '</span>';

				$storageSpace = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $ob_fastView->getLevelStorage(), 'storageSpace');
				$storageBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE);
				if ($storageBonus > 0) {
					$storageSpace += ($storageSpace * $storageBonus / 100);
				}
				$percent = Format::numberFormat($ob_fastView->getResourcesStorage() / $storageSpace * 100);
				echo '<span class="progress-bar hb bl" title="remplissage : ' . $percent . '%">';
					echo '<span style="width:' . $percent . '%;" class="content"></span>';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $ob_fastView->getId(), 'page' => 'refinery']) . '" class="link hb lt" title="vers la raffinerie">→</a>';
				echo '</span>';
			echo '</div>';

			echo '<h4>Générateur</h4>';

			$S_BQM_OBV = ASM::$bqm->getCurrentSession();
			ASM::$bqm->changeSession($ob_fastView->buildingManager);
			$nextTime = 0;
			$nextTotalTime = 0;
			$realSizeQueue = 0;

			echo '<div class="queue">';
			for ($j = 0; $j < OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::GENERATOR, 'level', $ob_fastView->levelGenerator, 'nbQueues'); $j++) {
				if (ASM::$bqm->get($j) !== FALSE) {
					$qe = ASM::$bqm->get($j);

					$realSizeQueue++;
					$nextTime = Utils::interval(Utils::now(), $qe->dEnd, 's');
					$nextTotalTime += OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'level', $qe->targetLevel, 'time');

					echo '<div class="item ' . (($realSizeQueue > 1) ? 'active' : '') . ' progress" data-progress-output="lite" data-progress-no-reload="true" data-progress-current-time="' . $nextTime . '" data-progress-total-time="' . $nextTotalTime . '">';
							'class="button hb lt" title="annuler la construction (attention, vous ne récupérerez que ' . BQM_RESOURCERETURN * 100 . '% du montant investi)">×</a>';
					echo '<img class="picto" src="' . MEDIA . 'orbitalbase/' . OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'imageLink') . '.png" alt="" />';
					echo '<strong>';
						echo OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'frenchName');
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
					echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $ob_fastView->getId(), 'page' => 'generator']) . '" class="item link">';
						echo 'Construire un bâtiment';
					echo '</a>';

					break;
				}
			}
			echo '</div>';

			ASM::$bqm->changeSession($S_BQM_OBV);

			if ($ob_fastView->getLevelDock1() > 0) {
				echo '<h4>Chantier Alpha</h4>';

				$S_SQM_OBV = ASM::$sqm->getCurrentSession();
				ASM::$sqm->changeSession($ob_fastView->dock1Manager);
				$realSizeQueue = 0;

				echo '<div class="queue">';
					for ($j = 0; $j < OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::DOCK1, 'level', $ob_fastView->levelDock1, 'nbQueues'); $j++) {
						if (ASM::$sqm->get($j) !== FALSE) {
							$queue = ASM::$sqm->get($j);
							$realSizeQueue++;
							$totalTimeShips = $queue->quantity * ShipResource::getInfo($queue->shipNumber, 'time');
							$remainingTime = Utils::interval(Utils::now(), $queue->dEnd, 's');

							echo $realSizeQueue > 1
								? '<div class="item">'
								: '<div class="item active progress" data-progress-output="lite" data-progress-no-reload="true" data-progress-current-time="' . $remainingTime . '" data-progress-total-time="' . $totalTimeShips . '">';
							echo  '<img class="picto" src="' . MEDIA . 'ship/picto/' . ShipResource::getInfo($queue->shipNumber, 'imageLink') . '.png" alt="" />';
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
							echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $ob_fastView->getId(), 'page' => 'dock1']) . '" class="item link">';
								echo 'Lancer la production';
							echo '</a>';

							break;
						}
					}
				echo '</div>';

				ASM::$sqm->changeSession($S_SQM_OBV);
			}

			if ($ob_fastView->getLevelDock2() > 0) {
				echo '<h4>Chantier de Ligne</h4>';

				$S_SQM_OBV = ASM::$sqm->getCurrentSession();
				ASM::$sqm->changeSession($ob_fastView->dock2Manager);
				$realSizeQueue = 0;

				echo '<div class="queue">';
					for ($j = 0; $j < OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::DOCK1, 'level', $ob_fastView->levelDock1, 'nbQueues'); $j++) {
						if (ASM::$sqm->get($j) !== FALSE) {
							$queue = ASM::$sqm->get($j);
							$realSizeQueue++;
							$totalTimeShips = $queue->quantity * ShipResource::getInfo($queue->shipNumber, 'time');
							$remainingTime = Utils::interval(Utils::now(), $queue->dEnd, 's');

							echo $realSizeQueue > 1
								? '<div class="item">'
								: '<div class="item active progress" data-progress-output="lite" data-progress-no-reload="true" data-progress-current-time="' . $remainingTime . '" data-progress-total-time="' . $totalTimeShips . '">';
							echo  '<img class="picto" src="' . MEDIA . 'ship/picto/' . ShipResource::getInfo($queue->shipNumber, 'imageLink') . '.png" alt="" />';
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
							echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $ob_fastView->getId(), 'page' => 'dock2']) . '" class="item link">';
								echo 'Lancer la production';
							echo '</a>';

							break;
						}
					}
				echo '</div>';

				ASM::$sqm->changeSession($S_SQM_OBV);
			}

			if ($ob_fastView->getLevelTechnosphere() > 0) {
				echo '<h4>Technosphère</h4>';

				$S_TQM_OFV = ASM::$tqm->getCurrentSession();
				ASM::$tqm->changeSession($ob_fastView->technoQueueManager);
				$realSizeQueue = 0;
				$remainingTotalTime = 0;
				$totalTimeTechno = 0;

				echo '<div class="queue">';
				for ($j = 0; $j < OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::TECHNOSPHERE, 'level', $ob_fastView->levelTechnosphere, 'nbQueues'); $j++) {
					if (ASM::$tqm->get($j) !== FALSE) {
						$queue = ASM::$tqm->get($j);
						$realSizeQueue++;
						$totalTimeTechno += TechnologyResource::getInfo($queue->technology, 'time', $queue->targetLevel);
						$remainingTotalTime = Utils::interval(Utils::now(), $queue->dEnd, 's');

						echo '<div class="item active progress" data-progress-output="lite" data-progress-no-reload="true" data-progress-current-time="' . $remainingTotalTime . '" data-progress-total-time="' . $totalTimeTechno . '">';
							echo  '<img class="picto" src="' . MEDIA . 'technology/picto/' . TechnologyResource::getInfo($queue->technology, 'imageLink') . '.png" alt="" />';
							echo '<strong>' . TechnologyResource::getInfo($queue->technology, 'name');
							if (!TechnologyResource::isAnUnblockingTechnology($queue->technology)) {
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
						echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $ob_fastView->getId(), 'page' => 'technosphere']) . '" class="item link">';
							echo 'Développer une technologie';
						echo '</a>';

						break;
					}
				}
				echo '</div>';

				ASM::$tqm->changeSession($S_TQM_OFV);
			}

			if ($ob_fastView->getLevelSpatioport() > 0) {
				echo '<h4>Spatioport</h4>';

				$S_CRM_OFV = ASM::$crm->getCurrentSession();
				ASM::$crm->changeSession($ob_fastView->routeManager);

				$nMaxCR = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $ob_fastView->getLevelSpatioport(), 'nbRoutesMax');
				$nCRWaitingForOther = 0; $nCRWaitingForMe = 0;
				$nCROperational = 0; $nCRInStandBy = 0;
				$nCRInDock = 0;

				if (ASM::$crm->size() > 0) {
					for ($j = 0; $j < ASM::$crm->size(); $j++) {
						if (ASM::$crm->get($j)->getStatement() == CRM_PROPOSED AND ASM::$crm->get($j)->getPlayerId1() == CTR::$data->get('playerId')) {
							$nCRWaitingForOther++;
						} elseif (ASM::$crm->get($j)->getStatement() == CRM_PROPOSED AND ASM::$crm->get($j)->getPlayerId1() != CTR::$data->get('playerId')) {
							$nCRWaitingForMe++;
						} elseif (ASM::$crm->get($j)->getStatement() == CRM_ACTIVE) {
							$nCROperational++;
						} elseif (ASM::$crm->get($j)->getStatement() == CRM_STANDBY) {
							$nCRInStandBy++;
						}
					}

					$nCRInDock = $nCROperational + $nCRInStandBy + $nCRWaitingForOther;
				}

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
						echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $ob_fastView->getId(), 'page' => 'spatioport']) . '" class="link hb lt" title="vers le spatioport">→</a>';
					echo '</span>';
				echo '</div>';

				ASM::$crm->changeSession($S_CRM_OFV);
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>