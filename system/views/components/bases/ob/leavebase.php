<?php
# leavebase component
# in base.ob package

# affichage du lien d'abandon de base

# require
	# {orbitalBase}		ob_obSituation
	# [{commander}]		commanders_obSituation

$onMission = FALSE;
foreach ($commanders_obSituation as $commander) {
	if (in_array($commander->statement, [Commander::MOVING])) {
		$onMission = TRUE;
	}
}

echo '<div class="component generator">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Abandonner cette planète</h4>';

			echo '<p>Pour abandonner une planète, aucun de vos commandants ne doit être en mission. De plus, vous devez posséder cette planète depuis plus de ' . OrbitalBase::COOL_DOWN . ' relèves.</p>';
			echo '<p>Une planète abandonnée peut être conquise par n\'importe qui. Les commandants en orbite autour de celle-ci continuent à la défendre. Les vaisseaux dans le hangar sont conservés. Cependant, les routes commerciales ainsi que les offres de ventes sont supprimées. De plus, toutes les constructions programmées sont annulées.</p>';

			echo '<hr>';

			if ($onMission) {
				echo '<span class="more-button">Action impossible</span>';
				echo '<p>Certains de vos commandants sont en mission.</p>';
			} elseif (Utils::interval(Utils::now(), $ob_obSituation->dCreation, 'h') < OrbitalBase::COOL_DOWN) {
				echo '<span class="more-button">Action impossible</span>';
				echo '<p>Vous possédez la planète depuis moins de ' . OrbitalBase::COOL_DOWN . ' relèves.</p>';
			} else {
				echo '<a class="more-button confirm" href="' . Format::actionBuilder('leavebase', ['id' => $ob_obSituation->getId()]) . '">Abandonner la planète</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';