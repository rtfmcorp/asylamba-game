<?php

use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Model\CommercialRoute;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

use Asylamba\Modules\Hermes\Model\Conversation;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\ConversationMessage;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$routeColorBonus = $this->getContainer()->getParameter('athena.trade.route.color_bonus');
$routeSectorBonus = $this->getContainer()->getParameter('athena.trade.route.sector_bonus');
$entityManager = $this->getContainer()->get('entity_manager');

$baseFrom 	= $session->get('playerParams')->get('base');
$baseTo 	= $request->request->get('base_id');

if (empty($baseFrom) || empty($baseTo)) {
	throw new FormException('pas assez d\'informations pour proposer une route commerciale');
}
if (($proposerBase = $orbitalBaseManager->get($baseFrom)) === null) {
    throw new ErrorException('une erreur est survenue avec votre base orbitale. Veuillez contacter un administrateur');
}
if (($otherBase = $orbitalBaseManager->get($baseTo)) === null) {
    throw new FormException('la base indiquée n\'existe pas');
}

$nbrMaxCommercialRoute = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $proposerBase->getLevelSpatioport(), 'nbRoutesMax');

if ($commercialRouteManager->countBaseRoutes($proposerBase->getId()) >= $nbrMaxCommercialRoute) {
    throw new FormException('votre spatioport est trop petit pour héberger une nouvelle route');
}
if ($commercialRouteManager->isAlreadyARoute($proposerBase->getId(), $otherBase->getId())) {
    throw new FormException('une route est déjà opérationnelle entre vos deux bases');
}
if ($proposerBase->getLevelSpatioport() === 0) {
    throw new FormException('vous ne disposez pas d\'un spatioport');
}
if ($otherBase->getLevelSpatioport() === 0) {
    throw new FormException('cette base ne dispose pas d\'un spatioport');
}
if (($player = $playerManager->get($otherBase->getRPlayer())) === null) {
    throw new FormException('cette base n\'est pas détenue par un joueur');
}

$playerFaction = $colorManager->get($session->get('playerInfo')->get('color'));
$otherFaction = $colorManager->get($player->rColor);

if ($playerFaction->colorLink[$player->rColor] === Color::ENEMY || $otherFaction->colorLink[$playerFaction->getId()] === Color::ENEMY) {
    throw new ErrorException('impossible de proposer une route commerciale à ce joueur, vos factions sont en guerre.');
}

if ($proposerBase->getRPlayer() === $otherBase->getRPlayer()) {
    throw new FormException('vous ne pouvez pas créer de route entre deux de vos bases');
}

$distance = Game::getDistance($proposerBase->getXSystem(), $otherBase->getXSystem(), $proposerBase->getYSystem(), $otherBase->getYSystem());
$bonusA = ($proposerBase->getSector() != $otherBase->getSector()) ? $routeSectorBonus : 1;
$bonusB = ($session->get('playerInfo')->get('color')) != $player->getRColor() ? $routeColorBonus : 1;
$price = Game::getRCPrice($distance);
$income = Game::getRCIncome($distance, $bonusA, $bonusB);

if ($distance == 1) {
    $imageLink = '1-' . rand(1, 3);
} elseif ($distance < 26) {
    $imageLink = '2-' . rand(1, 3);
} elseif ($distance < 126) {
    $imageLink = '3-' . rand(1, 3);
} else {
    $imageLink = '4-' . rand(1, 3);
}

$priceWithBonus = 
    (in_array(ColorResource::COMMERCIALROUTEPRICEBONUS, $playerFaction->bonus))
    ? round($price - ($price * ColorResource::BONUS_NEGORA_ROUTE / 100))
    : $price
;

if ($session->get('playerInfo')->get('credit') < $priceWithBonus) {
    throw new ErrorException('impossible de proposer une route commerciale - vous n\'avez pas assez de crédits');
}
# création de la route
$cr = new CommercialRoute();
$cr->setROrbitalBase($proposerBase->getId());
$cr->setROrbitalBaseLinked($otherBase->getId());
$cr->setImageLink($imageLink);
$cr->setDistance($distance);
$cr->setPrice($price);
$cr->setIncome($income);
$cr->setDProposition(Utils::now());
$cr->setDCreation(NULL);
$cr->setStatement(0);
$commercialRouteManager->add($cr);

# débit des crédits au joueur
$playerManager->decreaseCredit($playerManager->get($session->get('playerId')), $priceWithBonus);

$n = new Notification();
$n->setRPlayer($otherBase->getRPlayer());
$n->setTitle('Proposition de route commerciale');
$n->addBeg()->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'));
$n->addTxt(' vous propose une route commerciale liant ');
$n->addLnk('map/place-' . $proposerBase->getRPlace(), $proposerBase->getName())->addTxt(' et ');
$n->addLnk('map/place-' . $otherBase->getRPlace(), $otherBase->getName())->addTxt('.');
$n->addSep()->addTxt('Les frais de l\'opération vous coûteraient ' . Format::numberFormat($priceWithBonus) . ' crédits; Les gains estimés pour cette route sont de ' . Format::numberFormat($income) . ' crédits par relève.');
$n->addSep()->addLnk('action/a-switchbase/base-' . $otherBase->getRPlace() . '/page-spatioport', 'En savoir plus ?');
$n->addEnd();
$notificationManager->add($n);

echo(json_encode([
    'route' => $cr,
    'message' => 'Route commerciale proposée'
]));

if (empty($content = $request->request->get('content'))) {
    return;
}
$conversationManager = $this->getContainer()->get('hermes.conversation_manager');
$conversationUserManager = $this->getContainer()->get('hermes.conversation_user_manager');
$conversationMessageManager = $this->getContainer()->get('hermes.conversation_message_manager');

$content = $this->getContainer()->get('parser')->parse($content);

if (strlen($content) > 10000) {
    throw new FormException('votre message est trop long');
}

$conv = new Conversation();
$conv->messages = 1;
$conv->type = Conversation::TY_USER;
$conv->dCreation = Utils::now();
$conv->dLastMessage = Utils::now();
$conversationManager->add($conv);

$user = new ConversationUser();
$user->rConversation = $conv->id;
$user->rPlayer = $session->get('playerId');
$user->convPlayerStatement = ConversationUser::US_ADMIN;
$user->convStatement = ConversationUser::CS_DISPLAY;
$user->dLastView = Utils::now();
$conversationUserManager->add($user);

$otherUser = new ConversationUser();
$otherUser->rConversation = $conv->id;
$otherUser->rPlayer = $otherBase->getRPlayer();
$otherUser->convPlayerStatement = ConversationUser::US_STANDARD;
$otherUser->convStatement = ConversationUser::CS_DISPLAY;
// From conversation start.php. To be refactored
$otherUser->dLastView = date('Y-m-d H:i:s', (strtotime(Utils::now()) - 20));
$conversationUserManager->add($otherUser);

$message = new ConversationMessage();

$message->rConversation = $conv->id;
$message->rPlayer = $session->get('playerId');
$message->type = ConversationMessage::TY_STD;
$message->content = $content;
$message->dCreation = Utils::now();
$message->dLastModification = NULL;

$conversationMessageManager->add($message);