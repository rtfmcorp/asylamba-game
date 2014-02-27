<?php
$S_TRM1 = ASM::$trm->getCurrentSession();
$S_CSM1 = ASM::$csm->getCurrentSession();

ASM::$csm->changeSession($ob_compPlat->shippingManager);
$usedShips = 0;
for ($i = 0; $i < ASM::$csm->size(); $i++) { 
	if (ASM::$csm->get($i)->rBase == $ob_compPlat->getId()) {
		$usedShips += ASM::$csm->get($i)->shipQuantity;
	}
}

echo '<div class="component rc">';
	echo '<div class="head skin-2">';
		echo '<h2>Aperçu</h2>';
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

			echo '<p>transaction en cours</p>';

			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				if (ASM::$csm->get($i)->statement == CommercialShipping::ST_GOING) {
					var_dump(ASM::$csm->get($i));
				}
			}

			echo '<hr />';

			echo '<p>vos offres en attentes</p>';

			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				if (ASM::$csm->get($i)->statement == CommercialShipping::ST_WAITING) {
					var_dump(ASM::$csm->get($i));
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_RESOURCE, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$currentRate = ASM::$trm->get()->currentRate;

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_RESOURCE, 'statement' => Transaction::ST_PROPOSED), array('dPublication', 'DESC'), array(0, 20));

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Ressources</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($currentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="#" class="sh" data-target="sell-resources">vendre des ressources</a></span>';
				echo '<span><a href="#">?</a></span>';
			echo '</div>';

			echo '<div class="new-transaction resources" id="sell-resources" style="display: none">';
				echo '<form action="' . APP_ROOT . 'action/a-proposetransaction/type-' . Transaction::TYP_RESOURCE . '/rplace-' . $ob_compPlat->getId() . '" method="post">';
					echo '<label for="resources-quantity">';
						echo 'Quantité de ressources';
						echo '<input type="number" name="quantity" data-variation="30" data-rate="' . $currentRate . '" id="resources-quantity" placeholder="Maximum ' . Format::numberFormat($ob_compPlat->getResourcesStorage()) . '" />';
					echo '</label>';
					echo '<label for="resources-price">';
						echo 'Prix en crédit';
						echo '<input type="number" name="price" id="resources-price" />';
						echo '<span class="indicator">';
							echo '<span class="min-price">---</span>';
							echo '<span class="max-price">---</span>';
						echo '</span>';
					echo '</label>';
					echo '<input type="submit" value="proposer cette offre" />';
				echo '</form>';
			echo '</div>';

			for ($i = 0; $i < ASM::$trm->size(); $i++) {
				$p = ASM::$trm->get($i);

				if (CTR::$data->get('playerId') != $p->rPlayer) {
					$rateVariation = $currentRate - $p->price / $p->quantity;
					if ($rateVariation < 0) {
						$rateVariation = '+ ' . Format::numberFormat(abs($rateVariation), 3);
					} else {
						$rateVariation = '- ' . Format::numberFormat(abs($rateVariation), 3);
					}

					echo '<div class="transaction resources">';
						echo '<div class="product sh" data-target="transaction-' . $p->id . '">';
							echo '<img src="' . MEDIA . 'market/resources-pack-' . Transaction::getResourcesIcon($p->quantity) . '.png" alt="" class="picto" />';
							echo '<span class="rate">' . $rateVariation . '</span>';

							echo '<div class="offer">';
								echo Format::numberFormat($p->quantity) . ' <img src="' . MEDIA . 'resources/resource.png" alt="" class="icon-color" />';
							echo '</div>';
							echo '<div class="for">';
								echo '<span>pour</span>';
							echo '</div>';
							echo '<div class="price">';
								echo Format::numberFormat($p->price) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" />';
							echo '</div>';
						echo '</div>';

						echo '<div class="hidden" id="transaction-' . $p->id . '">';
							echo '<div class="info">';
								echo '<div class="seller">';
									echo '<p>vendu par<br /> <a href="' . APP_ROOT . 'diary/player-' . $p->rPlayer . '" class="color' . $p->playerColor . '">' . $p->playerName . '</a></p>';
									echo '<p>depuis<br /> <a href="' . APP_ROOT . 'map/place-' . $p->rPlace . '">' . $p->placeName . '</a> <span class="color' . $p->sectorColor . '">[' . $p->sector . ']</span></p>';
								echo '</div>';
								echo '<div class="price-detail">';
									echo '<p>--- <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
									echo '<p><span>+ taxe</span> --- <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
									echo '<hr />';
									echo '<p><span>=</span> --- <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
								echo '</div>';
							echo '</div>';

							echo '<div class="button">';
								echo '<a href="' . APP_ROOT . 'action/a-accepttransaction/rplace-' . $ob_compPlat->getId() . '/rtransaction-' . $p->id . '">';
									echo 'acheter pour ' . Format::numberFormat($p->price) . ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"><br /> ';
									echo 'durée du transit ' . Chronos::secondToFormat(Game::getTimeTravel(
										$p->rSystem, 
										$p->positionInSystem, 
										$p->xSystem, 
										$p->ySystem, 
										$ob_compPlat->getSystem(), 
										$ob_compPlat->getPosition(), 
										$ob_compPlat->getXSystem(), 
										$ob_compPlat->getYSystem()), 'lite') . ' <img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
								echo '</a>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->newSession(ASM_UMODE);
ASM::$trm->load(array('type' => Transaction::TYP_SHIP, 'statement' => Transaction::ST_PROPOSED));

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'school/size0-level0.png" alt="commandants" class="main" />';
		echo '<h2>Commandants</h2>';
		echo '<em>cours actuel | 0,33:1</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="#">vendre un commandants</a></span>';
			echo '</div>';

			for ($i=0; $i < 10; $i++) { 
				echo '<div class="transaction commander">';
					echo '<div class="product sh" data-target="transaction-commander-' . $i . '">';
						echo '<img src="' . MEDIA . 'commander/small/c1-l3-c' . rand(1, 7) . '.png" alt="" class="picto" />';
						echo '<span class="rate">-1.23</span>';

						echo '<div class="offer">';
							echo '<strong>Rakounga, grade 5</strong>';
							echo '<em>8 543 xp | 33 victoires</em>';
						echo '</div>';
						echo '<div class="for">';
							echo '<span>pour</span>';
						echo '</div>';
						echo '<div class="price">';
							echo Format::numberFormat(rand(100, 100000)) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" />';
						echo '</div>';
					echo '</div>';

					echo '<div class="hidden" id="transaction-commander-' . $i . '">';
						echo '<div class="info">';
							echo '<div class="seller">';
								echo '<p>vendu par<br /><a href="#" class="color1">Geaorge IV</a></p>';
								echo '<p>depuis<br /><a href="#">Chinatown <span class="color6">[17]</span></a></p>';
							echo '</div>';
							echo '<div class="price-detail">';
								echo '<p>prix : 1 000 c</p>';
								echo '<p>taxe : 100 c</p>';
								echo '<hr />';
								echo '<p>total : 1 100 c</p>';
							echo '</div>';
						echo '</div>';

						echo '<div class="button">';
							echo '<a href="#">';
								echo 'acheter pour 1 000 <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"><br />durée du transit 33:20:00 <img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
							echo '</a>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}

		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->newSession(ASM_UMODE);
ASM::$trm->load(array('type' => Transaction::TYP_COMMANDER, 'statement' => Transaction::ST_PROPOSED));

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'orbitalbase/dock2.png" alt="vaisseaux" class="main" />';
		echo '<h2>vaisseaux</h2>';
		echo '<em>cours actuel | 643,45:1</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="#">vendre des vaisseaux</a></span>';
			echo '</div>';

			for ($i=0; $i < 10; $i++) { 
				echo '<div class="transaction ship">';
					echo '<div class="product sh" data-target="transaction-ship-' . $i . '">';
						echo '<img src="' . MEDIA . 'ship/picto/lightFighter.png" alt="" class="picto" />';
						echo '<span class="rate">-24.4</span>';

						echo '<div class="offer">';
							echo '<strong>4 Pégases</strong>';
							echo '<em>8 pev</em>';
						echo '</div>';
						echo '<div class="for">';
							echo '<span>pour</span>';
						echo '</div>';
						echo '<div class="price">';
							echo Format::numberFormat(rand(100, 100000)) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" />';
						echo '</div>';
					echo '</div>';

					echo '<div class="hidden" id="transaction-ship-' . $i . '">';
						echo '<div class="info">';
							echo '<div class="seller">';
								echo '<p>vendu par<br /><a href="#" class="color1">Geaorge IV</a></p>';
								echo '<p>depuis<br /><a href="#">Chinatown <span class="color6">[17]</span></a></p>';
							echo '</div>';
							echo '<div class="price-detail">';
								echo '<p>prix : 1 000 c</p>';
								echo '<p>taxe : 100 c</p>';
								echo '<hr />';
								echo '<p>total : 1 100 c</p>';
							echo '</div>';
						echo '</div>';

						echo '<div class="button">';
							echo '<a href="#">';
								echo 'acheter pour 1 000 <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"><br />durée du transit 33:20:00 <img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
							echo '</a>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}

		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->changeSession($S_TRM1);
ASM::$csm->changeSession($S_CSM1);
?>