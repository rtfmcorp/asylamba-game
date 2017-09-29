<?php

$donationManager = $this->getContainer()->get('hephaistos.donation_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('session_wrapper');

$player = $playerManager->get($session->get('playerId'));
$playerSum = $donationManager->getPlayerSum($player);
$donations = $donationManager->getAllDonations();

# background paralax
echo '<div id="background-paralax" class="sponsorship"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';
	include COMPONENT . 'budget/infos.php';
	include COMPONENT . 'budget/donate.php';
	include COMPONENT . 'budget/statistics.php';
echo '</div>';