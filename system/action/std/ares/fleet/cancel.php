<?php
# send a fleet to loot a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place attaquée

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Ares\Model\Commander;

$request = $this->getContainer()->get('app.request');

if (($request->query->get('commanderid')) === null) {
	throw new ErrorException('Manque de précision sur le commandant ou la position.');
}
$commanderId = $request->query->get('commanderid');

$commanderManager = $this->getContainer()->get('ares.commander_manager');
$session = $this->getContainer()->get('session_wrapper');

if (($commander = $commanderManager->get($commanderId)) === null || $commander->rPlayer !== $session->get('playerId')) {
	throw new ErrorException('Ce commandant ne vous appartient pas ou n\'existe pas.');
}
if ($commander->travelType == Commander::BACK) {
	throw new ErrorException('Vous ne pouvez pas annuler un retour.');
}

$interval = Utils::interval($commander->dArrival, Utils::now(), 's');
$dStart = new \DateTime(Utils::now());
$dStart->modify('-' . $interval . ' second');

$duration = Utils::interval($commander->dStart, $commander->dArrival, 's');

$dArrival = new \DateTime($dStart->format('Y-m-d H:i:s'));
$dArrival->modify('+' . $duration . ' second');

$rDestinationPlace = $commander->rDestinationPlace;
$commander->rDestinationPlace = $commander->rStartPlace;
$commander->rStartPlace = $rDestinationPlace;
$startPlaceName = $commander->startPlaceName;
$commander->startPlaceName = $commander->destinationPlaceName;
$commander->destinationPlaceName = $startPlaceName;
$commander->dStart = $dStart->format('Y-m-d H:i:s');
$commander->dArrival = $dArrival->format('Y-m-d H:i:s');
$commander->travelType = Commander::BACK;

if ($session->exist('playerEvent')) {
	for ($i = 0; $i < $session->get('playerEvent')->size(); $i++) {
		if ($session->get('playerEvent')->get($i)->get('eventInfo') != NULL) {
			if ($session->get('playerEvent')->get($i)->get('eventInfo')->get('id') == $commander->id) {
				$session->get('playerEvent')->remove($i);
			}
		}
	}

	$session->get('playerEvent')->add(
		$commander->dArrival,
		EVENT_OUTGOING_ATTACK,
		$commander->id,
		$commanderManager->getEventInfo($commander)
	);
}

$response = $this->getContainer()->get('app.response');

$session->addFlashbag('Déplacement annulé.', Flashbag::TYPE_SUCCESS);

if ($request->query->has('redirect')) {
	$response->redirect('map/place-' . $request->query->get('redirect'));
}

$this->getContainer()->get('entity_manager')->flush();