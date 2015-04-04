<?php
include_once ATHENA;

$S_TRM1 = ASM::$trm->getCurrentSession();

# resources current rate
ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_RESOURCE, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$resourcesCurrentRate = ASM::$trm->get()->currentRate;

echo '<div class="component market-sell">';
	echo '<div class="head skin-4 sh">';
		echo '<img src="' . MEDIA . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Ressources</h2>';
		echo '<em>mettre en vente</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form class="sell-form" data-shipcom-size="' . CommercialShipping::WEDGE . '" data-resource-rate="1" data-max-quantity="' . $ob_compPlat->resourcesStorage . '" data-rate="' . $resourcesCurrentRate . '" data-min-price="' . Transaction::MIN_RATE_RESOURCE . '" action="' . Format::actionBuilder('proposetransaction', ['rplace' => $ob_compPlat->getId(), 'type' => Transaction::TYP_RESOURCE]) . '" method="post">';
				echo '<div class="label-box">';
					echo '<span class="label">Ressources</span>';
					echo '<span class="value">' . Format::numberFormat($ob_compPlat->resourcesStorage) . '</span>';
					echo '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">';
				echo '</div>';

				echo '<div class="label-box sf-quantity">';
					echo '<label for="sell-market-quantity-resources" class="label">Quantité</label>';
					echo '<input id="sell-market-quantity-resources" class="value" type="text" name="quantity" autocomplete="off" />';
					echo '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">';
				echo '</div>';

				echo '<hr />';

				echo '<div class="label-box sf-min-price">';
					echo '<span class="label">Prix minimum</span>';
					echo '<span class="value" data-price-raw="0"></span>';
					echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
				echo '</div>';

				echo '<div class="label-box sf-price">';
					echo '<label for="sell-market-price-resources" class="label">Prix</label>';
					echo '<input id="sell-market-price-resources" class="value" type="text" name="price" autocomplete="off"/>';
					echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
				echo '</div>';

				echo '<hr />';

				echo '<div class="label-box sf-comship">';
					echo '<span class="label">Vaisseaux</span>';
					echo '<span class="value"></span>';
					echo '<img class="icon-color" alt="vaisseaux transports" src="' . MEDIA . 'resources/transport.png">';
				echo '</div>';

				echo '<hr />';

				echo '<p><input type="submit" value="Vendre" /></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$S_COM1 = ASM::$com->getCurrentSession();
ASM::$com->newSession();
ASM::$com->load(array('c.statement' => Commander::INSCHOOL, 'c.rBase' => $ob_compPlat->getId()), array('c.experience', 'DESC'));

# commander current rate
ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_COMMANDER, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$commanderCurrentRate = ASM::$trm->get()->currentRate;

