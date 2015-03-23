<?php
# inclusion des modules
include_once ARES;

# loading des objets
$S_LRM1 = ASM::$lrm->getCurrentSession();
ASM::$lrm->newSession();
ASM::$lrm->load(
	[
		'p1.rColor' => CTR::$data->get('playerInfo')->get('color'),
		'p2.rColor' => [1, 2, 3, 4, 5, 6, 7]
	],
	['r.dFight', 'DESC'],
	[0, 25]
);

# work
echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Derniers combats</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Attaques</h4>';
				echo '<div class="set-item">';
					for ($i = 0; $i < ASM::$lrm->size(); $i++) {
						$r = ASM::$lrm->get($i);

						list($title, $img) = $r->getTypeOfReport(CTR::$data->get('playerInfo')->get('color'));

						echo '<div class="item">';
							echo '<div class="left">';
								echo '<img class="color' . $r->colorD . '" src="' . MEDIA . 'map/action/' . $img . '" alt="" />';
							echo '</div>';

							echo '<div class="center">';
								echo '<strong>' . $title . '</strong>';
								echo 'par <a href="' . APP_ROOT . 'embassy/player-' . $r->rPlayerAttacker . '">' . $r->playerNameA . '</a> (' . $r->isLegal . ')';
							echo '</div>';
						echo '</div>';
					}
				echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$lrm->changeSession($S_LRM1);
?>