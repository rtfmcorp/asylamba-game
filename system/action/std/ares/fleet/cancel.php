<?php
# send a fleet to loot a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place attaquée

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Ares\Model\Commander;

$request = $this->getContainer()->get('app.request');
$scheduler = $this->getContainer()->get(\Asylamba\Classes\Scheduler\RealtimeActionScheduler::class);

if (($request->query->get('commanderid')) === null) {
	throw new ErrorException('Manque de précision sur le commandant ou la position.');
}
$commanderId = $request->query->get('commanderid');

$commanderManager = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\CommanderManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

if (($commander = $commanderManager->get($commanderId)) === null || $commander->rPlayer !== $session->get('playerId')) {
	throw new ErrorException('Ce commandant ne vous appartient pas ou n\'existe pas.');
}
if ($commander->travelType == Commander::BACK) {
	throw new ErrorException('Vous ne pouvez pas annuler un retour.');
}
$scheduler->cancel($commander, $commander->dArrival);

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

$response = $this->getContainer()->get('app.response');

$session->addFlashbag('Déplacement annulé.', Flashbag::TYPE_SUCCESS);

if ($request->query->has('redirect')) {
	$response->redirect('map/place-' . $request->query->get('redirect'));
}
$scheduler->schedule(
	'ares.commander_manager',
	'uReturnBase',
	$commander,
	$commander->dArrival, 
	[
		'class' => Place::class,
		'id' => $commander->getRPlaceDestination()
	]
);
$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush();
