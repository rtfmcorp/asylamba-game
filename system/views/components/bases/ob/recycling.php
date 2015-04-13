<?php
# recycling component
# in athena.bases package

# affichage du Centre de Recyclage

# require
	# {orbitalBase}					ob_recycling
	# {RecyclingMission session} 	recyclingSession
	# {RecyclingLog session}	 	missionLogSessions

$S_REM2 = ASM::$rem->getCurrentSession();
ASM::$rem->changeSession($recyclingSession);

$S_RLM2 = ASM::$rlm->getCurrentSession();
ASM::$rlm->changeSession($missionLogSessions);

echo '<div class="component building">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/recycling.png" alt="" />';
		echo '<h2>' . OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::RECYCLING, 'frenchName') . '</h2>';
		echo '<em>Niveau ' . $ob_recycling->getLevelRecycling() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$totalRecyclers = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::RECYCLING, 'level', $ob_recycling->levelRecycling, 'nbRecyclers');
			$busyRecyclers  = 0;

			for ($i = 0; $i < ASM::$rem->size(); $i++) { 
				$busyRecyclers += ASM::$rem->get($i)->recyclerQuantity;
			}

			$freeRecyclers  = $totalRecyclers - $busyRecyclers;

			echo '<div class="number-box">';
				echo '<span class="label">recycleurs utilisés / totaux</span>';
				echo '<span class="value">' . $busyRecyclers . ' / ' . $totalRecyclers . '</span>';
			echo '</div>';

			echo '<div class="number-box grey">';
				echo '<span class="label">recycleur' . Format::plural($freeRecyclers) . ' libre' . Format::plural($freeRecyclers) . '</span>';
				echo '<span class="value">' . $freeRecyclers . '</span>';
			echo '</div>';

			echo '<div class="number-box grey">';
				echo '<span class="label">capacité de transport d\'un recycleur</span>';
				echo '<span class="value">' . Format::number(RecyclingMission::RECYCLER_CAPACTIY) . ' <img alt="ressources" src="' . MEDIA . 'resources/resource.png" class="icon-color"></span>';
			echo '</div>';

			echo '<hr />';

			$missionQuantity = ASM::$rem->size();

			echo '<div class="number-box ' . ($missionQuantity == 0 ? 'grey' : '') . '">';
				echo '<span class="label">missions actives</span>';
				echo '<span class="value">' .  $missionQuantity . '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

