<?php
# fleetFeesFinancial component
# in athena package

# détail des salaires des commandants

# require
	# [{commander}]			commander_fleetFeesFinancial

$totalFees = 0;

# view part
echo '<div class="component financial">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'financial/commander.png" alt="vaisseau mère" />';
		echo '<h2>Commandants</h2>';
		echo '<em>Salaires des commandants</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
		echo '<ul class="list-type-1">';
			foreach ($commander_fleetFeesFinancial as $commander) {
				$commanderFees = $commander->getLevel() * COM_LVLINCOMECOMMANDER;
				$totalFees += $commanderFees;

				echo '<li>';
					echo '<span class="label">Commandant ' . $commander->getName() . '</span>';
					echo '<span class="value">';
						echo Format::numberFormat($commanderFees);
						echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
					echo '</span>';
				echo '</li>';
			}

			echo '<li class="strong">';
				echo '<span class="label">total des salaires</span>';
				echo '<span class="value">';
					echo Format::numberFormat($totalFees);
					echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
				echo '</span>';
			echo '</li>';
		echo '</ul>';

		echo '<p class="info">La rubrique commandant ne correspond pas à l’investissement fait dans l’école de commandement, mais au salaire de vos 
		commandants. Plus un commandant a un niveau élevé, plus son salaire sera important. Les salaires des commandants sont payés chaque relève.</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';