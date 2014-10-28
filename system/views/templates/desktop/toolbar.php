<?php
# load base
include_once ATHENA;
include_once ARES;

$S_OBM1 = ASM::$obm->getCurrentSession();
ASM::$obm->newSession();
ASM::$obm->load(array('rPlace' => CTR::$data->get('playerParams')->get('base')));
$currentBase = ASM::$obm->get();

echo '</div>';

echo '<div id="tools">';
	# left
	echo '<div class="box left" style="left: 25px; right: auto;">';
		echo '<span class="sh releve-timer" data-target="tools-calendar" style="width: 35px; text-align: center;">';
			echo Chronos::getTimer('i') . ':' . Chronos::getTimer('s');
		echo '</span>';
	echo '</div>';

	echo '<div class="box left">';
		echo '<a href="#" class="resource-link sh" data-target="tools-refinery">' . Format::numberFormat($currentBase->getResourcesStorage()) . ' <img class="icon-color" src="' . MEDIA . 'resources/resource.png" alt="ressources" /></a>';
		
		$S_BQM1 = ASM::$bqm->getCurrentSession();
		ASM::$bqm->changeSession($currentBase->buildingManager);
		echo '<a href="#" class="square sh" data-target="tools-generator"><img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
			echo (ASM::$bqm->size()) ? '<span class="number">' . ASM::$bqm->size() . '</span>' : NULL;
		echo '</a>';
		ASM::$bqm->changeSession($S_BQM1);

		$S_TQM1 = ASM::$tqm->getCurrentSession();
		ASM::$tqm->changeSession($currentBase->technoQueueManager);
		echo '<a href="#" class="square sh" data-target="tools-technosphere"><img src="' . MEDIA . 'orbitalbase/technosphere.png" alt="" />';
			echo (ASM::$tqm->size()) ? '<span class="number">' . ASM::$tqm->size() . '</span>' : NULL;
		echo '</a>';
		ASM::$tqm->changeSession($S_TQM1);

		$S_SQM1 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->changeSession($currentBase->dock1Manager);
		echo '<a href="#" class="square sh" data-target="tools-dock1"><img src="' . MEDIA . 'orbitalbase/dock1.png" alt="" />';
			echo (ASM::$sqm->size()) ? '<span class="number">' . ASM::$sqm->size() . '</span>' : NULL;
		echo '</a>';
		ASM::$sqm->changeSession($S_SQM1);

		$S_SQM2 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->changeSession($currentBase->dock2Manager);
		echo '<a href="#" class="square sh" data-target="tools-dock2"><img src="' . MEDIA . 'orbitalbase/dock2.png" alt="" />';
			echo (ASM::$sqm->size()) ? '<span class="number">' . ASM::$sqm->size() . '</span>' : NULL;
		echo '</a>';
		ASM::$sqm->changeSession($S_SQM2);
	echo '</div>';

	# right
	echo '<div class="box right">';
		$incomingAttack = 0;
		$outgoingAttack = 0;
		for ($i = 0; $i < CTR::$data->get('playerEvent')->size(); $i++) {
			if (CTR::$data->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
				# check time to get in the circle
				$incomingAttack++;
			}
			if (CTR::$data->get('playerEvent')->get($i)->get('eventType') == EVENT_OUTGOING_ATTACK) {
				$outgoingAttack++;
			}
		}
		echo '<a href="#" class="square sh" data-target="tools-incoming-attack"><img src="' . MEDIA . 'common/nav-fleet-defense.png" alt="" />';
			echo ($incomingAttack > 0) ? '<span class="number">' . $incomingAttack . '</span>' : NULL;
		echo '</a>';

		echo '<a href="#" class="square sh" data-target="tools-outgoing-attack"><img src="' . MEDIA . 'common/nav-fleet-attack.png" alt="" />';
			echo ($outgoingAttack > 0) ? '<span class="number">' . $outgoingAttack . '</span>' : NULL;
		echo '</a>';

		echo '<span class="resource-link" style="width: 120px;">';
				echo Format::numberFormat(CTR::$data->get('playerInfo')->get('credit'));
				echo ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
		echo '</span>';
	echo '</div>';

	# overboxes
	echo '<div class="overbox left-pic" id="tools-calendar">';
		echo '<h2>Calendrier</h2>';
		echo '<div class="overflow">';
			echo '<p class="info">Nous sommes le ' . Format::ordinalNumber(Chronos::getDate('seg')) . ' segment de la ' . Format::ordinalNumber(Chronos::getDate('str')) . ' strate.</p>';
		echo '</div>';
	echo '</div>';

	echo '<div class="overbox left-pic" id="tools-refinery">';
		echo '<h2>Raffinerie</h2>';
		echo '<div class="overflow">';
			echo '<div class="number-box">';
				echo '<span class="label">production par relève</span>';
				echo '<span class="value">';
					$production = Game::resourceProduction(OrbitalBaseResource::getBuildingInfo(1, 'level', $currentBase->getLevelRefinery(), 'refiningCoefficient'), $currentBase->getPlanetResources());
					echo Format::numberFormat($production);
					$refiningBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_REFINING);
					if ($refiningBonus > 0) {
						echo '<span class="bonus">+' . Format::numberFormat(($production * $refiningBonus / 100)) . '</span>';
					}
					echo ' <img alt="ressources" src="' . MEDIA . 'resources/resource.png" class="icon-color">';
				echo '</span>';
			echo '</div>';
			echo '<a href="' . APP_ROOT . 'bases/view-refinery" class="more-link">vers la raffinerie</a>';
		echo '</div>';
	echo '</div>';

	echo '<div class="overbox left-pic" id="tools-generator">';
		echo '<h2>Générateur</h2>';
		echo '<div class="overflow">';
			$S_BQM1 = ASM::$bqm->getCurrentSession();
			ASM::$bqm->changeSession($currentBase->buildingManager);
			
			if (ASM::$bqm->size() > 0) {
				$qe = ASM::$bqm->get(0);
				echo '<div class="queue">';
					echo '<div class="item active progress" data-progress-no-reload="true" data-progress-output="lite" data-progress-current-time="' . Utils::interval(Utils::now(), $qe->dEnd, 's') . '" data-progress-total-time="' . OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'level', $qe->targetLevel, 'time') . '">';
						echo '<img class="picto" src="' . MEDIA . 'orbitalbase/' . OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'imageLink') . '.png" alt="" />';
						echo '<strong>';
							echo OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'frenchName');
							echo ' <span class="level">niv. ' . $qe->targetLevel . '</span>';
						echo '</strong>';
						
						echo '<em><span class="progress-text">' . Chronos::secondToFormat(Utils::interval(Utils::now(), $qe->dEnd, 's'), 'lite') . '</span></em>';

						echo '<span class="progress-container">';
							echo '<span style="width: ' . Format::percent(OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'level', $qe->targetLevel, 'time') - Utils::interval(Utils::now(), $qe->dEnd, 's'), OrbitalBaseResource::getBuildingInfo($qe->buildingNumber, 'level', $qe->targetLevel, 'time')) . '%;" class="progress-bar">';
							echo'</span>';
						echo '</span>';
					echo '</div>';
				echo '</div>';
			} else {
				echo '<p class="info">Aucun bâtiment en construction pour le moment.</p>';
			}

			echo '<a href="' . APP_ROOT . 'bases/view-generator" class="more-link">vers le générateur</a>';
			ASM::$bqm->changeSession($S_BQM1);
		echo '</div>';
	echo '</div>';

	echo '<div class="overbox left-pic" id="tools-dock1">';
		echo '<h2>Chantier Alpha</h2>';
		echo '<div class="overflow">';
			$S_SQM1 = ASM::$sqm->getCurrentSession();
			ASM::$sqm->changeSession($currentBase->dock1Manager);
			
			if (ASM::$sqm->size() > 0) {
				$qe = ASM::$sqm->get(0);
				echo '<div class="queue">';
					echo '<div class="item active progress" data-progress-no-reload="true" data-progress-output="lite" data-progress-current-time="' . Utils::interval(Utils::now(), $qe->dEnd, 's') . '" data-progress-total-time="' . $qe->quantity * ShipResource::getInfo($qe->shipNumber, 'time') . '">';
						echo '<img class="picto" src="' . MEDIA . 'ship/picto/' . ShipResource::getInfo($qe->shipNumber, 'imageLink') . '.png" alt="" />';
						echo '<strong>';
							echo $qe->quantity . ' ' . ShipResource::getInfo($qe->shipNumber, 'codeName') . Format::addPlural($qe->quantity);
						echo '</strong>';
						
						echo '<em><span class="progress-text">' . Chronos::secondToFormat(Utils::interval(Utils::now(), $qe->dEnd, 's'), 'lite') . '</span></em>';

						echo '<span class="progress-container">';
							echo '<span style="width: ' . Format::percent($qe->quantity * ShipResource::getInfo($qe->shipNumber, 'time') - Utils::interval(Utils::now(), $qe->dEnd, 's'), $qe->quantity * ShipResource::getInfo($qe->shipNumber, 'time')) . '%;" class="progress-bar">';
							echo'</span>';
						echo '</span>';
					echo '</div>';
				echo '</div>';
			} else {
				echo '<p class="info">Aucun vaisseau en construction pour le moment.</p>';
			}

			echo '<a href="' . APP_ROOT . 'bases/view-dock1" class="more-link">vers le chantier alpha</a>';
			ASM::$sqm->changeSession($S_SQM1);
		echo '</div>';
	echo '</div>';

	echo '<div class="overbox left-pic" id="tools-dock2">';
		echo '<h2>Chantier de ligne</h2>';
		echo '<div class="overflow">';
			$S_SQM1 = ASM::$sqm->getCurrentSession();
			ASM::$sqm->changeSession($currentBase->dock2Manager);
			
			if (ASM::$sqm->size() > 0) {
				$qe = ASM::$sqm->get(0);
				echo '<div class="queue">';
					echo '<div class="item active progress" data-progress-no-reload="true" data-progress-output="lite" data-progress-current-time="' . Utils::interval(Utils::now(), $qe->dEnd, 's') . '" data-progress-total-time="' . $qe->quantity * ShipResource::getInfo($qe->shipNumber, 'time') . '">';
						echo '<img class="picto" src="' . MEDIA . 'ship/picto/' . ShipResource::getInfo($qe->shipNumber, 'imageLink') . '.png" alt="" />';
						echo '<strong>';
							echo $qe->quantity . ' ' . ShipResource::getInfo($qe->shipNumber, 'codeName') . Format::addPlural($qe->quantity);
						echo '</strong>';
						
						echo '<em><span class="progress-text">' . Chronos::secondToFormat(Utils::interval(Utils::now(), $qe->dEnd, 's'), 'lite') . '</span></em>';

						echo '<span class="progress-container">';
							echo '<span style="width: ' . Format::percent($qe->quantity * ShipResource::getInfo($qe->shipNumber, 'time') - Utils::interval(Utils::now(), $qe->dEnd, 's'), $qe->quantity * ShipResource::getInfo($qe->shipNumber, 'time')) . '%;" class="progress-bar">';
							echo'</span>';
						echo '</span>';
					echo '</div>';
				echo '</div>';
			} else {
				echo '<p class="info">Aucun vaisseau en construction pour le moment.</p>';
			}

			echo '<a href="' . APP_ROOT . 'bases/view-dock2" class="more-link">vers le chantier de ligne</a>';
			ASM::$sqm->changeSession($S_SQM1);
		echo '</div>';
	echo '</div>';

	echo '<div class="overbox left-pic" id="tools-technosphere">';
		echo '<h2>Technosphère</h2>';
		echo '<div class="overflow">';
			$S_TQM1 = ASM::$tqm->getCurrentSession();
			ASM::$tqm->changeSession($currentBase->technoQueueManager);
			
			if (ASM::$tqm->size() > 0) {
				$qe = ASM::$tqm->get(0);
				echo '<div class="queue">';
					echo '<div class="item active progress" data-progress-no-reload="true" data-progress-output="lite" data-progress-current-time="' . Utils::interval(Utils::now(), $qe->dEnd, 's') . '" data-progress-total-time="' . TechnologyResource::getInfo($qe->technology, 'time', $qe->targetLevel) . '">';
						echo  '<img class="picto" src="' . MEDIA . 'technology/picto/' . TechnologyResource::getInfo($qe->technology, 'imageLink') . '.png" alt="" />';
						echo '<strong>' . TechnologyResource::getInfo($qe->technology, 'name');
						if (!TechnologyResource::isAnUnblockingTechnology($qe->technology)) {
							echo ' <span class="level">niv. ' . $qe->targetLevel . '</span>';
						}
						echo '</strong>';
						
						echo '<em><span class="progress-text">' . Chronos::secondToFormat(Utils::interval(Utils::now(), $qe->dEnd, 's'), 'lite') . '</span></em>';
						echo '<span class="progress-container">';
							echo '<span style="width: ' . Format::percent(TechnologyResource::getInfo($qe->technology, 'time', $qe->targetLevel) - Utils::interval(Utils::now(), $qe->dEnd, 's'), TechnologyResource::getInfo($qe->technology, 'time', $qe->targetLevel)) . '%;" class="progress-bar">';
							echo '</span>';
						echo '</span>';
					echo '</div>';
				echo '</div>';
			} else {
				echo '<p class="info">Aucune recherche en cours pour le moment.</p>';
			}

			echo '<a href="' . APP_ROOT . 'bases/view-technosphere" class="more-link">vers la technosphère</a>';
			ASM::$tqm->changeSession($S_TQM1);
		echo '</div>';
	echo '</div>';

	echo '<div class="overbox right-pic" id="tools-incoming-attack">';
		echo '<h2>Attaques entrantes</h2>';
		echo '<div class="overflow">';
			if ($incomingAttack > 0) {
				echo '<div class="queue">';

				for ($i = 0; $i < CTR::$data->get('playerEvent')->size(); $i++) {
					if (CTR::$data->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
						$commander = CTR::$data->get('playerEvent')->get($i);
						
						echo '<div class="item active progress" ';
							echo 'data-progress-no-reload="true" ';
							echo 'data-progress-output="lite" ';
							echo 'data-progress-current-time="' . Utils::interval(Utils::now(), $commander->get('eventInfo')->get('dArrival'), 's') . '" ';
							echo 'data-progress-total-time="' . Utils::interval($commander->get('eventInfo')->get('dStart'), $commander->get('eventInfo')->get('dArrival'), 's') . '">';
							echo  '<img class="picto" src="' . MEDIA . 'commander/small/' . $commander->get('eventInfo')->get('avatar') . '.png" alt="" />';
							echo '<strong>' . CommanderResources::getInfo($commander->get('eventInfo')->get('level'), 'grade') . ' ' . $commander->get('eventInfo')->get('name') . '</strong>';
							echo '<em>';
								switch ($commander->get('eventInfo')->get('travelType')) {
									case Commander::MOVE: echo 'déplacement vers ' . $commander->get('eventInfo')->get('nDestination'); break;
									case Commander::LOOT: echo 'pillage de ' . $commander->get('eventInfo')->get('nDestination'); break;
									case Commander::COLO: echo 'colonisation de ' . $commander->get('eventInfo')->get('nDestination'); break;
									case Commander::BACK: echo 'retour vers ' . $commander->get('eventInfo')->get('nStart'); break;
									default: echo 'autre'; break;
								}
							echo '</em>';
							echo '<em><span class="progress-text"></span></em>';
							echo '<span class="progress-container">';
								echo '<span class="progress-bar">';
								echo '</span>';
							echo '</span>';
						echo '</div>';
					}
				}

				echo '</div>';
			} else {
				echo '<p class="info">Aucune attaques entrantes.</p>';
			}
		echo '</div>';
		echo '<a href="' . APP_ROOT . 'fleet" class="more-link">vers l\'amirauté</a>';
	echo '</div>';

	echo '<div class="overbox right-pic" id="tools-outgoing-attack">';
		echo '<h2>Déplacement de vos flottes</h2>';
		echo '<div class="overflow">';
			if ($outgoingAttack > 0) {
				echo '<div class="queue">';

				for ($i = 0; $i < CTR::$data->get('playerEvent')->size(); $i++) {
					if (CTR::$data->get('playerEvent')->get($i)->get('eventType') == EVENT_OUTGOING_ATTACK) {
						$commander = CTR::$data->get('playerEvent')->get($i);

						echo '<div class="item active progress" ';
							echo 'data-progress-no-reload="true" ';
							echo 'data-progress-output="lite" ';
							echo 'data-progress-current-time="' . Utils::interval(Utils::now(), $commander->get('eventInfo')->get('dArrival'), 's') . '" ';
							echo 'data-progress-total-time="' . Utils::interval($commander->get('eventInfo')->get('dStart'), $commander->get('eventInfo')->get('dArrival'), 's') . '">';
							echo  '<img class="picto" src="' . MEDIA . 'commander/small/' . $commander->get('eventInfo')->get('avatar') . '.png" alt="" />';
							echo '<strong>' . CommanderResources::getInfo($commander->get('eventInfo')->get('level'), 'grade') . ' ' . $commander->get('eventInfo')->get('name') . '</strong>';
							echo '<em>';
								switch ($commander->get('eventInfo')->get('travelType')) {
									case Commander::MOVE: echo 'déplacement vers ' . $commander->get('eventInfo')->get('nArrival'); break;
									case Commander::LOOT: echo 'pillage de ' . $commander->get('eventInfo')->get('nArrival'); break;
									case Commander::COLO: echo 'colonisation de ' . $commander->get('eventInfo')->get('nArrival'); break;
									case Commander::BACK: echo 'retour vers ' . $commander->get('eventInfo')->get('nStart'); break;
									default: echo 'autre'; break;
								}
							echo '</em>';
							echo '<em><span class="progress-text"></span></em>';
							echo '<span class="progress-container">';
								echo '<span class="progress-bar"></span>';
							echo '</span>';
						echo '</div>';
					}
				}

				echo '</div>';
			} else {
				echo '<p class="info">Aucune flotte en route.</p>';
			}
		echo '</div>';
		echo '<a href="' . APP_ROOT . 'fleet" class="more-link">vers l\'amirauté</a>';
	echo '</div>';
echo '</div>';

ASM::$obm->changeSession($S_OBM1);
?>