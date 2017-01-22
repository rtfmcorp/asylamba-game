<?php
# modify investments action

# int baseId 		id de la base orbitale
# string category 	catégorie
# int credit 		nouveau montant à investir

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$credit = $request->request->get('credit');
$category = $request->query->get('category');


if ($baseId !== FALSE AND $credit !== FALSE AND $category !== FALSE AND in_array($baseId, $verif)) { 
		if (($base = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
			switch ($category) {
				case 'school':
					if ($credit <= 50000) {
						$base->setISchool($credit);
						$session->addFlashbag('L\'investissement dans l\'école de commandement de votre base ' . $base->getName() . ' a été modifié', Flashbag::TYPE_SUCCESS);
					} else {
						throw new ErrorException('La limite maximale d\'investissement dans l\'école de commandement est de 50\'000 crédits.');
					} 
					break;
				case 'antispy':
					if ($credit <= 100000) {
						$base->setIAntiSpy($credit);
						$session->addFlashbag('L\'investissement dans l\'anti-espionnage sur votre base ' . $base->getName() . ' a été modifié', Flashbag::TYPE_SUCCESS);
					} else {
						throw new ErrorException('La limite maximale d\'investissement dans l\'anti-espionnage est de 100\'000 crédits.');
					} 
					break;
				default:
					throw new ErrorException('modification d\'investissement impossible');
			}
		} else {
			throw new ErrorException('modification d\'investissement impossible - base inconnue');
		}
} else {
	throw new FormException('pas assez d\'informations pour modifier un investissement');
}