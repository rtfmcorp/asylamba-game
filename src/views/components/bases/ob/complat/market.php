<?php

use App\Modules\Athena\Model\CommercialShipping;
use App\Modules\Athena\Model\Transaction;
use App\Classes\Library\Format;

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');
$transactionManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\TransactionManager::class);
$commercialShippingManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\CommercialShippingManager::class);
$commercialTradeManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\CommercialTaxManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

$S_CTM1 = $commercialTradeManager->getCurrentSession();
$S_CTM2 = $commercialTradeManager->newSession();
$commercialTradeManager->load(array());

# work
$comingCommercialShipping = 0;
foreach ($ob_compPlat->commercialShippings as $commercialShipping) { 
	if ($commercialShipping->statement === CommercialShipping::ST_GOING && $commercialShipping->rBaseDestination == $ob_compPlat->getId()) {
		$comingCommercialShipping++;
	}
}

if ($comingCommercialShipping > 0) {
	echo '<div class="component transaction">';
		echo '<div class="head skin-2">';
			echo '<h2>Aperçu des achats</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				echo '<h4>Convoi en approche</h4>';
				foreach ($ob_compPlat->commercialShippings as $commercialShipping) { 
					if ($commercialShipping->statement == CommercialShipping::ST_GOING && $commercialShipping->rBaseDestination == $ob_compPlat->getId()) {
						$commercialShippingManager->render($commercialShipping);
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

$transaction = $transactionManager->getLastCompletedTransaction(Transaction::TYP_RESOURCE);
$ressourceCurrentRate = $transaction->currentRate;

$resourceTransactions = $transactionManager->getProposedTransactions(Transaction::TYP_RESOURCE);

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . $mediaPath . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Ressources</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($ressourceCurrentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool sort-button">';
				echo '<span>trier par</span>';
				echo '<span><a href="#" data-sort-type="quantity" data-sort-direction="up" class="hb lt" title="quantité de ressources"><img src="' . $mediaPath . 'resources/resource.png" class="icon-color" alt="ressources" /></a></span>';
				echo '<span><a href="#" data-sort-type="price" data-sort-direction="down" class="hb lt" title="prix"><img src="' . $mediaPath . 'resources/credit.png" class="icon-color" alt="crédit" /></a></span>';
				echo '<span><a href="#" data-sort-type="far" data-sort-direction="down" class="hb lt" title="temps de trajet"><img src="' . $mediaPath . 'resources/time.png" class="icon-color" alt="temps" /></a></span>';
				echo '<span><a href="#" data-sort-type="cr" data-sort-direction="down" class="hb lt" title="cours de la marchandise"><img src="' . $mediaPath . 'resources/rate.png" class="icon-color" alt="cours" /></a></span>';
			echo '</div>';

			echo '<div class="sort-content">';
				foreach ($resourceTransactions as $transaction) {
					if ($session->get('playerId') != $transaction->rPlayer) {
						$transactionManager->render($transaction, $ressourceCurrentRate, $S_CTM2, $ob_compPlat);
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$commanderCurrentRate = $transactionManager->getLastCompletedTransaction(Transaction::TYP_COMMANDER)->currentRate;

$commanderTransactions = $transactionManager->getProposedTransactions(Transaction::TYP_COMMANDER);

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . $mediaPath . 'orbitalbase/school.png" alt="commandants" class="main" />';
		echo '<h2>Commandants</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($commanderCurrentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool sort-button">';
				echo '<span>trier par</span>';
				echo '<span><a href="#" data-sort-type="xp" data-sort-direction="up" class="hb lt" title="expérience du commandant"><img src="' . $mediaPath . 'resources/xp.png" class="icon-color" alt="experience" /></a></span>';
				echo '<span><a href="#" data-sort-type="price" data-sort-direction="down" class="hb lt" title="prix"><img src="' . $mediaPath . 'resources/credit.png" class="icon-color" alt="crédit" /></a></span>';
				echo '<span><a href="#" data-sort-type="far" data-sort-direction="down" class="hb lt" title="temps de trajet"><img src="' . $mediaPath . 'resources/time.png" class="icon-color" alt="temps" /></a></span>';
				echo '<span><a href="#" data-sort-type="cr" data-sort-direction="down" class="hb lt" title="cours de la marchandise"><img src="' . $mediaPath . 'resources/rate.png" class="icon-color" alt="cours" /></a></span>';
			echo '</div>';

			echo '<div class="sort-content">';
				foreach ($commanderTransactions as $transaction) {
					if ($session->get('playerId') != $transaction->rPlayer) {
						$transactionManager->render($transaction, $commanderCurrentRate, $S_CTM2, $ob_compPlat);
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$shipCurrentRate = $transactionManager->getLastCompletedTransaction(Transaction::TYP_SHIP)->currentRate;

$shipTransactions = $transactionManager->getProposedTransactions(Transaction::TYP_SHIP);

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . $mediaPath . 'orbitalbase/dock2.png" alt="vaisseaux" class="main" />';
		echo '<h2>Vaisseaux</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($shipCurrentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool sort-button">';
				echo '<span>trier par</span>';
				echo '<span><a href="#" data-sort-type="quantity" data-sort-direction="up" class="hb lt" title="nombre de vaisseaux"><img src="' . $mediaPath . 'resources/pev.png" class="icon-color" alt="pev" /></a></span>';
				echo '<span><a href="#" data-sort-type="price" data-sort-direction="down" class="hb lt" title="prix"><img src="' . $mediaPath . 'resources/credit.png" class="icon-color" alt="crédit" /></a></span>';
				echo '<span><a href="#" data-sort-type="far" data-sort-direction="down" class="hb lt" title="temps de trajet"><img src="' . $mediaPath . 'resources/time.png" class="icon-color" alt="temps" /></a></span>';
				echo '<span><a href="#" data-sort-type="cr" data-sort-direction="down" class="hb lt" title="cours de la marchandise"><img src="' . $mediaPath . 'resources/rate.png" class="icon-color" alt="cours" /></a></span>';
			echo '</div>';

			echo '<div class="sort-content">';
				foreach ($shipTransactions as $transaction) {
					if ($session->get('playerId') != $transaction->rPlayer) {
						$transactionManager->render($transaction, $shipCurrentRate, $S_CTM2, $ob_compPlat);
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$commercialTradeManager->changeSession($S_CTM1);
