<?php
include_once ARES;

$S_COM_MAP_COM = ASM::$com->getCurrentSession();
ASM::$com->newSession();
ASM::$com->load(
	array(
		'c.rBase' => CTR::$data->get('playerParams')->get('base'),
		'c.statement' => array(Commander::AFFECTED, Commander::MOVING)
	),
	array(
		'c.line', 'DESC'
	)
);

$break = FALSE;

echo '<div id="subnav">';
	echo '<div class="overflow">';
		for ($i = 0; $i < ASM::$com->size(); $i++) {
			$commander = ASM::$com->get($i);

			if ($commander->line == 1 && $break == FALSE) {
				echo '<hr />';
				$break = TRUE;
			}

			echo '<a href="#" class="item ' . ($commander->statement == COM_MOVING ? 'striped' : NULL) . ' map-commander" data-id="' . $commander->id . '" data-max-jump="' . Game::getMaxTravelDistance(CTR::$data->get('playerBonus')) . '" data-available="' . ($commander->statement == COM_MOVING ? 'false' : 'true') . '" data-name="' . CommanderResources::getInfo($commander->level, 'grade') . ' ' . $commander->name . '" data-wedge="' . Format::numberFormat(Commander::COEFFLOOT * $commander->getPev()) . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'commander/small/' . $commander->avatar . '.png" alt="" />';
					echo '<span class="number">' . $commander->level . '</span>';
				echo '</span>';
				echo '<span class="content skin-2">';
					echo '<span class="sub-content">';
						echo CommanderResources::getInfo($commander->level, 'grade') . ' ' . $commander->name . '<br />';
						echo Format::numberFormat($commander->getPev()) . ' pev';
						echo '<hr />';
						if ($commander->statement == COM_MOVING) {
							switch ($commander->travelType) {
								case COM_MOVE: echo 'Déplacement'; break;
								case COM_LOOT: echo 'Pillage'; break;
								case COM_COLO: echo 'Colonisation'; break;
								case COM_BACK: echo 'Retour'; break;
								default: break;
							}
						} else {
							echo 'A quai';
						}
						echo '<hr />';

						foreach ($commander->getNbrShipByType() as $k => $nbr) {
							echo '<span class="ship">';
								echo '<img src="' . MEDIA . 'ship/picto/ship' . $k . '.png" ' . ($nbr == 0 ? 'class="zero"' : NULL) . '/>';
								echo '<span class="number">' . $nbr . '</span>';
							echo '</span>';
						}
					echo '</span>';
				echo '</span>';
			echo '</a>';
		}
	echo '</div>';
echo '</div>';

ASM::$com->changeSession($S_COM_MAP_COM);
?>