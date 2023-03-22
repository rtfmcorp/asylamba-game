<?php

use Asylamba\Classes\Container\Params;

$container = $this->getContainer();
$request = $this->getContainer()->get('app.request');
$mediaPath = $container->getParameter('media');

echo '<div id="map-option">';
	echo '<a ';
		echo 'href="#" ';
		echo 'class="sh switch-class ajax-switch-params hb lb ' . ($request->cookies->get('p' . Params::SHOW_MAP_MINIMAP, Params::$params[Params::SHOW_MAP_MINIMAP]) ? 'active' : NULL) . '" ';
		echo 'data-class="active" ';
		echo 'data-target="map-content" ';
		echo 'data-switch-params="' . Params::SHOW_MAP_MINIMAP . '" ';
		echo 'title="afficher/cacher la petite carte" ';
	echo '>';
		echo '<img src="' . $mediaPath . 'map/option/minimap.png" alt="minimap" />';
	echo '</a>';
	
	echo '<a ';
		echo 'href="#" ';
		echo 'class="sh switch-class ajax-switch-params hb lb ' . ($request->cookies->get('p' . Params::SHOW_MAP_RC, Params::$params[Params::SHOW_MAP_RC]) ? 'active' : NULL) . '" ';
		echo 'data-class="active" ';
		echo 'data-target="commercial-routes" ';
		echo 'data-switch-params="' . Params::SHOW_MAP_RC . '" ';
		echo 'title="afficher/cacher vos routes commerciales"';
	echo '>';
		echo '<img src="' . $mediaPath . 'orbitalbase/commercialplateforme.png" alt="" />';
	echo '</a>';

	echo '<a ';
		echo 'href="#" ';
		echo 'class="sh switch-class ajax-switch-params hb lb ' . ($request->cookies->get('p' . Params::SHOW_MAP_ANTISPY, Params::$params[Params::SHOW_MAP_ANTISPY]) ? 'active' : NULL) . '" ';
		echo 'data-class="active" ';
		echo 'data-target="spying" ';
		echo 'data-switch-params="' . Params::SHOW_MAP_ANTISPY . '" ';
		echo 'title="afficher/cacher vos cercles de contre-espionnage"';
	echo '>';
		echo '<img src="' . $mediaPath . 'orbitalbase/antispy.png" alt="" />';
	echo '</a>';

	echo '<a ';
		echo 'href="#" ';
		echo 'class="sh switch-class ajax-switch-params hb lb ' . ($request->cookies->get('p' . Params::SHOW_MAP_FLEETOUT, Params::$params[Params::SHOW_MAP_FLEETOUT]) ? 'active' : NULL) . '" ';
		echo 'data-class="active" ';
		echo 'data-target="fleet-movements" ';
		echo 'data-switch-params="' . Params::SHOW_MAP_FLEETOUT . '" ';
		echo 'title="afficher/cacher vos mouvements de flotte"';
	echo '>';
		echo '<img src="' . $mediaPath . 'fleet/general-quarter.png" alt="" />';
	echo '</a>';

	echo '<a ';
		echo 'href="#" ';
		echo 'class="sh switch-class ajax-switch-params hb lb ' . ($request->cookies->get('p' . Params::SHOW_MAP_FLEETIN, Params::$params[Params::SHOW_MAP_FLEETIN]) ? 'active' : NULL) . '" ';
		echo 'data-class="active" ';
		echo 'data-target="attacks" ';
		echo 'data-switch-params="' . Params::SHOW_MAP_FLEETIN . '" ';
		echo 'title="afficher/cacher les attaques entrantes""';
	echo '>';
		echo '<img src="' . $mediaPath . 'fleet/movement.png" alt="" />';
	echo '</a>';

	echo '<a ';
		echo 'href="#" ';
		echo 'class="sh hb lb moveTo switch-class" ';
		echo 'data-class="active" ';
		echo 'data-x-position="240" ';
		echo 'data-y-position="19" ';
		echo 'data-target="map-info" ';
		echo 'title="légende"';
	echo '>';
		echo '<img src="' . $mediaPath . 'map/option/info.png" alt="information" />';
	echo '</a>';
echo '</div>';
