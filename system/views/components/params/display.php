<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Container\Params;

$request = $this->getContainer()->get('app.request');
$sessionToken = $this->getContainer()->get('session_wrapper')->get('token');

echo '<div class="component">';
	echo '<div class="head skin-5">';
		echo '<h2>Paramètres de la carte</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_MAP_MINIMAP]) . '"" class="on-off-button ' . ($request->cookies->get('p' . Params::SHOW_MAP_MINIMAP, Params::$params[Params::SHOW_MAP_MINIMAP]) ? NULL : 'disabled') . '">';
				echo 'Afficher la minimap';
			echo '</a>';
	
			echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_MAP_RC]) . '"" class="on-off-button ' . ($request->cookies->get('p' . Params::SHOW_MAP_RC, Params::$params[Params::SHOW_MAP_RC]) ? NULL : 'disabled') . '">';
				echo 'Afficher les routes commerciales';
			echo '</a>';

			echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_MAP_ANTISPY]) . '"" class="on-off-button ' . ($request->cookies->get('p' . Params::SHOW_MAP_ANTISPY, Params::$params[Params::SHOW_MAP_ANTISPY]) ? NULL : 'disabled') . '">';
				echo 'Afficher les cercles de contre-espionnage';
			echo '</a>';

			echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_MAP_FLEETOUT]) . '"" class="on-off-button ' . ($request->cookies->get('p' . Params::SHOW_MAP_FLEETOUT, Params::$params[Params::SHOW_MAP_FLEETOUT]) ? NULL : 'disabled') . '">';
				echo 'Afficher les attaques sortantes';
			echo '</a>';

			echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_MAP_FLEETIN]) . '"" class="on-off-button ' . ($request->cookies->get('p' . Params::SHOW_MAP_FLEETIN, Params::$params[Params::SHOW_MAP_FLEETIN]) ? NULL : 'disabled') . '">';
				echo 'Afficher les attaques entrantes';
			echo '</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';
