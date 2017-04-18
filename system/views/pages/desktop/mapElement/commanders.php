<?php

use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Ares\Resource\CommanderResources;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Game;

$commanders = $this
	->getContainer()
	->get('ares.commander_manager')
	->getBaseCommanders(
		$this->getContainer()->get('session_wrapper')->get('playerParams')->get('base'),
		[Commander::AFFECTED, Commander::MOVING],
		['c.line' => 'DESC']
	)
;
$break = FALSE;

echo '<div id="subnav">';
	echo '<div class="overflow">';
		foreach ($commanders as $commander) {
			if ($commander->line == 1 && $break == FALSE) {
				echo '<hr />';
				$break = TRUE;
			}

			echo '<a href="#" class="item ' . ($commander->statement == Commander::MOVING ? 'striped' : NULL) . ' map-commander" data-id="' . $commander->id . '" data-color="' .$session->get('playerInfo')->get('color') . '" data-max-jump="' . Game::getMaxTravelDistance($session->get('playerBonus')) . '" data-available="' . ($commander->statement == Commander::MOVING ? 'false' : 'true') . '" data-name="' . CommanderResources::getInfo($commander->level, 'grade') . ' ' . $commander->name . '" data-wedge="' . Format::numberFormat(Commander::COEFFLOOT * $commander->getPev()) . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'commander/small/' . $commander->avatar . '.png" alt="" />';
					echo '<span class="number">' . $commander->level . '</span>';
				echo '</span>';
				echo '<span class="content skin-2">';
					echo '<span class="sub-content">';
						echo CommanderResources::getInfo($commander->level, 'grade') . ' ' . $commander->name . '<br />';
						echo Format::numberFormat($commander->getPev()) . ' pev';
						echo '<hr />';
						if ($commander->statement == Commander::MOVING) {
							switch ($commander->travelType) {
								case Commander::MOVE: echo 'Déplacement'; break;
								case Commander::LOOT: echo 'Pillage'; break;
								case Commander::COLO: echo 'Colonisation'; break;
								case Commander::BACK: echo 'Retour'; break;
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