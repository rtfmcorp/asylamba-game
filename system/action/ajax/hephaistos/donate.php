<?php

use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$donationManager = $this->getContainer()->get('hephaistos.donation_manager');

$data = $request->request->get('data');
$amount = (int) $request->request->get('amount');

if ($data === null || !isset($data->id)) {
	throw new FormException('Erreur dans la requête AJAX');
}
if ($amount === null) {
	throw new FormException('Le montant n\'est pas spécifié');
}

$donation = $donationManager->createDonation($data->id, $amount);

$response->headers->add('Content-Type', 'application/json');
echo(json_encode([
    'donation' => $donation,
    'message' => 'Votre paiement a bien été validé ! Merci beaucoup pour votre soutien à Asylamba !'
]));