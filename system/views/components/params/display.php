<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Container\Params;

$request = $this->getContainer()->get('app.request');
$sessionToken = $this->getContainer()->get('session_wrapper')->get('token');

# display
echo '<div class="component">';
	echo '<div class="head skin-5">';
		echo '<h2>Paramètres de la carte</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_MAP_MINIMAP]) . '"" class="on-off-button ' . ($request->cookies->get('p' . Params::SHOW_MAP_MINIMAP, Params::SHOW_MAP_MINIMAP) ? NULL : 'disabled') . '">';
				echo 'Afficher la minimap';
			echo '</a>';
	
			echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_MAP_RC]) . '"" class="on-off-button ' . ($request->cookies->get('p' . Params::SHOW_MAP_RC, Params::SHOW_MAP_RC) ? NULL : 'disabled') . '">';
				echo 'Afficher les routes commerciales';
			echo '</a>';

			echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_MAP_ANTISPY]) . '"" class="on-off-button ' . ($request->cookies->get('p' . Params::SHOW_MAP_ANTISPY, Params::SHOW_MAP_ANTISPY) ? NULL : 'disabled') . '">';
				echo 'Afficher les cercles de contre-espionnage';
			echo '</a>';

			echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_MAP_FLEETOUT]) . '"" class="on-off-button ' . ($request->cookies->get('p' . Params::SHOW_MAP_FLEETOUT, Params::SHOW_MAP_FLEETOUT) ? NULL : 'disabled') . '">';
				echo 'Afficher les attaques sortantes';
			echo '</a>';

			echo '<a href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::SHOW_MAP_FLEETIN]) . '"" class="on-off-button ' . ($request->cookies->get('p' . Params::SHOW_MAP_FLEETIN, Params::SHOW_MAP_FLEETIN) ? NULL : 'disabled') . '">';
				echo 'Afficher les attaques entrantes';
			echo '</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';

/*
echo '<h4>Paramètres généraux</h4>';
echo '<label class="checkbox hb rt" title="Affiche les ascenseurs par défaut de votre système. Ces derniers sont moins beaux mais peuvent résoudrent certains problèmes.">';
	if (CTR::$cookie->equal('movers', TRUE)) {
		$check = 'checked';
	} elseif (CTR::$cookie->equal('movers', FALSE)) {
		$check = NULL;
	} else {
		$check = NULL;
	}
	echo '<input type="checkbox" name="movers" value="1" ' . $check . ' />';
	echo 'Ascenseur système';
echo '</label>';

echo '<label class="checkbox hb rt" title="Améliore les performances d\'affichage lorsqu\'elles sont désactivées">';
	if (CTR::$cookie->equal('anims', TRUE)) {
		$check = 'checked';
	} elseif (CTR::$cookie->equal('anims', FALSE)) {
		$check = NULL;
	} else {
		$check = 'checked';
	}
	echo '<input type="checkbox" name="anims" value="1" ' . $check . ' />';
	echo 'Animations activées';
echo '</label>';

/*echo '<label class="radio">';
	echo '<input type="checkbox" name="panel" value="1" ' . $check . ' />';
	echo 'panneau vide par défaut';
echo '</label>';
*/