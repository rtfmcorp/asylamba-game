<?php
# createTopic component
# in demeter.forum package

use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Zeus\Model\Player;

$playerManager = $this->getContainer()->get('zeus.player_manager');
$factionRankingManager = $this->getContainer()->get('atlas.faction_ranking_manager');
$session = $this->getContainer()->get('app.session');
$sessionToken = $session->get('token');

# require
$S_PAM_DGG = $playerManager->getCurrentSession();
$playerManager->changeSession($PLAYER_GOV_TOKEN);

# status list
$status = ColorResource::getInfo($faction->id, 'status');

# ranking
$S_FRM1 = $factionRankingManager->getCurrentSession();
$factionRankingManager->newSession();
$factionRankingManager->loadLastContext();
for ($i = 0; $i < $factionRankingManager->size(); $i++) { 
	if ($factionRankingManager->get($i)->rFaction == $faction->id) {
		$factionRanking = $factionRankingManager->get($i)->generalPosition;
		break;
	}
}
$factionRankingManager->changeSession($S_FRM1);

echo '<div class="component size2 player new-message profil">';
	echo '<div class="head">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Agenda politique</h4>';

			if (in_array($faction->regime, array(Color::DEMOCRATIC, Color::THEOCRATIC))) {
				# time variables
				$startCampaign = Utils::addSecondsToDate($faction->dLastElection, ColorResource::getInfo($faction->id, 'mandateDuration'));
				$endCampaign   = Utils::addSecondsToDate($faction->dLastElection, ColorResource::getInfo($faction->id, 'mandateDuration') + Color::CAMPAIGNTIME);

				$startElection = Utils::addSecondsToDate($faction->dLastElection, ColorResource::getInfo($faction->id, 'mandateDuration') + Color::CAMPAIGNTIME);
				$endElection   = Utils::addSecondsToDate($faction->dLastElection, ColorResource::getInfo($faction->id, 'mandateDuration') + Color::CAMPAIGNTIME + Color::ELECTIONTIME);

				$startMandate  = $faction->dLastElection;
				$endMandate = $faction->regime == Color::DEMOCRATIC
					? $endElection
					: $endCampaign;

				# usefull variables
				$totalMandate  = Utils::interval($startMandate, $endMandate, 's');
				$remainMandate = Utils::interval(Utils::now(), $startMandate, 's');
				$totalBeforeCampaing = Utils::interval($startMandate, $startCampaign, 's');

				$totalCampEl   = Utils::interval($startCampaign, $endElection, 's');
				$totalCampaing = Utils::interval($startCampaign, $endCampaign, 's');
				$remainCampEl  = Utils::interval(Utils::now(), $startCampaign, 's');

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
								echo '<span class="label bottom right">Campagne</span>';
							echo '</span>';

							if ($faction->regime == Color::DEMOCRATIC) {
								echo '<span class="step" style="left: ' . Format::percent($totalCampaing, $totalCampEl, FALSE) . '%;">';
									echo '<span class="label bottom right">Elections</span>';
								echo '</span>';
								echo '<span class="step" style="left: 100%;">';
									echo '<span class="label bottom right">Résultats</span>';
								echo '</span>';
							} else {
								echo '<span class="step" style="left: 100%;">';
									echo '<span class="label bottom right">Décision divine</span>';
								echo '</span>';
							}
						echo '</span>';
					echo '</div>';
				echo '</div>';

				if ($faction->electionStatement == Color::CAMPAIGN) {
					echo $faction->regime == Color::DEMOCRATIC
						? '<a class="centred-link" href="' . APP_ROOT . 'faction/view-election">Présentez-vous aux élections</a>'
						: '<a class="centred-link" href="' . APP_ROOT . 'faction/view-election">Se proposer comme Guide</a>';
				} elseif ($faction->electionStatement == Color::ELECTION) {
					echo '<a class="centred-link" href="' . APP_ROOT . 'faction/view-election">Votez dès maintenant pour votre candidat favori</a>';
				}
			} else {
				echo '<div class="faction-flow" style="margin: 20px 0 30px 0;">';
					if ($faction->electionStatement == Color::ELECTION) {
						# time variables
						$startPutsch  = $faction->dLastElection;
						$endPutsch    = Utils::addSecondsToDate($faction->dLastElection, Color::PUTSCHTIME);

						# usefull variables
						$remainPutsch = Utils::interval(Utils::now(), $endPutsch, 's');

						echo '<div class="center-box">';
							echo '<span class="label">La tentative de coup d\'état se termine dans</span>';
							echo '<span class="value">' . Chronos::secondToFormat($remainPutsch, 'lite') . '</span>';
						echo '</div>';
						echo '<a class="centred-link" href="' . APP_ROOT . 'faction/view-election">Prendre position sur le coup d\'état</a>';
					} else {
						if (in_array($session->get('playerInfo')->get('status'), array(Player::WARLORD, Player::TREASURER, Player::MINISTER, Player::PARLIAMENT))) {
							echo '<a class="centred-link sh" href="#" data-target="makeacoup">Tenter un coup d\'état</a>';

							echo '<form action="' . Format::actionBuilder('makeacoup', $sessionToken) . '" method="post" id="makeacoup" style="display: none;">';
								echo '<p><label for="program">Votre message politique</label></p>';
								echo '<p class="input input-area"><textarea id="program" name="program" required style="height: 200px;"></textarea></p>';

								echo '<p class="button"><button type="submit">Lancer le coup d\'état</button></p>';
							echo '</form>';
						} else {
							echo '<span class="centred-link">Vous ne pouvez pas tenter un coup d\'état</span>';
						}
					}
				echo '</div>';
			}

			echo '<h4>Gouvernement actuel</h4>';

			for ($i = 0; $i < $playerManager->size(); $i++) { 
				echo '<div class="player">';
					echo '<a href="' . APP_ROOT . 'embassy/player-' .  $playerManager->get($i)->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' .  $playerManager->get($i)->avatar . '.png" alt="' .  $playerManager->get($i)->name . '" class="picto" />';
					echo '</a>';
					echo '<span class="title">' . $status[ $playerManager->get($i)->status - 1] . '</span>';
					echo '<strong class="name">' .  $playerManager->get($i)->name . '</strong>';
					echo '<span class="experience">' . Format::number( $playerManager->get($i)->factionPoint) . ' points</span>';
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
				echo '<span class="label">Nombre de points des territoires contrôlés</span>';
				echo '<span class="value">' . Format::number($faction->sectors) . '</span>';
			echo '</div>';
			echo '<div class="number-box half grey">';
				echo '<span class="label">Nombre de joueurs actifs</span>';
				echo '<span class="value">' . Format::number($faction->activePlayers) . '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$playerManager->changeSession($S_PAM_DGG);