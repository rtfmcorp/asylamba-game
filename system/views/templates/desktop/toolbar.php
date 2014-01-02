<?php
# load base
include_once ATHENA;
$S_OBM1 = ASM::$obm->getCurrentSession();
ASM::$obm->newSession();
ASM::$obm->load(array('rPlace' => CTR::$data->get('playerParams')->get('base')));
$currentBase = ASM::$obm->get();

echo '</div>';

echo '<div id="tools">';
	# left
	echo '<div class="box left">';
		echo '<a href="#" class="resource-link sh" data-target="tools-refinery">' . Format::numberFormat($currentBase->getResourcesStorage()) . ' <img class="icon-color" src="' . MEDIA . 'resources/resource.png" alt="ressources" /></a>';
		
		$S_BQM1 = ASM::$bqm->getCurrentSession();
		ASM::$bqm->changeSession($currentBase->buildingManager);
		echo '<a href="#" class="square sh" data-target="tools-generator"><img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
			echo '<span class="number">' . ASM::$bqm->size() . '</span>';
		echo '</a>';
		ASM::$bqm->changeSession($S_BQM1);

		$S_TQM1 = ASM::$tqm->getCurrentSession();
		ASM::$tqm->changeSession($currentBase->technoQueueManager);
		echo '<a href="#" class="square sh" data-target="tools-technosphere"><img src="' . MEDIA . 'orbitalbase/technosphere.png" alt="" />';
			echo '<span class="number">' . ASM::$tqm->size() . '</span>';
		echo '</a>';
		ASM::$tqm->changeSession($S_TQM1);

		$S_SQM1 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->changeSession($currentBase->dock1Manager);
		echo '<a href="#" class="square"><img src="' . MEDIA . 'orbitalbase/dock1.png" alt="" />';
			echo '<span class="number">' . ASM::$sqm->size() . '</span>';
		echo '</a>';
		ASM::$sqm->changeSession($S_SQM1);

		$S_SQM2 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->changeSession($currentBase->dock2Manager);
		echo '<a href="#" class="square"><img src="' . MEDIA . 'orbitalbase/dock2.png" alt="" />';
			echo '<span class="number">' . ASM::$sqm->size() . '</span>';
		echo '</a>';
		ASM::$sqm->changeSession($S_SQM2);
	echo '</div>';

	# right
	echo '<div class="box right">';
		$incomingAttack = 0;
		for ($i = 0; $i < CTR::$data->get('playerEvent')->size(); $i++) {
			if (CTR::$data->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
				$info = CTR::$data->get('playerEvent')->get($i)->get('eventInfo');
				if ($info[0] === TRUE) { $incomingAttack++; }
			}
		}
		echo '<a href="#" class="square"><img src="' . MEDIA . 'common/nav-fleet.png" alt="" />';
			echo ($incomingAttack > 0) ? '<span class="number">' . $incomingAttack . '</span>' : '';
		echo '</a>';

		/*echo '<a href="#" class="resource-link sh">';
				echo 'il reste <span class="releve-timer">' . Chronos::getTimer('i') . ':' . Chronos::getTimer('s') . '</span>';
		echo '</a>';*/

		echo '<a href="' . APP_ROOT . 'financial" class="resource-link" style="width: 120px;">';
				echo Format::numberFormat(CTR::$data->get('playerInfo')->get('credit'));
				echo ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
		echo '</a>';
		echo '<a href="#" class="resource-link sh" data-target="tools-pa" style="width: 60px;">';
				echo CTR::$data->get('playerInfo')->get('actionPoint');
				echo ' <img class="icon-color" src="' . MEDIA . 'resources/pa.png" alt="points d\'attaque" />';
		echo '</a>';
	echo '</div>';

	# overboxes
	echo '<div class="overbox left-pic" id="tools-refinery">';
		echo '<div class="overflow">';
			echo '<div class="number-box">';
				echo '<span class="label">production par relève</span>';
				echo '<span class="value">';
					$production = Game::resourceProduction(OrbitalBaseResource::getBuildingInfo(1, 'level', $currentBase->getLevelRefinery(), 'refiningCoefficient'), $currentBase->getPlanetResources());
					echo Format::numberFormat($production);
					$refiningBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_REFINING);
					if ($currentBase->getIsProductionRefinery() == 1 && $refiningBonus > 0) {
						echo '<span class="bonus">+' . Format::numberFormat(($production * OBM_COEFPRODUCTION) + ($production * $refiningBonus / 100)) . '</span>';
					} elseif ($currentBase->getIsProductionRefinery() == 1) {
						echo '<span class="bonus">+' . Format::numberFormat(($production * OBM_COEFPRODUCTION)) . '</span>';
					} elseif ($refiningBonus > 0) {
						echo '<span class="bonus">+' . Format::numberFormat(($production * $refiningBonus / 100)) . '</span>';
					}
					echo ' <img alt="ressources" src="' . MEDIA . 'resources/resource.png" class="icon-color">';
				echo '</span>';
			echo '</div>';
			echo '<a href="' . APP_ROOT . 'bases/view-refinery" class="more-link">vers la raffinerie</a>';
		echo '</div>';
	echo '</div>';

	echo '<div class="overbox left-pic" id="tools-generator">';
		echo '<div class="overflow">';
			$S_BQM1 = ASM::$bqm->getCurrentSession();
			ASM::$bqm->changeSession($currentBase->buildingManager);
			
			if (ASM::$bqm->size() > 0) {
				$qe = ASM::$bqm->get(0);
				echo '<div class="queue">';
					echo '<div class="item active progress" data-progress-output="lite" data-progress-current-time="' . $qe->getRemainingTime() . '" data-progress-total-time="' . OrbitalBaseResource::getBuildingInfo($qe->getBuildingNumber(), 'level', $qe->getTargetLevel(), 'time') . '">';
						echo '<img class="picto" src="' . MEDIA . 'orbitalbase/' . OrbitalBaseResource::getBuildingInfo($qe->getBuildingNumber(), 'imageLink') . '.png" alt="" />';
						echo '<strong>';
							echo OrbitalBaseResource::getBuildingInfo($qe->getBuildingNumber(), 'frenchName');
							echo ' <span class="level">niv. ' . $qe->getTargetLevel() . '</span>';
						echo '</strong>';
						
						echo '<em><span class="progress-text">' . Chronos::secondToFormat($qe->getRemainingTime(), 'lite') . '</span></em>';

						echo '<span class="progress-container">';
							echo '<span style="width: ' . Format::percent(OrbitalBaseResource::getBuildingInfo($qe->getBuildingNumber(), 'level', $qe->getTargetLevel(), 'time') - $qe->getRemainingTime(), OrbitalBaseResource::getBuildingInfo($qe->getBuildingNumber(), 'level', $qe->getTargetLevel(), 'time')) . '%;" class="progress-bar">';
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

	echo '<div class="overbox left-pic" id="tools-technosphere">';
		echo '<div class="overflow">';
			$S_TQM1 = ASM::$tqm->getCurrentSession();
			ASM::$tqm->changeSession($currentBase->technoQueueManager);
			
			if (ASM::$tqm->size() > 0) {
				$qe = ASM::$tqm->get(0);
				echo '<div class="queue">';
					echo '<div class="item active progress" data-progress-output="lite" data-progress-current-time="' . $qe->remainingTime . '" data-progress-total-time="' . TechnologyResource::getInfo($qe->technology, 'time', $qe->targetLevel) . '">';
						echo  '<img class="picto" src="' . MEDIA . 'technology/picto/' . TechnologyResource::getInfo($qe->technology, 'imageLink') . '.png" alt="" />';
						echo '<strong>' . TechnologyResource::getInfo($qe->technology, 'name');
						if (!TechnologyResource::isAnUnblockingTechnology($qe->technology)) {
							echo ' <span class="level">niv. ' . $qe->targetLevel . '</span>';
						}
						echo '</strong>';
						
						echo '<em><span class="progress-text">' . Chronos::secondToFormat($qe->remainingTime, 'lite') . '</span></em>';
						echo '<span class="progress-container">';
							echo '<span style="width: ' . Format::percent(TechnologyResource::getInfo($qe->technology, 'time', $qe->targetLevel) - $qe->remainingTime, TechnologyResource::getInfo($qe->technology, 'time', $qe->targetLevel)) . '%;" class="progress-bar">';
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

	echo '<div class="overbox" id="tools-pa">';
		echo '<div class="overflow">';
			echo '<div class="number-box">';
				echo '<span class="label">PA à la prochaine relève</span>';
				echo '<span class="value">';
					echo (((CTR::$data->get('playerInfo')->get('level') * PAM_COEFFAP) + PAM_BASEAP) + ceil(CTR::$data->get('playerInfo')->get('actionPoint') / 2));
					echo ' <img src="' . MEDIA . 'resources/pa.png" alt="pa" class="icon-color" />';
				echo '</span>';
			echo '</div>';
			echo '<a href="' . APP_ROOT . 'fleet" class="more-link">vers la gestion des flottes</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$obm->changeSession($S_OBM1);
?>