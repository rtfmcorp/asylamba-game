<?php
$S_VOM_ELC = ASM::$vom->getCurrentSession();
ASM::$vom->changeSession($VOM_ELC_TOTAL_TOKEN);

$S_PAM_ELC = ASM::$pam->getCurrentSession();
ASM::$pam->changeSession($PAM_ELC_TOKEN);

$follower = 0;
for ($i = 0; $i < ASM::$vom->size(); $i++) { 
	if (ASM::$vom->get($i)->rCandidate == $candidat->rPlayer) {
		$follower++;
	}
}

echo '<div class="component profil">';
	echo '<div class="head skin-1">';
		echo '<h1>Coup d\'état</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$startPutsch  = $faction->dLastElection;
			$endPutsch    = Utils::addSecondsToDate($faction->dLastElection, Color::PUTSCHTIME);
			$remainPutsch = Utils::interval(Utils::now(), $endPutsch, 's');
			
			echo '<div class="center-box">';
				echo '<span class="label">La tentative de coup d\'état se termine dans</span>';
				echo '<span class="value">' . Chronos::secondToFormat($remainPutsch, 'lite') . '</span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">Nombre de soutien pour réussir</span>';
				echo '<span class="value">' . $follower . '</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . Format::percent($follower, ASM::$pam->size()) . '%;" class="content"></span>';
					echo '<span class="step" style="left: ' . (Color::PUTSCHPERCENTAGE + 2) . '%;">';
						echo '<span class="label bottom">soutien nécessaire</span>';
					echo '</span>';
				echo '</span>';
					echo '<br />';
			echo '</div>';

			echo '<hr / style="margin-top: 25px;">';

			echo '<p class="info">' . ColorResource::getInfo(CTR::$data->get('playerInfo')->get('color'), 'campaignDesc') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$pam->changeSession($S_PAM_ELC);
ASM::$vom->changeSession($S_VOM_ELC);
?>