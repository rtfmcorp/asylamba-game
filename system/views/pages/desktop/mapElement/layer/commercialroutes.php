<?php
$db = DataBase::getInstance();
$qr = $db->query('SELECT
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
		ob1.rPlayer = ' . CTR::$data->get('playerId') . ' OR
		ob2.rPlayer = ' . CTR::$data->get('playerId'));
$aw = $qr->fetchAll();

echo '<div id="commercial-routes" style="display: none;">';
	echo '<svg viewBox="0, 0, ' . (GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size']) . ', ' . (GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
			foreach ($aw as $route) {
				$class = ($route['statement'] == CRM_ACTIVE) ? 'active' : 'standBy';
				echo '<line class="commercialRoute ' . $class . '" x1="' . ($route['sy1x'] * GalaxyConfiguration::$scale) . '" x2="' . ($route['sy2x'] * GalaxyConfiguration::$scale) . '" y1="' . ($route['sy1y'] * GalaxyConfiguration::$scale) . '" y2="' . ($route['sy2y'] * GalaxyConfiguration::$scale) . '" />';
			}
	echo '</svg>';
echo '</div>';
?>