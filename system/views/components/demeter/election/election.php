<?php
# require
$S_ELM_ELC = ASM::$elm->getCurrentSession();
ASM::$elm->changeSession($ELM_ELECTION_TOKEN);

$S_VOM_ELC = ASM::$vom->getCurrentSession();
ASM::$vom->changeSession($VOM_ELC_TOTAL_TOKEN);

$S_PAM_ELC = ASM::$pam->getCurrentSession();
ASM::$pam->changeSession($PAM_ELC_TOKEN);

echo '<div class="component profil">';
	echo '<div class="head skin-1">';
		echo '<h1>Election</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="center-box">';
				echo '<span class="label">Fin des élections le</span>';
				echo '<span class="value">' . Utils::addSecondsToDate($faction->dLastElection, (ColorResource::getInfo($faction->id, 'mandateDuration') + Color::CAMPAIGNTIME + Color::ELECTIONTIME)) . '</span>';
			echo '</div>';

			echo '<hr / style="margin-top: 25px;">';

			echo '<div class="number-box">';
				echo '<span class="label">nombre de candidats</span>';
				echo '<span class="value">' . $nbCandidate . '</span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">taux de participation</span>';
				echo '<span class="value">' . Format::percent(ASM::$vom->size(), ASM::$pam->size()) . ' %</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . Format::percent(ASM::$vom->size(), ASM::$pam->size()) . '%;" class="content"></span>';
				echo '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$pam->changeSession($S_PAM_ELC);
ASM::$vom->changeSession($S_VOM_ELC);
ASM::$elm->changeSession($S_ELM_ELC);
?>