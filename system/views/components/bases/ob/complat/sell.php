<?php
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
		echo '<h2>Truc en attente/vente</h2>';
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

			echo '<hr />';
			echo '<p>vente en déplacement</p>';
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

echo '<div class="component rc">';
	echo '<div class="head skin-2">';
		echo '<h2>Vente ressources</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . APP_ROOT . 'action/a-proposetransaction/rplace-' . $ob_compPlat->getId() . '/type-' . Transaction::TYP_RESOURCE . '" method="post">';
				echo '<p><input type="text" name="quantity" placeholder="Quantité" /></p>';
				echo '<p><input type="text" name="price" placeholder="Prix" /></p>';
				echo '<p><input type="submit" value="on y va !!!" /></p>';
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
?>