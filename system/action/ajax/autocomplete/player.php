<?php
include_once ZEUS;
$S_PAM1 = ASM::$pam->newSession();
ASM::$pam->search($_GET['q']);

if (ASM::$pam->size() != 0) {
	for ($i = 0; $i < ASM::$pam->size(); $i++) {
		$player = ASM::$pam->get($i);

		echo '<img class="img" src="' . MEDIA . 'avatar/small/' . $player->getAvatar() . '.png" alt="' . $player->getName() . '" /> ';
		echo '<span class="value-1"><span class="ac_value">' . $player->getName() . '</span></span>';
		echo '<span class="value-2">grade ' . $player->getLevel() . '</span>';
		echo "\n";
	}
}
?>