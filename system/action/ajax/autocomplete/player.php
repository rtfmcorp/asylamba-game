<?php

use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Zeus\Model\Player;

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);

$players = $playerManager->search($this->getContainer()->get('app.request')->query->get('q'));

if (count($players) > 0) {
	foreach ($players as $player) {
		if (in_array($player->getStatement(), array(Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY, Player::BANNED))) {
			if ($player->getRColor() > 0) {
				$status = ColorResource::getInfo($player->getRColor(), 'status');
				$status = $status[$player->getStatus() - 1];
			} else {
				$status = 'Rebelle';
			}

			echo '<img class="img" src="' . $mediaPath . 'avatar/small/' . $player->getAvatar() . '.png" alt="' . $player->getName() . '" /> ';
			echo '<span class="value-2">' . $status . '</span>';
			echo '<span class="value-1"><span class="ac_value" data-id="' . $player->getId() . '">' . $player->getName() . '</span></span>';
			echo "\n";
		}
	}
}
