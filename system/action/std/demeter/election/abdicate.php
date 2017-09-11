<?php
#rplayer	id du joueur
#department

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get('session_wrapper');
$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');

$rPlayer = $request->request->get('rplayer');

if ($statusArray = ColorResource::getInfo($session->get('playerInfo')->get('color'), 'regime') == Color::DEMOCRATIC) {
    if ($session->get('playerInfo')->get('status') == Player::CHIEF) {
        $faction = $colorManager->get($session->get('playerInfo')->get('color'));
        
        if ($faction->electionStatement == Color::MANDATE) {
            $date = new \DateTime(Utils::now());
            $date->modify('-' . $faction->mandateDuration . ' second');
            $date = $date->format('Y-m-d H:i:s');
            $faction->dLastElection = $date;
            $session->addFlashbag('Des élections anticipées vont être lancées.', Flashbag::TYPE_SUCCESS);
        } else {
            throw new ErrorException('Des élections sont déjà en cours.');
        }
    } else {
        throw new ErrorException('Vous n\'êtes pas le chef de votre faction.');
    }
} else {
    if ($rPlayer !== false) {
        if ($session->get('playerInfo')->get('status') == Player::CHIEF) {
            if (($heir = $playerManager->get($rPlayer)) !== null) {
                if ($heir->rColor == $session->get('playerInfo')->get('color')) {
                    if ($heir->status >= Player::PARLIAMENT) {
                        $faction = $colorManager->get($session->get('playerInfo')->get('color'));

                        if ($faction->electionStatement == Color::MANDATE) {
                            $heir->status = Player::CHIEF;
                            // The player is now a member of Parliament
                            $playerManager->get($session->get('playerId'))->status = Player::PARLIAMENT;
                            $session->get('playerInfo')->add('status', Player::PARLIAMENT);

                            $statusArray = ColorResource::getInfo($heir->rColor, 'status');
                            $notif = new Notification();
                            $notif->setRPlayer($rPlayer);
                            $notif->setTitle('Héritier du Trône.');
                            $notif->addBeg()
                                ->addTxt('Vous avez été choisi par le ' . $statusArray[5] . ' de votre faction pour être son successeur, vous prenez la tête du gouvernement immédiatement.');
                            $notificationManager->add($notif);

                            $this->getContainer()->get('entity_manager')->flush();
                            $session->addFlashbag($heir->name . ' est désigné comme votre successeur.', Flashbag::TYPE_SUCCESS);
                        } else {
                            throw new ErrorException('vous ne pouvez pas abdiquer pendant un putsch.');
                        }
                    } else {
                        throw new ErrorException('Vous ne pouvez choisir qu\'un membre du sénat ou du gouvernement.');
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
        throw new ErrorException('Informations manquantes.');
    }
}
