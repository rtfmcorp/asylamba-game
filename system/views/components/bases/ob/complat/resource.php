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


echo '<div class="component new-message">';
	echo '<div class="head skin-4 sh">';
		echo '<img src="' . MEDIA . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Envoi de ressources</h2>';
		echo '<em>à une autre base</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . APP_ROOT . 'action/a-giveresource/baseid-' . $ob_compPlat->rPlace . '" method="post">';
				echo '<p><label for="send-resources-target">Base destinataire</label></p>';
				echo '<input class="autocomplete-hidden" type="hidden" name="otherbaseid" />';
				echo '<p class="input input-text"><input type="text" id="send-resources-target" class="autocomplete-orbitalbase" name="name" autocomplete="off" /></p>';

				echo '<p><label for="send-resources-quantity">Nombre de ressources</label></p>';
				echo '<p class="input input-text"><input type="text" id="send-resources-quantity" name="quantity" /></p>';

				echo '<p class="button"><button type="submit">Envoyer</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$csm->changeSession($S_CSM1);
?>