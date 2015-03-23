<?php
# listReport componant
# in aress package

# liste tous les derniers rapports de combats du joueur

# require
	# [{report}]	report_listReport
	# (INT)			type_listReport

echo '<div class="component report">';
	echo '<div class="head skin-2">';
		if ($type_listReport == 1) {
			echo '<h2>Archives Militaires</h2>';
		}
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo $type_listReport == 1
				? '<h4>Rapports d\'attaque</h4>'
				: '<h4>Rapports de défense</h4>';
			
			if (count($report_listReport) > 0) {
				echo '<div class="set-item">';
					foreach ($report_listReport as $r) {
						list($title, $img) = $r->getTypeOfReport(CTR::$data->get('playerInfo')->get('color'));

						echo '<div class="item">';
							echo '<div class="left">';
								echo '<img class="color' . ($type_listReport == 1 ? $r->colorD : $r->colorA) . '" src="' . MEDIA . 'map/action/' . $img . '" alt="" />';
							echo '</div>';

							echo '<div class="center">';
								echo '<strong>' . $title . '</strong>';
								echo Chronos::transform($r->dFight);
							echo '</div>';

							echo '<div class="right">';
								echo '<a class="' . (CTR::$get->equal('report', $r->id)  ? 'active' : NULL) . '" href="' . APP_ROOT . 'fleet/view-archive/report-' . $r->id . '"></a>';
							echo '</div>';
						echo '</div>';
					}
				echo '</div>';
			} else {
				echo '<p>Il n\'y a aucun rapport de combat dans vos archives militaires.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>