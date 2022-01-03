<?php

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

$godSons = $playerManager->getGodSons($session->get('playerId'));

# display
echo '<div class="component player rank">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Liste de vos filleuls</h4>';

			foreach ($godSons as $player) {
				echo '<div class="player color' . $player->rColor . ' active">';
					echo '<a href="' . $appRoot . 'embassy/player-' . $player->id . '">';
						echo '<img src="' . $mediaPath . 'avatar/small/' . $player->avatar . '.png" alt="' . $player->name . '" class="picto">';
					echo '</a>';
					echo '<span class="title">' . $player->name . '</span>';
					echo '<strong class="name">' . $player->name . '</strong>';
					echo '<span class="experience">niveau ' . $player->level . '</span>';
				echo '</div>';
			}

			if (count($godSons) === 0) {
				echo '<p>Vous n\'avez encore aucun filleul.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
