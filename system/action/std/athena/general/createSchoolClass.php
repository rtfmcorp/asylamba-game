<?php
include_once ATHENA;
# create school class action

# int baseid 		id de la base orbitale
# int id 			type de base

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

if (CTR::$get->exist('baseid')) {
	$baseId = CTR::$get->get('baseid');
} elseif (CTR::$post->exist('baseid')) {
	$baseId = CTR::$post->get('baseid');
} else {
	$baseId = FALSE;
}
if (CTR::$get->exist('school')) {
	$school = CTR::$get->get('school');
} elseif (CTR::$post->exist('school')) {
	$school = CTR::$post->get('school');
} else {
	$school = FALSE;
}

if ($baseId !== FALSE AND $school !== FALSE AND in_array($baseId, $verif)) {
	include_once ARES;

	$school = intval($school);

	$name = array('Ametah', 'Anla', 'Aumshi', 'Bastier', 'Enigma', 'Eirukis', 'Erah', 'Ehdis', 'Fransa', 'Greider', 'Grerid', 'Haema', 'Hemhild', 'Renga', 'Hidar', 'Horski', 'Hreirek', 'Hroa', 'Hordis', 'Hydring', 'Imsin', 'Asmin', 'Ansami', 'Kar', 'Kili', 'Kolver', 'Kolfinna', 'Lisa', 'Marta', 'Meto', 'Leto', 'Ragni', 'Ranela', 'Runa', 'Siri', 'Mastro', 'Svenh', 'Thalestris', 'Thannd', 'Arsine', 'Val', 'Vori', 'Yi', 'Agata', 'Agneta', 'Nolgi', 'Edla', 'Else', 'Eyja', 'Jensine', 'Kirsten', 'Maeva', 'Malena', 'Magarte', 'Olava', 'Petrine', 'Rigmor', 'Signy', 'Sigrid', 'Skjorta');

	$nbrCommandersToCreate = rand(SchoolClassResource::getInfo($school, 'minSize'), SchoolClassResource::getInfo($school, 'maxSize'));
	
	// débit des crédits au joueur
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession(ASM_UMODE);
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
	ASM::$pam->get()->decreaseCredit(SchoolClassResource::getInfo($school, 'credit'));
	ASM::$pam->changeSession($S_PAM1);

	$S_COM1 = ASM::$com->getCurrentSession();
	ASM::$com->newSession(ASM_UMODE);

	for ($i = 0; $i < $nbrCommandersToCreate; $i++) {
		$newCommander = new Commander();
		$newCommander->upExperience(rand(SchoolClassResource::getInfo($school, 'minExp'), SchoolClassResource::getInfo($school, 'maxExp')));
		$newCommander->setRPlayer(CTR::$data->get('playerId'));
		$newCommander->setRBase($baseId);
		$newCommander->setPalmares(0);
		$newCommander->setStatement(0);
		$newCommander->setName($name[rand(0, (count($name) - 1))]);
		$newCommander->setAvatar('1');
		$newCommander->setDCreation(Utils::now());
		$newCommander->setUExperience(Utils::now());
		if (rand(0,9) < 8) {
			$newCommander->setSexe(1);
		} else {
			$newCommander->setSexe(2);
		}
		$newCommander->setAge(rand(40, 70));
		ASM::$com->add($newCommander);
	}
	CTR::$alert->add($nbrCommandersToCreate . ' commandant' . Format::addPlural($nbrCommandersToCreate) . ' inscrit' . Format::addPlural($nbrCommandersToCreate) . ' au programme d\'entraînement.', ALERT_STD_SUCCESS);
	ASM::$com->changeSession($S_COM1);
} else {
	CTR::$alert->add('pas assez d\'arguments', ALERT_STD_ERROR);
}