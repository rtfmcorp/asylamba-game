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

			echo '<p>Pour abandonner une planète, aucun de vos commandants ne doivent être en mission.</p>';
			echo '<p>Une planète abandonnée peut être conquise par n\'importe qui. Les commandants en orbite autour de celle-ci continuent à la défendre. Les vaisseaux dans le hangar sont conservés. Cepandant, les routes commerciales ainsi que les offres de ventes sont supprimées. De plus toutes les constuctions programmées sont annulées.</p>';

			echo '<hr>';

			if ($onMission) {
				echo '<span class="more-button">Action impossible</span>';
				echo '<p>Certains de vos commandants sont en mission.</p>';
			} else {
				echo '<a class="more-button" href="' . Format::actionBuilder('leavebase', ['id' => $ob_obSituation->getId()]) . '">Abandonner la planète</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';