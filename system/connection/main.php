<?php
# vérification du joueur
# ajout des informations dans le managers

use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\API;

$security = $this->getContainer()->get('security');
$request = $this->getContainer()->get('app.request');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('app.session');

# extraction du bindkey
$query  = $security->uncrypt($request->query->get('bindkey'), KEY_SERVER);
$bindKey= $security->extractBindkey($query);
$time 	= $security->extractTime($query);

# vérification de la validité du bindkey
if (abs((int)$time - time()) > 300) {
	header('Location: ' . GETOUT_ROOT . 'profil');
	exit();
}

if (($player = $playerManager->getByBindKey($bindKey)) !== null && in_array($player->getStatement(), [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY])) {
	$player->setStatement(Player::ACTIVE);

	$session->initLastUpdate();
	$session->add('token', Utils::generateString(5));

	include_once CONNECTION . '/create-session.php';

	# mise de dLastConnection + dLastActivity
	$player->setDLastConnection(Utils::now());
	$player->setDLastActivity(Utils::now());

	# confirmation au portail
	if (APIMODE) {
		$api = new API(GETOUT_ROOT, APP_ID, KEY_API);
		$api->confirmConnection($bindkey, APP_ID);
	}
	$this->getContainer()->get('entity_manager')->flush($player);
	// redirection vers page de départ
	$this->getContainer()->get('app.response')->redirect(
		($request->query->get('mode') === 'splash')
		? 'profil/mode-splash'
		: 'profil'
	);
} else {
	$this->getContainer()->get('app.response')->redirect('profil');
}
