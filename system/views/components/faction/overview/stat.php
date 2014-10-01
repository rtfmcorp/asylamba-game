<?php
# createTopic component
# in demeter.forum package

# création d'un topic

# require
$S_PAM_DGG = ASM::$pam->getCurrentSession();
ASM::$pam->changeSession($PLAYER_GOV_TOKEN);

# status list
$status = ColorResource::getInfo($faction->id, 'status');

# ranking
include_once ATLAS;
$S_FRM1 = ASM::$frm->getCurrentSession();
ASM::$frm->newSession();
ASM::$frm->loadLastContext();
for ($i = 0; $i < ASM::$frm->size(); $i++) { 
	if (ASM::$frm->get($i)->rFaction == $faction->id) {
		$factionRanking = ASM::$frm->get($i)->generalPosition;
		break;
	}
}
ASM::$frm->changeSession($S_FRM1);

# time variables
$startCampaign = Utils::addSecondsToDate($faction->dLastElection, ColorResource::getInfo($faction->id, 'mandateDuration'));
$endCampaign   = Utils::addSecondsToDate($faction->dLastElection, ColorResource::getInfo($faction->id, 'mandateDuration') + Color::CAMPAIGNTIME);

$startElection = Utils::addSecondsToDate($faction->dLastElection, ColorResource::getInfo($faction->id, 'mandateDuration') + Color::CAMPAIGNTIME);
$endElection   = Utils::addSecondsToDate($faction->dLastElection, ColorResource::getInfo($faction->id, 'mandateDuration') + Color::CAMPAIGNTIME + Color::ELECTIONTIME);

$startMandate  = $faction->dLastElection;
$endMandate    = $endElection;

# usefull variables
$totalMandate  = Utils::interval($startMandate, $endMandate, 's');
$remainMandate = Utils::interval(Utils::now(), $startMandate, 's');
$totalBeforeCampaing = Utils::interval($startMandate, $startCampaign, 's');

$totalCampEl   = Utils::interval($startCampaign, $endElection, 's');
$totalCampaing = Utils::interval($startCampaign, $endCampaign, 's');
$remainCampEl  = Utils::interval(Utils::now(), $startCampaign, 's');

echo '<div class="component size2 player">';
	echo '<div class="head">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Agenda politique</h4>';

			echo '<div class="faction-flow" style="margin: 10px 0 40px 0;">';
				echo '<div class="mandate" style="padding: 50px 20px 10px 20px;">';
					echo '<span class="progress-bar">';
						echo '<span style="width: ' . Format::percent($remainMandate, $totalMandate, FALSE) . '%;" class="content"></span>';
						echo '<span class="step" style="left: 0%;">';
							echo '<span class="label">Début du mandat courant</span>';
						echo '</span>';
						echo '<span class="step" style="left: 100%;">';
							echo '<span class="label right">Fin du mandat courant</span>';
						echo '</span>';
					echo '</span>';
				echo '</div>';

				echo '<div class="election" style="padding: 10px 20px;">';
					echo '<span class="progress-bar" style="width: ' . (100 - Format::percent($totalBeforeCampaing, $totalMandate, FALSE)) . '%; margin-left: auto;">';
						echo '<span style="width: ' . (Utils::now() > $startCampaign ? Format::percent($remainCampEl, $totalCampEl, FALSE) : '0') . '%;" class="content"></span>';
						echo '<span class="step" style="left: 0%;">';
							echo '<span class="label bottom">Campagne</span>';
						echo '</span>';
						echo '<span class="step" style="left: ' . Format::percent($totalCampaing, $totalCampEl, FALSE) . '%;">';
							echo '<span class="label bottom right">Elections</span>';
						echo '</span>';
						echo '<span class="step" style="left: 100%;">';
							echo '<span class="label bottom right">Résultats</span>';
						echo '</span>';
					echo '</span>';
				echo '</div>';
			echo '</div>';

			echo '<h4>Gouvernement actuel</h4>';

			for ($i = 0; $i < ASM::$pam->size(); $i++) { 
				echo '<div class="player">';
					echo '<a href="' . APP_ROOT . 'diary/player-' .  ASM::$pam->get($i)->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' .  ASM::$pam->get($i)->avatar . '.png" alt="' .  ASM::$pam->get($i)->name . '" />';
					echo '</a>';
					echo '<span class="title">' . $status[ ASM::$pam->get($i)->status - 1] . '</span>';
					echo '<strong class="name">' .  ASM::$pam->get($i)->name . '</strong>';
					echo '<span class="experience">' . Format::number( ASM::$pam->get($i)->factionPoint) . ' de prestige</span>';
				echo '</div>';
			}

			echo '<h4>Statistiques générales</h4>';

			echo '<div class="number-box half">';
				echo '<span class="label">Classement général de la faction</span>';
				echo '<span class="value">' . Format::rankingFormat($factionRanking) . '</span>';
			echo '</div>';
			echo '<div class="number-box half grey">';
				echo '<span class="label">Nombre de points de la faction</span>';
				echo '<span class="value">' . Format::number($faction->points) . '</span>';
			echo '</div>';
			echo '<div class="number-box half grey">';
				echo '<span class="label">Richesse de la faction</span>';
				echo '<span class="value">' . Format::number($faction->credits) . ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" /></span>';
			echo '</div>';
			echo '<div class="number-box half grey">';
				echo '<span class="label">Nombre de territoires contrôlés</span>';
				echo '<span class="value">' . Format::number($faction->sectors) . '</span>';
			echo '</div>';
			echo '<div class="number-box half grey">';
				echo '<span class="label">Nombre de joueurs actifs</span>';
				echo '<span class="value">' . Format::number($faction->activePlayers) . '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$pam->changeSession($S_PAM_DGG);