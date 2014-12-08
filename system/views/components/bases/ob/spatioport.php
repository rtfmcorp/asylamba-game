<?php
# spatioport component
# in athena.bases package

# affichage du spatioport

# require
	# {orbitalBase}		ob_spatioport

$S_CRM1 = ASM::$crm->getCurrentSession();
ASM::$crm->changeSession($ob_spatioport->routeManager);

$nMaxCR = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $ob_spatioport->getLevelSpatioport(), 'nbRoutesMax');
$nCRWaitingForOther = 0; $nCRWaitingForMe = 0;
$nCROperational = 0; $nCRInStandBy = 0;
$nCRInDock = 0;
$totalIncome = 0;

if (ASM::$crm->size() > 0) {
	for ($i = 0; $i < ASM::$crm->size(); $i++) {
		if (ASM::$crm->get($i)->getStatement() == CRM_PROPOSED AND ASM::$crm->get($i)->getPlayerId1() == CTR::$data->get('playerId')) {
			$nCRWaitingForOther++;
		} elseif (ASM::$crm->get($i)->getStatement() == CRM_PROPOSED AND ASM::$crm->get($i)->getPlayerId1() != CTR::$data->get('playerId')) {
			$nCRWaitingForMe++;
		} elseif (ASM::$crm->get($i)->getStatement() == CRM_ACTIVE) {
			$nCROperational++;
			$totalIncome += ASM::$crm->get($i)->getIncome();
		} elseif (ASM::$crm->get($i)->getStatement() == CRM_STANDBY) {
			$nCRInStandBy++;
		}
	}

	$nCRInDock = $nCROperational + $nCRInStandBy + $nCRWaitingForOther;
}

# view
echo '<div class="component building rc">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
		echo '<h2>' . OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'frenchName') . '</h2>';
		echo '<em>niveau ' . $ob_spatioport->getLevelSpatioport() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box">';
				echo '<span class="label">routes commerciales</span>';
				echo '<span class="value">' . $nCRInDock . ' / ' . $nMaxCR . '</span>';

				echo '<span class="progress-bar">';
				echo '<span style="width:' . Format::percent($nCRInDock, $nMaxCR) . '%;" class="content"></span>';
			echo '</div>';

			echo '<div class="number-box ' . (($nCROperational == 0) ? 'grey' : '') . '">';
				echo '<span class="label">routes commerciales actives</span>';
				echo '<span class="value">' . $nCROperational . '</span>';
			echo '</div>';

			echo '<div class="number-box ' . (($nCRWaitingForOther == 0) ? 'grey' : '') . '">';
				echo '<span class="label">routes commerciales en attente</span>';
				echo '<span class="value">' . $nCRWaitingForOther . '</span>';
			echo '</div>';

			echo '<div class="number-box ' . (($nCRWaitingForMe == 0) ? 'grey' : '') . '">';
				echo '<span class="label">propositions commerciales</span>';
				echo '<span class="value">' . $nCRWaitingForMe . '</span>';
			echo '</div>';

			if ($nCRInStandBy > 0) {
				echo '<div class="number-box">';
					echo '<span class="label">routes commerciales bloquées</span>';
					echo '<span class="value">' . $nCRInStandBy . '</span>';
				echo '</div>';
			}

			echo '<hr />';

			echo '<div class="number-box">';
				echo '<span class="label">revenu total de cette base</span>';
				echo '<span class="value">';
					echo Format::numberFormat($totalIncome);
					if (CTR::$data->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) != 0) {
						echo '<span class="bonus">+ ' .  Format::numberFormat(CTR::$data->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) * $totalIncome / 100)  . '</span>';
					}
					echo ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" />';
				echo '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$j = 0;
