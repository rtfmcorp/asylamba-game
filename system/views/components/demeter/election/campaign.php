<?php
# require
$S_ELM_ELC = ASM::$elm->getCurrentSession();
ASM::$elm->changeSession($ELM_CAMPAIGN_TOKEN);

echo '<div class="component">';
	echo '<div class="head skin-1">';
		echo '<h1>Campagne</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="info">text de présentation des elections</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component profil">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="center-box">';
				echo '<span class="label">Fin de la campagne le</span>';
				echo '<span class="value">' . Chronos::secondToFormat(Utils::interval(Utils::now(), date('Y-m-d H:i:s', (strtotime($faction->dLastElection) + ColorResource::getInfo($faction->id, 'mandateDuration') + Color::CAMPAIGNTIME)), 's'), 'lite') . '</span>';
				echo '<span class="value">' . Chronos::transform(Utils::addSecondsToDate($faction->dLastElection, (ColorResource::getInfo($faction->id, 'mandateDuration') + Color::CAMPAIGNTIME)), FALSE, TRUE) . '</span>';
				echo '<span class="value">' . Utils::addSecondsToDate($faction->dLastElection, (ColorResource::getInfo($faction->id, 'mandateDuration') + Color::CAMPAIGNTIME)) . '</span>';
			echo '</div>';

			echo '<hr / style="margin-top: 25px;">';

			echo '<div class="number-box">';
				echo '<span class="label">nombre de candidats</span>';
				echo '<span class="value">' . $nbCandidate . '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$elm->changeSession($S_ELM_ELC);
?>