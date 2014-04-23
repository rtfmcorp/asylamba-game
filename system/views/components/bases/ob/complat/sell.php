<?php
include_once ARES;

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

echo '<div class="component transaction">';
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

# resources current rate
ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_RESOURCE, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$resourcesCurrentRate = ASM::$trm->get()->currentRate;

echo '<div class="component market-sell">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Ressources</h2>';
		echo '<em>bra !</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . APP_ROOT . 'action/a-proposetransaction/rplace-' . $ob_compPlat->getId() . '/type-' . Transaction::TYP_RESOURCE . '" method="post">';
				
				echo '<div class="label-box">';
					echo '<span class="label">Ressources</span>';
					echo '<span class="value">' . Format::numberFormat($ob_compPlat->resourcesStorage) . '</span>';
					echo '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">';
				echo '</div>';

				echo '<div class="label-box">';
					echo '<label for="sell-market-quantity-resources" class="label">Quantité</label>';
					echo '<input id="sell-market-quantity-resources" class="value" type="text" name="quantity"/>';
					echo '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">';
				echo '</div>';

				echo '<hr />';

				echo '<div class="label-box">';
					echo '<span class="label">Prix minimum</span>';
					echo '<span class="value"></span>';
					echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
				echo '</div>';

				echo '<div class="label-box">';
					echo '<label for="sell-market-price-resources" class="label">Prix</label>';
					echo '<input id="sell-market-price-resources" class="value" type="text" name="price" value=""/>';
					echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
				echo '</div>';

				echo '<hr />';

				echo '<p><input type="submit" value="vendre" /></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$S_COM1 = ASM::$com->getCurrentSession();
ASM::$com->newSession();
ASM::$com->load(array('c.statement' => Commander::INSCHOOL, 'c.rBase' => $ob_compPlat->getId()), array('c.experience', 'DESC'));

# resources current rate
ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_COMMANDER, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$commanderCurrentRate = ASM::$trm->get()->currentRate;

echo '<div class="component market-sell">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'orbitalbase/school.png" alt="commandants" class="main" />';
		echo '<h2>Commandants</h2>';
		echo '<em>bra !</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$com->size(); $i++) {
				$commander = ASM::$com->get($i);

				echo '<div class="queue">';
					echo '<div class="item">';
						echo '<img class="picto" src="' . MEDIA . 'commander/small/c1-l' . rand(1, 3) . '-c' . CTR::$data->get('playerInfo')->get('color') . '.png" alt="" />';
						echo '<strong>' . CommanderResources::getInfo($commander->getLevel(), 'grade') . ' ' . $commander->getName() . '</strong>';
						echo '<em>' . Format::numberFormat($commander->getExperience()) . ' points d\'expérience</em>';
					echo '</div>';
				echo '</div>';

				echo '<form action="' . APP_ROOT . 'action/a-proposetransaction/rplace-' . $ob_compPlat->getId() . '/type-' . Transaction::TYP_COMMANDER . '/identifier-' . $commander->getId() . '" method="post">';

					echo '<div class="label-box">';
						echo '<span class="label">Prix minimum</span>';
						echo '<span class="value">' . Game::getMinPriceRelativeToRate(Transaction::TYP_COMMANDER, $commander->experience) . '</span>';
						echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
					echo '</div>';

					echo '<div class="label-box">';
						echo '<label for="sell-market-price-commander" class="label">Prix</label>';
						echo '<input id="sell-market-price-commander" class="value" type="text" name="price" value="' . round($commander->experience * $commanderCurrentRate) . '" />';
						echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
					echo '</div>';

					echo '<hr />';

					echo '<p><input type="submit" value="vendre" /></p>';
				echo '</form>';
			}

			if (ASM::$com->size() == 0) {
				echo '<p><em>Vous n\'avez aucun commandant dans l\'école.</em></p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$com->changeSession($S_COM1);

# resources current rate
ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_SHIP, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$shipCurrentRate = ASM::$trm->get()->currentRate;

echo '<div class="component market-sell">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'orbitalbase/dock2.png" alt="vaisseaux" class="main" />';
		echo '<h2>Vaisseaux</h2>';
		echo '<em>bra !</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($ob_compPlat->shipStorage as $key => $ship) {
				if ($ship > 0) {
					echo '<div class="queue">';
						echo '<div class="item">';
							echo '<img class="picto" src="' . MEDIA . 'ship/picto/ship' . $key . '.png" alt="" />';
							echo '<strong>' . ShipResource::getInfo($key, 'codeName') . '</strong>';
							echo '<em>' . ShipResource::getInfo($key, 'name') . '</em>';
							echo '<em>' . ShipResource::getInfo($key, 'pev') . ' pev</em>';
						echo '</div>';
					echo '</div>';

					echo '<form action="' . APP_ROOT . 'action/a-proposetransaction/rplace-' . $ob_compPlat->getId() . '/type-' . Transaction::TYP_SHIP . '/identifier-' . $key . '" method="post">';

						echo '<div class="label-box">';
							echo '<span class="label">Quantité max.</span>';
							echo '<span class="value">' . $ship . '</span>';
						echo '</div>';

						echo '<div class="label-box">';
							echo '<label for="sell-market-quantity-ship" class="label">Quantité</label>';
							echo '<input id="sell-market-quantity-ship" class="value" type="text" name="quantity" />';
						echo '</div>';

						echo '<hr />';

						echo '<div class="label-box">';
							echo '<span class="label">Prix minimum</span>';
							echo '<span class="value"></span>';
							echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
						echo '</div>';

						echo '<div class="label-box">';
							echo '<label for="sell-market-price-ship" class="label">Prix</label>';
							echo '<input id="sell-market-price-ship" class="value" type="text" name="price" />';
							echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
						echo '</div>';

						echo '<hr />';

						echo '<p><input type="submit" value="vendre" /></p>';
					echo '</form>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$csm->changeSession($S_CSM1);
ASM::$ctm->changeSession($S_CTM1);
ASM::$trm->changeSession($S_TRM1);
?>