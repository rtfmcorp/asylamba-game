<?php
# listReport componant
# in poseidon package

# liste tous les derniers rapports d'espionnage du joueur

# require
	# [{report}]	report_listReport

echo '<div class="component report">';
	echo '<div class="head skin-2">';
		echo '<h2>Rapports d\'espionnage</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="' . APP_ROOT . 'action/a-deleteallspyreport" class="hb lt" title="suppression définitive de tous les rapports">tout supprimer</a></span>';
				echo '<span><a href="#" class="hb lt sh" data-target="info-report" title="plus d\'infos">?</a></span>';
			echo '</div>';

			echo '<p class="info" id="info-report" style="display:none;">';
				echo 'Tmp.';
			echo '</p>'; 
			
			if (count($report_listReport) > 0) {
				foreach ($report_listReport as $r) {
					$title = 'Espionnage de ' . $r->placeName;

					echo '<div class="small-report">';
						if (CTR::$get->get('report') != $r->id) {
							echo '<a class="open-button" href="' . APP_ROOT . 'fleet/view-spyreport/report-' . $r->id . '">&#8594;</a>';
						} else {
							echo '<a class="open-button" href="' . APP_ROOT . 'fleet/view-spyreport">&#215;</a>';
						}
						echo '<h4 class="switch-class-parent" data-class="open">' . $title . '</h4>';
						echo '<div class="content">l\'espionnage a eu lieu ' . Chronos::transform($r->dSpying) . '</div>';
						echo '<div class="footer">';
							echo '<a href="' . APP_ROOT . 'action/a-deletespyreport/id-' . $r->id . '">supprimer</a>';
						echo '</div>';
					echo '</div>';
				}
			} else {
				echo '<p>Il n\'y a aucun rapport d\'espionnage.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>