<?php
# load notif
include_once HERMES;
$S_NTM1 = ASM::$ntm->getCurrentSession();
ASM::$ntm->newSession();
ASM::$ntm->load(array('rPlayer' => CTR::$data->get('playerId'), 'readed' => 0));

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
	echo '<div class="temp-box right">';
		echo '<a href="#" class="couple hb lt" title="temps avant prochaine relève">';
				echo 'il reste <span class="releve-timer">' . Chronos::getTimer('i') . ':' . Chronos::getTimer('s') . '</span>';
		echo '</a>';

		echo '<a href="' . APP_ROOT . 'financial" class="couple hb lt" title="crédits à votre disposition">';
			echo '<strong>';
				echo Format::numberFormat(CTR::$data->get('playerInfo')->get('credit'));
				echo ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
			echo '</strong>';
		echo '</a>';
		echo '<a href="' . APP_ROOT . 'fleet" class="couple hb lt" title="points d\'attaque à votre disposition">';
			echo '<strong>';
				echo CTR::$data->get('playerInfo')->get('actionPoint');
				echo ' <img class="icon-color" src="' . MEDIA . 'resources/pa.png" alt="points d\'attaque" />';
			echo '</strong>';
		echo '</a>';

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT COUNT(id) AS n FROM message WHERE readed = 0 AND rPlayerReader = ? GROUP BY rPlayerReader');
		$qr->execute(array(CTR::$data->get('playerId')));
		$aw = $qr->fetch();
		$message = (count($aw['n']) > 0) ? $aw['n'] : 0;

		echo '<a href="' . APP_ROOT . 'message" class="couple ' . (($message > 0) ? 'active' : '') . '">';
			echo 'message' . Format::addPlural($message);
			echo '<strong>' . $message . '</strong>';
		echo '</a>';

		echo '<a href="' . APP_ROOT . 'message" id="general-notif-container" class="couple ' . ((ASM::$ntm->size() > 0) ? 'active' : '') . ' sh" data-target="new-notifications">';
			echo 'notification' . Format::addPlural(ASM::$ntm->size());
			echo '<strong>' . ASM::$ntm->size() . '</strong>';
		echo '</a>';

		$incomingAttack = 0;
		for ($i = 0; $i < CTR::$data->get('playerEvent')->size(); $i++) {
			if (CTR::$data->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
				$info = CTR::$data->get('playerEvent')->get($i)->get('eventInfo');
				if ($info[0] === TRUE) { $incomingAttack++; }
			}
		}
		if ($incomingAttack > 0) {
			echo '<a href="' . APP_ROOT . 'fleet" class="active couple hb lt" title="' . $incomingAttack . ' attaque' . Format::addPlural($incomingAttack) . ' entrante' . Format::addPlural($incomingAttack) . '">';
				echo '<strong>';
					echo $incomingAttack;
					echo ' <img class="icon-color" src="' . MEDIA . 'resources/attack.png" alt="points d\'attaque" />';
				echo '</strong>';
			echo '</a>';
		}
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

	echo '<div class="overbox" id="new-notifications">';
		echo '<div class="overflow">';
			if (ASM::$ntm->size() > 0) {
				for ($i = 0; $i < ASM::$ntm->size(); $i++) {
					$n = ASM::$ntm->get($i);
					echo '<div class="notif unreaded" data-notif-id="' . $n->getId() . '">';
						echo '<h4 class="read-notif switch-class-parent" data-class="open">' . $n->getTitle() . '</h4>';
						echo '<div class="content">' . $n->getContent() . '</div>';
						echo '<div class="footer">';
							echo '<a href="' . APP_ROOT . 'action/a-archivenotif/id-' . $n->getId() . '">archiver</a> ou ';
							echo '<a href="' . APP_ROOT . 'action/a-deletenotif/id-' . $n->getId() . '">supprimer</a><br />';
							echo '— ' . Chronos::transform($n->getDSending());
						echo '</div>';
					echo '</div>';

					if ($i == NTM_TOOLDISPLAY - 1) {
						break;
					}
				}
			} else {
				echo '<p class="info">Aucune nouvelle notification.</p>';
			}
			echo '<a href="' . APP_ROOT . 'message" class="more-link">toutes vos notifications</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$ntm->changeSession($S_NTM1);
ASM::$obm->changeSession($S_OBM1);
?>