<?php
# generalFinancial component
# in athena package

# affiche l'aperçu des finances d'un joueur, ainsi que sa balance au prochain tour

# require
	# *

# view part

use App\Classes\Library\Format;

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');
$sessionToken = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class)->get('token');

echo '<div class="component size2 financial">';
	echo '<div class="head">';
		echo '<h1>Finance</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<table><tr><td>';
				echo '<h4>recettes</h4>';
				echo '<ul class="list-type-1">';
					echo '<li>';
						echo '<span class="label">impôts</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_totalTaxIn);
							if ($taxBonus > 0) {
								echo '<span class="bonus">+' . Format::numberFormat($financial_totalTaxInBonus) . '</span>';
							}
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						'</span>';
					echo '</li>';
					echo '<li>';
						echo '<span class="label">taxes commerciales</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_totalRouteIncome);
							if ($rcBonus > 0) {
								echo '<span class="bonus">+' . Format::numberFormat($financial_totalRouteIncomeBonus) . '</span>';
							}
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					echo '<li class="empty"></li>';
					echo '<li class="empty"></li>';
					echo '<li class="empty"></li>';
					echo '<li class="strong">';
						echo '<span class="label">total des recettes</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_totalIncome);
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					if ($financial_benefice < 0) {
						echo '<li class="strong">';
							echo '<span class="label">perte</span>';
							echo '<span class="value">';
								echo Format::numberFormat(abs($financial_benefice));
								echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
							echo '</span>';
						echo '</li>';
					}
				echo '</ul>';

			echo '</td><td>';

				echo '<h4>dépenses</h4>';
				echo '<ul class="list-type-1">';
					echo '<li>';
						echo '<span class="label">investissements planétaires</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_totalInvest);
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					echo '<li>';
						echo '<span class="buttons">';
							echo '<a href="#" class="sh" data-target="invest-uni">↓</a>';
						echo '</span>';
						echo '<span class="label">investissements universitaires</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_totalInvestUni);
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';

						echo '<form action="' . Format::actionBuilder('updateuniinvest', $sessionToken) . '" method="POST" id="invest-uni">';
							echo '<p>';
								echo '<input type="text" name="credit" value="' . $financial_totalInvestUni . '" />';
								echo '<input type="submit" value="ok" />';
							echo '</p>';
						echo '</form>';
					echo '</li>';
					echo '<li>';
						echo '<span class="label">salaire des commandants</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_totalFleetFees);
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					# TODO
					echo '<li>';
						echo '<span class="label">entretien des vaisseaux</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_totalShipsFees);
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					echo '<li>';
						echo '<span class="label">redevances aux factions</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_totalTaxOut);
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					echo '<li class="strong">';
						echo '<span class="label">total des charges</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_totalFess);
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					if ($financial_benefice >= 0) {
						echo '<li class="strong">';
							echo '<span class="label">bénéfice</span>';
							echo '<span class="value">';
								echo Format::numberFormat(abs($financial_benefice));
								echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
							echo '</span>';
						echo '</li>';
					}
				echo '</ul>';

				echo '</td></tr><tr><td>';

				echo '<h4>évolution de finances</h4>';
				echo '<ul class="list-type-1">';
					echo '<li>';
						echo '<span class="label">crédits en possession</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_credit);
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					echo '<li>';
						echo '<span class="label">bénéfice</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_benefice);
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					echo '<li class="strong">';
						echo '<span class="label">prévision à la prochaine relève</span>';
						echo '<span class="value">';
							echo Format::numberFormat($financial_remains);
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
				echo '</ul>';

				echo '</td><td>';

				echo '<p class="info">Ces deux colonnes sont un résumé de vos recettes et de vos dépenses pendant la durée d’une relève. Dans la 
				colonne des recettes vous pouvez voir tous les éléments qui vous rapportent des crédits dans Asylamba. Dans la colonne des dépenses,
				 c’est l’inverse, vous voyez les éléments qui vous coûtent sur vos bases. La différence entre ces deux colonnes vous donne soit une 
				 perte (sous la colonne des recettes) soit un bénéfice (sous la colonne des dépenses).</p>';

			echo '</td></tr></table>';
		echo '</div>';
	echo '</div>';
echo '</div>';
