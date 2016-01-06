<?php
include_once ATHENA;

$ship = CTR::$get->get('ship');

switch(ShipResource::getInfo($ship, 'class')) {
	case 0:
		$bonusSPE = CTR::$data->get('playerBonus')->get(PlayerBonus::FIGHTER_SPEED);
		$bonusATT = CTR::$data->get('playerBonus')->get(PlayerBonus::FIGHTER_ATTACK);
		$bonusDEF = CTR::$data->get('playerBonus')->get(PlayerBonus::FIGHTER_DEFENSE); break;
	case 1:
		$bonusSPE = CTR::$data->get('playerBonus')->get(PlayerBonus::CORVETTE_SPEED);
		$bonusATT = CTR::$data->get('playerBonus')->get(PlayerBonus::CORVETTE_ATTACK);
		$bonusDEF = CTR::$data->get('playerBonus')->get(PlayerBonus::CORVETTE_DEFENSE); break;
	case 2:
		$bonusSPE = CTR::$data->get('playerBonus')->get(PlayerBonus::FRIGATE_SPEED);
		$bonusATT = CTR::$data->get('playerBonus')->get(PlayerBonus::FRIGATE_ATTACK);
		$bonusDEF = CTR::$data->get('playerBonus')->get(PlayerBonus::FRIGATE_DEFENSE); break;
	case 3:
		$bonusSPE = CTR::$data->get('playerBonus')->get(PlayerBonus::DESTROYER_SPEED);
		$bonusATT = CTR::$data->get('playerBonus')->get(PlayerBonus::DESTROYER_ATTACK);
		$bonusDEF = CTR::$data->get('playerBonus')->get(PlayerBonus::DESTROYER_DEFENSE); break;
	default:
		$bonusSPE = 0;
		$bonusATT = 0;
		$bonusDEF = 0; break;
}

