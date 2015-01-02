<?php
# obFastView componant
# in athena package

# affiche les infos importantes d'une orbital base, dispose de lien rapide vers la main page

# require
	# {orbitalBase}		ob_obFastView
	# (int)				ob_index

echo '<div class="component">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($ob_obFastView->getPlanetPopulation()) . '.png" alt="' . $ob_obFastView->getName() . '" />';
		echo '<h2>' . $ob_obFastView->getName() . '</h2>';
		echo '<em>';
			echo PlaceResource::get($ob_obFastView->typeOfBase, 'name') . ' — ' . $ob_obFastView->getPoints() . ' points';
		echo '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="' . Format::actionBuilder('switchbase', ['base' => $ob_obFastView->getId(), 'page' => 'bases']) . '">vers la gestion de la base</a></span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">Ressources en stock</span>';
				echo '<span class="value">';
					echo Format::numberFormat($ob_obFastView->getResourcesStorage());
					echo ' <img alt="ressources" src="' . MEDIA . 'resources/resource.png" class="icon-color">';
				echo '</span>';

				$storageSpace = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $ob_obFastView->getLevelStorage(), 'storageSpace');
				$storageBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE);
				if ($storageBonus > 0) {
					$storageSpace += ($storageSpace * $storageBonus / 100);
				}
				$percent = Format::numberFormat($ob_obFastView->getResourcesStorage() / $storageSpace * 100);
				echo '<span class="progress-bar hb bl" title="remplissage : ' . $percent . '%">';
					echo '<span style="width:' . $percent . '%;" class="content"></span>';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $ob_obFastView->getId(), 'page' => 'refinery']) . '" class="link hb lt" title="vers la raffinerie">→</a>';
				echo '</span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">Production par relève</span>';
				echo '<span class="value">';
					$production = Game::resourceProduction(OrbitalBaseResource::getBuildingInfo(1, 'level', $ob_obFastView->getLevelRefinery(), 'refiningCoefficient'), $ob_obFastView->getPlanetResources());
					echo Format::numberFormat($production);
					$refiningBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_REFINING);
					if ($refiningBonus > 0) {
						echo '<span class="bonus">+' . Format::numberFormat(($production * $refiningBonus / 100)) . '</span>';
					}
					echo ' <img alt="ressources" src="' . MEDIA . 'resources/resource.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			echo '<hr />';

			$S_BQM1 = ASM::$bqm->getCurrentSession();
			ASM::$bqm->changeSession($ob_obFastView->buildingManager);
			echo '<div class="number-box ' . ((ASM::$bqm->size() == 0) ? 'grey' : '') . '">';
				echo '<span class="label">Activités du Générateur</span>';
				echo '<span class="value">' . ASM::$bqm->size() . '</span>';
				echo '<span class="group-link">';
					echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $ob_obFastView->getId(), 'page' => 'generator']) . '" class="link hb lt" title="vers le générateur">→</a>';
				echo '</span>';
			echo '</div>';
			ASM::$bqm->changeSession($S_BQM1);

			$S_SQM1 = ASM::$sqm->getCurrentSession();
			ASM::$sqm->changeSession($ob_obFastView->dock1Manager);
			$dock1  = ASM::$sqm->size();
			ASM::$sqm->changeSession($ob_obFastView->dock2Manager);
			$dock2  = ASM::$sqm->size();
			echo '<div class="number-box ' . ((($dock1 + $dock2) == 0) ? 'grey' : '') . '">';
				echo '<span class="label">Activités du Chantier Alpha / de Ligne</span>';
				echo '<span class="value">' . $dock1 . ' / ' . $dock2 . '</span>';
				echo '<span class="group-link">';
					echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $ob_obFastView->getId(), 'page' => 'dock1']) . '" class="link hb lt" title="vers le chantier alpha">→</a>';
				echo '</span>';
			echo '</div>';
			ASM::$sqm->changeSession($S_SQM1);

			$S_TQM1 = ASM::$tqm->getCurrentSession();
			ASM::$tqm->changeSession($ob_obFastView->technoQueueManager);
			echo '<div class="number-box ' . ((ASM::$tqm->size() == 0) ? 'grey' : '') . '">';
				echo '<span class="label">Activités de la Technosphère</span>';
				echo '<span class="value">' . ASM::$tqm->size() . '</span>';
				echo '<span class="group-link">';
					echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $ob_obFastView->getId(), 'page' => 'technosphere']) . '" class="link hb lt" title="vers la technosphère">→</a>';
				echo '</span>';
			echo '</div>';
			ASM::$tqm->changeSession($S_TQM1);

			echo '<hr />';

			include_once ARES;

			$S_COM1 = ASM::$com->getCurrentSession();
			ASM::$com->newSession();
			ASM::$com->load(array('c.rPlayer' => $ob_obFastView->getRPlayer(), 'c.rBase' => $ob_obFastView->getRPlace(), 'c.statement' => array(COM_AFFECTED, COM_MOVING)));
			
			$movingFleets = 0;
			$defenseFleets = 0;
			for ($j = 0; $j < ASM::$com->size(); $j++) { 
				if (ASM::$com->get($j)->getStatement() == COM_MOVING) {
					$movingFleets++;
				} else {
					$defenseFleets++;
				}
			}	
			echo '<div class="number-box grey">';
				echo '<span class="label">Flotte de défense / Flotte en mission</span>';
				echo '<span class="value">' . $defenseFleets . ' / ' . $movingFleets . '</span>';
			echo '</div>';
			ASM::$com->changeSession($S_COM1);

			echo '<div class="number-box grey">';
				echo '<span class="label">Attaque entrante</span>';
				echo '<span class="value">0</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>