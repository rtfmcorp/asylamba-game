<?php
# vérification du joueur
# ajout des informations dans le managers
include_once ZEUS;
include_once ATHENA;

$S_PAM1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession(ASM_UMODE);
ASM::$pam->load(array('bind' => CTR::$get->get('bindkey'), 'statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY)));

if (ASM::$pam->size() == 1) {
	$player = ASM::$pam->get();
	$player->setStatement(PAM_ACTIVE);

	# création des tableaux de données dans le contrôler
	CTRHelper::initializePlayerInfo();
	CTRHelper::initializePlayerBase();
	CTRHelper::initializePlayerBonus();

	# remplissage des données du joueur
	CTR::$data->add('playerId', $player->getId());

	CTR::$data->get('playerInfo')->add('color', $player->getRColor());
	CTR::$data->get('playerInfo')->add('name', $player->getName());
	CTR::$data->get('playerInfo')->add('avatar', $player->getAvatar());
	CTR::$data->get('playerInfo')->add('credit', $player->getCredit());
	CTR::$data->get('playerInfo')->add('actionPoint', $player->getActionPoint());
	CTR::$data->get('playerInfo')->add('experience', $player->getExperience());
	CTR::$data->get('playerInfo')->add('level', $player->getLevel());

	if (Utils::isAdmin($player->getBind())) {
		CTR::$data->get('playerInfo')->add('admin', TRUE);
	} else {
		CTR::$data->get('playerInfo')->add('admin', FALSE);
	}

	# remplissage des bases
	$S_OBM1 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlayer' => $player->getId()), array('dCreation', 'ASC'));
	for ($i = 0; $i < ASM::$obm->size(); $i++) {
		$base = ASM::$obm->get($i);
		CTRHelper::addBase(
			'ob', $base->getId(), 
			$base->getName(), 
			$base->getSector(), 
			$base->getSystem(), 
			'1-' . Game::getSizeOfPlanet($base->getPlanetPopulation())
		);
	}
	ASM::$obm->changeSession($S_OBM1);

	# remplissage des bonus
	$bonus = new PlayerBonus($player->getId());
	$bonus->initialize();

	# création des paramètres utilisateur
	CTR::$data->add('playerParams', new ArrayList());

	# mise de dLastConnection + dLastActivity
	$player->setDLastConnection(Utils::now());
	$player->setDLastActivity(Utils::now());

	# confirmation au portail
	$api = new API(GETOUT_ROOT);
	$api->confirmConnection(CTR::$get->get('bindkey'), APP_ID);

	# redirection vers page de départ
	CTR::redirect('profil');
} else { 
	header('Location: ' . GETOUT_ROOT . 'accueil/speak-noplayerfound');
	exit();
}

ASM::$pam->changeSession($S_PAM1);
?>