<?php
# listReport componant
# in aress package

# liste tous les derniers rapports de combats du joueur

# require
	# [{report}]	report_listReport

echo '<div class="component report">';
	echo '<div class="head skin-2">';
		echo '<h2>Archives Militaires</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="#" class="hb lt" title="cette action n\'a pas encore été développée">tout supprimer</a></span>';
				echo '<span><a href="#" class="hb lt sh" data-target="info-report" title="plus d\'infos">?</a></span>';
			echo '</div>';

			echo '<p class="info" id="info-report" style="display:none;">';
				echo 'Tmp.';
			echo '</p>'; 
			
			if (count($report_listReport) > 0) {
				foreach ($report_listReport as $r) {
					if ($r->rPlayerAttacker == CTR::$data->get('playerId')) {
						if ($r->rPlayerWinner == $r->rPlayerAttacker) {
							if ($r->type == 1) {
								$title = 'Pillage de ' . $r->placeName;
							} else {
								if ($r->rPlayerDefender == 0) {
									$title = 'Colonisation réussie';
								} else {
									$title = 'Conquête de ' . $r->placeName;
								}
							}
						} else {
							if ($r->type == 1) {
								$title = 'Pillage raté de ' . $r->placeName;
							} else {
								if ($r->rPlayerDefender == 0) {
									$title = 'Colonisation ratée';
								} else {
									$title = 'Conquête ratée de ' . $r->placeName;
								}
							}
						}
					} else {
						if ($r->rPlayerWinner == $r->rPlayerDefender) {
							if ($r->type == 1) {
								$title = 'Pillage repoussé';
							} else {
								$title = 'Conquête repoussée';
							}
						} else {
							if ($r->type == 1) {
								$title = 'Défense ratée lors d\'un pillage';
							} else {
								$title = 'Défense ratée lors d\'un conquête';
							}
						}
					}

					echo '<div class="small-report">';
						if (CTR::$get->get('report') != $r->id) {
							echo '<a class="open-button" href="' . APP_ROOT . 'fleet/view-archive/report-' . $r->id . '">&#8594;</a>';
						} else {
							echo '<a class="open-button" href="' . APP_ROOT . 'fleet/view-archive">&#215;</a>';
						}
						echo '<h4 class="switch-class-parent" data-class="open">' . $title . '</h4>';
						echo '<div class="content">le combat à eu lieu ' . Chronos::transform($r->dFight) . '</div>';
						echo '<div class="footer">';
							# echo '<a href="' . APP_ROOT . 'action/a-archivereport/id-' . $r->id . '">archiver</a> ou ';
							echo '<a href="#" class="hb lt" title="non implémenté">supprimer</a><br />';
						echo '</div>';
					echo '</div>';
				}
			} else {
				echo '<p>Il n\'y a aucun rapport de combat dans vos archives militaires.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>