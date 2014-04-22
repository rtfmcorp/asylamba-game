<?php
$S_TRM1 = ASM::$trm->getCurrentSession();
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

echo '<div class="component rc">';
	echo '<div class="head skin-2">';
		echo '<h2>Aperçu des ventes</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$maxShip = OrbitalBaseResource::getBuildingInfo(6, 'level', $ob_compPlat->getLevelCommercialPlateforme(),  'nbCommercialShip');

			echo '<div class="number-box">';
				echo '<span class="label">vaisseaux de commerce disponibles</span>';
				echo '<span class="value">' . Format::numberFormat($maxShip - $usedShips) . ' / ' . Format::numberFormat($maxShip) . '</span>';

				echo '<span class="progress-bar">';
				echo '<span style="width:' . Format::percent($maxShip - $usedShips, $maxShip) . '%;" class="content"></span>';
			echo '</div>';

			echo '<h4>vente en déplacement</h4>';
			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				if (ASM::$csm->get($i)->statement == CommercialShipping::ST_GOING && ASM::$csm->get($i)->rBase == $ob_compPlat->getId()) {
					var_dump(ASM::$csm->get($i));
				}
			}

			echo '<hr />';
			echo '<p>flotte en retour</p>';
			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				if (ASM::$csm->get($i)->statement == CommercialShipping::ST_MOVING_BACK && ASM::$csm->get($i)->rBase == $ob_compPlat->getId()) {
					var_dump(ASM::$csm->get($i));
				}
			}

			echo '<hr />';
			echo '<p>vente en attente d\'achat</p>';
			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				if (ASM::$csm->get($i)->statement == CommercialShipping::ST_WAITING && ASM::$csm->get($i)->rBase == $ob_compPlat->getId()) {
					var_dump(ASM::$csm->get($i));
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_RESOURCE, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$currentRate = ASM::$trm->get()->currentRate;

echo '<div class="component market-sell">';
	echo '<div class="head skin-2">';
		echo '<h2>Vente ressources</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . APP_ROOT . 'action/a-proposetransaction/rplace-' . $ob_compPlat->getId() . '/type-' . Transaction::TYP_RESOURCE . '" method="post">';
				
				echo '<div class="label-box">';
					echo '<span class="label">Ressources</span>';
					echo '<span class="value">' . $ob_compPlat->resourcesStorage. '</span>';
					echo '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">';
				echo '</div>';

				echo '<div class="label-box">';
					echo '<label for="sell-market-quantity" class="label">Quantité</label>';
					echo '<input id="sell-market-quantity" class="value" type="text" name="quantity"/>';
					echo '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">';
				echo '</div>';

				echo '<div class="label-box">';
					echo '<label for="sell-market-price" class="label">Prix</label>';
					echo '<input id="sell-market-price" class="value" type="text" name="price"/></span>';
					echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
				echo '</div>';

				echo '<hr />';

				echo '<p><input type="submit" value="vendre" /></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component rc">';
	echo '<div class="head skin-2">';
		echo '<h2>Vente commandant</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component rc">';
	echo '<div class="head skin-2">';
		echo '<h2>Vente vaisseaux</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$csm->changeSession($S_CSM1);
ASM::$ctm->changeSession($S_CTM1);
ASM::$trm->changeSession($S_TRM1);
?>