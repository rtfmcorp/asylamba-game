<?php
include_once ARES;

$S_COM_MAP_COM = ASM::$com->getCurrentSession();
ASM::$com->newSession();
ASM::$com->load(array('rBase' => CTR::$data->get('playerParams')->get('base'), 'statement' => array(COM_AFFECTED, COM_MOVING)));

echo '<div id="map-commanders">';
	for ($i = 0; $i < ASM::$com->size(); $i++) { 
		echo '<div class="commander">';
			echo '<div class="avatar">';
				echo '<img src="' . MEDIA . 'commander/medium/c1-l1-c6.png" alt="" />';
				echo '<span class="level">' . ASM::$com->get($i)->getLevel() . '</span>';
			echo '</div>';
			echo '<div class="army">';
				echo '<div class="label">' . ASM::$com->get($i)->getName() . ' <span class="bonus">' . ASM::$com->get($i)->getPev() . '</span></div>';
				echo '<div class="value">';
					for ($j = 0; $j < 12; $j++) {
						$nbr = rand(0, 10);

						if ($nbr < 7) {
							$nbr = 0;
						} elseif ($nbr == 8) {
							$nbr = 5;
						} elseif ($nbr == 9) {
							$nbr = 10;
						} elseif ($nbr == 10) {
							$nbr = 25;
						}

						echo '<span class="picto ' . ($nbr == 0 ? 'zero' : '') . '">';
							echo '<img src="' . MEDIA . 'ship/picto/' . ShipResource::getInfo($j, 'imageLink') . '.png" alt="" />';
							if ($nbr > 0) {
								echo '<span class="number">' . $nbr . '</span>';
							}
						echo '</span>';
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
echo '</div>';

ASM::$com->changeSession($S_COM_MAP_COM);
?>