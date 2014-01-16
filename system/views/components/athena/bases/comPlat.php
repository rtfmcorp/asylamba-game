<?php
# compPlat component
# in athena.bases package

# affichage de plateforme commercial

# require
	# {orbitalBase}		ob_compPlat

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

			$maxShip = OrbitalBaseResource::getBuildingInfo(6, 'level', $ob_compPlat->getLevelCommercialPlateforme(),  'nbCommercialShip');
			$availableShip = 14;

			echo '<div class="number-box">';
				echo '<span class="label">vaisseaux de commerce</span>';
				echo '<span class="value">' . Format::numberFormat($availableShip) . ' / ' . Format::numberFormat($maxShip) . '</span>';

				echo '<span class="progress-bar">';
				echo '<span style="width:' . Format::percent($availableShip, $maxShip) . '%;" class="content"></span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

if (!CTR::$get->exist('mode') || CTR::$get->get('mode') == 'market') {
	include COMPONENT . 'athena/bases/complat/market.php';
} elseif (CTR::$get->get('mode') == 'offer') {
	include COMPONENT . 'athena/bases/complat/offer.php';
} else {
	include COMPONENT . 'athena/bases/complat/route.php';
}

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
