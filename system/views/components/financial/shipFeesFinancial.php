<?php

$commanderByBase = [];

foreach ($commander_shipsFeesFinancial as $commander) {
	if (!isset($commanderByBase[$commander->getRBase()])) {
		$commanderByBase[$commander->getRBase()] = [];
	}

	$commanderByBase[$commander->getRBase()][] = $commander;
}

echo '<div class="component financial">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'financial/commander.png" alt="flottes" />';
		echo '<h2>Entretien</h2>';
		echo '<em>Entretien des vaisseaux</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
		echo '<ul class="list-type-1">';
			foreach ($ob_impositionFinancial as $base) {
				$totalBase = Game::getFleetCost($base->shipStorage, FALSE);
				$cbb = isset($commanderByBase[$base->getId()])
					? $commanderByBase[$base->getId()] : [];

				foreach ($cbb as $commander) {
					$totalBase += Game::getFleetCost($commander->getNbrShipByType());
				}

				echo '<li>';
					echo '<span class="buttons">';
						echo '<a href="#" class="sh" data-target="ships-base-' . $base->getId() . '">↓</a>';
					echo '</span>';

					echo '<span class="label">' . $base->getName() . '</span>';
					echo '<span class="value">';
						echo Format::numberFormat($totalBase);
						echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
					echo '</span>';

					echo '<ul class="sub-list-type-1" id="ships-base-' . $base->getId() . '">';
						echo '<li>';
							echo '<span class="label">Hangar</span>';
							echo '<span class="value">';
								echo Format::numberFormat(Game::getFleetCost($base->shipStorage, FALSE));
							echo '</span>';
						echo '</li>';

						echo '<li>';
							echo '<span class="label">En vente # TODO</span>';
							echo '<span class="value">';
								echo Format::numberFormat(0);
							echo '</span>';
						echo '</li>';

						foreach ($cbb as $commander) {
							echo '<li>';
								echo '<span class="label">' . CommanderResources::getInfo($commander->level, 'grade') . ' ' . $commander->name . '</span>';
								echo '<span class="value">';
									echo Format::numberFormat(Game::getFleetCost($commander->getNbrShipByType()));
								echo '</span>';
							echo '</li>';
						}
					echo '</ul>';
				echo '</li>';
			}

			echo '<li class="strong">';
				echo '<span class="label">total des coûts</span>';
				echo '<span class="value">';
					echo Format::numberFormat($financial_totalShipsFees);
					echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
				echo '</span>';
			echo '</li>';
		echo '</ul>';

		echo '<p class="info">TODO</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';