for ($i = 0; $i < ASM::$rem->size(); $i++) { 
	$mission = ASM::$rem->get($i);

	echo '<div class="component">';
		echo '<div class="head skin-5">';
			if ($i == 0) {
				echo '<h2>Mission' . Format::plural(ASM::$rem->size()) . ' en cours</h2>';
			}
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				# usefull vars
				$missionID = md5($mission->id . $mission->recyclerQuantity);
				$missionID = strtoupper(substr($missionID, 0, 3) . '-' . substr($missionID, 3, 6) . '-' . substr($missionID, 10, 2));

				$percent   = Utils::interval(Utils::now(), $mission->uRecycling, 's') / $mission->cycleTime * 100;
				$travelTime= ($mission->cycleTime - RecyclingMission::RECYCLING_TIME) / 2;
				$beginRECY = Format::percent($travelTime, $mission->cycleTime);
				$endRECY   = Format::percent($travelTime + RecyclingMission::RECYCLING_TIME, $mission->cycleTime);

				echo '<div class="build-item base-type">';
					echo '<div class="name">';
						echo '<img src="' . MEDIA . 'orbitalbase/recycler.png" alt="">';
						echo '<strong>Mission<br /> ' . $missionID . '</strong>';
					echo '</div>';

					echo '<p class="desc">La mission recycle la <strong>' . Game::convertPlaceType($mission->typeOfPlace) . '</strong> située aux coordonnées <strong><a href="'. APP_ROOT . 'map/place-' . $mission->rTarget . '">' . Game::formatCoord($mission->xSystem, $mission->ySystem, $mission->position, $mission->sectorId) . '</a></strong>.<br /><br />
					Il reste <strong>' . Format::number($mission->resources * $mission->coefResources / 100) . '</strong> ressources, <strong>' . Format::number($mission->resources * $mission->coefHistory / 100) . '</strong> débris et <strong>' . Format::number($mission->resources * $mission->population / 100) . '</strong> gaz nobles.</p>';

					echo '<p>Retour ' . Chronos::transform(Utils::addSecondsToDate($mission->uRecycling, $mission->cycleTime)) . '</p>';
					echo '<p><span class="progress-bar">';
						echo '<span style="width:' . $percent . '%;" class="content"></span>';
						echo '<span class="step hb lt" title="début du recyclage" style="left: ' . $beginRECY . '%;"></span>';
						echo '<span class="step hb lt" title="fin du recyclage" style="left: ' . $endRECY . '%;"></span>';
					echo '</span></p>';
				echo '</div>';

				echo $mission->statement == RecyclingMission::ST_BEING_DELETED
					? '<p>Cette mission a été annulée, les recycleurs terminent la mission puis deviennent disponibles.</p>'
					: '<p><a href="' . Format::actionBuilder('cancelmission', ['id' => $mission->id, 'place' => $mission->rBase]) . '" class="common-link">Annuler la mission</a></p>';

				echo '<ul class="list-type-1">';
					echo '<li>';
						echo '<span class="label">Recycleurs engagés dans la mission</span>';
						echo '<span class="value">' . Format::number($mission->recyclerQuantity) . '</span>';
					echo '</li>';
					echo '<li>';
						echo '<span class="label">Soute totale de la mission</span>';
						echo '<span class="value">' . Format::number($mission->recyclerQuantity * RecyclingMission::RECYCLER_CAPACTIY) . ' <img alt="ressources" src="' . MEDIA . 'resources/resource.png" class="icon-color"></span>';
					echo '</li>';
					echo '<li>';
						echo '<span class="label">Durée du cycle</span>';
						echo '<span class="value">' . Chronos::secondToFormat($mission->cycleTime, 'short') . '</span>';
					echo '</li>';
				echo '</ul>';

				echo '<h4>Dernières livraisons</h4>';
				$nb = 0;
				for ($j = 0; $j < ASM::$rlm->size(); $j++) {
					if (ASM::$rlm->get($j)->rRecycling == $mission->id) {
						$log = ASM::$rlm->get($j);

						$wedge['ressource'] = $log->resources;
						$wedge['crédit'] = $log->credits;
						$wedge['pégase'] = $log->ship0;
						$wedge['satyre'] = $log->ship1;
						$wedge['chimère'] = $log->ship2;
						$wedge['sirène'] = $log->ship3;
						$wedge['dryade'] = $log->ship4;
						$wedge['méduse'] = $log->ship5;
						$wedge['griffon'] = $log->ship6;
						$wedge['cyclope'] = $log->ship7;
						$wedge['minotaure'] = $log->ship8;
						$wedge['hydre'] = $log->ship9;
						$wedge['cerbère'] = $log->ship10;
						$wedge['phénix'] = $log->ship11;
						$wedge = array_filter($wedge);

						echo '<p class="info">';
							echo 'La mission a ramené ';
							$n = 1;
							foreach ($wedge as $type => $number) {
								echo '<strong>' . Format::number($number) . '</strong> ' . $type . Format::plural($number);
								echo $n == count($wedge) - 1
									? ' et ' : ', ';
								$n++;
							}
							echo '<em>' . Chronos::transform($log->dLog) . '</em>';
						echo '</p>';

						$nb++;
					}

					if ($nb > 10) {
						break;
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::RECYCLING, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$rlm->changeSession($S_RLM2);
ASM::$rem->changeSession($S_REM2);
?>