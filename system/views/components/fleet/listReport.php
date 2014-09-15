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
			/*echo '<div class="tool">';
				echo '<span><a href="#" class="hb lt" title="cette action n\'a pas encore été développée">tout supprimer</a></span>';
				echo '<span><a href="#" class="hb lt sh" data-target="info-report" title="plus d\'infos">?</a></span>';
			echo '</div>';*/
			echo $type_listReport == 1
				? '<h3>Rapports d\'attaque</h3>'
				: '<h3>Rapports de défense</h3>';
			
			if (count($report_listReport) > 0) {
				echo '<div class="set-report">';
					foreach ($report_listReport as $r) {
						if ($r->rPlayerAttacker == CTR::$data->get('playerId')) {
							if ($r->rPlayerWinner == $r->rPlayerAttacker) {
								if ($r->type == Commander::LOOT) {
									$title = 'Pillage de ' . $r->placeName;
									$img = 'loot.png';
								} else {
									$title = $r->rPlayerDefender == 0
										? 'Colonisation réussie'
										: 'Conquête de ' . $r->placeName;
									$img = 'colo.png';
								}
							} else {
								if ($r->type == Commander::LOOT) {
									$title = 'Pillage raté de ' . $r->placeName;
									$img = 'loot.png';
								} else {
									$title = $r->rPlayerDefender == 0
										? 'Colonisation ratée'
										: 'Conquête ratée de ' . $r->placeName;
									$img = 'colo.png';
								}
							}
						} else {
							if ($r->rPlayerWinner == $r->rPlayerDefender) {
								$title = $r->type == Commander::LOOT
									? 'Pillage repoussé'
									: 'Conquête repoussée';
								$img = 'shield.png';
							} else {
								$title = $r->type == Commander::LOOT
									? 'Défense ratée lors d\'un pillage'
									: 'Défense ratée lors d\'une conquête';
								$img = 'shield.png';
							}
						}

						echo '<div class="item">';
							echo '<div class="left">';
								echo '<img src="' . MEDIA . 'map/action/' . $img . '" alt="" />';
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