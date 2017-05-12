<?php

use Asylamba\Classes\Library\Format;

$colorManager = $this->getContainer()->get('demeter.color_manager');
$database = $this->getContainer()->get('database');

$qr = 'SELECT
		se.id AS id,
		se.rColor AS color,
		se.name AS name,
		se.points AS points,
		(SELECT COUNT(sy.id) FROM system AS sy WHERE sy.rSector = se.id) AS nbc0,';

$factions = $colorManager->getAll();
foreach ($factions as $f) {
	$qr .= '(SELECT COUNT(sy.id) FROM system AS sy WHERE sy.rColor = ' . $f->id . ' AND sy.rSector = se.id) AS nbc' . $f->id .',';
}

$qr = substr($qr, 0, -1) . ' FROM sector AS se ORDER BY (nbc' . $faction->id . ' / nbc0) DESC';

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
					
					foreach ($factions as $f) {
						if ($f->id === 0) {
							continue;
						}
						$percents['color' . $f->id] = Format::percent($sector['nbc' . $f->id], $sector['nbc0']);
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
