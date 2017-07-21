<?php

$session = $this->getContainer()->get('session_wrapper');

$reports = $this->getContainer()->get('ares.live_report_manager')->getFactionAttackReports($session->get('playerInfo')->get('color'));

# work
echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Derniers combats</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Attaques</h4>';
				echo '<div class="set-item">';
					foreach ($reports as $r) {
						list($title, $img) = $r->getTypeOfReport($session->get('playerInfo')->get('color'));

						echo '<div class="item">';
							echo '<div class="left">';
								echo '<img class="color' . $r->colorD . '" src="' . MEDIA . 'map/action/' . $img . '" alt="" />';
							echo '</div>';

							echo '<div class="center">';
								echo '<strong>' . $title . '</strong>';
								echo 'par <a href="' . APP_ROOT . 'embassy/player-' . $r->rPlayerAttacker . '">' . $r->playerNameA . '</a>';
							echo '</div>';

							echo !$r->isLegal ? '<span class="group-link"><a href="#" class="hb lt" title="cette attaque viole un traité">!</a></span>' : NULL;
						echo '</div>';
					}
				echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
