<?php
include_once ATHENA;

$S_CSM1 = ASM::$csm->getCurrentSession();

ASM::$csm->changeSession($ob_compPlat->shippingManager);
$usedShips = 0;
for ($i = 0; $i < ASM::$csm->size(); $i++) { 
	if (ASM::$csm->get($i)->rBase == $ob_compPlat->getId()) {
		$usedShips += ASM::$csm->get($i)->shipQuantity;
	}
}

echo '<div class="component transaction">';
	echo '<div class="head skin-2">';
		echo '<h2>Aperçu</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$maxShip = OrbitalBaseResource::getBuildingInfo(6, 'level', $ob_compPlat->getLevelCommercialPlateforme(),  'nbCommercialShip');

			echo '<div class="number-box">';
				echo '<span class="label">vaisseaux de commerce disponibles</span>';
				echo '<span class="value">';
					echo Format::numberFormat($maxShip - $usedShips);
					echo ' <img class="icon-color" alt="vaisseaux" src="' . MEDIA . 'resources/transport.png"> / ';
					echo Format::numberFormat($maxShip);
					echo ' <img class="icon-color" alt="vaisseaux" src="' . MEDIA . 'resources/transport.png">';
				echo '</span>';

				echo '<span class="progress-bar">';
				echo '<span style="width:' . Format::percent($maxShip - $usedShips, $maxShip) . '%;" class="content"></span>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box">';
				echo '<span class="label">ressources disponibles</span>';
				echo '<span class="value">';
					echo Format::numberFormat($ob_compPlat->resourcesStorage);
					echo ' <img src="' . MEDIA . 'resources/resource.png" class="icon-color" />';
				echo '</span>';
			echo '</div>';

		echo '</div>';
	echo '</div>';
echo '</div>';


echo '<div class="component market-sell">';
	echo '<div class="head skin-4 sh">';
		echo '<img src="' . MEDIA . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Envoi de ressources</h2>';
		echo '<em>à une autre base</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form class="sell-form" action="' . APP_ROOT . 'action/a-giveresource/baseid-' . $ob_compPlat->rPlace . '" method="post">';
				# require : baseid, otherbaseid, quantity
				
				echo '<div class="label-box sf-quantity">';
					echo '<label for="sell-market-quantity-resources" class="label">Base</label>';
					echo '<input id="sell-market-quantity-resources" class="value" type="text" name="otherbaseid" autocomplete="off" />';
				echo '</div>';

				echo '<div class="label-box sf-quantity">';
					echo '<label for="sell-market-quantity-resources" class="label">Quantité</label>';
					echo '<input id="sell-market-quantity-resources" class="value" type="text" name="quantity" autocomplete="off" />';
					echo '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">';
				echo '</div>';

				echo '<div class="label-box">';
					echo '<span class="label">Vaisseaux requis</span>';
					echo '<span class="value">x</span>';
					echo '<img class="icon-color" alt="vaisseaux" src="' . MEDIA . 'resources/transport.png">';
				echo '</div>';

				echo '<hr />';

				echo '<p><input type="submit" value="envoyer" /></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$csm->changeSession($S_CSM1);
?>