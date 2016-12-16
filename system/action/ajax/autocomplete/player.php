<?php

use Asylamba\Modules\Demeter\Resource\ColorResource;

$playerManager = $this->getContainer()->get('zeus.player_manager');

$S_PAM1 = $playerManager->newSession(FALSE);

$playerManager->search($_GET['q'], array('experience', 'DESC'), array(0, 20));

if ($playerManager->size() != 0) {
	for ($i = 0; $i < $playerManager->size(); $i++) {
		$player = $playerManager->get($i);

		if (in_array($player->getStatement(), array(Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY, Player::BANNED))) {
			if ($player->getRColor() > 0) {
				$status = ColorResource::getInfo($player->getRColor(), 'status');
				$status = $status[$player->getStatus() - 1];
			} else {
				$status = 'Rebelle';
			}

			echo '<img class="img" src="' . MEDIA . 'avatar/small/' . $player->getAvatar() . '.png" alt="' . $player->getName() . '" /> ';
			echo '<span class="value-2">' . $status . '</span>';
			echo '<span class="value-1"><span class="ac_value" data-id="' . $player->getId() . '">' . $player->getName() . '</span></span>';
			echo "\n";
		}
	}
}
$playerManager->changeSession($S_PAM1);