for ($i = 0; $i < ASM::$crm->size(); $i++) {
	$rc = ASM::$crm->get($i);

	if ($rc->getStatement() == CRM_PROPOSED && $rc->getPlayerId2() == CTR::$data->get('playerId')) {
		$base1  = '<div class="base">';
			$base1 .= '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($rc->getPopulation1()) . '.png" alt="' . $ob_spatioport->getName() . '" class="place" />';
			$base1 .= '' . PlaceResource::get($rc->baseType1, 'name') . ' <a href="' . APP_ROOT . 'map/place-' . $rc->getROrbitalBase() . '">' . $rc->getBaseName1() . '</a><br />';
			$base1 .= 'de <a href="' . APP_ROOT . 'diary/player-' . $rc->getPlayerId1() . '">' . $rc->getPlayerName1() . '</a><br />';
			$base1 .= Format::numberFormat($rc->getPopulation1()) . ' millions de population';
		$base1 .= '</div>';

		$base2  = '<div class="base">';
			$base2 .= '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($rc->getPopulation2()) . '.png" alt="' . $ob_spatioport->getName() . '" class="place" />';
			$base2 .= '' . PlaceResource::get($rc->baseType2, 'name') . ' <a href="' . APP_ROOT . 'map/place-' . $rc->getROrbitalBaseLinked() . '">' . $rc->getBaseName2() . '</a><br />';
			$base2 .= 'de <a href="' . APP_ROOT . 'diary/player-' . $rc->getPlayerId2() . '">' . $rc->getPlayerName2() . '</a><br />';
			$base2 .= Format::numberFormat($rc->getPopulation2()) . ' millions de population';
		$base2 .= '</div>';

		echo '<div class="component rc">';
			echo '<div class="head skin-2">';
				echo ($j == 0) ? '<h2>Propositions</h2>' : '';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<div class="tool">';
						if ($rc->getPrice() > CTR::$data->get('playerInfo')->get('credit')) {
							echo '<span><a href="#">pas assez de crédits pour accepter</a></span>';
						} elseif ($nCRInDock >= $nMaxCR) {
							echo '<span><a href="#">pas d\'emplacement libre pour accepter</a></span>';
						} else {
							$price = $rc->getPrice();
							if (CTR::$data->get('playerInfo')->get('color') == ColorResource::NEGORA) {
								# bonus if the player is from Negore
								$price -= round($price * ColorResource::BONUS_NEGORA_ROUTE / 100);
							}
							echo '<span><a href="' . Format::actionBuilder('acceptroute', ['base' => $rc->getROrbitalBaseLinked(), 'route' => $rc->getId()]) . '">accepter pour ' . Format::numberFormat($price) . ' crédits</a></span>';
						}
						echo '<span><a href="' . Format::actionBuilder('refuseroute', ['base' => $rc->getROrbitalBaseLinked(), 'route' => $rc->getId()]) . '" class="hb lt" title="refuser l\'offre">x</a></span>';
					echo '</div>';

					echo '<div class="number-box grey">';
						echo '<span class="label">Etat de la route commerciale</span>';
						echo '<span class="value">';
							echo 'En attente';
						echo '</span>';
					echo '</div>';

					echo '<div class="rc ' . (($rc->getStatement() != CRM_ACTIVE) ? 'no-tax' : '') . '" style="height: ' . (370 + $rc->getDistance()) . 'px;">';
						echo ($rc->getPlayerId1() == CTR::$data->get('playerId')) ? $base2 : $base1;

						echo '<ul class="general">';
							echo '<li>Distance <strong>' . $rc->getDistance() . ' al.</strong></li>';
							echo '<li>Prix <strong>';
								echo Format::numberFormat($rc->getPrice());
								if (CTR::$data->get('playerInfo')->get('color') == ColorResource::NEGORA) {
									# bonus if the player is from Negore
									echo '<span class="bonus">- ' .  Format::numberFormat(round(ColorResource::BONUS_NEGORA_ROUTE * $rc->getPrice() / 100))  . '</span>';
								}
								echo ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" />';
							echo '</strong></li>';
							echo '<li>Estimation du revenu par relève<strong>';
								echo Format::numberFormat($rc->getIncome());
								if (CTR::$data->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) != 0) {
									echo '<span class="bonus">+ ' .  Format::numberFormat(CTR::$data->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) * $rc->getIncome() / 100)  . '</span>';
								}
								echo ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" />';		
							echo '</strong></li>';
							echo '<li>Population touchée <strong>' . Format::numberFormat($rc->getPopulation1() + $rc->getPopulation2()) . ' millions</strong></li>';

							if (in_array($rc->getStatement(), array(CRM_ACTIVE, CRM_STANDBY))) {
								echo '<li>En service depuis <br />' . Chronos::transform($rc->dCreation) . '</li>';
							}
						echo '</ul>';

						echo ($rc->getPlayerId1() == CTR::$data->get('playerId')) ? $base1 : $base2;
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		$j++;
	}
}

