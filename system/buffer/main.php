<?php
if (DEVMODE || CTR::$get->equal('pwd', PWD_SCRIPT)) {
include_once ZEUS;

$S_PAM1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession(FALSE);
ASM::$pam->load(array('statement' => PAM_ACTIVE));

?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<title><?php echo CTR::getTitle() ?> — <?php echo APP_SUBNAME ?> — Expansion</title>

	<meta charset="utf-8" />
	<meta name="description" content="<?php echo APP_DESCRIPTION ?>" />

	<link rel="icon" type="image/png" href="<?php echo MEDIA ?>/favicon/color1.png" />
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo CSS ?>buffer/main.css" />
</head>

<body>
	<h1><img src="http://asylamba.com/public/media/asylamba.png" alt="test" /></h1>

	<div class="content">
<?php
		echo '<a href="' . APP_ROOT . 'inscription/bindkey-' . Utils::generateString(10) . '">';
			echo '<em>Créer un</em>';
			echo '<strong>Personnage</strong>';
			echo '<img src="' . MEDIA . 'avatar/big/000-0.png" alt="" />';
		echo '</a>';

		for ($i = 0; $i < ASM::$pam->size(); $i++) {
			$player = ASM::$pam->get($i);

			echo '<a class="color' . $player->rColor . '" href="' . APP_ROOT . 'connection/bindkey-' . $player->bind . '">';
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
ASM::$pam->changeSession($S_PAM1);
}