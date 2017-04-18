<?php

$session = $this->getContainer()->get('session_wrapper');
$littleReportManager = $this->getContainer()->get('ares.little_report_manager');

# loading des objets
$S_LRM1 = $littleReportManager->getCurrentSession();
$littleReportManager->newSession();
$littleReportManager->loadByRequest(
	'WHERE p2.rColor = ? AND p1.rColor != 0 ORDER BY dFight DESC LIMIT 0, 30',
	[$session->get('playerInfo')->get('color')]
);

# work
echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Défenses</h4>';
				echo '<div class="set-item">';
					for ($i = 0; $i < $littleReportManager->size(); $i++) {
						$r = $littleReportManager->get($i);

						list($title, $img) = $r->getTypeOfReport($session->get('playerInfo')->get('color'));

						echo '<div class="item">';
							echo '<div class="left">';
								echo '<img class="color' . $r->colorA . '" src="' . MEDIA . 'map/action/' . $img . '" alt="" />';
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

$littleReportManager->changeSession($S_LRM1);