echo '<div class="component market-sell">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'orbitalbase/school.png" alt="commandants" class="main" />';
		echo '<h2>Commandants</h2>';
		echo '<em>mettre en vente</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$com->size(); $i++) {
				$commander = ASM::$com->get($i);

				echo '<div class="queue">';
					echo '<div class="item sh" data-target="sell-commander-' . $i . '">';
						echo '<img class="picto" src="' . MEDIA . 'commander/small/' . $commander->avatar . '.png" alt="" />';
						echo '<strong>' . CommanderResources::getInfo($commander->getLevel(), 'grade') . ' ' . $commander->getName() . '</strong>';
						echo '<em>' . Format::numberFormat($commander->getExperience()) . ' points d\'expérience</em>';
					echo '</div>';
				echo '</div>';

				echo '<form class="sell-form" id="sell-commander-' . $i . '" action="' . Format::actionBuilder('proposetransaction', ['rplace' => $ob_compPlat->getId(), 'type' => Transaction::TYP_COMMANDER, 'identifier' => $commander->getId()]) . '" method="post" style="display:none;">';

					echo '<div class="label-box">';
						echo '<span class="label">Prix minimum</span>';
						echo '<span class="value">' . Game::getMinPriceRelativeToRate(Transaction::TYP_COMMANDER, $commander->experience) . '</span>';
						echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
					echo '</div>';

					echo '<div class="label-box">';
						echo '<label for="sell-market-price-commander" class="label">Prix</label>';
						echo '<input id="sell-market-price-commander" class="value" type="text" name="price" value="' . ceil($commander->experience * $commanderCurrentRate) . '" autocomplete="off" />';
						echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
					echo '</div>';

					echo '<div class="label-box">';
						echo '<span class="label">Vaisseaux</span>';
						echo '<span class="value">1</span>';
						echo '<img class="icon-color" alt="vaisseaux transports" src="' . MEDIA . 'resources/transport.png">';
					echo '</div>';

					echo '<hr />';

					echo '<input type="hidden" value="' . $commander->experience . '" name="quantity" />';

					echo '<p><input type="submit" value="Vendre" /></p>';
				echo '</form>';
			}

			if (ASM::$com->size() == 0) {
				echo '<p><em>Vous n\'avez aucun commandant dans l\'école.</em></p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$com->changeSession($S_COM1);

# ship current rate
ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_SHIP, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$shipCurrentRate = ASM::$trm->get()->currentRate;

echo '<div class="component market-sell">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'orbitalbase/dock2.png" alt="vaisseaux" class="main" />';
		echo '<h2>Vaisseaux</h2>';
		echo '<em>mettre en vente</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($ob_compPlat->shipStorage as $key => $ship) {
				if ($ship > 0) {
					echo '<div class="queue sh" data-target="sell-ships-' . $key . '">';
						echo '<div class="item">';
							echo '<img class="picto" src="' . MEDIA . 'ship/picto/ship' . $key . '.png" alt="" />';
							echo '<strong>' . ShipResource::getInfo($key, 'codeName') . '</strong>';
							echo '<em>' . ShipResource::getInfo($key, 'name') . '</em>';
							echo '<em>' . ShipResource::getInfo($key, 'pev') . ' pev</em>';
						echo '</div>';
					echo '</div>';

					echo '<form id="sell-ships-' . $key . '" class="sell-form" data-shipcom-size="' . CommercialShipping::WEDGE . '" data-resource-rate="' . (ShipResource::getInfo($key, 'pev') * 1000) . '" data-max-quantity="' . $ship . '" data-rate="' . ($shipCurrentRate * ShipResource::getInfo($key, 'resourcePrice')) . '" data-min-price="' . Game::getMinPriceRelativeToRate(Transaction::TYP_SHIP, 1, $key) . '" action="' . Format::actionBuilder('proposetransaction', ['rplace' => $ob_compPlat->getId(), 'type' => Transaction::TYP_SHIP, 'identifier' => $key]) . '" method="post" style="display:none;">';
						echo '<div class="label-box">';
							echo '<span class="label">Quantité max.</span>';
							echo '<span class="value">' . $ship . '</span>';
						echo '</div>';

						echo '<div class="label-box sf-quantity">';
							echo '<label for="sell-market-quantity-ship" class="label">Quantité</label>';
							echo '<input id="sell-market-quantity-ship" class="value" type="text" name="quantity" autocomplete="off" />';
						echo '</div>';

						echo '<hr />';

						echo '<div class="label-box sf-min-price">';
							echo '<span class="label">Prix minimum</span>';
							echo '<span class="value"></span>';
							echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
						echo '</div>';

						echo '<div class="label-box sf-price">';
							echo '<label for="sell-market-price-ship" class="label">Prix</label>';
							echo '<input id="sell-market-price-ship" class="value" type="text" name="price" autocomplete="off" />';
							echo '<img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png">';
						echo '</div>';

						echo '<hr />';

						echo '<div class="label-box sf-comship">';
							echo '<span class="label">Vaisseaux</span>';
							echo '<span class="value"></span>';
							echo '<img class="icon-color" alt="vaisseaux transports" src="' . MEDIA . 'resources/transport.png">';
						echo '</div>';

						echo '<hr />';

						echo '<p><input type="submit" value="Vendre" /></p>';
					echo '</form>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->changeSession($S_TRM1);
?>