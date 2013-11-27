<?php
# generalFinancial component
# in athena package

# affiche l'aperçu des finances d'un joueur, ainsi que sa balance au prochain tour

# require
	# [{orbitalBase}]			ob_generalFinancial
	# [{commander}]				commander_generalFinancial

# work part
$credit = CTR::$data->get('playerInfo')->get('credit');

$totalTaxIn = 0;
$totalRouteIncome = 0;

$totalInvest = 0;
$totalTaxOut = 0;
$totalMSFees = 0;
$totalFleetFees = 0;

# bonus
$taxBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::POPULATION_TAX);

foreach ($ob_generalFinancial as $base) {
	$totalTaxIn  += Game::getTaxFromPopulation($base->getPlanetPopulation());
	$totalTaxOut += (Game::getTaxFromPopulation($base->getPlanetPopulation()) + (Game::getTaxFromPopulation($base->getPlanetPopulation()) * $taxBonus / 100)) * $base->getTax() / 100;
																	/* le bonus est ajouté à la somme pour déduire à l'alliance					*/
	$totalInvest += $base->getISchool();
	$totalInvest += $base->getIUniversity();
	$totalInvest += $base->getIAntiSpy();

	$S_CRM1 = ASM::$crm->getCurrentSession();
	ASM::$crm->changeSession($base->routeManager);
	for ($k = 0; $k < ASM::$crm->size(); $k++) { 
		if (ASM::$crm->get($k)->getStatement() == CRM_ACTIVE) {
			$totalRouteIncome += ASM::$crm->get($k)->getIncome();
		}
	}
	ASM::$crm->changeSession($S_CRM1);
}

foreach ($commander_generalFinancial as $commander) {
	$totalFleetFees += $commander->getLevel() * COM_LVLINCOMECOMMANDER;
}

$totalIncome = $totalTaxIn + $totalRouteIncome + ($totalTaxIn * $taxBonus / 100);
$totalFess = $totalInvest + $totalTaxOut + $totalMSFees + $totalFleetFees;
$benefice =  $totalIncome - $totalFess;
$remains  = $credit + $benefice;


# view part
echo '<div class="component size2 financial">';
	echo '<div class="head">';
		echo '<h1>Aperçu</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<table><tr><td>';
				echo '<h4>recettes</h4>';
				echo '<ul class="list-type-1">';
					echo '<li>';
						echo '<span class="label">impôts</span>';
						echo '<span class="value">';
							echo Format::numberFormat($totalTaxIn);
							if ($taxBonus > 0) {
								echo '<span class="bonus">+' . Format::numberFormat($totalTaxIn * $taxBonus / 100) . '</span>';
							}
							echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
						'</span>';
					echo '</li>';
					echo '<li>';
						echo '<span class="label">taxes commerciales</span>';
						echo '<span class="value">';
							echo Format::numberFormat($totalRouteIncome);
							echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					//echo '<li class="empty"></li>';
					echo '<li class="empty"></li>';
					echo '<li class="strong">';
						echo '<span class="label">total des recettes</span>';
						echo '<span class="value">';
							echo Format::numberFormat($totalIncome);
							echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					if ($benefice < 0) {
						echo '<li class="strong">';
							echo '<span class="label">perte</span>';
							echo '<span class="value">';
								echo Format::numberFormat(abs($benefice));
								echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
							echo '</span>';
						echo '</li>';
					}
				echo '</ul>';

			echo '</td><td>';

				echo '<h4>dépenses</h4>';
				echo '<ul class="list-type-1">';
					echo '<li>';
						echo '<span class="label">investissements</span>';
						echo '<span class="value">';
							echo Format::numberFormat($totalInvest);
							echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					/*echo '<li>';
						echo '<span class="label">frais des vaisseaux mères</span>';
						echo '<span class="value">';
							echo Format::numberFormat($totalMSFees);
							echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';*/
					echo '<li>';
						echo '<span class="label">salaire des commandants</span>';
						echo '<span class="value">';
							echo Format::numberFormat($totalFleetFees);
							echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					echo '<li>';
						echo '<span class="label">redevances aux factions</span>';
						echo '<span class="value">';
							echo Format::numberFormat($totalTaxOut);
							echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					echo '<li class="strong">';
						echo '<span class="label">total des charges</span>';
						echo '<span class="value">';
							echo Format::numberFormat($totalFess);
							echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					if ($benefice >= 0) {
						echo '<li class="strong">';
							echo '<span class="label">bénéfice</span>';
							echo '<span class="value">';
								echo Format::numberFormat(abs($benefice));
								echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
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
							echo Format::numberFormat($credit);
							echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					echo '<li>';
						echo '<span class="label">bénéfice</span>';
						echo '<span class="value">';
							echo Format::numberFormat($benefice);
							echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
					echo '<li class="strong">';
						echo '<span class="label">prévision à la prochaine relève</span>';
						echo '<span class="value">';
							echo Format::numberFormat($remains);
							echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
				echo '</ul>';

				echo '</td><td>';

				echo '<p class="info">Ces deux colonnes sont un résumé de vos recettes et de vos dépenses pendant la durée d’une relève. Dans la 
				colonne des recettes vous pouvez voir tous les éléments qui vous rapportent des crédits dans Expansion. Dans la colonne des dépenses,
				 c’est l’inverse, vous voyez les éléments qui vous coûtent sur vos bases. La différence entre ces deux colonnes vous donne soit une 
				 perte (sous la colonne des recettes) soit un bénéfice (sous la colonne des dépenses).</p>';

			echo '</td></tr></table>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>