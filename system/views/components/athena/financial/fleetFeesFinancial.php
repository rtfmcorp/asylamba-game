<?php
# fleetFeesFinancial component
# in athena package

# détail des salaires des commandants

# require
	# [{commander}]			commander_fleetFeesFinancial

$commanderByBase = array();

foreach ($commander_fleetFeesFinancial as $commander) {
	if (!isset($commanderByBase[$commander->getRBase()])) {
		$commanderByBase[$commander->getRBase()] = array();
	}

	$commanderByBase[$commander->getRBase()][] = $commander;
}

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
			foreach ($commanderByBase as $base => $commanders) {
				$commanderFees = 0;
				foreach ($commanders as $commander) {
					$commanderFees += $commander->getLevel() * COM_LVLINCOMECOMMANDER;
				}

				echo '<li>';
					echo '<span class="buttons">';
						echo '<a href="#" class="sh" data-target="commander-base-' . $base . '">↓</a>';
					echo '</span>';

					foreach ($ob_fleetFeesFinancial as $ob) {
						if ($base == $ob->getId()) {
							echo '<span class="label">' . $ob->getName() . ' [' . count($commanders) . ' officier' . Format::addPlural($commanders) . ']</span>';
							break;
						}
					}

					echo '<span class="value">';
						echo Format::numberFormat($commanderFees);
						echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
					echo '</span>';

					echo '<ul class="sub-list-type-1" id="commander-base-' . $base . '">';
						foreach ($commanders as $commander) {
							echo '<li>';
								echo '<span class="label">' . CommanderResources::getInfo($commander->getLevel(), 'grade') . ' ' . $commander->getName() . '</span>';
								echo '<span class="value">';
									echo Format::numberFormat($commander->getLevel() * COM_LVLINCOMECOMMANDER);
									echo ' <a href="#" class="button">v</a> ';
								echo '</span>';
							echo '</li>';
						}
					echo '</ul>';
				echo '</li>';
			}

			echo '<li class="strong">';
				echo '<span class="label">total des salaires</span>';
				echo '<span class="value">';
					echo Format::numberFormat($financial_totalFleetFees);
					echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
				echo '</span>';
			echo '</li>';
		echo '</ul>';

		echo '<p class="info">La rubrique commandant ne correspond pas à l’investissement fait dans l’école de commandement, mais au salaire de vos 
		commandants. Plus un commandant a un niveau élevé, plus son salaire sera important. Les salaires des commandants sont payés chaque relève.</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';