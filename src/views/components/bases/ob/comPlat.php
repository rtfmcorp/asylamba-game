<?php
# compPlat component
# in athena.bases package

# affichage de la plateforme commerciale

# require
	# {orbitalBase}		ob_compPlat

use App\Modules\Athena\Resource\OrbitalBaseResource;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$componentPath = $container->getParameter('component');
$orbitalBaseHelper = $this->getContainer()->get(\App\Modules\Athena\Helper\OrbitalBaseHelper::class);
$request = $this->getContainer()->get('app.request');

echo '<div class="component building">';
	echo '<div class="head skin-1">';
		echo '<img src="' . $mediaPath . 'orbitalbase/commercialplateforme.png" alt="" />';
		echo '<h2>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::COMMERCIAL_PLATEFORME, 'frenchName') . '</h2>';
		echo '<em>niveau ' . $ob_compPlat->getLevelCommercialPlateforme() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!$request->query->has('mode') || $request->query->get('mode') == 'market') ? 'active' : '';
			echo '<a href="' . $appRoot . 'bases/view-commercialplateforme/mode-market" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Place du commerce</strong>';
				echo '<em>Achetez sur le marché</em>';
			echo '</a>';

			$active = ($request->query->get('mode') == 'sell') ? 'active' : '';
			echo '<a href="' . $appRoot . 'bases/view-commercialplateforme/mode-sell" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Salle des ventes</strong>';
				echo '<em>Vendez sur le marché</em>';
			echo '</a>';

			echo '<hr />';

			$active = ($request->query->has('mode') && $request->query->get('mode') == 'resource') ? 'active' : '';
			echo '<a href="' . $appRoot . 'bases/view-commercialplateforme/mode-resource" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'orbitalbase/commercialplateforme.png" alt="" />';
				echo '<strong>Halle de transfert</strong>';
				echo '<em>Envoyez des ressources et des vaisseaux</em>'; 
			echo '</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';

include $componentPath . 'bases/ob/complat/transaction.php';

if (!$request->query->has('mode') || $request->query->get('mode') == 'market') {
	include $componentPath . 'bases/ob/complat/market.php';
} elseif ($request->query->get('mode') == 'sell') {
	include $componentPath . 'bases/ob/complat/sell.php';
} else {
	include $componentPath . 'bases/ob/complat/resource.php';
}

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>A propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::COMMERCIAL_PLATEFORME, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
