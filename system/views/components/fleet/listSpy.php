<?php
# listReport componant
# in aress package

# liste tous les derniers rapports de combats du joueur

# require
	# [{spyreport}]	spyreport_listSpy

echo '<div class="component report">';
	echo '<div class="head skin-2">';
		echo '<h2>Archives d\'espionnage</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if (count($spyreport_listSpy) > 0) {
				echo '<div class="set-report">';
					foreach ($spyreport_listSpy as $r) {
						echo '<div class="item">';
							echo '<div class="left">';
								echo '<img src="' . MEDIA . 'map/action/spy.png" alt="" class="color' . $r->placeColor . '" />';
							echo '</div>';

							echo '<div class="center">';
								echo '<strong>' . $r->placeName . '</strong>';
								echo Chronos::transform($r->dSpying);
							echo '</div>';

							echo '<div class="right">';
								echo '<a class="' . (CTR::$get->equal('report', $r->id)  ? 'active' : NULL) . '" href="' . APP_ROOT . 'fleet/view-spyreport/report-' . $r->id . '"></a>';
							echo '</div>';
						echo '</div>';
					}
				echo '</div>';
			} else {
				echo '<p>Il n\'y a aucun rapport d\'espionnage dans vos archives.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>