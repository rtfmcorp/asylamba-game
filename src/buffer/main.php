<?php

use App\Classes\Library\Utils;

use App\Modules\Zeus\Model\Player;

$container = $this->getContainer();
if ($this->getContainer()->getParameter('environment') === 'dev' || $this->getContainer()->get('app.request')->query->get('key') === $this->getContainer()->getParameter('security_buffer_key')) {

	$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
	$security = $this->getContainer()->get(\Asylamba\Classes\Library\Security::class);
	
	$activePlayers = $playerManager->getByStatements([Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<title><?php echo $this->getContainer()->get('app.response')->getTitle() ?> — <?php echo $container->getParameter('app_subname') ?> — Asylamba</title>

	<meta charset="utf-8" />
	<meta name="description" content="<?php echo $container->getParameter('app_description') ?>" />

	<link rel="icon" type="image/png" href="<?php echo $container->getParameter('media') ?>/favicon/color1.png" />
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $container->getParameter('css') ?>buffer/main.css" />
</head>

<body>
	<h1><img src="http://asylamba.com/public/media/asylamba.png" alt="test" /></h1>

	<div class="content">
<?php
		echo '<a href="' . $container->getParameter('app_root') . 'inscription/bindkey-' . $security->crypt($security->buildBindkey(Utils::generateString(10))) . '">';
			echo '<em>Créer un</em>';
			echo '<strong>Personnage lvl. 1</strong>';
			echo '<img src="' . $container->getParameter('media') . 'avatar/big/empty.png" alt="" />';
		echo '</a>';

		if ($container->getParameter('highmode')) {
			echo '<a href="' . $container->getParameter('app_root') . 'inscription/bindkey-' . $security->crypt($security->buildBindkey(Utils::generateString(10))) . '/mode-high">';
				echo '<em>Créer un</em>';
				echo '<strong>Personnage lvl. 5</strong>';
				echo '<img src="' . $container->getParameter('media') . 'avatar/big/empty.png" alt=œ"" />';
				echo '<span class="number">+5</span>';
			echo '</a>';
		}

		foreach ($activePlayers as $player) {
			echo '<a class="color' . $player->rColor . '" href="' . $container->getParameter('app_root') . 'connection/bindkey-' . $security->crypt($security->buildBindkey($player->bind)) . '">';
				echo '<em>Grade ' . $player->level . '</em>';
				echo '<strong>' . $player->name . '</strong>';
				echo '<img src="' . $container->getParameter('media') . 'avatar/big/' . $player->avatar . '.png" alt="" />';
				echo '<span class="number">' . $player->level . '</span>';
			echo '</a>';
		}
?>
	</div>
</body>

<?php
}
