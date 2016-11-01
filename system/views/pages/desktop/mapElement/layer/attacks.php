<?php

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Container\Params;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Ares\Model\Commander;

# chargement des id des commandants attaquants
$commandersId = array(0);
for ($i = 0; $i < CTR::$data->get('playerEvent')->size(); $i++) {
	if (CTR::$data->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
		if (CTR::$data->get('playerEvent')->get($i)->get('eventInfo')->size() > 0) {
			$commandersId[] = CTR::$data->get('playerEvent')->get($i)->get('eventId');
		}
	}
}

# chargement des commandants attaquants
$S_COM_ATT = ASM::$com->getCurrentSession();
ASM::$com->newSession();
ASM::$com->load(array('c.id' => $commandersId));

# chargement des places relatives aux commandants attaquants
$placesId = array(0);
for ($i = 0; $i < ASM::$com->size(); $i++) {
	$placesId[] = ASM::$com->get($i)->rStartPlace;
	$placesId[] = ASM::$com->get($i)->rDestinationPlace;
}

$S_PLM_MAPLAYER = ASM::$plm->getCurrentSession();
ASM::$plm->newSession();
ASM::$plm->load(array('id' => $placesId));

echo '<div id="attacks" ' . (Params::check(Params::SHOW_MAP_FLEETIN) ? NULL : 'style="display:none;"') . '>';
	echo '<svg viewBox="0, 0, ' . (GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size']) . ', ' . (GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
			for ($i = 0; $i < ASM::$com->size(); $i++) {
				$commander = ASM::$com->get($i);

				if ($commander->travelType != Commander::BACK) {
					$x1 = ASM::$plm->getById($commander->rStartPlace)->getXSystem() * GalaxyConfiguration::$scale;
					$x2 = ASM::$plm->getById($commander->rDestinationPlace)->getXSystem() * GalaxyConfiguration::$scale;
					$y1 = ASM::$plm->getById($commander->rStartPlace)->getYSystem() * GalaxyConfiguration::$scale;
					$y2 = ASM::$plm->getById($commander->rDestinationPlace)->getYSystem() * GalaxyConfiguration::$scale;
					list($x3, $y3) = $commander->getPosition($x1, $y1, $x2, $y2);
					$rt = Utils::interval($commander->dArrival, Utils::now(), 's');

					echo '<line class="color' . $commander->playerColor . '" x1="' . $x1 . '" x2="' . $x2 . '" y1="' . $y1 . '" y2="' . $y2 . '" />';
					echo '<circle class="color' . $commander->playerColor . '" cx="0" cy="0" r="3">';
						echo '<animate attributeName="cx" attributeType="XML" fill="freeze" from="' . $x3 . '" to="' . $x2 . '" begin="0s" dur="' . $rt . 's"/>';
						echo '<animate attributeName="cy" attributeType="XML" fill="freeze" from="' . $y3 . '" to="' . $y2 . '" begin="0s" dur="' . $rt . 's"/>';
					echo '</circle>';
				}
			}
	echo '</svg>';
echo '</div>';

ASM::$plm->changeSession($S_PLM_MAPLAYER);
ASM::$com->changeSession($S_COM_ATT);
