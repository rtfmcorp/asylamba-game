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
			echo '<a href="' . APP_ROOT . 'bases/view-commercialplateforme/mode-market" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Place du commerce</strong>';
				echo '<em>Achetez sur le marché</em>';
			echo '</a>';

			$active = (CTR::$get->get('mode') == 'sell') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/view-commercialplateforme/mode-sell" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Salle des ventes</strong>';
				echo '<em>Vendez sur le marché</em>';
			echo '</a>';

			echo '<hr />';

			$active = (CTR::$get->exist('mode') && CTR::$get->get('mode') == 'route') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/view-commercialplateforme/mode-route" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Quais commerciaux</strong>';
				echo '<em>Gérez vos routes commerciales</em>';
			echo '</a>';

			$active = (CTR::$get->exist('mode') && CTR::$get->get('mode') == 'resource') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'bases/view-commercialplateforme/mode-resource" class="nav-element ' . $active . '">';
				echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Halle de transfert</strong>';
				echo '<em>Envoyez des ressources</em>'; 
			echo '</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';

if (!CTR::$get->exist('mode') || CTR::$get->get('mode') == 'market') {
	include COMPONENT . 'bases/ob/complat/market.php';
} elseif (CTR::$get->get('mode') == 'sell') {
	include COMPONENT . 'bases/ob/complat/sell.php';
} elseif (CTR::$get->get('mode') == 'route') {
	include COMPONENT . 'bases/ob/complat/route.php';
} else {
	include COMPONENT . 'bases/ob/complat/resource.php';
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
