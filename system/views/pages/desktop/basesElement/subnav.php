<?php
echo '<div id="bases-subnav">';
	echo '<div class="bind"></div>';
	echo '<div class="head">';
		echo '<h2>' . CTR::$data->get('playerInfo')->get('name') . '</h2>';
	echo '</div>';
	echo '<div class="body">';
		echo '<div class="black-box">';
			echo '<h2>' . $base->getName() . '</h2>';

			echo '<p>base orbitale</p>';

			echo '<div class="resources">';
				$storageSpace = OrbitalBaseResource::getBuildingInfo(1, 'level', $base->getLevelRefinery(), 'storageSpace');
				$storageBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE);
				if ($base->getIsProductionRefinery() == 0 && $storageBonus > 0) {
					$storageSpace += ($storageSpace * OBM_COEFPRODUCTION) + ($storageSpace * $storageBonus / 100);
				} elseif ($base->getIsProductionRefinery() == 0) {
					$storageSpace += ($storageSpace * OBM_COEFPRODUCTION);
				} elseif ($storageBonus > 0) {
					$storageSpace += ($storageSpace * $storageBonus / 100);
				}
				$percent = Format::numberFormat($base->getResourcesStorage() / $storageSpace * 100);

				echo '<strong>' . Format::numberFormat($base->getResourcesStorage()) . ' <img alt="ressources" src="' . MEDIA . 'resources/resource.png" class="icon-color"></strong>';
				echo '<span title="remplissage : ' . $percent . '%" class="progress-bar hb bl"><span class="content" style="width:' . $percent . '%;"></span></span>';
			echo '</div>';

			echo '<ul class="icon">';
				echo '<li>';
					echo '<img src="' . MEDIA . 'orbitalbase/generator.png" alt="générateur" />';
					$S_BQM1 = ASM::$bqm->getCurrentSession();
					ASM::$bqm->changeSession($base->buildingManager);
					echo '<span class="nbr">' . ASM::$bqm->size() . '</span>';
					ASM::$bqm->changeSession($S_BQM1);
				echo '</li>';
				echo '<li>';
					echo '<img src="' . MEDIA . 'orbitalbase/technosphere.png" alt="technosphère" />';
					$S_TQM1 = ASM::$tqm->getCurrentSession();
					ASM::$tqm->changeSession($base->technoQueueManager);
					echo '<span class="nbr">' . ASM::$tqm->size() . '</span>';
					ASM::$tqm->changeSession($S_TQM1);
				echo '</li>';
				echo '<li>';
					echo '<img src="' . MEDIA . 'orbitalbase/dock1.png" alt="chantier alpha" />';
					$S_SQM1 = ASM::$sqm->getCurrentSession();
					ASM::$sqm->changeSession($base->dock1Manager);
					echo '<span class="nbr">' . ASM::$sqm->size() . '</span>';
					ASM::$sqm->changeSession($S_SQM1);
				echo '</li>';
				echo '<li>';
					echo '<img src="' . MEDIA . 'orbitalbase/dock2.png" alt="chantier de ligne" />';
					$S_SQM2 = ASM::$sqm->getCurrentSession();
					ASM::$sqm->changeSession($base->dock2Manager);
					echo '<span class="nbr">' . ASM::$sqm->size() . '</span>';
					ASM::$sqm->changeSession($S_SQM2);
				echo '</li>';
			echo '</ul>';

			# affichage du nombre d'attaques entrantes
			$incomingAttack = 0;
			for ($i=0; $i < CTR::$data->get('playerEvent')->size(); $i++) { 
				if (CTR::$data->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
					$dateList = CTR::$data->get('playerEvent')->get($i)->get('eventInfo');
					if ($dateList[0] === TRUE) {
						$incomingAttack++;
					}
				}
			}
			if ($incomingAttack == 1) {
				echo 'une attaque entrante';
			} else if ($incomingAttack > 1) {
				echo $incomingAttack . ' attaques entrantes';
			}
			
		echo '</div>';
		
		if (CTR::$data->get('playerBase')->get('ob')->size() >= 2) {
			echo '<a class="toggle-bases sh" data-target="base-bull" href="#">Changer de base</a>';
			echo '<div class="toogle-bases-content" id="base-bull">';
				for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
					echo '<a href="' . APP_ROOT . 'bases/base-' . CTR::$data->get('playerBase')->get('ob')->get($i)->get('id') . '">';
						echo '<em>Base orbitale</em>';
						echo '<strong>' . CTR::$data->get('playerBase')->get('ob')->get($i)->get('name') . '</strong>';
					echo '</a>';
				}
			echo '</div>';
		}
	echo '</div>';
	echo '<div class="foot"></div>';
echo '</div>';
?>