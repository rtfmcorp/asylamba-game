<?php

use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Ares\Resource\CommanderResources;

$commanderByBase = [];
foreach ($commander_shipsFeesFinancial as $commander) {
	if (!isset($commanderByBase[$commander->getRBase()])) {
		$commanderByBase[$commander->getRBase()] = [];
	}

	$commanderByBase[$commander->getRBase()][] = $commander;
}

echo '<div class="component financial">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'financial/fleet.png" alt="flottes" />';
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

				$baseTransaction = 0;
				foreach ($transaction_shipsFeesFinancial as $t) {
					if ($t->rPlace == $base->getId()) {
						$baseTransaction += ShipResource::getInfo($transaction->identifier, 'cost') * ShipResource::COST_REDUCTION * $transaction->quantity;
					}
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
							echo '<span class="label">Dans le hangar</span>';
							echo '<span class="value">';
								echo Format::numberFormat(Game::getFleetCost($base->shipStorage, FALSE));
							echo '</span>';
						echo '</li>';

						echo '<li>';
							echo '<span class="label">En vente</span>';
							echo '<span class="value">';
								echo Format::numberFormat($baseTransaction);
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

		echo '<p class="info">Les frais d\'entretien des vaisseaux sont nécessaires pour que les différents appareils continuent à voler sans risque. Ils représentent une part importante de vos finances et sont par nature très volatiles, pensez donc à garder un oeil dessus.</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';