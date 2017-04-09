<?php

use Asylamba\Modules\Athena\Model\CommercialShipping;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Classes\Library\Format;

$transactionManager = $this->getContainer()->get('athena.transaction_manager');
$commercialShippingManager = $this->getContainer()->get('athena.commercial_shipping_manager');
$commercialTradeManager = $this->getContainer()->get('athena.commercial_tax_manager');
$session = $this->getContainer()->get('app.session');

$S_CSM1 = $commercialShippingManager->getCurrentSession();
$commercialShippingManager->changeSession($ob_compPlat->shippingManager);

$S_CTM1 = $commercialTradeManager->getCurrentSession();
$S_CTM2 = $commercialTradeManager->newSession();
$commercialTradeManager->load(array());

# work
$comingCommercialShipping = 0;
for ($i = 0; $i < $commercialShippingManager->size(); $i++) { 
	if ($commercialShippingManager->get($i)->statement == CommercialShipping::ST_GOING && $commercialShippingManager->get($i)->rBaseDestination == $ob_compPlat->getId()) {
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
				for ($i = 0; $i < $commercialShippingManager->size(); $i++) { 
					if ($commercialShippingManager->get($i)->statement == CommercialShipping::ST_GOING && $commercialShippingManager->get($i)->rBaseDestination == $ob_compPlat->getId()) {
						$commercialShippingManager->render($commercialShippingManager->get($i));
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

$transaction = $transactionManager->getLastCompletedTransaction(Transaction::TYP_RESOURCE)[0];
$ressourceCurrentRate = $transaction->currentRate;

$resourceTransactions = $transactionManager->getProposedTransactions(Transaction::TYP_RESOURCE);

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Ressources</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($ressourceCurrentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool sort-button">';
				echo '<span>trier par</span>';
				echo '<span><a href="#" data-sort-type="quantity" data-sort-direction="up" class="hb lt" title="quantité de ressources"><img src="' . MEDIA . 'resources/resource.png" class="icon-color" alt="ressources" /></a></span>';
				echo '<span><a href="#" data-sort-type="price" data-sort-direction="down" class="hb lt" title="prix"><img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></a></span>';
				echo '<span><a href="#" data-sort-type="far" data-sort-direction="down" class="hb lt" title="temps de trajet"><img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="temps" /></a></span>';
				echo '<span><a href="#" data-sort-type="cr" data-sort-direction="down" class="hb lt" title="cours de la marchandise"><img src="' . MEDIA . 'resources/rate.png" class="icon-color" alt="cours" /></a></span>';
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
		echo '<img src="' . MEDIA . 'orbitalbase/school.png" alt="commandants" class="main" />';
		echo '<h2>Commandants</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($commanderCurrentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool sort-button">';
				echo '<span>trier par</span>';
				echo '<span><a href="#" data-sort-type="xp" data-sort-direction="up" class="hb lt" title="expérience du commandant"><img src="' . MEDIA . 'resources/xp.png" class="icon-color" alt="experience" /></a></span>';
				echo '<span><a href="#" data-sort-type="price" data-sort-direction="down" class="hb lt" title="prix"><img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></a></span>';
				echo '<span><a href="#" data-sort-type="far" data-sort-direction="down" class="hb lt" title="temps de trajet"><img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="temps" /></a></span>';
				echo '<span><a href="#" data-sort-type="cr" data-sort-direction="down" class="hb lt" title="cours de la marchandise"><img src="' . MEDIA . 'resources/rate.png" class="icon-color" alt="cours" /></a></span>';
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

$shipTransactions = $transactionManager->egtProposedTransactions(Transaction::TYP_SHIP);

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'orbitalbase/dock2.png" alt="vaisseaux" class="main" />';
		echo '<h2>Vaisseaux</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($shipCurrentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool sort-button">';
				echo '<span>trier par</span>';
				echo '<span><a href="#" data-sort-type="quantity" data-sort-direction="up" class="hb lt" title="nombre de vaisseaux"><img src="' . MEDIA . 'resources/pev.png" class="icon-color" alt="pev" /></a></span>';
				echo '<span><a href="#" data-sort-type="price" data-sort-direction="down" class="hb lt" title="prix"><img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></a></span>';
				echo '<span><a href="#" data-sort-type="far" data-sort-direction="down" class="hb lt" title="temps de trajet"><img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="temps" /></a></span>';
				echo '<span><a href="#" data-sort-type="cr" data-sort-direction="down" class="hb lt" title="cours de la marchandise"><img src="' . MEDIA . 'resources/rate.png" class="icon-color" alt="cours" /></a></span>';
			echo '</div>';

			echo '<div class="sort-content">';
				foreach ($shipTransactions as $transaction) {
					if ($session->get('playerId') != $transaction) {
						$transactionManager->render($transaction, $shipCurrentRate, $S_CTM2, $ob_compPlat);
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$commercialShippingManager->changeSession($S_CSM1);
$commercialTradeManager->changeSession($S_CTM1);