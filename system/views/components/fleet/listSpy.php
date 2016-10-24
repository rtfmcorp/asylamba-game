<?php
# listReport componant
# in aress package

# liste tous les derniers rapports de combats du joueur

# require
	# [{spyreport}]	spyreport_listSpy

use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Chronos;
use Asylamba\Classes\Worker\CTR;

$i = 0;

echo '<div class="component report">';
	echo '<div class="head skin-2">';
		echo '<h2>Archives d\'espionnage</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="' . Format::actionBuilder('deleteallspyreport') . '">tout supprimer</a></span>';
			echo '</div>';
			if (count($spyreport_listSpy) > 0) {
				echo '<div class="set-item">';
					foreach ($spyreport_listSpy as $r) {
						echo '<div class="item">';
							echo '<div class="left">';
								echo '<img src="' . MEDIA . 'map/action/spy.png" alt="" class="color' . $r->placeColor . '" />';
							echo '</div>';

							echo '<div class="center">';
								echo '<strong><a href="' . APP_ROOT . 'map/place-' . $r->rPlace . '">' . $r->placeName . '</a></strong>';
								echo Chronos::transform($r->dSpying);
							echo '</div>';

							echo '<div class="right">';
								echo '<a class="' . ((CTR::$get->equal('report', $r->id) OR (!CTR::$get->exist('report') AND $i == 0))  ? 'active' : NULL) . '" href="' . APP_ROOT . 'fleet/view-spyreport/report-' . $r->id . '"></a>';
							echo '</div>';
						echo '</div>';

						$i++;
					}
				echo '</div>';
			} else {
				echo '<p>Il n\'y a aucun rapport d\'espionnage dans vos archives.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
