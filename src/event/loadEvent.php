<?php

use App\Classes\Library\Utils;

$container = $this->getContainer();
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$commanderManager = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\CommanderManager::class);
$placeManager = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\PlaceManager::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);

# chargement des événements concernant les flottes qui attaquent le joueur
if (Utils::interval($session->get('lastUpdate')->get('event'), Utils::now(), 's') > $container->getParameter('time_event_update')) {

	# update de l'heure dans le contrôleur
	$session->get('lastUpdate')->add('event', Utils::now());

	# ajout des événements des flottes entrantes dans le périmètre de contre-espionnage
	$places = array();
	for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
		$places[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
	}
	for ($i = 0; $i < $session->get('playerBase')->get('ms')->size(); $i++) {
		$places[] = $session->get('playerBase')->get('ms')->get($i)->get('id');
	}
	# mettre à jour dLastActivity
	$player = $playerManager->get($session->get('playerId'));
	$player->setDLastActivity(Utils::now());
	$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush($player);
}
