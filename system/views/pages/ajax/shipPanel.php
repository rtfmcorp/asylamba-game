<?php
include_once ATHENA;

$ship = CTR::$get->get('ship');

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
					echo '<td>' . (ShipResource::getInfo($ship, 'pev') * 250) . ' <img src="' .  MEDIA. 'resources/resource.png" alt="ressource" class="icon-color" /></td>';
				echo '</tr>';
				/*echo '<tr>';
					echo '<td class="hb lt" title="points que rapporte la construction du vaisseau au joueur">points</td>';
					echo '<td>' . ShipResource::getInfo($ship, 'points') . '</td>';
				echo '</tr>';*/
			echo '</table></div>';

			echo '<h4>Caractéristiques</h4>';

			# MAXIMA
			$life 		= ($ship > 5) ? 1600 : 135;
			$defense 	= ($ship > 5) ? 200 : 20;
			$speeda 	= ($ship > 5) ? 25 : 100;
			$speedb 	= ($ship > 5) ? 250 : 150;
			$attack 	= ($ship > 5) ? 1000 : 50;

			echo '<div class="skill-box">';
				$attacks = ShipResource::getInfo($ship, 'attack');
				$eachValues = array_unique($attacks);
				$nbr = array_count_values($attacks);
				$value = '';
				foreach ($nbr as $k => $v) { $value .= ($v != 1) ? $v . '×' . $k . ' + ' : $k . ' + '; }
				echo '<span class="label">attaque</span>';
				echo '<span class="value"><img src="' .  MEDIA. 'resources/attack.png" class="icon-color" /> ' . substr($value, 0, -3) . '</span>';
				echo '<span class="progress-bar">';
					for ($j = 0; $j < count($attacks); $j++) {
						$theme = (($j % 2) == 0) ? 1 : 3;
						echo '<span class="content" style="width: ' . Format::percent($attacks[$j], $attack) . '%;"></span>';
					}
				echo '</span>';
			echo '</div>';
			echo '<div class="skill-box">';
				echo '<span class="label">défense</span>';
				echo '<span class="value"><img src="' .  MEDIA. 'resources/defense.png" class="icon-color" /> ' . ShipResource::getInfo($ship, 'defense') . '</span>';
				echo '<span class="progress-bar"><span class="content" style="width: ' . Format::percent(ShipResource::getInfo($ship, 'defense'), $defense) . '%;"></span></span>';
			echo '</div>';
			echo '<div class="skill-box">';
				echo '<span class="label">vitesse</span>';
				echo '<span class="value"><img src="' .  MEDIA. 'resources/speed.png" class="icon-color" /> ' . ShipResource::getInfo($ship, 'speed') . '</span>';
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