<?php
# vérification du joueur
# ajout des informations dans le managers

use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Classes\Library\Utils;

$container = $this->getContainer();
$security = $this->getContainer()->get(\Asylamba\Classes\Library\Security::class);
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

# extraction du bindkey
$query  = $security->uncrypt($request->query->get('bindkey'));
$bindKey= $security->extractBindkey($query);
$time 	= $security->extractTime($query);

# vérification de la validité du bindkey
if (abs((int)$time - time()) > 300) {
	$response->redirect($this->getContainer()->getParameter('getout_root') . 'profil');
}

if (($player = $playerManager->getByBindKey($bindKey)) !== null && in_array($player->getStatement(), [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY])) {
	$player->synchronized = true;
	$player->setStatement(Player::ACTIVE);

	$session->initLastUpdate();
	$session->add('token', Utils::generateString(5));

	include $container->getParameter('connection') . '/create-session.php';

	# mise de dLastConnection + dLastActivity
	$player->setDLastConnection(Utils::now());
	$player->setDLastActivity(Utils::now());

	# confirmation au portail
	if ($this->getContainer()->getParameter('apimode') === 'enabled') {
		$this->getContainer()->get('api')->confirmConnection($bindKey);
	}
	$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush($player);
	// redirection vers page de départ
	$response->redirect(
		($request->query->get('mode') === 'splash')
		? 'profil/mode-splash'
		: 'profil'
	);
} else {
	$response->redirect('profil');
}
