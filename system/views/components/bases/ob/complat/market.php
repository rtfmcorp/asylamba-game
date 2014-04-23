<?php
$S_TRM1 = ASM::$trm->getCurrentSession();
$S_CSM1 = ASM::$csm->getCurrentSession();
ASM::$csm->changeSession($ob_compPlat->shippingManager);

$S_CTM1 = ASM::$ctm->getCurrentSession();
ASM::$ctm->newSession();
ASM::$ctm->load(array());

echo '<div class="component transaction">';
	echo '<div class="head skin-2">';
		echo '<h2>Aperçu des achats</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Convoi en approche</h4>';
			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				if (ASM::$csm->get($i)->statement == CommercialShipping::ST_GOING && ASM::$csm->get($i)->rBaseDestination == $ob_compPlat->getId()) {
					ASM::$csm->get($i)->render();
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_RESOURCE, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$ressourceCurrentRate = ASM::$trm->get()->currentRate;

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_RESOURCE, 'statement' => Transaction::ST_PROPOSED), array('dPublication', 'DESC'), array(0, 20));

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Ressources</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($ressourceCurrentRate, 3) . '</em>';
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
				if (CTR::$data->get('playerId') != ASM::$trm->get($i)->rPlayer) {
					ASM::$trm->get($i)->render($ressourceCurrentRate, $S_CTM1, $ob_compPlat);
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_COMMANDER, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$currentRate = ASM::$trm->get()->currentRate;

ASM::$trm->newSession();
ASM::$trm->load(
	array('type' => Transaction::TYP_COMMANDER, 'statement' => Transaction::ST_PROPOSED),
	array('dPublication', 'DESC'),
	array(0, 20),
	Transaction::TYP_COMMANDER
);

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

			for ($i = 0; $i < ASM::$trm->size(); $i++) {
				if (CTR::$data->get('playerId') != ASM::$trm->get($i)->rPlayer) {
					ASM::$trm->get($i)->render($ressourceCurrentRate, $S_CTM1, $ob_compPlat);
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