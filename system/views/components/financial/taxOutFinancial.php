<?php
# taxOutFinancial component
# in athena package

# détail l'imposition sectorielle

# require
	# [{orbitalBase}]			ob_taxOutFinancial

# view part

use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Format;

$container = $this->getContainer();
$taxCoeff = $this->getContainer()->getParameter('zeus.player.tax_coeff');
$mediaPath = $container->getParameter('media');

echo '<div class="component financial">';
	echo '<div class="head skin-1">';
		echo '<img src="' . $mediaPath . 'financial/taxout.png" alt="" />';
		echo '<h2>Redevances</h2>';
		echo '<em>Redevances aux factions</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<ul class="list-type-1">';
				foreach ($ob_taxOutFinancial as $base) {
					$baseTaxOut = 
						(Game::getTaxFromPopulation($base->getPlanetPopulation(), $base->typeOfBase, $taxCoeff) +
						(Game::getTaxFromPopulation($base->getPlanetPopulation(), $base->typeOfBase, $taxCoeff) * $taxBonus / 100))
						* $base->getTax() / 100
					;

					echo '<li>';
						echo '<span class="label">' . $base->getName() . ' [' . $base->getTax() . '% de taxe]</span>';
						echo '<span class="value">';
							echo Format::numberFormat($baseTaxOut);
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
				}

				echo '<li class="strong">';
					echo '<span class="label">total de la redevance</span>';
					echo '<span class="value">';
						echo Format::numberFormat($financial_totalTaxOut);
						echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
					echo '</span>';
				echo '</li>';
			echo '</ul>';

			echo '<p class="info">La redevance de faction est une taxe que vous devez payer. Cette taxe est versée à la faction qui a le contrôle 
			du secteur dans lequel vous vous situez. De ce fait, vous pouvez très bien verser un impôt à une faction ennemie. Cette taxe est 
			versée chaque relève.</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
