<?php
# compPlat component
# in athena.bases package

# affichage de plateforme commercial

# require
	# {orbitalBase}		ob_compPlat

$S_CRM1 = ASM::$crm->getCurrentSession();
ASM::$crm->changeSession($ob_compPlat->routeManager);

$nMaxCR = OrbitalBaseResource::getBuildingInfo(6, 'level', $ob_compPlat->getLevelCommercialPlateforme(), 'nbRoutesMax');
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

echo '<div class="component building">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
		echo '<h2>' . OrbitalBaseResource::getBuildingInfo(6, 'frenchName') . '</h2>';
		echo '<em>niveau ' . $ob_compPlat->getLevelCommercialPlateforme() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!CTR::$get->exist('mode') || CTR::$get->get('mode') == 'market') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_compPlat->getId() . '/view-commercialplateforme/mode-market" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
				echo '<strong>Place du commerce</strong>';
				echo '<em>Achetez sur le marché</em>';
			echo '</a>';
			$active = (CTR::$get->exist('mode') && CTR::$get->get('mode') == 'offer') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_compPlat->getId() . '/view-commercialplateforme/mode-offer" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
				echo '<strong>Proposition d\'offre</strong>';
				echo '<em>Vendez sur le marché</em>';
			echo '</a>';
			$active = (CTR::$get->exist('mode') && CTR::$get->get('mode') == 'route') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_compPlat->getId() . '/view-commercialplateforme/mode-route" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/refinery.png" alt="" />';
				echo '<strong>Quais commerciaux</strong>';
				echo '<em>Gérez vos routes commerciales</em>';
			echo '</a>';

			echo '<hr />';

			if (!CTR::$get->exist('mode') || CTR::$get->get('mode') == 'market') {
				$maxShip = OrbitalBaseResource::getBuildingInfo(6, 'level', $ob_compPlat->getLevelCommercialPlateforme(),  'nbCommercialShip');
				$availableShip = 14;

				echo '<div class="number-box">';
					echo '<span class="label">vaisseaux de commerce</span>';
					echo '<span class="value">' . Format::numberFormat($availableShip) . ' / ' . Format::numberFormat($maxShip) . '</span>';

					echo '<span class="progress-bar">';
					echo '<span style="width:' . Format::percent($availableShip, $maxShip) . '%;" class="content"></span>';
				echo '</div>';
			} else {
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
					echo '<span class="label">Revenu total de cette base</span>';
					echo '<span class="value">';
						echo Format::numberFormat($totalIncome);
						if (CTR::$data->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) != 0) {
							echo '<span class="bonus">+ ' .  Format::numberFormat(CTR::$data->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) * $totalIncome / 100)  . '</span>';
						}
						echo ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" />';
					echo '</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

if (!CTR::$get->exist('mode') || CTR::$get->get('mode') == 'market') {
	include COMPONENT . 'athena/bases/complat/market.php';
} else {
	if (CTR::$get->get('mode') == 'offer') {
		include COMPONENT . 'athena/bases/complat/offer.php';
	} else {
		include COMPONENT . 'athena/bases/complat/route.php';
	}
}

ASM::$crm->changeSession($S_CRM1);

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>A propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . OrbitalBaseResource::getBuildingInfo(6, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>
