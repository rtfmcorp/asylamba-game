<?php

use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$stripeManager = $this->getContainer()->get('hephaistos.donation_manager');

$data = $request->request->get('data');
$amount = (int) $request->request->get('amount');

if ($data === null || !isset($data->id)) {
	throw new FormException('Erreur dans la requête AJAX');
}
if ($amount === null) {
	throw new FormException('Le montant n\'est pas spécifié');
}

$charge = $stripeManager->createCharge($data->id, $amount);

