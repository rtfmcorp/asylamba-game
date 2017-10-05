<?php

$request = $this->getContainer()->get('app.request');
$transactionManager = $this->getContainer()->get('hephaistos.transaction_manager');
$chargeManager = $this->getContainer()->get('hephaistos.charge_manager');
$donationManager = $this->getContainer()->get('hephaistos.donation_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('session_wrapper');


# background paralax
echo '<div id="background-paralax" class="sponsorship"></div>';

# inclusion des elements
include 'budgetElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
    include COMPONENT . 'publicity.php';
if ($request->query->has('view') && $request->query->get('view') === 'statistics') {
    $treasury = array_column($transactionManager->getTreasury(), 'treasury');
    array_walk($treasury, function(&$item) {
        $item = $item / 100;  
    });
    
    $monthlyIncome = $donationManager->getMonthlyIncome() / 100;
    $monthlyExpenses = $chargeManager->getMonthlyExpenses();
    array_walk($monthlyExpenses, function(&$item) {
        $item = -($item / 100);  
    });
    
    $globalIncome = $donationManager->getGlobalIncome() / 100;
    $globalExpenses = $chargeManager->getGlobalExpenses();
    array_walk($globalExpenses, function(&$item) {
        $item = -($item / 100);  
    });
    
    
    include COMPONENT . 'budget/statistics.php';
} else {
    $player = $playerManager->get($session->get('playerId'));
    $playerSum = $donationManager->getPlayerSum($player);
    $donations = $donationManager->getAllDonations();

    include COMPONENT . 'budget/infos.php';
    include COMPONENT . 'budget/donate.php';
    include COMPONENT . 'budget/donations.php';
}
echo '</div>';