echo '<div class="component panel-info">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>' . ShipResource::getInfo($ship, 'name') . '</h4>';
			echo '<a href="#" class="removeInfoPanel remove-info hb lt" title="fermer le panneau">x</a>';

			echo '<div class="table"><table>';
				echo '<tr>';
					echo '<td class="hb lt" title="coût de construction en ressource">coût</td>';
					echo '<td>' . Format::numberFormat(ShipResource::getInfo($ship, 'resourcePrice')) . ' <img src="' .  MEDIA. 'resources/resource.png" alt="ressource" class="icon-color" /></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="hb lt" title="temps de construction (heures:minutes:secondes)">temps</td>';
					echo '<td>' . Chronos::secondToFormat(ShipResource::getInfo($ship, 'time'), 'lite') . ' <img src="' .  MEDIA. 'resources/time.png" alt="relève" class="icon-color" /></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="hb lt" title="points-équivalent-vaisseau, définit la taille qu\'occupe ce vaisseau dans une escadrille">pev</td>';
					echo '<td>' . ShipResource::getInfo($ship, 'pev') . ' <img src="' .  MEDIA. 'resources/pev.png" alt="pev" class="icon-color" /></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="hb lt" title="nombre de ressources que le vaisseau peut transporter">soute</td>';
					echo '<td>' . Format::numberFormat(ShipResource::getInfo($ship, 'pev') * 250) . ' <img src="' .  MEDIA. 'resources/resource.png" alt="ressource" class="icon-color" /></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="hb lt" title="nombre de crédit par relève que coûte le vaisseau affecté à un commandant">entretien en affectation</td>';
					echo '<td>' . Format::numberFormat(ShipResource::getInfo($ship, 'cost')) . ' <img src="' .  MEDIA. 'resources/credit.png" alt="ressource" class="icon-color" /> / <img src="' .  MEDIA. 'resources/time.png" alt="relève" class="icon-color" /></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="hb lt" title="nombre de crédit par relève que coûte le vaisseau à quai ou en vente au marché">entretien à quai</td>';
					echo '<td>' . Format::numberFormat(ceil(ShipResource::getInfo($ship, 'cost') * ShipResource::COST_REDUCTION)) . ' <img src="' .  MEDIA. 'resources/credit.png" alt="ressource" class="icon-color" /> / <img src="' .  MEDIA. 'resources/time.png" alt="relève" class="icon-color" /></td>';
				echo '</tr>';
			echo '</table></div>';

			echo '<h4>Caractéristiques</h4>';

			# MAXIMA
			$life 		= ($ship > 5) ? 1600 : 135;
			$defense 	= ($ship > 5) ? 200 : 25;
			$speeda 	= ($ship > 5) ? 25 : 100;
			$speedb 	= ($ship > 5) ? 250 : 150;
			$attack 	= ($ship > 5) ? 700 : 90;

			echo '<div class="skill-box">';
				$attacks = ShipResource::getInfo($ship, 'attack');
				$eachValues = array_unique($attacks);
				$nbr = array_count_values($attacks);
				$value = '';

				foreach ($nbr as $k => $v) {
					$bonus = round($k * $bonusATT / 100);
					$bonus = $bonus == 0
						? NULL
						: '<span class="bonus">' . ($bonus > 0 ? '+' : NULL) . $bonus . '</span>';
					$value .= ($v != 1)
						? $v . '×' . $k . $bonus . ' + '
						: $k . $bonus . ' + ';
				}

				echo '<span class="label">attaque</span>';
				echo '<span class="value"><img src="' .  MEDIA. 'resources/attack.png" class="icon-color" /> ' . substr($value, 0, -3) . '</span>';
				echo '<span class="progress-bar">';
					for ($j = 0; $j < count($attacks); $j++) {
						$theme = (($j % 2) == 0) ? 1 : 3;
						echo '<span class="content" style="width: ' . Format::percent($attacks[$j], $attack) . '%;"></span>';
					}
				echo '</span>';
			echo '</div>';

			$bonus = round(ShipResource::getInfo($ship, 'defense') * $bonusDEF / 100);
			$bonus = $bonus == 0
				? NULL
				: '<span class="bonus">' . ($bonus > 0 ? '+' : NULL) . $bonus . '</span>';
			echo '<div class="skill-box">';
				echo '<span class="label">défense</span>';
				echo '<span class="value"><img src="' .  MEDIA. 'resources/defense.png" class="icon-color" /> ' . ShipResource::getInfo($ship, 'defense') . $bonus . '</span>';
				echo '<span class="progress-bar"><span class="content" style="width: ' . Format::percent(ShipResource::getInfo($ship, 'defense'), $defense) . '%;"></span></span>';
			echo '</div>';

			$bonus = round(ShipResource::getInfo($ship, 'speed') * $bonusSPE / 100);
			$bonus = $bonus == 0
				? NULL
				: '<span class="bonus">' . ($bonus > 0 ? '+' : NULL) . $bonus . '</span>';
			echo '<div class="skill-box">';
				echo '<span class="label">maniabilité</span>';
				echo '<span class="value"><img src="' .  MEDIA. 'resources/speed.png" class="icon-color" /> ' . ShipResource::getInfo($ship, 'speed') . $bonus . '</span>';
				echo '<span class="progress-bar"><span class="content" style="width: ' . Format::percent((ShipResource::getInfo($ship, 'speed') - $speeda), $speedb) . '%;"></span></span>';
			echo '</div>';

			echo '<div class="skill-box">';
				echo '<span class="label">coque</span>';
				echo '<span class="value"><img src="' .  MEDIA. 'resources/life.png" class="icon-color" /> ' . ShipResource::getInfo($ship, 'life') . '</span>';
				echo '<span class="progress-bar"><span class="content" style="width: ' . Format::percent(ShipResource::getInfo($ship, 'life'), $life) . '%;"></span></span>';
			echo '</div>';

			# description
			echo '<h4>Description</h4>';

			echo '<p class="info">' . ShipResource::getInfo($ship, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>