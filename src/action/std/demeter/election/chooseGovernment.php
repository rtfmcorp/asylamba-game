<?php
#rplayer	id du joueur
#department

use App\Modules\Zeus\Model\Player;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Demeter\Resource\ColorResource;
use App\Classes\Exception\ErrorException;
use App\Classes\Library\Flashbag;


$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);

$rPlayer = $request->request->get('rplayer');
$department = $request->query->get('department');

if ($rPlayer !== FALSE && $department !== FALSE) {
	if (($minister = $playerManager->getGovernmentMember($session->get('playerInfo')->get('color'), $department)) === null) {
		if ($session->get('playerInfo')->get('status') == Player::CHIEF) {
			if (($appointee = $playerManager->get($rPlayer)) !== null) {
				if ($appointee->rColor == $session->get('playerInfo')->get('color')) {
					if ($appointee->status == Player::PARLIAMENT) {
						if ($department > Player::PARLIAMENT && $department < Player::CHIEF) {
							$appointee->status = $department;
							
							$statusArray = ColorResource::getInfo($appointee->rColor, 'status');
							$notif = new Notification();
							$notif->setRPlayer($rPlayer);
							$notif->setTitle('Nomination au gouvernement');
							$notif->addBeg()
								->addTxt('Vous avez été choisi pour être le ' . $statusArray[$department - 1] . ' de votre faction.');
							$notificationManager->add($notif);

							$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush($appointee);
							$session->addFlashbag($appointee->name . ' a rejoint votre gouvernement.', Flashbag::TYPE_SUCCESS);	
						} else {
							throw new ErrorException('Ce département est inconnu.');
						}
					} else {
						throw new ErrorException('Vous ne pouvez choisir qu\'un membre du sénat.');
					}
				} else {
					throw new ErrorException('Vous ne pouvez pas choisir un joueur d\'une autre faction.');
				}
			} else {
				throw new ErrorException('Ce joueur n\'existe pas.');
			}
		} else {
			throw new ErrorException('Vous n\'êtes pas le chef de votre faction.');	
		}
	} else {
		throw new ErrorException('Quelqu\'un occupe déjà ce poste.');	
	}
} else {
	throw new ErrorException('Informations manquantes.');
}