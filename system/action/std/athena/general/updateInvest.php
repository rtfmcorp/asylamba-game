<?php
# modify investments action

# int baseId 		id de la base orbitale
# string category 	catégorie
# int credit 		nouveau montant à investir

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');

for ($i=0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$baseId = $request->query->get('baseid');
$credit = $request->query->get('credit');
$category = $request->query->get('category');


if ($baseId !== FALSE AND $credit !== FALSE AND $category !== FALSE AND in_array($baseId, $verif)) { 
		$S_OBM1 = $orbitalBaseManager->getCurrentSession();
		$orbitalBaseManager->newSession(ASM_UMODE);
		$orbitalBaseManager->load(array('rPlace' => $baseId, 'rPlayer' => $session->get('playerId')));
		if ($orbitalBaseManager->size() == 1) {
			$base = $orbitalBaseManager->get();
			switch ($category) {
				case 'school':
					if ($credit <= 50000) {
						$base->setISchool($credit);
						$response->flashbag->add('L\'investissement dans l\'école de commandement de votre base ' . $base->getName() . ' a été modifié', Response::FLASHBAG_SUCCESS);
					} else {
						throw new ErrorException('La limite maximale d\'investissement dans l\'école de commandement est de 50\'000 crédits.');
					} 
					break;
				case 'antispy':
					if ($credit <= 100000) {
						$base->setIAntiSpy($credit);
						$response->flashbag->add('L\'investissement dans l\'anti-espionnage sur votre base ' . $base->getName() . ' a été modifié', Response::FLASHBAG_SUCCESS);
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
		$orbitalBaseManager->changeSession($S_OBM1);
} else {
	throw new FormException('pas assez d\'informations pour modifier un investissement');
}