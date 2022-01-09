<?php

use App\Classes\Container\Params;
use App\Modules\Athena\Model\CommercialRoute;

$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$galaxyConfiguration = $this->getContainer()->get(\App\Modules\Gaia\Galaxy\GalaxyConfiguration::class);

$qr = $this->getContainer()->get(\App\Classes\Database\Database::class)->query('SELECT
		sy1.xPosition AS sy1x,
		sy1.yPosition AS sy1y,
		sy2.xPosition AS sy2x,
		sy2.yPosition AS sy2y,
		cr.statement AS statement
	FROM commercialRoute AS cr
	LEFT JOIN place AS pl1
		ON cr.rOrbitalBase = pl1.id
		LEFT JOIN system AS sy1
			ON pl1.rSystem = sy1.id
			LEFT JOIN orbitalBase AS ob1
				ON cr.rOrbitalBase = ob1.rPlace
	LEFT JOIN place AS pl2
		ON cr.rOrbitalBaseLinked = pl2.id
		LEFT JOIN system AS sy2
			ON pl2.rSystem = sy2.id
			LEFT JOIN orbitalBase AS ob2
				ON cr.rOrbitalBaseLinked = ob2.rPlace
	WHERE
		ob1.rPlayer = ' . $session->get('playerId') . ' OR
		ob2.rPlayer = ' . $session->get('playerId'));
$aw = $qr->fetchAll();

echo '<div id="commercial-routes" ' . ($request->cookies->get('p' . Params::SHOW_MAP_RC, Params::$params[Params::SHOW_MAP_RC]) ? NULL : 'style="display:none;"') . '>';
	echo '<svg viewBox="0, 0, ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . ', ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
			foreach ($aw as $route) {
				$class = ($route['statement'] == CommercialRoute::ACTIVE) ? 'active' : 'standBy';
				echo '<line class="commercialRoute ' . $class . '" x1="' . ($route['sy1x'] * $galaxyConfiguration->scale) . '" x2="' . ($route['sy2x'] * $galaxyConfiguration->scale) . '" y1="' . ($route['sy1y'] * $galaxyConfiguration->scale) . '" y2="' . ($route['sy2y'] * $galaxyConfiguration->scale) . '" />';
			}
	echo '</svg>';
echo '</div>';
