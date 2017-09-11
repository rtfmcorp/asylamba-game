<?php
# impositionFinancial component
# in athena package

# détail l'imposition par base

# require
    # [{orbitalBase}]			ob_impositionFinancial

# view part

use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Game;

$taxCoeff = $this->getContainer()->getParameter('zeus.player.tax_coeff');

echo '<div class="component financial">';
    echo '<div class="head skin-1">';
        echo '<img src="' . MEDIA . 'financial/taxin.png" alt="" />';
        echo '<h2>Impôts</h2>';
        echo '<em>Imposition par planète</em>';
    echo '</div>';
    echo '<div class="fix-body">';
        echo '<div class="body">';
            echo '<ul class="list-type-1">';
                foreach ($ob_impositionFinancial as $base) {
                    $baseImpot = Game::getTaxFromPopulation($base->getPlanetPopulation(), $base->typeOfBase, $taxCoeff);

                    echo '<li>';
                    echo '<span class="label">' . $base->getName() . ' [' . Format::numberFormat($base->getPlanetPopulation()) . ' Mio hab.]</span>';
                    echo '<span class="value">';
                    echo Format::numberFormat($baseImpot);
                    if ($taxBonus > 0) {
                        echo '<span class="bonus">+' . Format::numberFormat($baseImpot * $taxBonus / 100) . '</span>';
                    }
                    echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
                    echo '</span>';
                    echo '</li>';
                }

                echo '<li class="strong">';
                    echo '<span class="label">total de l\'imposition</span>';
                    echo '<span class="value">';
                        echo Format::numberFormat($financial_totalTaxIn);
                        if ($taxBonus > 0) {
                            echo '<span class="bonus">+' . Format::numberFormat($financial_totalTaxInBonus) . '</span>';
                        }
                        echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
                    echo '</span>';
                echo '</li>';
            echo '</ul>';

            echo '<p class="info">Les impôts sont les crédits que vous récoltez auprès de la population de vos planètes. Plus vos planètes sont 
			grosses en terme de population, plus vous collecterez d’impôts. Ils peuvent être augmentés grâce à la technologie « Economie Sociale de 
			Marché », ce qui vous permettra d’améliorer vos recettes.</p>';
        echo '</div>';
    echo '</div>';
echo '</div>';
