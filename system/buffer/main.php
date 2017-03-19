<?php

use Asylamba\Classes\Library\Utils;

if (DEVMODE || $this->getContainer()->get('app.request')->query->get('key') === KEY_BUFFER) {

	$playerManager = $this->getContainer()->get('zeus.player_manager');
	$security = $this->getContainer()->get('security');
	
	$activePlayers = $playerManager->getActivePlayers();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<title><?php echo $this->getContainer()->get('app.response')->getTitle() ?> — <?php echo APP_SUBNAME ?> — Asylamba</title>

	<meta charset="utf-8" />
	<meta name="description" content="<?php echo APP_DESCRIPTION ?>" />

	<link rel="icon" type="image/png" href="<?php echo MEDIA ?>/favicon/color1.png" />
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo CSS ?>buffer/main.css" />
</head>

<body>
	<h1><img src="http://asylamba.com/public/media/asylamba.png" alt="test" /></h1>

	<div class="content">
<?php
		echo '<a href="' . APP_ROOT . 'inscription/bindkey-' . $security->crypt($security->buildBindkey(Utils::generateString(10)), KEY_SERVER) . '">';
			echo '<em>Créer un</em>';
			echo '<strong>Personnage lvl. 1</strong>';
			echo '<img src="' . MEDIA . 'avatar/big/empty.png" alt="" />';
		echo '</a>';

		if (HIGHMODE) {
			echo '<a href="' . APP_ROOT . 'inscription/bindkey-' . $security->crypt($security->buildBindkey(Utils::generateString(10)), KEY_SERVER) . '/mode-high">';
				echo '<em>Créer un</em>';
				echo '<strong>Personnage lvl. 5</strong>';
				echo '<img src="' . MEDIA . 'avatar/big/empty.png" alt="" />';
				echo '<span class="number">+5</span>';
			echo '</a>';
		}

		foreach ($activePlayers as $player) {
			echo '<a class="color' . $player->rColor . '" href="' . APP_ROOT . 'connection/bindkey-' . $security->crypt($security->buildBindkey($player->bind), KEY_SERVER) . '">';
				echo '<em>Grade ' . $player->level . '</em>';
				echo '<strong>' . $player->name . '</strong>';
				echo '<img src="' . MEDIA . 'avatar/big/' . $player->avatar . '.png" alt="" />';
				echo '<span class="number">' . $player->level . '</span>';
			echo '</a>';
		}
?>
	</div>
</body>

<?php
}