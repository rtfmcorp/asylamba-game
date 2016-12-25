<?php

use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Library\Format;

$colorManager = $this->getContainer()->get('demeter.color_manager');
$database = $this->getContainer()->get('database');

$_CLM = $colorManager->getCurrentSession();
$colorManager->newSession();
$colorManager->load();

$qr = 'SELECT
		se.id AS id,
		se.rColor AS color,
		se.name AS name,
		se.points AS points,
		(SELECT COUNT(sy.id) FROM system AS sy WHERE sy.rSector = se.id) AS nbc0,';

for ($i = 1; $i < $colorManager->size(); $i++) {
	if ($i < $colorManager->size() - 1) {
		$qr .= '(SELECT COUNT(sy.id) FROM system AS sy WHERE sy.rColor = ' . $colorManager->get($i)->id . ' AND sy.rSector = se.id) AS nbc' . $colorManager->get($i)->id .',';
	} else {
		$qr .= '(SELECT COUNT(sy.id) FROM system AS sy WHERE sy.rColor = ' . $colorManager->get($i)->id . ' AND sy.rSector = se.id) AS nbc' . $colorManager->get($i)->id;
	}
}

$qr .= '	FROM sector AS se ORDER BY (nbc' . $faction->id . ' / nbc0) DESC';

$qr = $database->prepare($qr);
$qr->execute();
$aw = $qr->fetchAll(); $qr->closeCursor();


$sectort = array(
	'Secteurs conquis' => array(),
	'Secteurs en balance' => array()
);

for ($i = 0; $i < count($aw); $i++) {
	if ($aw[$i]['color'] == $faction->id) {
		$sectort['Secteurs conquis'][] = $aw[$i];
	} else {
		$sectort['Secteurs en balance'][] = $aw[$i];
	}
}

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Territoires</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($sectort as $type => $sectors) {
				$displayed = 0;

				echo '<h4>' . $type . '</h4>';
				echo '<ul class="list-type-1">';

				foreach ($sectors as $sector) {
					$percents = array();
					
					for ($j = 1; $j < $colorManager->size(); $j++) {
						$percents['color' . $colorManager->get($j)->id] = Format::percent($sector['nbc' . $colorManager->get($j)->id], $sector['nbc0']);
					}

					arsort($percents);

					if ($sector['color'] == $faction->id || ($sector['nbc' . $faction->id] > 0)) {
						echo '<li>';
							echo '<a href="#" class="picto color' . $sector['color'] . '">' . $sector['id'] . '</a>';
							echo '<span class="label">' . $sector['name'] . ' (' . $sector['points'] . ' point' . Format::plural($sector['points']). ')</span>';
							echo '<span class="value">' . Format::percent($sector['nbc' . $faction->id], $sector['nbc0']) . ' %</span>';
							echo '<span class="progress-bar hb bl" title="partage des systèmes entre les factions">';
							foreach ($percents as $color => $percent) {
								echo '<span style="width:' . $percent . '%;" class="content ' . $color . '"></span>';
							}
							echo '</span>';
						echo '</li>';

						$displayed++;
					}
				}

				echo '</ul>';
				
				if ($displayed == 0) {
					echo '<p>Aucun secteur</p>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
$colorManager->changeSession($_CLM);
