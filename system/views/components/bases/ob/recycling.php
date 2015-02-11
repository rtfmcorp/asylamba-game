<?php
# recycling component
# in athena.bases package

# affichage du Centre de Recyclage

# require
	# {orbitalBase}					ob_recycling
	# {RecyclingMission session} 	recyclingSession
	# {RecyclingLog session array} 	missionLogSessions[]

$S_REM2 = ASM::$rem->getCurrentSession();
ASM::$rem->changeSession($recyclingSession);


echo '<div class="component building">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/recycling.png" alt="" />';
		echo '<h2>' . OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::RECYCLING, 'frenchName') . '</h2>';
		echo '<em>niveau ' . $ob_recycling->getLevelRecycling() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$totalRecyclers = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::RECYCLING, 'level', $ob_recycling->levelRecycling, 'nbRecyclers');
			echo '<div class="number-box">';
				echo '<span class="label">Quantité totale de recycleurs</span>';
				echo '<span class="value">' . $totalRecyclers . '</span>';
			echo '</div>';

			$freeRecyclers = $totalRecyclers;
			for ($i = 0; $i < ASM::$rem->size(); $i++) { 
				$freeRecyclers -= ASM::$rem->get($i)->recyclerQuantity;
			}
			echo '<div class="number-box ' . ($freeRecyclers == 0 ? 'grey' : '') . '">';
				echo '<span class="label">Recycleurs disponibles</span>';
				echo '<span class="value">' .  $freeRecyclers . '</span>';
			echo '</div>';

			echo '<hr />';

			$missionQuantity = ASM::$rem->size();
			echo '<div class="number-box ' . ($missionQuantity == 0 ? 'grey' : '') . '">';
				echo '<span class="label">Missions actives</span>';
				echo '<span class="value">' .  $missionQuantity . '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

for ($i = 0; $i < ASM::$rem->size(); $i++) { 
	$mission = ASM::$rem->get($i);

	$S_RLM2 = ASM::$rlm->getCurrentSession();
	ASM::$rlm->changeSession($missionLogSessions[$i]);

	echo '<div class="component">';
		echo '<div class="head skin-2">';
			echo '<h2>Mission en cours</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				echo '<h3>Descriptif de mission</h3>';
				echo '<p>Recycleurs utilisés : ' . $mission->recyclerQuantity . '</p>';
				echo '<p>Lieu cible : ' . $mission->rTarget . '</p>';
				echo '<p>Temps de cycle : ' . $mission->cycleTime . ' secondes</p>';

				echo '<div class="number-box">';
					echo '<span class="label">progression de la mission</span>';
					echo '<span class="value">';
						echo Format::numberFormat($mission->cycleTime);
						echo ' <img alt="ressources" src="' . MEDIA . 'resources/time.png" class="icon-color">';
					echo '</span>';					  
					$percent = Format::numberFormat(Utils::interval(Utils::now(), $mission->uRecycling, 's') / $mission->cycleTime * 100);
					echo '<span class="progress-bar hb bl" title="remplissage : ' . $percent . '%">';
						echo '<span style="width:' . $percent . '%;" class="content"></span>';
					echo '</span>';
				echo '</div>';

				echo '<p>Résumé des gains lors des 10 derniers chargements : </p>';
				echo '<ul class="list-type-1">';
				for ($i = 0; $i < min(ASM::$rlm->size(), 10); $i++) {
					$log = ASM::$rlm->get($i);
					echo ($i == 0) ? '<li class="strong">' : '<li>';
						echo '<span class="label">chargement ' . $i . '</span>';
						echo '<span class="value">';
							echo Format::numberFormat($log->resources) . ' ressources, ' . Format::numberFormat($log->credits) . ' crédits et x vaisseaux gagnés';
						echo '</span>';
					echo '</li>';
				}
				echo '</ul>';
				echo '<a href="' . Format::actionBuilder('cancelmission', ['id' => $mission->id]) . '" class="hb lt right-link" title="annuler la mission">annuler la mission</a>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	ASM::$rlm->changeSession($S_RLM2);
}

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Hangar à recycleurs</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h3>Quantité de recycleurs par niveau</h3>';
			echo '<ul class="list-type-1">';
				$level = $ob_recycling->getLevelRecycling();
				$from  = ($level < 3)  ? 1  : $level - 2;
				$to    = ($level > 25) ? 31 : $level + 5;
				for ($i = $from; $i < $to; $i++) {
					echo ($i == $level) ? '<li class="strong">' : '<li>';
						echo '<span class="label">niveau ' . $i . '</span>';
						echo '<span class="value">';
							$recyclerQty = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::RECYCLING, 'level', $i, 'nbRecyclers');
							echo Format::numberFormat($recyclerQty) . ' recycleur' . Format::addPlural($recyclerQty);
						echo '</span>';
					echo '</li>';
				}
				echo '</ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';

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

ASM::$rem->changeSession($S_REM2);

?>