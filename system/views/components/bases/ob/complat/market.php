<?php
$S_TRM1 = ASM::$trm->getCurrentSession();
$S_CSM1 = ASM::$csm->getCurrentSession();

$S_CTM1 = ASM::$ctm->getCurrentSession();
ASM::$ctm->newSession();
ASM::$ctm->load(array());

echo '<div class="component rc">';
	echo '<div class="head skin-2">';
		echo '<h2>Truc qui viennent (cond)</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p>transaction en cours</p>';

			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				if (ASM::$csm->get($i)->statement == CommercialShipping::ST_GOING && ASM::$csm->get($i)->rBaseDestination == $ob_compPlat->getId()) {
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
				echo '<span>sorting</span>';
				echo '<span><a href="#">P</a></span>';
				echo '<span><a href="#">P</a></span>';
				echo '<span><a href="#">R</a></span>';
				echo '<span><a href="#">S</a></span>';
			echo '</div>';

			for ($i = 0; $i < ASM::$trm->size(); $i++) {
				$tr = ASM::$trm->get($i);

				if (CTR::$data->get('playerId') != $tr->rPlayer) {
					$rateVariation = $currentRate - $tr->price / $tr->quantity;
					if ($rateVariation < 0) {
						$rateVariation = '+ ' . Format::numberFormat(abs($rateVariation), 3);
					} else {
						$rateVariation = '- ' . Format::numberFormat(abs($rateVariation), 3);
					}

					echo '<div class="transaction resources">';
						echo '<div class="product sh" data-target="transaction-' . $tr->id . '">';
							echo '<img src="' . MEDIA . 'market/resources-pack-' . Transaction::getResourcesIcon($tr->quantity) . '.png" alt="" class="picto" />';
							echo '<span class="rate">' . $rateVariation . '</span>';

							echo '<div class="offer">';
								echo Format::numberFormat($tr->quantity) . ' <img src="' . MEDIA . 'resources/resource.png" alt="" class="icon-color" />';
							echo '</div>';
							echo '<div class="for">';
								echo '<span>pour</span>';
							echo '</div>';
							echo '<div class="price">';
								echo Format::numberFormat($tr->price) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" />';
							echo '</div>';
						echo '</div>';

						echo '<div class="hidden" id="transaction-' . $tr->id . '">';
							echo '<div class="info">';
								echo '<div class="seller">';
									echo '<p>vendu par<br /> <a href="' . APP_ROOT . 'diary/player-' . $tr->rPlayer . '" class="color' . $tr->playerColor . '">' . $tr->playerName . '</a></p>';
									echo '<p>depuis<br /> <a href="' . APP_ROOT . 'map/place-' . $tr->rPlace . '">' . $tr->placeName . '</a> <span class="color' . $tr->sectorColor . '">[' . $tr->sector . ']</span></p>';
								echo '</div>';
								echo '<div class="price-detail">';
									echo '<p>' . Format::numberFormat($tr->price) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
									for ($i = 0; $i < ASM::$ctm->size(); $i++) { 
										$comTax = ASM::$ctm->get($i);
										if ($comTax->faction == $tr->sectorColor AND $comTax->relatedFaction == $ob_compPlat->sectorColor) {
											$exportTax = $comTax->exportTax;
										}
										if ($comTax->faction == $ob_compPlat->sectorColor AND $comTax->relatedFaction == $tr->sectorColor) {
											$importTax = $comTax->importTax;
										}
									}
									$exportTax = round($tr->price * $exportTax / 100);
									$importTax = round($tr->price * $importTax / 100);

									echo '<p><span>+ taxe </span>' . Format::numberFormat($exportTax) . ' <span>+ taxe </span>' . Format::numberFormat($exportTax) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
									echo '<hr />';
									$totalPrice = $tr->price + $exportTax + $importTax;
									echo '<p><span>=</span> ' . Format::numberFormat($totalPrice) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
								echo '</div>';
							echo '</div>';

							echo '<div class="button">';
								echo '<a href="' . APP_ROOT . 'action/a-accepttransaction/rplace-' . $ob_compPlat->getId() . '/rtransaction-' . $tr->id . '">';
									echo 'acheter pour ' . Format::numberFormat($tr->price) . ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"><br /> ';
									echo 'durée du transit ' . Chronos::secondToFormat(Game::getTimeTravel(
										$tr->rSystem, 
										$tr->positionInSystem, 
										$tr->xSystem, 
										$tr->ySystem, 
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

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_SHIP, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$currentRate = ASM::$trm->get()->currentRate;

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_SHIP, 'statement' => Transaction::ST_PROPOSED), array('dPublication', 'DESC'), array(0, 20));

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'orbitalbase/school.png" alt="commandants" class="main" />';
		echo '<h2>Commandants</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($currentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span>sorting</span>';
				echo '<span><a href="#">P</a></span>';
				echo '<span><a href="#">P</a></span>';
				echo '<span><a href="#">R</a></span>';
				echo '<span><a href="#">S</a></span>';
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

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_COMMANDER, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$currentRate = ASM::$trm->get()->currentRate;

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_COMMANDER, 'statement' => Transaction::ST_PROPOSED), array('dPublication', 'DESC'), array(0, 20));

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'orbitalbase/dock2.png" alt="vaisseaux" class="main" />';
		echo '<h2>Vaisseaux</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($currentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span>sorting</span>';
				echo '<span><a href="#">P</a></span>';
				echo '<span><a href="#">P</a></span>';
				echo '<span><a href="#">R</a></span>';
				echo '<span><a href="#">S</a></span>';
			echo '</div>';

			for ($i=0; $i < 10; $i++) { 
				echo '<div class="transaction ship">';
					echo '<div class="product sh" data-target="transaction-ship-' . $i . '">';
						echo '<img src="' . MEDIA . 'ship/picto/ship' . rand(0, 11) . '.png" alt="" class="picto" />';
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
ASM::$ctm->changeSession($S_CTM1);
?>