$j = 0;
for ($i = 0; $i < ASM::$crm->size(); $i++) {
	$rc = ASM::$crm->get($i);

	if ($rc->getStatement() != CRM_PROPOSED || $rc->getPlayerId2() != CTR::$data->get('playerId')) {
		$base1  = '<div class="base">';
			$base1 .= '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($rc->getPopulation1()) . '.png" class="place" />';
			$base1 .= '' . PlaceResource::get($rc->baseType1, 'name') . ' <a href="' . APP_ROOT . 'map/place-' . $rc->getROrbitalBase() . '">' . $rc->getBaseName1() . '</a><br />';
			$base1 .= 'de <a href="' . APP_ROOT . 'diary/player-' . $rc->getPlayerId1() . '">' . $rc->getPlayerName1() . '</a><br />';
			$base1 .= Format::numberFormat($rc->getPopulation1()) . ' millions de population';
		$base1 .= '</div>';

		$base2  = '<div class="base">';
			$base2 .= '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($rc->getPopulation2()) . '.png" class="place" />';
			$base2 .= '' . PlaceResource::get($rc->baseType2, 'name') . ' <a href="' . APP_ROOT . 'map/place-' . $rc->getROrbitalBaseLinked() . '">' . $rc->getBaseName2() . '</a><br />';
			$base2 .= 'de <a href="' . APP_ROOT . 'diary/player-' . $rc->getPlayerId2() . '">' . $rc->getPlayerName2() . '</a><br />';
			$base2 .= Format::numberFormat($rc->getPopulation2()) . ' millions de population';
		$base2 .= '</div>';

		echo '<div class="component rc">';
			echo '<div class="head skin-2">';
				echo ($j == 0) ? '<h2>Routes commerciales</h2>' : '';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<div class="tool">';
					if ($rc->getStatement() == CRM_PROPOSED) {
						echo '<span><a href="' . Format::actionBuilder('cancelroute', ['base-' => $ob_spatioport->getId(), 'route' => $rc->getId()]) . '">annuler la proposition commerciale</a></span>';
					} else {
						echo '<span><a href="' . Format::actionBuilder('deleteroute', ['base-' => $ob_spatioport->getId(), 'route' => $rc->getId()]) . '">démanteler la route commerciale</a></span>';
					}
					echo '</div>';

					echo '<div class="number-box ' . (($rc->getStatement() != CRM_ACTIVE) ? 'grey' : '') . '">';
						echo '<span class="label">Etat de la route commerciale</span>';
						echo '<span class="value">';
							switch ($rc->getStatement()) {
								case CRM_PROPOSED:	echo 'En attente'; break;		
								case CRM_ACTIVE:	echo 'En activité'; break;		
								case CRM_STANDBY:	echo 'Gelée'; break;		
								default: break;
							}
						echo '</span>';
					echo '</div>';

					echo '<div class="rc ' . (($rc->getStatement() != CRM_ACTIVE) ? 'no-tax' : '') . '" style="height: ' . (370 + $rc->getDistance()) . 'px;">';
						echo ($rc->getPlayerId1() == CTR::$data->get('playerId')) ? $base2 : $base1;

						echo '<ul class="general">';
							echo '<li>Distance <strong>' . $rc->getDistance() . ' al.</strong></li>';
							echo '<li>Revenu par relève ' . (($rc->getStatement() != CRM_ACTIVE) ? '[non effectif]' : '') . '<strong>';
								echo Format::numberFormat($rc->getIncome());
								if (CTR::$data->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) != 0) {
									echo '<span class="bonus">+ ' .  Format::numberFormat(CTR::$data->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) * $rc->getIncome() / 100)  . '</span>';
								}
								echo ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" />';		
							echo '</strong></li>';
							echo '<li>Population touchée <strong>' . Format::numberFormat($rc->getPopulation1() + $rc->getPopulation2()) . ' millions</strong></li>';

							if (in_array($rc->getStatement(), array(CRM_ACTIVE, CRM_STANDBY))) {
								echo '<li>En service depuis <br />' . Chronos::transform($rc->dCreation) . '</li>';
							}
						echo '</ul>';

						echo ($rc->getPlayerId1() == CTR::$data->get('playerId')) ? $base1 : $base2;
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
		$j++;
	}
}

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>A propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

if (ASM::$crm->size() == 0) {
	include COMPONENT . 'default.php';
}

ASM::$crm->changeSession($S_CRM1);
?>
