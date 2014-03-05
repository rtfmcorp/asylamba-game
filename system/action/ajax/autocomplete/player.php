<?php
include_once ZEUS;
$S_PAM1 = ASM::$pam->newSession();
ASM::$pam->search($_GET['q']);

if (ASM::$pam->size() != 0) {
	for ($i = 0; $i < ASM::$pam->size(); $i++) {
		$player = ASM::$pam->get($i);

		if (in_array($player->getStatement(), array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY, PAM_BANNED))) {
			if ($player->getRColor() > 0) {
				$status = ColorResource::getInfo($player->getRColor(), 'status');
				$status = $status[$player->getStatus() - 1];
			} else {
				$status = 'Rebelle';
			}

			echo '<img class="img" src="' . MEDIA . 'avatar/small/' . $player->getAvatar() . '.png" alt="' . $player->getName() . '" /> ';
			echo '<span class="value-2">' . $status . '</span>';
			echo '<span class="value-1"><span class="ac_value">' . $player->getName() . '</span></span>';
			echo "\n";
		}
	}
}
?>