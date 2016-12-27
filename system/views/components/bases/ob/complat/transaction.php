<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Model\CommercialShipping;

$commercialShippingManager = $this->getContainer()->get('athena.commercial_shipping_manager');
$commercialTradeManager = $this->getContainer()->get('athena.commercial_tax_manager');

$S_CSM1 = $commercialShippingManager->getCurrentSession();
$S_CTM1 = $commercialTradeManager->getCurrentSession();

$commercialShippingManager->changeSession($ob_compPlat->shippingManager);
$usedShips = 0;
for ($i = 0; $i < $commercialShippingManager->size(); $i++) { 
	if ($commercialShippingManager->get($i)->rBase == $ob_compPlat->getId()) {
		$usedShips += $commercialShippingManager->get($i)->shipQuantity;
	}
}

$commercialTradeManager->newSession();
$commercialTradeManager->load(array());

echo '<div class="component transaction">';
	echo '<div class="head skin-2">';
		echo '<h2>Transactions</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$maxShip = $orbitalBaseHelper->getBuildingInfo(6, 'level', $ob_compPlat->getLevelCommercialPlateforme(),  'nbCommercialShip');

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
			for ($i = 0; $i < $commercialShippingManager->size(); $i++) { 
				if ($commercialShippingManager->get($i)->statement == CommercialShipping::ST_GOING && $commercialShippingManager->get($i)->rBase == $ob_compPlat->getId()) {
					$commercialShippingManager->render($commercialShippingManager->get($i));
				}
			}
			echo '<hr />';
			echo '<h4>Retour de convoi</h4>';
			for ($i = 0; $i < $commercialShippingManager->size(); $i++) { 
				if ($commercialShippingManager->get($i)->statement == CommercialShipping::ST_MOVING_BACK && $commercialShippingManager->get($i)->rBase == $ob_compPlat->getId()) {
					$commercialShippingManager->render($commercialShippingManager->get($i));
				}
			}
			echo '<hr />';
			echo '<h4>Convoi à quai</h4>';
			for ($i = 0; $i < $commercialShippingManager->size(); $i++) { 
				if ($commercialShippingManager->get($i)->statement == CommercialShipping::ST_WAITING && $commercialShippingManager->get($i)->rBase == $ob_compPlat->getId()) {
					$commercialShippingManager->render($commercialShippingManager->get($i));
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

$commercialShippingManager->changeSession($S_CSM1);
$commercialTradeManager->changeSession($S_CTM1);