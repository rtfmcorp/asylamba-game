<?php
include_once ATHENA;

$S_CSM1 = ASM::$csm->getCurrentSession();
$S_CTM1 = ASM::$ctm->getCurrentSession();

ASM::$csm->changeSession($ob_compPlat->shippingManager);
$usedShips = 0;
for ($i = 0; $i < ASM::$csm->size(); $i++) { 
	if (ASM::$csm->get($i)->rBase == $ob_compPlat->getId()) {
		$usedShips += ASM::$csm->get($i)->shipQuantity;
	}
}

ASM::$ctm->newSession();
ASM::$ctm->load(array());

echo '<div class="component transaction">';
	echo '<div class="head skin-2">';
		echo '<h2>Transactions</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$maxShip = OrbitalBaseResource::getBuildingInfo(6, 'level', $ob_compPlat->getLevelCommercialPlateforme(),  'nbCommercialShip');

			echo '<div class="number-box">';
				echo '<span class="label">Vaisseaux de commerce disponibles</span>';
				echo '<span class="value">';
					echo Format::numberFormat($maxShip - $usedShips);
					echo ' <img class="icon-color" alt="vaisseaux" src="' . MEDIA . 'resources/transport.png"> / ';
					echo Format::numberFormat($maxShip);
					echo ' <img class="icon-color" alt="vaisseaux" src="' . MEDIA . 'resources/transport.png">';
				echo '</span>';

				echo '<span class="progress-bar">';
				echo '<span style="width:' . Format::percent($maxShip - $usedShips, $maxShip) . '%;" class="content"></span>';
			echo '</div>';

			echo '<h4>Convoi en route</h4>';
			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				if (ASM::$csm->get($i)->statement == CommercialShipping::ST_GOING && ASM::$csm->get($i)->rBase == $ob_compPlat->getId()) {
					ASM::$csm->get($i)->render();
				}
			}
			echo '<hr />';
			echo '<h4>Retour de convoi</h4>';
			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				if (ASM::$csm->get($i)->statement == CommercialShipping::ST_MOVING_BACK && ASM::$csm->get($i)->rBase == $ob_compPlat->getId()) {
					ASM::$csm->get($i)->render();
				}
			}
			echo '<hr />';
			echo '<h4>Convoi à quai</h4>';
			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				if (ASM::$csm->get($i)->statement == CommercialShipping::ST_WAITING && ASM::$csm->get($i)->rBase == $ob_compPlat->getId()) {
					ASM::$csm->get($i)->render();
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$csm->changeSession($S_CSM1);
ASM::$ctm->changeSession($S_CTM1);