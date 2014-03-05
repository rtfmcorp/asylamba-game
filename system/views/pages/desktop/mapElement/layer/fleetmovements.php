<?php
include_once ARES;
include_once GAIA;

$S_COM_MAPLAYER = ASM::$com->getCurrentSession();
ASM::$com->newSession();
ASM::$com->load(array('c.statement' => Commander::MOVING, 'c.rPlayer' => CTR::$data->get('playerId')));
$placesId = array(0);
for ($i = 0; $i < ASM::$com->size(); $i++) {
	$placesId[] = ASM::$com->get($i)->getRBase();
	$placesId[] = ASM::$com->get($i)->getRPlaceDestination();
}

$S_PLM_MAPLAYER = ASM::$plm->getCurrentSession();
ASM::$plm->newSession();
ASM::$plm->load(array('id' => $placesId));

echo '<div id="fleet-movements">';
	echo '<svg viewBox="0, 0, ' . (GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size']) . ', ' . (GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
			for ($i = 0; $i < ASM::$com->size(); $i++) {
				$commander = ASM::$com->get($i);
				if ($commander->getTypeOfMove() !== COM_BACK) {
					echo '<line x1="' . (ASM::$plm->getById($commander->getRBase())->getXSystem() * GalaxyConfiguration::$scale) . '" x2="' . (ASM::$plm->getById($commander->getRPlaceDestination())->getXSystem() * GalaxyConfiguration::$scale) . '" y1="' . (ASM::$plm->getById($commander->getRBase())->getYSystem() * GalaxyConfiguration::$scale) . '" y2="' . (ASM::$plm->getById($commander->getRPlaceDestination())->getYSystem() * GalaxyConfiguration::$scale) . '" />';
				}
			}
	echo '</svg>';
echo '</div>';

ASM::$plm->changeSession($S_PLM_MAPLAYER);
ASM::$com->changeSession($S_COM_MAPLAYER);
?>