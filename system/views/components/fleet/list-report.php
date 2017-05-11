<?php
# listReport componant
# in aress package

# liste tous les derniers rapports de combats du joueur

# require
	# [{report}]	report_listReport
	# (INT)			type_listReport

use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Container\Params;
use Asylamba\Classes\Library\Chronos;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$sessionToken = $session->get('token');

echo '<div class="component report">';
	echo '<div class="head skin-2">';
		if ($type_listReport == 1) {
			echo '<h2>Archives Militaires</h2>';
		}
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span>';
					echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_ATTACK_REPORT]) . '" class="active">' . ($request->cookies->get('p' . Params::SHOW_ATTACK_REPORT, Params::SHOW_ATTACK_REPORT) ? 'Rapports d\'attaque' : 'Rapports de défense') . '</a>';
				echo '</span>';
				echo '<span>';
					echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_REBEL_REPORT]) . '" class="hb lt ' . ($request->cookies->get('p' . Params::SHOW_REBEL_REPORT, Params::SHOW_REBEL_REPORT) ? 'active' : NULL) . '" title="' . ($request->cookies->get('p' . Params::SHOW_REBEL_REPORT, Params::SHOW_REBEL_REPORT) ? 'masquer' : 'afficher') . ' les rapports contre des rebelles">R</a>';
				echo '</span>';
				echo '<span>';
					echo '<a href="' . Format::actionBuilder('deleteallreport', $sessionToken) . '" class="hb lt" title="supprimer tout les rapports">&#215;</a>';
				echo '</span>';
			echo '</div>';
			
			if (count($report_listReport) > 0) {
				echo '<div class="set-item">';
					foreach ($report_listReport as $r) {
						list($title, $img) = $r->getTypeOfReport($session->get('playerInfo')->get('color'));

						echo '<div class="item">';
							echo '<div class="left">';
								echo '<img class="color' . ($type_listReport == 1 ? $r->colorD : $r->colorA) . '" src="' . MEDIA . 'map/action/' . $img . '" alt="" />';
							echo '</div>';

							echo '<div class="center">';
								echo '<strong>' . $title . '</strong>';
								echo Chronos::transform($r->dFight);
							echo '</div>';

							echo '<div class="right">';
								echo '<a class="' . ($request->query->get('report') === $r->id  ? 'active' : NULL) . '" href="' . APP_ROOT . 'fleet/view-archive/report-' . $r->id . '"></a>';
							echo '</div>';
						echo '</div>';
					}
				echo '</div>';
			} else {
				echo '<p>Il n\'y a aucun rapport de combat dans vos archives militaires.</p>';
			}

			if ($request->query->get('mode') === 'archived') {
				echo '<a class="more-button" href="' . APP_ROOT . 'fleet/view-archive">Voir tous les rapports</a>';
			} else {
				echo '<a class="more-button" href="' . APP_ROOT . 'fleet/view-archive/mode-archived">Voir les archives des rapports</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
