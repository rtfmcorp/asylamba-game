<?php

use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Classes\Library\Format;

$pointsToWin = $this->getContainer()->getParameter('points_to_win');
$mode = isset($targetMode) ? $targetMode : FALSE;

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Objectifs de victoires</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($faction->isWinner == Color::NOT_WIN) {

				echo '<h4>Conditions de victoire</h4>';
				echo '<p>Pour gagner, votre faction doit collecter ' . Format::number($pointsToWin) . ' points au classement total de faction.</p>';
				echo '<p>Vous avez actuellement ' . $faction->rankingPoints . ' points.</p>';

				$percent = Format::percent($faction->rankingPoints, $pointsToWin);
				echo '<ul class="list-type-1">';
				echo '<li>';
					echo '<span class="label">avancement</span>';
					echo '<span class="value">' . $percent . ' %</span>';
					echo '<span class="progress-bar hb bl" title="points">';
					echo '<span style="width:' . $percent . '%;" class="content ' . $faction->id . '"></span>';
					echo '</span>';
				echo '</li>';
				echo '</ul>';

			} elseif ($faction->isWinner == Color::WIN) {
				echo '<h4>Vous avez gagné</h4>';
				echo '<p>Vous avez atteint les ' . $pointsToWin . ', vous avez donc gagner la partie. Félicitations !</p>';
			}

		echo '</div>';
	echo '</div>';
echo '</div>';
