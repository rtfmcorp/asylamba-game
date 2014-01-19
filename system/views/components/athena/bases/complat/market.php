<?php
echo '<div class="component rc">';
	echo '<div class="head skin-2">';
		echo '<h2>Aperçu</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$maxShip = OrbitalBaseResource::getBuildingInfo(6, 'level', $ob_compPlat->getLevelCommercialPlateforme(),  'nbCommercialShip');
			$availableShip = 14;

			echo '<div class="number-box">';
				echo '<span class="label">vaisseaux de commerce disponibles</span>';
				echo '<span class="value">' . Format::numberFormat($availableShip) . ' / ' . Format::numberFormat($maxShip) . '</span>';

				echo '<span class="progress-bar">';
				echo '<span style="width:' . Format::percent($availableShip, $maxShip) . '%;" class="content"></span>';
			echo '</div>';
		echo '</div>';

		echo '<p>transaction en cours</p>';
		echo '<p>vos offres en attentes</p>';
	echo '</div>';
echo '</div>';


$S_TRM1 = ASM::$trm->getCurrentSession();
ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_RESOURCE, 'statement' => Transaction::ST_PROPOSED));

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Ressources</h2>';
		echo '<em>cours actuel | 11,3:1</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="#">vendre des ressources</a></span>';
			echo '</div>';

			for ($i=0; $i < 10; $i++) { 
				echo '<div class="transaction resources">';
					echo '<div class="product sh" data-target="transaction-resources-' . $i . '">';
						echo '<img src="' . MEDIA . 'resources/resource.png" alt="" class="picto" />';
						echo '<span class="rate">+0.23</span>';

						echo '<div class="offer">';
							echo Format::numberFormat(rand(1000, 1000000)) . ' <img src="' . MEDIA . 'resources/resource.png" alt="" class="icon-color" />';
						echo '</div>';
						echo '<div class="for">';
							echo '<span>pour</span>';
						echo '</div>';
						echo '<div class="price">';
							echo Format::numberFormat(rand(100, 100000)) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" />';
						echo '</div>';
					echo '</div>';

					echo '<div class="hidden" id="transaction-resources-' . $i . '">';
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
		echo '<h2>Vaisseaux</h2>';
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
?>